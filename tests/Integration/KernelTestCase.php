<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Tests\Integration;

use Elbformat\FieldHelperBundle\Registry\Registry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase as SymfonyKernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class KernelTestCase extends SymfonyKernelTestCase
{
    protected ContainerInterface $containerInstance;

    public function setUp(): void
    {
        $_ENV['KERNEL_CLASS'] = TestKernel::class;
        self::bootKernel(['environment' => 'prod']);
        $this->containerInstance = static::getContainer();
    }
}
