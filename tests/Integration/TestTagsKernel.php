<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Tests\Integration;

use Ibexa\Bundle\AdminUi\IbexaAdminUiBundle;
use Ibexa\Bundle\Rest\IbexaRestBundle;
use Lolautruche\EzCoreExtraBundle\EzCoreExtraBundle;
use Netgen\TagsBundle\NetgenTagsBundle;

/**
 * Kernel to test bundle with netgentags installed
 *
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class TestTagsKernel extends TestKernel
{
    public function registerBundles()
    {
        return array_merge(parent::registerBundles(), [
            new NetgenTagsBundle(),
            new EzCoreExtraBundle(),
            new EzPlatformRestBundle(),
            new EzPlatformAdminUiBundle(),
        ]);
    }
}
