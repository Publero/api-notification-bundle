<?php
namespace Publero\ApiNotificationBundle\Tests\DependencyInjection;

use Publero\ApiNotificationBundle\DependencyInjection\PubleroApiNotificationExtension;
use Publero\Component\Test\ExtensionTestCase;

class PubleroApiNotificationExtensionTest extends ExtensionTestCase
{
    public function testLoadFullConfiguration()
    {
        $config = $this->parseConfig($this->getFullConfig());

        $this->loadExtension(new PubleroApiNotificationExtension(), [$config]);
        $container = $this->getContainer();

        $dbDriver = $config['storage_configuration']['doctrine']['db_driver'];
        $className = $container->getParameter('publero_api_notification.storage.doctrine.class_name');
        $incrementSeconds = $container->getParameter('publero_api_notification.scheduler.incremental.increment_seconds');
        $maxSeconds = $container->getParameter('publero_api_notification.scheduler.incremental.max_seconds');

        $this->assertTrue($container->getParameter('publero_api_notification.storage.doctrine.db_driver_' . $dbDriver));
        $this->assertEquals($config['storage_configuration']['doctrine']['class_name'], $className);
        $this->assertEquals($config['scheduler_configuration']['increment']['increment_seconds'], $incrementSeconds);
        $this->assertEquals($config['scheduler_configuration']['increment']['max_seconds'], $maxSeconds);

        $definition = $container->getDefinition('publero_api_notification.notifier.example');
        $arguments = $definition->getArguments();
        $configHost = $config['hosts']['example'];
        $this->assertEquals($configHost['client'], $arguments[0]);
        $this->assertEquals($configHost['scheduler'], $arguments[1]);
        $this->assertEquals($configHost['storage'], $arguments[2]);
        $this->assertEquals('example', $arguments[3]);
        $this->assertEquals($configHost['uri'], $arguments[4]);
    }

    /**
     * @return string
     */
    private function getFullConfig()
    {
        return <<<EOF
storage_configuration:
    doctrine:
        db_driver: orm
        class_name: PubleroApiNotificationBundle:Notification
scheduler_configuration:
    increment:
        increment_seconds: 60
        max_seconds: 3600
notifier_class: Publero\ApiNotificationBundle\Notifier\SimpleNotifier
notifier_list: publero_api_notification.notifier_list
client: publero_api_notification.client.buzz
scheduler: publero_api_notification.scheduler.incremental
storage: publero_api_notification.storage.doctrine
hosts:
    example:
        uri: http://example.com/notify
        notifier_class: Publero\ApiNotificationBundle\Notifier\SimpleNotifier
        client: publero_api_notification.client.buzz
        scheduler: publero_api_notification.scheduler.incremental
        storage: publero_api_notification.storage.doctrine
EOF;
    }

    public function testLoadMinimalConfig()
    {
        $config = $this->parseConfig('');

        $this->loadExtension(new PubleroApiNotificationExtension(), [$config]);
        $container = $this->getContainer();
        $className = $container->getParameter('publero_api_notification.storage.doctrine.class_name');
        $incrementSeconds = $container->getParameter('publero_api_notification.scheduler.incremental.increment_seconds');
        $maxSeconds = $container->getParameter('publero_api_notification.scheduler.incremental.max_seconds');

        $this->assertEquals('PubleroApiNotificationBundle:Notification', $className);
        $this->assertEquals(60, $incrementSeconds);
        $this->assertEquals(3600, $maxSeconds);
        $this->assertFalse($container->hasParameter('publero_api_notification.storage.doctrine.db_driver_orm'));
        $this->assertFalse($container->hasParameter('publero_api_notification.storage.doctrine.db_driver_mongodb'));
    }

    public function testLoadMinimalConfigWithHosts()
    {
        $config = $this->parseConfig($this->getMinimalConfigWithHosts());

        $this->loadExtension(new PubleroApiNotificationExtension(), [$config]);
        $container = $this->getContainer();

        $definition = $container->getDefinition('publero_api_notification.notifier.example');
        $arguments = $definition->getArguments();
        $this->assertEquals('publero_api_notification.client.buzz', $arguments[0]);
        $this->assertEquals('publero_api_notification.scheduler.incremental', $arguments[1]);
        $this->assertEquals('publero_api_notification.storage.doctrine', $arguments[2]);
        $this->assertEquals('example', $arguments[3]);
        $this->assertEquals('http://example.com/notify', $arguments[4]);
    }

    private function getMinimalConfigWithHosts()
    {
        return <<<EOF
storage_configuration:
    doctrine:
        db_driver: orm
        class_name: PubleroApiNotificationBundle:Notification
hosts:
    example:
        uri: http://example.com/notify
EOF;
    }
}
