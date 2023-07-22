<?php

namespace Maris\Symfony\TollRoad\Command;

use Maris\Symfony\Geo\Factory\LocationFactory;
use Maris\Symfony\Geo\Service\EllipsoidalCalculator;
use Maris\Symfony\TollRoad\Entity\TollRoad;
use Maris\Symfony\TollRoad\Factory\TollRoadFactory;
use Maris\Symfony\TollRoad\Repository\TollRoadRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;


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
        $io = new SymfonyStyle( $input, $output );
        $countNew = 0;
        $countUpdate = 0;
        try {
            $data = $this->createData();
            $instances = $this->repository->findAll();

            array_map(function ( TollRoad $i, TollRoad $d ) use ( &$countNew, &$countUpdate ){
                if($i->isThis($d)){
                    # Обновляем данные
                    $i->setBearing($d->getBearing())
                        ->setTrackData($d->getTrackData())
                        ->setPrices($d->getPrices())
                        ->setLocation($d->getLocation())
                        ->setName($d->getName())
                        ->setParent($d->getParent());
                    $this->repository->save($i);
                    $countUpdate++;
                }else
                    $this->repository->save($d);
                $countNew++;
            }, $instances, $data );
        }catch (\Exception $exception ){
            $io->error($exception->getMessage());
            return  Command::FAILURE;
        }

        $io->success("Добавлено $countNew записей , обновлено $countUpdate $countNew.");

        return Command::SUCCESS;
    }

    /**
     * @return array<TollRoad>
     */
    public static function createData():array
    {
        $factory = new TollRoadFactory( new LocationFactory() );
        $calculator = new EllipsoidalCalculator();
        $locationFactory = new LocationFactory();
        $data = [];
        $startDir = __DIR__."/../../Resources/tollroads";
        foreach (scandir($startDir) as $dir)
            if(!in_array($dir,[".",".."]))
                foreach ( scandir("$startDir/$dir") as $file)
                    if(!in_array($file,[".",".."]))
                        foreach (Yaml::parseFile( "$startDir/$dir/$file" ) as $item){
                            if(isset($item["location2"])){
                                $item["bearing"] = $calculator->getInitialBearing(
                                    $locationFactory->create( $item["location2"] ),
                                    $locationFactory->create( $item["location2"] )
                                );
                            }
                            $data[] = $factory->create($item);
                        }
        return $data;
    }
}