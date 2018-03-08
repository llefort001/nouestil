<?php

namespace AppBundle\Controller;

use AppBundle\Form\PaymentType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Payment;

class PaymentController extends Controller
{
    /**
     * Lists all Payments.
     *
     */
    public function indexAction()
    {
//        $userManager = $this->get('fos_user.user_manager');
        $entityManager = $this->getDoctrine()->getManager();
//        $users = $userManager->findUsers();
        $payments = $entityManager->getRepository('AppBundle:Payment')->findAll();
        return $this->render('AppBundle:Payments:payments.html.twig', array(
//            'users' => $users,
            'payments' => $payments,
        ));
    }

//    public function withdrawAction($userId)
//    {
//        $entityManager = $this->getDoctrine()->getManager();
//        $userToWithdraw = $entityManager->getRepository('AppBundle:User')->find(array("id" => $userId));
//
//        if ($userToWithdraw instanceof User) {
//            $old_roticoin = $userToWithdraw->getRoticoin();
//            $value = 20000;
//            $new_roticoin = $old_roticoin - $value;
//            if ($new_roticoin < 0) {
//                $this->addFlash('Erreur', 'Impossible de retirer, solde insuffisant.');
//            } else {
//                $userToWithdraw->setRoticoin($new_roticoin);
//                $entityManager->flush();
//                //TODO:Actions a faire suite a un retrait
//            }
//        }
//        return $this->redirect($this->generateUrl('base_users'));
//
//
//    }
//
//    public function updateGroupUserAction($userId, $group)
//    {
//        $entityManager = $this->getDoctrine()->getManager();
//        $userToUpdate = $entityManager->getRepository('AppBundle:User')->find(array("id" => $userId));
//
//        if ($userToUpdate instanceof User) {
//            $groupToUpdate = $entityManager->getRepository('AppBundle:Group')->find(array("id" => $group));
//            $userToUpdate->setGroup($groupToUpdate);
//            $groupToUpdate->addUserGroup($userToUpdate);
//            $entityManager->flush();
//
//        }
//        return $this->redirect($this->generateUrl('base_users'));
//    }
//
//    public function updateUserAction(Request $request)
//    {
//
//        if ($request->isMethod('POST')) {
//            $data = $request->request->all();
//
//            $entityManager = $this->getDoctrine()->getManager();
//            $userToUpdate = $entityManager->getRepository('AppBundle:User')->find(array("id" => $data['id']));
//
//            $userToUpdate->setRoticoin($data['roticoin']);
//            $entityManager->flush();
//
//
//        }
//        return $this->redirect($this->generateUrl('base_users'));
//    }

    public function createPaymentAction(Request $request)
    {
        $formCreatePayment = $this->createForm(PaymentType::class);

        if ($request->isMethod('POST')) {

            // On récupère le gestionnaire d'entités
            $em = $this->getDoctrine()->getManager();

            $formCreatePayment->submit($request->request->get($formCreatePayment->getName()));
            // Enregistrer après soumission du formulaire les données dans l'objet $user

            if ($formCreatePayment->isSubmitted() && $formCreatePayment->isValid()) {


                $paymentData = $formCreatePayment->getData();
                $this->get('nouestil.payment')->save($paymentData);


                // on redirige l'administrateur vers la liste des clients si aucune erreur
                $this->addFlash('success', 'L\'utilisateur a bien été enregistré');
                return $this->redirect($this->generateUrl("users"));
            }
        }


        return $this->render('AppBundle:Payments:create.html.twig', array(
            'formCreatePayment' => $formCreatePayment->createView(),
        ));
    }
}