<?php declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Tests;

use Elbformat\FieldHelperBundle\DependencyInjection\Compiler\FieldHelperPass;
use Elbformat\FieldHelperBundle\ElbformatFieldHelperBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class BundleTest extends TestCase
{
    public function testBuild(): void
    {
        $bundle = new ElbformatFieldHelperBundle();
        $container = $this->createMock(ContainerBuilder::class);
        $container->expects($this->once())->method('addCompilerPass')->with($this->isInstanceOf(FieldHelperPass::class))->willReturn(null);
        $bundle->build($container);
    }
}