<?php

namespace AppBundle\Controller;

use AppBundle\Form\UserType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;



/**
 * User controller.
 *
 */
class UserController extends Controller
{
    public function indexAction()
    {
        $user = $this->getUser();


        $entityManager = $this->getDoctrine()->getManager();
        $groups = $entityManager->getRepository('AppBundle:Group')->findAll();
        $users = $this->get('nouestil.user');
        return $this->render('AppBundle:Users:users.html.twig', array('users' => $users->getUsersList(), "groups" => $groups, "currentUser" => $user));

    }

    public function deleteUserAction($userId)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', 'Access denied', 'You cannot edit this item.');
        $userManager = $this->get('nouestil.user');
        $userToDelete = $userManager->getUser($userId);
        if ($userToDelete == $this->getUser()) {
            $this->addFlash('danger', 'Impossible de supprimer l\'utilisateur actuellement connecté.');
        } else {
            $userManager->deleteUser($userToDelete);
            $this->addFlash('success', 'L utilisateur a bien été supprimé');

        }
        return $this->redirect($this->generateUrl('users'));

    }

    public function updateUserAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', 'Access denied', 'You cannot edit this item.');
        if ($request->isMethod('POST')) {

            $data = $request->request->all();

            $user= $this->get('nouestil.user');

            $user->updateGroup($data['id'], $data['group']);
            $user->updateUser($data['id'],$data['lastname'], $data['firstname'], $data['birthdate'], $data['phoneNumber'], $data['email'], $data['publicNote'], $data['privateNote'] );

        }

        $this->addFlash('success', 'L utilisateur a bien été modifié');
        return $this->redirect($this->generateUrl('users'));
    }

    public function createUserAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', 'Access denied', 'You cannot edit this item.');
        $userManager = $this->get('nouestil.user');
        $formRegistration = $this->createForm(UserType::class);

        if ($request->isMethod('POST')) {
            $formRegistration->submit($request->request->get($formRegistration->getName()));
            // Enregistrer après soumission du formulaire les données dans l'objet $user
            if ($formRegistration->isSubmitted() && $formRegistration->isValid()) {
                $tokenGenerator = $this->get('fos_user.util.token_generator');
                $user = $formRegistration->getData();
                if (null === $user->getConfirmationToken()) {
                    $token = substr($tokenGenerator->generateToken(), 0, 20);
                    $user->setConfirmationToken($token);
                }
                $password = substr($tokenGenerator->generateToken(), 0, 8); // 8 chars
                $username = strtolower
                (substr($user->getFirstname(), 0, 1)
                    . $user->getLastname());
                $users = $userManager->getUsersLike($username);
                if (!empty($users)) {
                    $tempUsernameCount = $users[0]->getUsername();
                    $tempUsernameCount = str_replace($username, '', $tempUsernameCount);
                    $user->setUsername($username . ($tempUsernameCount + 1));
                } else {
                    $user->setUsername($username);
                }
                $user->setPlainpassword($password);
                $userManager->save($user);
                dump($user);
                $userManager->updateGroup($user,$user->getGroup()); //adds role for security access
                $view= $this->renderView('email/register_confirmed.email.twig', array(
                    'user' => $user,
                    'password' => $password,
                    'token' => $user->getConfirmationToken()
                ));
                $mailFrom= $this->getParameter('mailer_user');
                $mailTo= $user->getEmail();
                $this->get('mailer')->send($userManager->sendConfirmMail($view, $mailFrom, $mailTo));
                $this->addFlash('success', 'L\'utilisateur ' . $user->getUsername() . ' a bien été enregistré, veuillez maintenant lui créer un contact.');
                return $this->redirect($this->generateUrl("createContact"));
            }
        }
        return $this->render('AppBundle:Users:create.html.twig', array(
            'formRegistration' => $formRegistration->createView(),
        ));
    }

    public function unlinkContactAction($userId, $contactId)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', 'Access denied', 'You cannot edit this item.');
        $userManager = $this->get('nouestil.user');
        $contactManager = $this->get('nouestil.contact');
        $contactToDelete = $contactManager->getContactById($contactId);
        $userManager->unlinkContact($userId, $contactToDelete);
        $this->addFlash('success', 'Le contact a bien été délié');
        return $this->redirect($this->generateUrl('users'));

    }

    public function confirmUserAction($token, $userId){
        $userManager= $this->get('nouestil.user');
        $userToConfirm= $userManager->getUser($userId);
        if (!is_null($userToConfirm) && $userToConfirm->getConfirmationToken() === $token){
            $userToConfirm->setEnabled(true);
            $userManager->save($userToConfirm);
            $this->addFlash('success',  'Votre compte a bien été activé');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $this->addFlash('danger','Erreur lors de l\'activation de votre compte');
        return $this->redirect($this->generateUrl('fos_user_security_login'));

    }

}