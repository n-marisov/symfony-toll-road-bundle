<?php

namespace Maris\Symfony\TollRoad\Entity;

/**
 * Ценовое правило
 */
class PriceBlock
{
    /**
     * Легковой транспорт.
     * Группа 1.
     * @var float
     */
    protected float $passenger;

    /**
     * Среднегабаритный транспорт.
     * Группа 2.
     * @var float
     */
    protected float $mediumSized;

    /**
     * Грузовой транспорт.
     * @var float
     */
    protected float $cargo;

    /**
     * Крупногабаритный транспорт.
     * @var float
     */
    protected float $largeSized;

    /**
     * @param float $passenger
     * @param float $mediumSized
     * @param float $cargo
     * @param float $largeSized
     */
    public function __construct( float $passenger, float $mediumSized, float $cargo, float $largeSized )
    {
        $this->passenger = $passenger;
        $this->mediumSized = $mediumSized;
        $this->cargo = $cargo;
        $this->largeSized = $largeSized;
    }


    /**
     * @return float
     */
    public function getPassenger(): float
    {
        return $this->passenger;
    }

    /**
     * @param float $passenger
     * @return $this
     */
    public function setPassenger(float $passenger): self
    {
        $this->passenger = $passenger;
        return $this;
    }

    /**
     * @return float
     */
    public function getMediumSized(): float
    {
        return $this->mediumSized;
    }

    /**
     * @param float $mediumSized
     * @return $this
     */
    public function setMediumSized(float $mediumSized): self
    {
        $this->mediumSized = $mediumSized;
        return $this;
    }

    /**
     * @return float
     */
    public function getCargo(): float
    {
        return $this->cargo;
    }

    /**
     * @param float $cargo
     * @return $this
     */
    public function setCargo(float $cargo): self
    {
        $this->cargo = $cargo;
        return $this;
    }

    /**
     * @return float
     */
    public function getLargeSized(): float
    {
        return $this->largeSized;
    }

    /**
     * @param float $largeSized
     * @return $this
     */
    public function setLargeSized(float $largeSized): self
    {
        $this->largeSized = $largeSized;
        return $this;
    }

    /**
     * Возвращает цену на основании высоты автомобиля и количества осей.
     * @param float $carHeight
     * @param int $calAxes
     * @return float
     */
    public function getPriceCalculate( float $carHeight , int $calAxes = 2 ):float
    {
        if($calAxes == 2 && $carHeight <= 2)
            return $this->passenger;
        elseif ( $carHeight <= 2.6 )
            return $this->mediumSized;
        elseif ($calAxes == 2 && $carHeight > 2.6 )
            return $this->cargo;

        return $this->largeSized;
    }

    /**
     * Возвращает цену за проезд на основании группы автомобиля.
     * @param int $group Число (номер группы) от 1 до 4.
     * @return float
     */
    public function getPriceGroup( int $group ):float
    {
        return match ($group){
            1 => $this->passenger,
            2 => $this->mediumSized,
            3 => $this->cargo,
            default => $this->largeSized
        };
    }

}