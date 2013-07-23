<?php
namespace Publero\ApiNotificationBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler\DoctrineMongoDBMappingsPass;
use Publero\ApiNotificationBundle\DependencyInjection\PubleroApiNotificationExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PubleroApiNotificationBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $managerName = 'publero_token_authentication.model_manager_name';
        $mappings = [
            realpath(__DIR__ . '/Resources/config/doctrine/model') => __NAMESPACE__ . '\Model',
        ];

        if (class_exists('Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass')) {
            $type = PubleroApiNotificationExtension::STORAGE_DOCTRINE_DB_DRIVER . '_orm';
            $mappingDriver = DoctrineOrmMappingsPass::createYamlMappingDriver($mappings, [$managerName], $type);
            $container->addCompilerPass($mappingDriver);
        }

        if (class_exists('Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler\DoctrineMongoDBMappingsPass')) {
            $type = PubleroApiNotificationExtension::STORAGE_DOCTRINE_DB_DRIVER . '_mongodb';
            $mappingDriver = DoctrineMongoDBMappingsPass::createYamlMappingDriver($mappings, [$managerName], $type);
            $container->addCompilerPass($mappingDriver);
        }
    }
}
