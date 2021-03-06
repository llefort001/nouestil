<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Course;
use Doctrine\ORM\EntityManager;

/**
 * Class CourseManager
 * @package AppBundle\Manager
 */
class CourseManager
{
    protected $style;
    protected $category;
    protected $timeslot;
    protected $professor;
    protected $em;

    /**
     * CourseManager constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em= $em;
    }


    /**
     * @param Course $course
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteCourse(Course $course)
    {
        $this->em->remove($course);
        $this->em->flush();
    }

    /**
     * @return mixed
     */
    public function getCourseList()
    {
        $course = $this->em
            ->getRepository('AppBundle:Course')
            ->findAll();
        return $course;
    }

    /**
     * @param $id
     * @param $name
     * @param $session
     * @return null|object
     */
    public function updateCourse($id, $style, $category,$timeslot, $userTeach)
    {
        $course= $this->em
            ->getRepository('AppBundle:Course')
            ->findOneById($id);
        $course->setStyle($style);
        $course->setCategory($category);
        $course->setTimeslot($timeslot);
        $teacher = $this->em->getRepository('AppBundle:User')->findById($userTeach);
        $course->setUserTeach($teacher[0]);
        try{
//            $this->em->persist($course);
            $this->em->flush();
        } catch (\exception $e){
            $e->getMessage();
        }

        return $course;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getCourse($id)
{
    $course= $this->em
        ->getRepository('AppBundle:Course')
        ->findOneById($id);
    if (!$course instanceof Course){
        throw $this->createNotFoundException(
            'Pas de cours'
        );
    }
    return $course;
}

    public function addCourse($style, $category, $timeslot,$userTeach)
    {
        $course =new Course();
        $course->setStyle($style);
        $course->setCategory($category);
        $course->setTimeslot($timeslot);
        $teacher = $this->em->getRepository('AppBundle:User')->findById($userTeach);
        $course->setUserTeach($teacher[0]);
        try{
            $this->em->persist($course);
            $this->em->flush();
        } catch (\exception $e){
            $e->getMessage();
        }
           return $course;
    }

    public function addUsersCourse($users, $id)
    {
        $course= $this->em
            ->getRepository('AppBundle:Course')
            ->findOneById($id);
        foreach ($users as $value) {
            $user = $this->em
                ->getRepository('AppBundle:User')
                ->findOneById($value);
            $course->addUser($user);
        }
        try{
            $this->em->flush();
        } catch (\exception $e){
            $e->getMessage();
        }
    }

    public function removeUserCourse($userId, $courseId)
    {
        $course= $this->em
            ->getRepository('AppBundle:Course')
            ->findOneById($courseId);
        $user= $this->em
            ->getRepository('AppBundle:User')
            ->findOneById($userId);
        $course->removeUser($user);
        try{
            $this->em->persist($course);
            $this->em->flush();
        } catch(\exception $e){
            $e->getMessage();
        }
    }

}
