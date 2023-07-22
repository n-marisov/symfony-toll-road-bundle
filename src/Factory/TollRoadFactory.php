<?php

namespace Maris\Symfony\TollRoad\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Maris\Symfony\Geo\Entity\Location;
use Maris\Symfony\TollRoad\Entity\PriceRuleI;
use Maris\Symfony\TollRoad\Entity\PriceRuleII;
use Maris\Symfony\TollRoad\Entity\PriceRuleIII;
use Maris\Symfony\TollRoad\Entity\PriceRuleVI;
use Maris\Symfony\TollRoad\Entity\TollRoad;
use ReflectionClass;

class TollRoadFactory
{

    protected ReflectionClass $reflection;

    protected ?TollRoad $instance = null;

    public function __construct(  )
    {
        $this->reflection = new ReflectionClass(TollRoad::class);
        //$this->priceRuleFactory = $priceRuleFactory;
    }

    private function start():self
    {
        $this->instance = $this->reflection->newInstance();
        return $this;
    }

    private function end():TollRoad
    {
        $instance = $this->instance ;
        $this->instance = null;
        return $instance;
    }


    protected function setName( string $name ):self
    {
        $this->reflection->getProperty("name")->setValue( $this->instance, $name );
        return $this;
    }

    protected function setTrack( string $track ):self
    {
        $this->reflection->getProperty("track")->setValue( $this->instance, $track );
        return $this;
    }

    protected function setStartTrackMark( int $mark ):self
    {
        $this->reflection->getProperty("startTrackMark")->setValue( $this->instance, $mark );
        return $this;
    }

    protected function setEndTrackMark( int $mark ):self
    {
        $this->reflection->getProperty("endTrackMark")->setValue( $this->instance, $mark );
        return $this;
    }

    protected function setLocation( Location $location ):self
    {
        $this->reflection->getProperty("location")->setValue( $this->instance, $location );
        return $this;
    }
    protected function setRoadside( Location $location ):self
    {
        $this->reflection->getProperty("roadside")->setValue( $this->instance, $location );
        return $this;
    }

    protected function setPrices( Collection $prices ):self
    {
        $this->reflection->getProperty("priceRules")->setValue( $this->instance, $prices );
        return $this;
    }

    public function create( array $data ):TollRoad
    {
        $pricesCollection = new ArrayCollection();
        foreach ($data["prices"] as $day => $prices ){
            foreach ($prices as $group => $price){
                $rule = match ( $group ){
                    0 => new PriceRuleI(),
                    1 => new PriceRuleII(),
                    2 => new PriceRuleIII(),
                    3 => new PriceRuleVI()
                };
                $rule->setWeekDay($day + 1 );
                $rule->setPrice( $price );
                $pricesCollection->add($rule);
            }
        }


        return $this->start()

                ->setName( $data["name"] )
                ->setTrack( $data["track"]["name"] )
                ->setStartTrackMark( $data["track"]["start"] )
                ->setEndTrackMark($data["track"]["end"] )
                ->setLocation( Location::fromString($data["location"]) )
                ->setRoadside( Location::fromString($data["roadside"]) )
                ->setPrices( $pricesCollection )

            ->end();
    }
}