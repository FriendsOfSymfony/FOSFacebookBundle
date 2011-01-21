<?php

namespace FOS\FacebookBundle\DependencyInjection\Security\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;

class FacebookFactory implements SecurityFactoryInterface
{
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $provider = 'security.authentication.provider.pre_authenticated.'.$id;
        $container
            ->register($provider, '%security.authentication.provider.pre_authenticated.class%')
            ->setArguments(array(new Reference($userProvider), new Reference('security.account_checker')))
        ;

        // listener
        $listenerId = 'fos_facebook.security.authentication.listener.'.$id;
        $listener = $container->setDefinition($listenerId, clone $container->getDefinition('fos_facebook.security.authentication.listener'));
        $arguments = $listener->getArguments();
        $arguments[1] = new Reference($provider);
        $listener->setArguments($arguments);

        return array($provider, $listenerId, 'fos_facebook.security.authentication.entry_point');
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'fos_facebook';
    }
}