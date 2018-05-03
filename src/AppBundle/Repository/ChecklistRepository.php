<?php

namespace AppBundle\Repository;

/**
 * ChecklistRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ChecklistRepository extends \Doctrine\ORM\EntityRepository
{
    public function queryLastChecklist($id)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('ch', 'co')
            ->from('AppBundle\Entity\Checklist', 'ch')
            ->leftJoin('ch.course', 'co')
            ->where('co.id= :id')
            ->orderBy('ch.datetime','DESC')
            ->setParameter('id', $id)
            ->setMaxResults(1);
        return $qb->getQuery()->getResult();
    }

    public function queryChecklists(){
        $qb= $this->_em->createQueryBuilder();
        $qb->select('ch')
            ->from('AppBundle\Entity\Checklist', 'ch')
            ->orderBy('ch.datetime','DESC');
        return $qb->getQuery()->getResult();
    }
}
