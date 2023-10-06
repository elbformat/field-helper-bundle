<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\DependencyInjection;

use Elbformat\FieldHelperBundle\FieldHelper\NetgenTagsFieldHelper;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class ElbformatFieldHelperExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yml');

        // Add tags only, if bundle is installed
        if (class_exists('Netgen\TagsBundle\API\Repository\TagsService')) {
            $fhTags = new Definition(NetgenTagsFieldHelper::class);
            $fhTags->addTag('elbformat_field_helper.field_helper');
            $fhTags->setArgument('$tagsService', '@eztags.api.service.tags');
            $container->setDefinition('elbformat_field_helper.field_helper.netgentags', $fhTags);
        }

    }
}
