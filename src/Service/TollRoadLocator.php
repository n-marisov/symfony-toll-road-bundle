<?php

namespace Maris\Symfony\TollRoad\Service;

use Maris\Symfony\Geo\Entity\Polyline;
use Maris\Symfony\Geo\Service\GeoCalculator;
use Maris\Symfony\TollRoad\Entity\TollRoad;

/**
 * Определяет пересичеие платой дороги и полилинии
 */
class TollRoadLocator
{

    protected GeoCalculator $calculator;

    /**
     * @param GeoCalculator $calculator
     */
    public function __construct( GeoCalculator $calculator )
    {

        # Устанавливаем допустимое расстояние от терминала до линии 20 метров.
        $this->calculator = $calculator->build(allowed: 30);
    }


    public function intersect( TollRoad $tollRoad , Polyline $polyline ):bool
    {
        $bounds = $polyline->getBounds()->increase(20,$this->calculator);

        if(!$bounds->contains( $tollRoad->getLocation() ))
            return false;

        for ($i = 0, $j = 1; $polyline->offsetExists($j); $i = $j, $j++)
        {
            $p = (new Polyline())
                ->addLocation( $polyline[$i] )
                ->addLocation( $polyline[$j] );

            if($this->calculator->intersects( $p, $tollRoad->getLocation() )){
                $bearing = $this->calculator->getFinalBearing( $polyline[$i],$tollRoad->getLocation() );
                return abs($bearing - $tollRoad->getBearing() ) < 10;
            }
        }

        return false;
    }
}