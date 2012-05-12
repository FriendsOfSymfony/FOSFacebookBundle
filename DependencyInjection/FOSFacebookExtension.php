<?php

/*
 * This file is part of the FOSFacebookBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\FacebookBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class FOSFacebookExtension extends Extension
{
    protected $resources = array(
        'facebook' => 'facebook.xml',
        'security' => 'security.xml',
    );

    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $this->loadDefaults($container);

        if (isset($config['alias'])) {
            $container->setAlias($config['alias'], 'fos_facebook.api');
        }

        foreach (array('api', 'helper', 'twig') as $attribute) {
            $container->setParameter('fos_facebook.'.$attribute.'.class', $config['class'][$attribute]);
        }

        foreach (array('app_id', 'secret', 'cookie', 'domain', 'logging', 'culture', 'permissions') as $attribute) {
            $container->setParameter('fos_facebook.'.$attribute, $config[$attribute]);
        }

        if (isset($config['file']) && $container->hasDefinition('fos_facebook.api')) {
            $facebookApi = $container->getDefinition('fos_facebook.api');
            $facebookApi->setFile($config['file']);
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__ . '/../Resources/config/schema';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getNamespace()
    {
        return 'http://symfony.com/schema/dic/fos_facebook';
    }

    /**
     * @codeCoverageIgnore
     */
    protected function loadDefaults($container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        foreach ($this->resources as $resource) {
            $loader->load($resource);
        }
    }
}
