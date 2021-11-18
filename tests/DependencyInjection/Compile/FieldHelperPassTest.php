<?php declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Tests\DependencyInjection\Compiler;

use Elbformat\FieldHelperBundle\DependencyInjection\Compiler\FieldHelperPass;
use Elbformat\FieldHelperBundle\FieldHelper\BoolFieldHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class FieldHelperPassTest extends TestCase
{
    public function testProcess(): void
    {
        $fhp = new FieldHelperPass();
        $container = $this->createMock(ContainerBuilder::class);
        $registryDefinition = $this->createMock(Definition::class);
        $registryDefinition->expects($this->once())->method('setArgument')->with('$helper',$this->callback(function($arg) {
            if (1!== count($arg)) {
                throw new \Exception('No helper registered');
            }
            if (BoolFieldHelper::class !== array_keys($arg)[0]) {
                throw new \Exception('Invalid helper name: '.array_keys($arg)[0]);
            }
            if (! array_values($arg)[0] instanceof Reference) {
                throw new \Exception('Helper not a reference');
            }
            return ('elbformat_field_helper.field_helper.test' === (string) array_values($arg)[0]);
        }));
        $helperDefinition = $this->createMock(Definition::class);
        $helperClass = new BoolFieldHelper();
        $helperDefinition->method('getClass')->willReturn($helperClass);
        $container->method('findDefinition')->withConsecutive(['elbformat_field_helper.registry'],['elbformat_field_helper.field_helper.test'])->willReturnOnConsecutiveCalls($registryDefinition,$helperDefinition);
        $container->method('findTaggedServiceIds')->with('elbformat_field_helper.field_helper')->willReturn(['elbformat_field_helper.field_helper.test' => []]);
        $fhp->process($container);
    }

    public function testProcessIgnoreInvalidHelper(): void
    {
        $fhp = new FieldHelperPass();
        $container = $this->createMock(ContainerBuilder::class);
        $registryDefinition = $this->createMock(Definition::class);
        $registryDefinition->expects($this->once())->method('setArgument')->with('$helper',[]);
        $helperDefinition = $this->createMock(Definition::class);
        $helperDefinition->method('getClass')->willReturn(new \stdClass());
        $container->method('findDefinition')->withConsecutive(['elbformat_field_helper.registry'],['elbformat_field_helper.field_helper.test'])->willReturnOnConsecutiveCalls($registryDefinition,$helperDefinition);
        $container->method('findTaggedServiceIds')->with('elbformat_field_helper.field_helper')->willReturn(['elbformat_field_helper.field_helper.test' => []]);
        $fhp->process($container);
    }
}