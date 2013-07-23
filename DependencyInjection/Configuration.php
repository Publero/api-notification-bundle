<?php
namespace Publero\ApiNotificationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('publero_api_notification', 'array');
        $rootNode
            ->children()
                ->arrayNode('storage_configuration')
                    ->children()
                        ->arrayNode('doctrine')
                            ->children()
                                ->enumNode('db_driver')
                                    ->values(['orm', 'mongodb'])
                                    ->defaultValue('orm')
                                    ->cannotBeOverwritten()
                                ->end()
                                ->scalarNode('class_name')->defaultValue('Publero\ApiNotificationBundle\Model\Notification')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('scheduler_configuration')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('increment')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->integerNode('increment_seconds')
                                    ->min(0)
                                    ->defaultValue(60)
                                ->end()
                                ->integerNode('max_seconds')
                                    ->min(0)
                                    ->defaultValue(3600)
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('notifier_class')->defaultValue('Publero\ApiNotificationBundle\Notifier\SimpleNotifier')->end()
                ->scalarNode('notifier_list')->defaultValue('publero_api_notification.notifier_list')->end()
                ->scalarNode('client')->defaultValue('publero_api_notification.client.buzz')->end()
                ->scalarNode('scheduler')->defaultValue('publero_api_notification.scheduler.incremental')->end()
                ->scalarNode('storage')->defaultValue('publero_api_notification.storage.doctrine')->end()
                ->arrayNode('hosts')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('uri')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('notifier_class')->end()
                            ->scalarNode('client')->end()
                            ->scalarNode('scheduler')->end()
                            ->scalarNode('storage')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
