<?php

namespace Maris\Symfony\TollRoad\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Maris\Symfony\Geo\Embeddable\Model\Entity\Bounds;
use Maris\Symfony\TollRoad\Entity\TollRoad;
/**
 * @extends ServiceEntityRepository<TollRoad>
 *
 * @method TollRoad|null find($id, $lockMode = null, $lockVersion = null)
 * @method TollRoad|null findOneBy(array $criteria, array $orderBy = null)
 * @method TollRoad[]    findAll()
 * @method TollRoad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TollRoadRepository extends ServiceEntityRepository
{
    public function __construct( ManagerRegistry $registry )
    {
        parent::__construct( $registry, TollRoad::class );
    }

    /***
     * Сохраняет в базу сущность или массив сущностей.
     * @param TollRoad|array $tollRoad
     * @param bool $flush
     * @return void
     */
    public function save( TollRoad|array $tollRoad , bool $flush = false ):void
    {
        if(is_array($tollRoad))
            foreach ($tollRoad as $value)
                if(is_a($tollRoad ,TollRoad::class))
                    $this->save( $value );
        else $this->getEntityManager()->persist( $tollRoad );

        if($flush)
            $this->getEntityManager()->flush();
    }

    /***
     * Удаляет из базы сущность или массив сущностей.
     * @param TollRoad|array $tollRoad
     * @param bool $flush
     * @return void
     */
    public function remove( TollRoad|array $tollRoad , bool $flush = false ):void
    {
        if(is_array($tollRoad))
            foreach ($tollRoad as $value)
                if(is_a($tollRoad ,TollRoad::class))
                    $this->save( $value );
                else $this->getEntityManager()->remove( $tollRoad );

        if($flush)
            $this->getEntityManager()->flush();
    }

    /**
     * Выбирает все сущности которые попадают
     * в переданный объект границ.
     * @param Bounds $bounds
     * @return array
     */
    public function findByBounds( Bounds $bounds ):array
    {
        return $this->createQueryBuilder("tr")

            ->leftJoin("tr.location","l")
            ->andWhere("l.latitude <= :north")
            ->andWhere("l.latitude >= :south")
            ->andWhere("l.longitude >= :west")
            ->andWhere("l.longitude <= :east")

            ->setParameter("north",$bounds->getNorth())
            ->setParameter("west",$bounds->getWest())
            ->setParameter("south",$bounds->getSouth())
            ->setParameter("east",$bounds->getEast())
            ->getQuery()->getResult();

        # Создать JOIN запрос для выборки по полю location.latitude и location.longitude
    }

}