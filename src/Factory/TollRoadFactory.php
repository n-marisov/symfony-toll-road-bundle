<?php

namespace Maris\Symfony\TollRoad\Factory;

use Maris\Interfaces\Geo\Factory\LocationFactoryInterface;
use Maris\Interfaces\Geo\Model\LocationInterface;
use Maris\Symfony\TollRoad\Entity\PriceBlock;
use Maris\Symfony\TollRoad\Entity\TollRoad;
use Maris\Symfony\TollRoad\Entity\TrackData;
use ReflectionClass;

class TollRoadFactory
{

    protected ReflectionClass $reflection;

    protected ?TollRoad $instance = null;

    protected LocationFactoryInterface $locationFactory;

    public function __construct( LocationFactoryInterface $locationFactory )
    {
        $this->reflection = new ReflectionClass(TollRoad::class);
        $this->locationFactory = $locationFactory;
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
        $instance->getUuid();
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

    protected function setLocation( LocationInterface $location ):self
    {
        $this->reflection->getProperty("location")->setValue( $this->instance, $location );
        return $this;
    }
    protected function setBearing( float $bearing ):self
    {
        $this->reflection->getProperty("bearing")->setValue( $this->instance, $bearing );
        return $this;
    }

    protected function setPrices( PriceBlock $prices ):self
    {
        $this->reflection->getProperty("prices")->setValue( $this->instance, $prices );
        return $this;
    }

    protected function setTrackData( TrackData $trackData ):self
    {
        $this->reflection->getProperty("trackData")->setValue( $this->instance, $trackData );
        return $this;
    }

    public function create( array $data ):TollRoad
    {

        return $this->start()

                ->setName( $data["name"] )
                ->setTrackData(
                    (new TrackData())
                        ->setName($data["track"]["name"])
                        ->setStart($data["track"]["start"])
                        ->setEnd($data["track"]["end"])
                        ->setTerminal($data["track"]["terminal"])
                )
                ->setLocation( $this->locationFactory->fromString( $data["location"] ) )
                ->setBearing($data["bearing"])
                ->setPrices( new PriceBlock( ...$data["prices"] ) )

            ->end();
    }
}