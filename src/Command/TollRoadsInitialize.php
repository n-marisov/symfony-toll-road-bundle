<?php

namespace Maris\Symfony\TollRoad\Command;

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
        dd( $this->repository );
    }
}