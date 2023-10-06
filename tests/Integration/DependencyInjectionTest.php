<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Tests\Integration;

use Elbformat\FieldHelperBundle\Registry\Registry;

/**
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class DependencyInjectionTest extends KernelTestCase
{
    public function testContainerConfig(): void
    {
        // Must be injected correctly into our test service
        $registry = $this->containerInstance->get('test_registry_consumer')->registry;
        $this->assertInstanceOf(Registry::class, $registry);

        // Fetch helpers via registry or directly from container
        foreach (glob(__DIR__ . '/../../src/FieldHelper/*FieldHelper.php') as $fieldHelperFile) {
            $class = preg_replace('/^(.*).php$/', '$1', basename($fieldHelperFile));
            $prefix = strtolower(preg_replace('/^(.*)FieldHelper$/', '$1', $class));
            if ('abstract' === $prefix || 'netgentags' === $prefix) {
                continue;
            }
            $expectedClass = 'Elbformat\\FieldHelperBundle\\FieldHelper\\'.$class;

            $this->assertInstanceOf($expectedClass, call_user_func([$registry,'get'.$class]));
            $this->assertInstanceOf($expectedClass, $this->containerInstance->get('elbformat_field_helper.field_helper.'. $prefix), 'Missing service for '.$prefix.' helper.');
        }
    }
}
