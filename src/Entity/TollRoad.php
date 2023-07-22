<?php

namespace Maris\Symfony\TollRoad\Entity;

use Maris\Symfony\Geo\Entity\Location;

/***
 * Сущность терминала платной дороги.
 * Для одного терминала с поездами в
 * разные стороны будет создано 2 сущности.
 */
class TollRoad
{
    /**
     * ID в базе данных
     * @var int|null
     */
    protected ?int $id;

    /**
     * Уникальная строка однозначно
     * определяющая текущий терминал.
     * @var string|null
     */
    protected ?string $uuid = null;

    /***
     * Название трассы
     * @var TrackData
     */
    protected TrackData $trackData;

    /**
     * Название терминала
     * @var string
     */
    protected string $name;

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
     * Азимут по направлению движения.
     * @var float
     */
    protected float $bearing;

    /**
     * Цены за проезд
     * @var PriceBlock
     */
    protected PriceBlock $prices;

    /**
     * Uuid используется для однозначного определения
     * терминала, для одного и того же терминала всегда одинаково.
     * Для формирования используются данные,
     * которые в априори не могут быть изменены,
     * название трассы, километры начала и конца
     * участка.
     * Если по каким-то причинам эти данные были
     * изменены текущая сущность подлежит удалению,
     * и создается новая.
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid ?? $this->uuid = md5(serialize([
            "track" => [
                "name" => $this->trackData->getName(),
                "start" => $this->trackData->getStart(),
                "end" => $this->trackData->getEnd(),
                "terminal" => $this->trackData->getTerminal()
            ],
        ]));
    }


    public function isThis( self $tollRoad ):bool
    {
        return $this->getUuid() == $tollRoad->getUuid();
    }

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
     * @return TrackData
     */
    public function getTrackData(): TrackData
    {
        return $this->trackData;
    }

    /**
     * @param TrackData $trackData
     * @return $this
     */
    public function setTrackData(TrackData $trackData): self
    {
        $this->trackData = $trackData;
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
     * @return float
     */
    public function getBearing(): float
    {
        return $this->bearing;
    }

    /**
     * @param float $bearing
     * @return $this
     */
    public function setBearing(float $bearing): self
    {
        $this->bearing = $bearing;
        return $this;
    }

    /**
     * @return PriceBlock
     */
    public function getPrices(): PriceBlock
    {
        return $this->prices;
    }

    /**
     * @param PriceBlock $prices
     * @return $this
     */
    public function setPrices(PriceBlock $prices): self
    {
        $this->prices = $prices;
        return $this;
    }



}