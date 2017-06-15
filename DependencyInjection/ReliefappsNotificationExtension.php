<?php

namespace Reliefapps\NotificationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class ReliefappsNotificationExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('reliefapps_notification.android.server_key', $config['android']['server_key']);
        $container->setParameter('reliefapps_notification.android.gcm_server', $config['android']['gcm_server']);
        $container->setParameter('reliefapps_notification.ios.push_certificate', $config['ios']['push_certificate']);
        $container->setParameter('reliefapps_notification.ios.push_passphrase', $config['ios']['push_passphrase']);
        $container->setParameter('reliefapps_notification.ios.protocol', $config['ios']['protocol']);
        $container->setParameter('reliefapps_notification.ios.apns_server', $config['ios']['apns_server']);
        $container->setParameter('reliefapps_notification.ios.apns_topic', $config['ios']['apns_topic']);
        $container->setParameter('reliefapps_notification.device.class', $config['device']['class']);
        $container->setParameter('reliefapps_notification.model_manager_name', $config['model_manager_name']);

        if( array_key_exists("contexts", $config))
        {
            $container->setParameter('reliefapps_notification.contexts', $config["contexts"]);
        }
    }
}
