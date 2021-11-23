<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\DependencyInjection;

use Elbformat\FieldHelperBundle\Registry\Registry;
use Elbformat\FieldHelperBundle\Registry\RegistryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class ElbformatFieldHelperExtensionTest extends TestCase
{
    public function testLoad(): void
    {
        $ext = new ElbformatFieldHelperExtension();
        $container = new ContainerBuilder();
        $ext->load([], $container);

        // Make sure the Interface is an alias to the service itself
        $this->assertEquals('elbformat_field_helper.registry', (string) $container->getAlias(RegistryInterface::class));
        $this->assertEquals(Registry::class, $container->getDefinition('elbformat_field_helper.registry')->getClass());

        // Make sure all helpers are registered and tagged
        foreach (glob(__DIR__ . '/../../src/FieldHelper/*FieldHelper.php') as $fieldHelperFile) {
            $class = preg_replace('/^(.*).php$/', '$1', basename($fieldHelperFile));
            $prefix = strtolower(preg_replace('/^(.*)FieldHelper$/', '$1', $class));
            if ('abstract' === $prefix) {
                continue;
            }
            $this->assertTrue($container->hasDefinition('elbformat_field_helper.field_helper.' . $prefix), 'Missing service for '.$prefix.' helper.');
            $def = $container->getDefinition('elbformat_field_helper.field_helper.' . $prefix);
            $reflect = new \ReflectionClass($def->getClass());
            $this->assertSame($class, $reflect->getShortName(), 'Wrong class for '.$prefix.' helper.');
            $this->assertContains('elbformat_field_helper.field_helper', array_keys($def->getTags()), 'Missing service tag for '.$prefix.' helper.');
        }
    }
}
