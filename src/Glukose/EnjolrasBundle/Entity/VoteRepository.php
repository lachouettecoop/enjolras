<?php

namespace Glukose\EnjolrasBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

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

}