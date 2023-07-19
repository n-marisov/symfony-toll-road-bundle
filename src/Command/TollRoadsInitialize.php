<?php

namespace Maris\Symfony\TollRoad\Command;

use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'toll_road:init')]
class TollRoadsInitialize extends Command
{

    protected static $defaultName = 'toll_road:init';

    protected ManagerRegistry $em;

    public function __construct( ManagerRegistry $manager )
    {
        $this->em = $manager;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        dd( $this->em->getManager( ) );
    }
}