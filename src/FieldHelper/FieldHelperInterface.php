<?php declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\FieldHelper;

/**
 * FieldHelpers are different by nature and thus have no common accessors.
 *
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
interface FieldHelperInterface
{
    public static function getName(): string;
}