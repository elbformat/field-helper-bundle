<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Tests\Integration;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Elbformat\FieldHelperBundle\ElbformatFieldHelperBundle;
use eZ\Bundle\EzPublishCoreBundle\EzPublishCoreBundle;
use eZ\Bundle\EzPublishIOBundle\EzPublishIOBundle;
use eZ\Bundle\EzPublishLegacySearchEngineBundle\EzPublishLegacySearchEngineBundle;
use EzSystems\DoctrineSchemaBundle\DoctrineSchemaBundle;
use EzSystems\EzPlatformRichTextBundle\EzPlatformRichTextBundle;
use EzSystems\PlatformHttpCacheBundle\EzSystemsPlatformHttpCacheBundle;
use EzSystems\PlatformInstallerBundle\EzSystemsPlatformInstallerBundle;
use FOS\HttpCacheBundle\FOSHttpCacheBundle;
use FOS\JsRoutingBundle\FOSJsRoutingBundle;
use Hautelook\TemplatedUriBundle\HautelookTemplatedUriBundle;
use JMS\TranslationBundle\JMSTranslationBundle;
use Liip\ImagineBundle\LiipImagineBundle;
use Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class TestKernel extends Kernel
{
    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new SecurityBundle(),
            new TwigBundle(),
            new DoctrineBundle(),
            new SensioFrameworkExtraBundle(),
            new FOSJsRoutingBundle(),
            new JMSTranslationBundle(),
            new LiipImagineBundle(),
            new FOSHttpCacheBundle(),
            new EzPublishCoreBundle(),
            new EzPublishLegacySearchEngineBundle(),
//            new EzPublishIOBundle(),
            new DoctrineSchemaBundle(),
            new EzSystemsPlatformHttpCacheBundle(),
            new HautelookTemplatedUriBundle(),
            new ElbformatFieldHelperBundle(),
            new EzSystemsPlatformInstallerBundle(),
            new EzPlatformRichTextBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->setParameter('container.dumper.inline_class_loader', true);
    }

    protected function build(ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__));
        $loader->load('config_test.yml');
    }
}
