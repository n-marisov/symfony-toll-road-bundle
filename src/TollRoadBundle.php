<?php

namespace Maris\Symfony\Address;

use Maris\Symfony\Address\DependencyInjection\TollRoadExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class TollRoadBundle extends AbstractBundle{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new TollRoadExtension();
    }

}