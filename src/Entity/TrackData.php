<?php

namespace Maris\Symfony\TollRoad\Entity;

class TrackData
{
    /**
     * Название трасы
     * @var string
     */
    protected string $name;

    /**
     * Километр дороги на котором расположено начало участка.
     * @var int
     */
    protected int $start;

    /**
     * Километр дороги на котором расположен конец участка.
     * @var int
     */
    protected int $end;

    /**
     * Километр дороги на котором расположен терминал оплаты.
     * @var int
     */
    protected int $terminal;

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
    public function getStart(): int
    {
        return $this->start;
    }

    /**
     * @param int $start
     * @return $this
     */
    public function setStart(int $start): self
    {
        $this->start = $start;
        return $this;
    }

    /**
     * @return int
     */
    public function getEnd(): int
    {
        return $this->end;
    }

    /**
     * @param int $end
     * @return $this
     */
    public function setEnd(int $end): self
    {
        $this->end = $end;
        return $this;
    }

    /**
     * @return int
     */
    public function getTerminal(): int
    {
        return $this->terminal;
    }

    /**
     * @param int $terminal
     * @return $this
     */
    public function setTerminal(int $terminal): self
    {
        $this->terminal = $terminal;
        return $this;
    }


}