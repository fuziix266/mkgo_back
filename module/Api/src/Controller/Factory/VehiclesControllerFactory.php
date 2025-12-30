<?php

namespace Api\Controller\Factory;

use Api\Controller\VehiclesController;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Db\Adapter\Adapter;

class VehiclesControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new VehiclesController($container->get(Adapter::class));
    }
}
