<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle;

use Elbformat\FieldHelperBundle\DependencyInjection\Compiler\FieldHelperPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class ElbformatFieldHelperBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new FieldHelperPass());
    }
}
