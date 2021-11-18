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
    }
}
