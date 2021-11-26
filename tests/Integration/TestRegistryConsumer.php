<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Tests\Integration;

use Elbformat\FieldHelperBundle\Registry\RegistryInterface;

/**
 * This services is needed for integration test, as the other unused services are removed otherwise.
 *
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class TestRegistryConsumer
{
    public RegistryInterface $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }
}
