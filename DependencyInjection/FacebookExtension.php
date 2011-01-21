<?php

namespace FOS\FacebookBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FacebookExtension extends Extension
{
    protected $resources = array(
        'facebook' => 'facebook.xml',
        'security' => 'security.xml',
    );

    public function apiLoad($config, ContainerBuilder $container)
    {
        if (!$container->hasDefinition('fos_facebook.api')) {
            $this->loadDefaults($container);
        }

        if (isset($config['alias'])) {
            $container->setAlias($config['alias'], 'fos_facebook.api');
        }

        foreach (array('class', 'file', 'app_id', 'secret', 'cookie', 'domain', 'logging', 'culture', 'permissions') as $attribute) {
            if (isset($config[$attribute])) {
                $container->setParameter('fos_facebook.' . $attribute, $config[$attribute]);
            }
        }

        if (isset($config['login_url']) && is_array($config['login_url'])) {
            foreach (array('cancel_url', 'canvas', 'display', 'fbconnect', 'next') as $attribute) {
                if (isset($config['login_url'][$attribute])) {
                    $container->setParameter('fos_facebook.login_url.' . $attribute, $config['login_url'][$attribute]);
                }
            }
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
        return 'http://www.symfony-project.org/schema/dic/fos_facebook';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getAlias()
    {
        return 'fos_facebook';
    }

    /**
     * @codeCoverageIgnore
     */
    protected function loadDefaults($container)
    {
        $loader = new XmlFileLoader($container, __DIR__ . '/../Resources/config');
        $loader->load($this->resources['facebook']);
        $loader->load($this->resources['security']);
    }
}
