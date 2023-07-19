<?php

namespace Maris\Symfony\TollRoad\Entity;

/**
 * Класс родитель для всех ценовых правил.
 * Каждое правило действует для конкретного дня недели
 * т.е. если цена одинакова для всех дней недели будет создано 7 правил.
 */
abstract class PriceRule
{

    /**
     * ID в базе данных.
     * @var int|null
     */
    protected ?int $id;

    /**
     * Терминал, которому принадлежит правило.
     *
     * @var TollRoad
     */
    protected TollRoad $tollRoad;

    /**
     * День недели для которого действует данное правило.
     * Хранит в себе номер дня недели созданный функцией date().
     * @var int|null
     */
    protected ?int $weekDay = null;

    /**
     * Цена проезда.
     * @var float
     */
    protected float $price;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return TollRoad
     */
    public function getTollRoad(): TollRoad
    {
        return $this->tollRoad;
    }

    /**
     * @param TollRoad $tollRoad
     * @return $this
     */
    public function setTollRoad(TollRoad $tollRoad): self
    {
        $this->tollRoad = $tollRoad;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getWeekDay(): ?int
    {
        return $this->weekDay;
    }

    /**
     * @param int|null $weekDay
     * @return $this
     */
    public function setWeekDay(?int $weekDay): self
    {
        $this->weekDay = $weekDay;
        return $this;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     * @return $this
     */
    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }



}