<?php

namespace Maris\Symfony\TollRoad\Entity;

use Doctrine\Common\Collections\Collection;
use Maris\Symfony\Geo\Entity\Location;

/***
 * Сущность терминала платной дороги.
 * Для одного терминала с поездами в
 * разные стороны будет создано 2 сущности.
 */
class TollRoad
{
    protected ?int $id;

    /***
     * Название трассы
     * @var string
     */
    protected string $track;

    /**
     * Название терминала
     * @var string
     */
    protected string $name;

    /**
     * Километр (метка) трассы с которой начинается участок.
     * @var int
     */
    protected int $startTrackMark;

    /**
     * Километр (метка) трассы с которым заканчивается участок.
     * @var int
     */
    protected int $endTrackMark;

    /***
     * Родительский терминал.
     * Если путь проходил через него,
     * то плата за текущий участок
     * не взимается.
     * Вычисляется на основании названия
     * трассы и меток дороги.
     * @var TollRoad|null
     */
    protected ?TollRoad $parent = null;

    /**
     * Точка центра дороги на которой находится терминал.
     * @var Location
     */
    protected Location $location;

    /**
     * Точка на обочине возле терминала
     * @var Location
     */
    protected Location $roadside;

    /**
     * Ценовые правила для проезда через текущий терминал.
     * Может быть больше четырех.
     * @var Collection
     */
    protected Collection $priceRules;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTrack(): string
    {
        return $this->track;
    }

    /**
     * @param string $track
     * @return $this
     */
    public function setTrack(string $track): self
    {
        $this->track = $track;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getStartTrackMark(): int
    {
        return $this->startTrackMark;
    }

    /**
     * @param int $startTrackMark
     * @return $this
     */
    public function setStartTrackMark(int $startTrackMark): self
    {
        $this->startTrackMark = $startTrackMark;
        return $this;
    }

    /**
     * @return int
     */
    public function getEndTrackMark(): int
    {
        return $this->endTrackMark;
    }

    /**
     * @param int $endTrackMark
     * @return $this
     */
    public function setEndTrackMark(int $endTrackMark): self
    {
        $this->endTrackMark = $endTrackMark;
        return $this;
    }

    /**
     * @return TollRoad|null
     */
    public function getParent(): ?TollRoad
    {
        return $this->parent;
    }

    /**
     * @param TollRoad|null $parent
     * @return $this
     */
    public function setParent(?TollRoad $parent): self
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return Location
     */
    public function getLocation(): Location
    {
        return $this->location;
    }

    /**
     * @param Location $location
     * @return $this
     */
    public function setLocation(Location $location): self
    {
        $this->location = $location;
        return $this;
    }

    /**
     * @return Location
     */
    public function getRoadside(): Location
    {
        return $this->roadside;
    }

    /**
     * @param Location $roadside
     * @return $this
     */
    public function setRoadside(Location $roadside): self
    {
        $this->roadside = $roadside;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getPriceRules(): Collection
    {
        return $this->priceRules;
    }

    /**
     * @param Collection $priceRules
     * @return $this
     */
    public function setPriceRules(Collection $priceRules): self
    {
        $this->priceRules = $priceRules;
        return $this;
    }


    public static function create( array $data ):static
    {
        $instance = new static();

        $instance->name = $data["name"];
        $instance->track = $data["track"]["name"];
        $instance->startTrackMark = $data["track"]["start"];
        $instance->endTrackMark = $data["track"]["end"];

        $instance->location = Location::fromString($data["location"]);
        $instance->roadside = Location::fromString($data["roadside"]);

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
                $rule->setTollRoad( $instance );
            }
        }
        return $instance;
    }

}