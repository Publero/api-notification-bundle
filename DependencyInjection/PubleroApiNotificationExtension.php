<?php
namespace Publero\ApiNotificationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class PubleroApiNotificationExtension extends Extension
{
    const STORAGE_DOCTRINE_DB_DRIVER = 'publero_api_notification.storage.doctrine.db_driver';

    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('service.yml');

        $this->createNotifiers($config, $container);
        if (!empty($config['storage_configuration']['doctrine'])) {
            $this->loadDoctrineStorage($container, $config, $loader);
        }
        $this->loadIncrementalScheduler($container, $config);
    }

    /**
     * @param ContainerBuilder $container
     * @param $config
     */
    private function createNotifiers(array $config, ContainerBuilder $container)
    {
        $notifierList = $container->getDefinition('publero_api_notification.notifier_list');

        foreach ($config['hosts'] as $code => $values) {
            $class = isset($values['notifier_class']) ? $values['notifier_class'] : $config['notifier_class'];
            $client = isset($values['client']) ? $values['client'] : $config['client'];
            $scheduler = isset($values['scheduler']) ? $values['scheduler'] : $config['scheduler'];
            $storage = isset($values['storage']) ? $values['storage'] : $config['storage'];

            $notifier = new Definition($class, [
                new Reference($client),
                new Reference($scheduler),
                new Reference($storage),
                $code,
                $values['uri'],
            ]);

            $container->setDefinition('publero_api_notification.notifier.' . $code, $notifier);
            $notifierList->addMethodCall('addNotifier', [$code, $notifier]);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param $config
     * @param $loader
     * @throws \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function loadDoctrineStorage(ContainerBuilder $container, $config, $loader)
    {
        $doctrineConfig = $config['storage_configuration']['doctrine'];
        $container->setParameter(self::STORAGE_DOCTRINE_DB_DRIVER . '_' . $doctrineConfig['db_driver'], true);
        $loader->load($doctrineConfig['db_driver'] . '.yml');

        $class = 'Publero\ApiNotificationBundle\Storage\DoctrineStorage';
        $definition = new Definition($class, [
            new Reference('doctrine.orm.entity_manager'),
            $doctrineConfig['class_name']
        ]);
        $container->setDefinition('publero_api_notification.storage.doctrine', $definition);
    }

    /**
     * @param ContainerBuilder $container
     * @param $config
     */
    public function loadIncrementalScheduler(ContainerBuilder $container, $config)
    {
        $schedulerConfig = $config['scheduler_configuration']['increment'];
        $container->setParameter('publero_api_notification.scheduler.incremental.increment_seconds', $schedulerConfig['increment_seconds']);
        $container->setParameter('publero_api_notification.scheduler.incremental.max_seconds', $schedulerConfig['max_seconds']);
    }
}
