<?php

namespace Reliefapps\NotificationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

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
                    ->scalarNode('gcm_server')->defaultValue('android.googleapis.com')->end()
                ->end()
            ->end()
            ->arrayNode('ios')
                ->children()
                    ->scalarNode('push_certificate')->end()
                    ->scalarNode('push_passphrase')->end()
                    ->scalarNode('apns_server')->defaultValue('api.push.apple.com')->end()
                    ->scalarNode('apns_topic')->defaultValue('reliefapps_notification')->end()
                    ->enumNode('protocol')->values(array('legacy', 'http2'))->defaultValue('http2')->end()
                ->end()
            ->end()
        ->end();

        $this->addEntityManagementSection($rootNode);

        return $treeBuilder;
    }

    private function addEntityManagementSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('device')->addDefaultsIfNotSet()->canBeUnset()
                    ->children()
                        ->scalarNode('class')->end()
                        ->scalarNode('manager')->defaultValue('@reliefapps_notification.device.manager.doctrine')->end()
                    ->end()
                ->end()
                ->scalarNode('model_manager_name')->defaultNull()->end()
            ->end();

    }
}
