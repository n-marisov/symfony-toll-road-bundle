<?php

namespace Maris\Symfony\TollRoad;

use Maris\Symfony\TollRoad\DependencyInjection\TollRoadExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class TollRoadBundle extends AbstractBundle{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new TollRoadExtension();
    }

}