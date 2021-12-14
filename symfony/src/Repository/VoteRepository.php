<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class VoteRepository extends EntityRepository
{

    public function findVoteByUserSubject($idSubject, $idUser)
    {

        $qb = $this
            ->createQueryBuilder('v')
            ->where('v.subject = :idsujet')
            ->andWhere('v.user = :iduser')
            ->setParameter('idsujet', $idSubject)
            ->setParameter('iduser', $idUser)
            ->setMaxResults(1)
            ;

        return $qb
            ->getQuery()
            ->getOneOrNullResult();            
    }
    
    public function findVotesBySubjectVote($idSubject, $title)
    {

        $qb = $this
            ->createQueryBuilder('v')
            ->select('COUNT(v)')
            ->where('v.subject = :idsujet')
            ->andWhere('v.vote LIKE :vote')
            ->setParameter('idsujet', $idSubject)
            ->setParameter('vote', $title)
            ;

        return $qb
            ->getQuery()
            ->getOneOrNullResult();            
    }

}