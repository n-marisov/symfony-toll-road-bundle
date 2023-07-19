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

}