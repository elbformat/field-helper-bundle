<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Tests\Integration;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Elbformat\FieldHelperBundle\ElbformatFieldHelperBundle;
use eZ\Bundle\EzPublishCoreBundle\EzPublishCoreBundle;
use eZ\Bundle\EzPublishIOBundle\EzPublishIOBundle;
use eZ\Bundle\EzPublishLegacySearchEngineBundle\EzPublishLegacySearchEngineBundle;
use EzSystems\DoctrineSchemaBundle\DoctrineSchemaBundle;
use EzSystems\EzPlatformAdminUiBundle\EzPlatformAdminUiBundle;
use EzSystems\EzPlatformContentFormsBundle\EzPlatformContentFormsBundle;
use EzSystems\EzPlatformDesignEngineBundle\EzPlatformDesignEngineBundle;
use EzSystems\EzPlatformGraphQL\EzSystemsEzPlatformGraphQLBundle;
use EzSystems\EzPlatformMatrixFieldtypeBundle\EzPlatformMatrixFieldtypeBundle;
use EzSystems\EzPlatformRestBundle\EzPlatformRestBundle;
use EzSystems\EzPlatformRichTextBundle\EzPlatformRichTextBundle;
use EzSystems\EzPlatformUserBundle\EzPlatformUserBundle;
use EzSystems\PlatformHttpCacheBundle\EzSystemsPlatformHttpCacheBundle;
use EzSystems\PlatformInstallerBundle\EzSystemsPlatformInstallerBundle;
use FOS\HttpCacheBundle\FOSHttpCacheBundle;
use FOS\JsRoutingBundle\FOSJsRoutingBundle;
use Hautelook\TemplatedUriBundle\HautelookTemplatedUriBundle;
use Ibexa\Platform\Bundle\Search\IbexaPlatformSearchBundle;
use JMS\TranslationBundle\JMSTranslationBundle;
use Knp\Bundle\MenuBundle\KnpMenuBundle;
use Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle;
use Liip\ImagineBundle\LiipImagineBundle;
use Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\WebpackEncoreBundle\WebpackEncoreBundle;

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
            new EzPlatformMatrixFieldtypeBundle(),
            new EzPlatformAdminUiBundle(),
            new EzPlatformDesignEngineBundle(),
            new EzPlatformContentFormsBundle(),
            new EzPlatformUserBundle(),
            new IbexaPlatformSearchBundle(),
            new EzPlatformRestBundle(),
            new EzSystemsEzPlatformGraphQLBundle(),
            new WebpackEncoreBundle(),
            new LexikJWTAuthenticationBundle(),
            new KnpMenuBundle(),
            new SwiftmailerBundle(),
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
