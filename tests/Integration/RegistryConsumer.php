<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Tests\Integration;

use Elbformat\FieldHelperBundle\Registry\RegistryInterface;
use EzSystems\DoctrineSchema\API\Builder\SchemaBuilder;

/**
 * This services is needed for integration test, as the other unused services are removed otherwise.
 *
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class RegistryConsumer
{
    public RegistryInterface $registry;

    public function __construct(RegistryInterface $registry, SchemaBuilder $builder)
    {
        $this->registry = $registry;
    }
}
