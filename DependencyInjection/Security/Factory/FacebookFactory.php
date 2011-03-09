<?php

namespace FOS\FacebookBundle\DependencyInjection\Security\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;

class FacebookFactory extends AbstractFactory
{
    public function __construct()
    {
        $this->addOption('cancel_url', '');
        $this->addOption('canvas', 0);
        $this->addOption('display', 'page');
        $this->addOption('fbconnect', 1);
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'fos_facebook';
    }

    protected function getListenerId()
    {
        return 'fos_facebook.security.authentication.listener';
    }

    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        // with user provider
        if (isset($config['provider'])) {
            $authProviderId = 'fos_facebook.auth.'.$id;

            $container
                ->setDefinition($authProviderId, new DefinitionDecorator('fos_facebook.auth'))
                ->addArgument(new Reference($userProviderId))
                ->addArgument(new Reference('security.account_checker'))
            ;

            return $authProviderId;
        }

        // without user provider
        return 'fos_facebook.auth';
    }

    protected function createEntryPoint($container, $id, $config, $defaultEntryPointId)
    {
        $entryPointId = 'fos_facebook.security.authentication.entry_point.'.$id;
        $container
            ->setDefinition($entryPointId, new DefinitionDecorator('fos_facebook.security.authentication.entry_point'))
            ->setArgument(1, $config)
        ;

        // set options to container for use by other classes
        $container->setParameter('fos_facebook.options.'.$id, $config);

        return $entryPointId;
    }
}
