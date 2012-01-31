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

use Symfony\Component\Config\Definition\Builder\TreeBuilder,
    Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author Lukas Kahwe Smith <smith@pooteeweet.org>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('fos_facebook');

        $rootNode
            ->fixXmlConfig('permission', 'permissions')
            ->children()
                ->scalarNode('app_id')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('secret')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('file')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('cookie')->defaultFalse()->end()
                ->scalarNode('domain')->defaultNull()->end()
                ->scalarNode('alias')->defaultNull()->end()
                ->scalarNode('logging')->defaultValue('%kernel.debug%')->end()
                ->scalarNode('culture')->defaultValue('en_US')->end()
                ->arrayNode('class')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('api')->defaultValue('FOS\FacebookBundle\Facebook\FacebookSessionPersistence')->end()
                        ->scalarNode('helper')->defaultValue('FOS\FacebookBundle\Templating\Helper\FacebookHelper')->end()
                        ->scalarNode('twig')->defaultValue('FOS\FacebookBundle\Twig\Extension\FacebookExtension')->end()
                    ->end()
                ->end()
                ->arrayNode('permissions')->prototype('scalar')->end()
            ->end();

        return $treeBuilder;
    }
}
