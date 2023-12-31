<?php

namespace Maris\Symfony\TollRoad\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Maris\Interfaces\Geo\Factory\LocationFactoryInterface;
use Maris\Symfony\TollRoad\Entity\TollRoad;
use Maris\Symfony\TollRoad\Factory\TollRoadFactory;
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

    protected TollRoadFactory $tollRoadFactory;

    protected LocationFactoryInterface $locationFactory;

    public function __construct( EntityManagerInterface $em , TollRoadFactory $tollRoadFactory, LocationFactoryInterface $locationFactory )
    {
        $this->em = $em;
        $this->tollRoadFactory = $tollRoadFactory;
        $this->locationFactory = $locationFactory;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle( $input, $output );
        $countNew = 0;
        $countUpdate = 0;
        $ids = [];
        try {
            $data = $this->createData();
            dump($data);
            $repository = $this->em->getRepository(TollRoad::class);

            foreach ( $data as $newTollRoad ){
                $tollRoad = $repository->findOneBy([
                    "uuid" => $newTollRoad->getUuid()
                ]);
                if(empty($tollRoad)){
                    $this->em->persist( $newTollRoad );
                    $countNew++;
                }elseif(!$newTollRoad->equals($tollRoad)){
                    $tollRoad->setBearing($newTollRoad->getBearing())
                        ->setTrackData($newTollRoad->getTrackData())
                        ->setPrices($newTollRoad->getPrices())
                        ->setLocation($newTollRoad->getLocation())
                        ->setName($newTollRoad->getName())
                        ->setParent($newTollRoad->getParent());
                    $ids[] = $tollRoad->getId();
                    $this->em->persist( $tollRoad );
                    $countUpdate++;
                    $this->em->persist($tollRoad);
                }
            }

            $all = $repository->findAll();

            $ignore = count(array_filter($all,fn (TollRoad $t) => !in_array($t->getId(),$ids)));

            $this->em->flush();
        }catch (\Exception $exception ){
            $io->error($exception->getMessage());
            return  Command::FAILURE;
        }




        $io->success("Добавлено $countNew, обновлено $countUpdate, проигнорировано $ignore записей.");
        return Command::SUCCESS;
    }

    /**
     * @return array<TollRoad>
     */
    public function createData():array
    {
        $data = [];
        $startDir = __DIR__."/../../Resources/tollroads";
        foreach (scandir($startDir) as $dir)
            if(!in_array($dir,[".",".."]))
                foreach ( scandir("$startDir/$dir") as $file)
                    if(!in_array($file,[".",".."]))
                        $data[] = $this->tollRoadFactory->create( Yaml::parseFile( "$startDir/$dir/$file" ) );

                        /*foreach (Yaml::parseFile( "$startDir/$dir/$file" ) as $item){
                            /*if(isset($item["location2"])){
                                $item["bearing"] = $calculator->getInitialBearing(
                                    $this->locationFactory->fromString( $item["location2"] ),
                                    $this->locationFactory->fromString( $item["location2"] )
                                );
                            }*/
                           /* dump( $item );
                            $data[] = $this->tollRoadFactory->create($item);
                        }*/
        return $data;
    }
}