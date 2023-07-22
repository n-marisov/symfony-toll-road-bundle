<?php

namespace Maris\Symfony\TollRoad\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Maris\Symfony\Geo\Factory\LocationFactory;
use Maris\Symfony\Geo\Service\EllipsoidalCalculator;
use Maris\Symfony\TollRoad\Entity\TollRoad;
use Maris\Symfony\TollRoad\Factory\TollRoadFactory;
use Maris\Symfony\TollRoad\Repository\TollRoadRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;

#[AsCommand(
    name: 'tollroad:init',
    description: "Команда записывает в базу данных все платные дороги."
)]
class TollRoadsInitialize extends Command
{
    protected TollRoadRepository $repository;

    protected ManagerRegistry $doctrine;

    protected EntityManager $em;

    public function __construct( TollRoadRepository $repository , ManagerRegistry $doctrine   )
    {
        $this->repository = $repository;
        $this->doctrine = $doctrine;
        $this->em = $doctrine->getManager();
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle( $input, $output );
        $countNew = 0;
        $countUpdate = 0;
        try {
            $data = $this->createData();
            $instances = $this->repository->findAll() ?? [];

            foreach ($data as $tollRoad){
                $arr = array_filter( $instances, fn (TollRoad $i) => $tollRoad->isThis($i) );
                if(isset($arr[0])){
                    # Обновляем данные
                    $arr[0]->setBearing($tollRoad->getBearing())
                        ->setTrackData($tollRoad->getTrackData())
                        ->setPrices($tollRoad->getPrices())
                        ->setLocation($tollRoad->getLocation())
                        ->setName($tollRoad->getName())
                        ->setParent($tollRoad->getParent());
                    $this->repository->save($arr[0],true);
                    $countUpdate++;
                }else{
                    $this->repository->save($tollRoad,true);
                    $countNew++;
                }
            }
            dump($this->repository);
            dump($this->em);
            //$this->em->flush();
        }catch (\Exception $exception ){
            $io->error($exception->getMessage());
            return  Command::FAILURE;
        }

        $io->success("Добавлено $countNew записей , обновлено $countUpdate.");
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