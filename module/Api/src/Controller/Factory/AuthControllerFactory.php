<?php

namespace Api\Controller\Factory;

use Api\Controller\AuthController;
use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Db\Adapter\Adapter;

class AuthControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new AuthController($container->get(Adapter::class));
    }
}
