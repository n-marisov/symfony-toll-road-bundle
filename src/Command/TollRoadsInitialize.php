<?php

namespace Maris\Symfony\TollRoad\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
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
    description: "Команда записывает в базу данных все платные дороги.",
    hidden: false
)]
class TollRoadsInitialize extends Command
{
    protected EntityManager $em;

    public function __construct( EntityManagerInterface $em )
    {
        $this->em = $em;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle( $input, $output );
        $countNew = 0;
        $countUpdate = 0;
        try {
            $data = $this->createData();
            $instances = $this->em->getRepository(TollRoad::class)->findAll() ?? [];

            foreach ( $data as $newTollRoad)
                foreach ($instances as $tollRoad)
                    if($newTollRoad->isThis($tollRoad)) {
                        $tollRoad->setBearing($tollRoad->getBearing())
                            ->setTrackData($tollRoad->getTrackData())
                            ->setPrices($tollRoad->getPrices())
                            ->setLocation($tollRoad->getLocation())
                            ->setName($tollRoad->getName())
                            ->setParent($tollRoad->getParent());
                        $this->em->persist( $tollRoad );
                        $countUpdate++;
                    } else {
                        $this->em->persist( $newTollRoad );
                        $countNew++;
                    }
            $this->em->flush();
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