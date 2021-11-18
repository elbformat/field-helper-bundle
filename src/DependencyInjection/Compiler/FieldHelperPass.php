<?php declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\DependencyInjection\Compiler;

use Elbformat\FieldHelperBundle\FieldHelper\FieldHelperInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Collect field helpers and inject them into the registry
 *
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class FieldHelperPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void {
        $definition = $container->findDefinition('elbformat_field_helper.registry');

        // find all tagged services
        $taggedServices = $container->findTaggedServiceIds('elbformat_field_helper.field_helper');

        $helpers = [];
        foreach ($taggedServices as $id => $tags) {
            $class = $container->findDefinition($id)->getClass();
            if (!$class instanceof FieldHelperInterface) {
                continue;
            }
            $helperName = $class::getName();
            $helpers[$helperName] = new Reference($id);
        }
        $definition->setArgument('$helper',$helpers);
    }
}