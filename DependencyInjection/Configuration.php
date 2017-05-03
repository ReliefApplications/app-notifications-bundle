<?php

namespace Reliefapps\NotificationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('reliefapps_notification');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        $rootNode
            ->children()
                ->arrayNode('android')
                    ->children()
                        ->scalarNode('server_key')->end()
                    ->end()
                ->end()
                ->arrayNode('ios')
                    ->children()
                        ->scalarNode('push_certificate')->end()
                        ->scalarNode('push_passphrase')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
