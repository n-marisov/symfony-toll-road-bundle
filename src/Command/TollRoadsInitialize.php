<?php

namespace Maris\Symfony\TollRoad\Command;

use Maris\Symfony\TollRoad\Entity\TollRoad;
use Maris\Symfony\TollRoad\Repository\TollRoadRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

//#[AsCommand(name: 'toll_road:init')]
class TollRoadsInitialize extends Command
{
    protected static $defaultName = 'tollroad:init';

    protected TollRoadRepository $repository;

    public function __construct( TollRoadRepository $repository )
    {
        $this->repository = $repository;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $data = $this->createData();
        foreach ( $this->createData() as $datum )
            $this->repository->save($datum);

        dd( $data );
    }

    /**
     * @return array<TollRoad>
     */
    protected function createData():array
    {
        # Индекс номер группы -1 значение цена за проезд
        $price = [ 150, 180, 220, 370 ];
        # Индексы номер недели -1
        $prices = [
            $price,$price,$price,$price,$price,$price,$price
        ];

        return [
            TollRoad::create([
                "location" => "55.600250, 36.980265",
                "roadside" => "55.600431, 36.980196",
                "name" => "ПВП 46 КМ",
                "track" => [
                    "name" => "M-1 Белорусь",
                    "start" => "33",
                    "end" => "66"
                ],
                "prices" => $prices
            ]),
            TollRoad::create([
                "location" => "55.600125, 36.980347",
                "roadside" => "55.599683, 36.980564",
                "name" => "ПВП 46 КМ",
                "track" => [
                    "name" => "M-1 Белорусь",
                    "start" => "66",
                    "end" => "33"
                ],
                "prices" => $prices
            ]),
        ];
    }
}