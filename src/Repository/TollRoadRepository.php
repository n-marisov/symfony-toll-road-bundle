<?php

namespace Maris\Symfony\TollRoad\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Maris\Symfony\TollRoad\Entity\TollRoad;

class TollRoadRepository extends ServiceEntityRepository
{
    public function __construct( ManagerRegistry $registry )
    {
        parent::__construct( $registry, TollRoad::class );
    }


    public function save( TollRoad $tollRoad , bool $flush = true ):void
    {
        $this->getEntityManager()->persist( $tollRoad );

        if($flush)
            $this->getEntityManager()->flush();
    }

}