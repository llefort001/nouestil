<?php

namespace AppBundle\Repository;


/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{
    public function findUsersByGroup($id = null)
    {
        if (!isset($id))
            return null;

        $qb = $this->_em->createQueryBuilder();
        $qb->select('u', 'g')
            ->from('AppBundle\Entity\User', 'u')
            ->leftJoin('u.group', 'g')
            ->where('u.group = :id')
            ->setParameter('id', $id);

        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function findProfessor()
    {
        $qb= $this->_em->createQueryBuilder();
        $qb->select('u','g')
            ->from('AppBundle\Entity\User','u')
            ->leftJoin('u.group','g')
            ->where('g.name= :prof')
            ->setParameter('prof', 'professor');
        $query= $qb->getQuery();
        return $qb;
    }

    public function queryNotUserCourse($courseId)
    {
        $qb2= $this->_em->createQueryBuilder();
        $qb2->select('c')
            ->from('AppBundle\Entity\Course','c')
            ->where('c.id= :id')
            ->setParameter('id', $courseId);


        return $qb2;
    }

}
