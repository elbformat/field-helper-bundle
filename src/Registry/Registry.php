<?php
declare(strict_types=1);

namespace Elbformat\IbexaFieldHelperBundle\Registry;

use Elbformat\IbexaFieldHelperBundle\Exception\UnknownHelperException;
use Elbformat\IbexaFieldHelperBundle\FieldHelper\FieldHelperInterface;
use Elbformat\IbexaFieldHelperBundle\FieldHelper\TextFieldHelper;
use ProxyManager\Proxy\VirtualProxyInterface;

/**
 * Single Service to inject for easier access to multiple field helpers.
 *
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class Registry implements RegistryInterface
{

    /** @var array <string,FieldHelperInterface> */
    protected $helper = [];

    public function __construct(array $helper)
    {
        $this->helper = $helper;
    }

    public function getFieldHelper(string $class): FieldHelperInterface
    {
        if (!isset($this->helper[$class])) {
            throw new UnknownHelperException($class,array_keys($this->helper));
        }
        return $this->helper[$class];
    }

    // The following are type-hinted shortcuts to built-in field helpers. That makes the usage much easier.

    public function getAccountFieldHelper(): AccountFieldHelper
    {
        $this->resolveProxy('accountFieldHelper');

        return $this->accountFieldHelper;
    }

    public function getBooleanFieldHelper(): BooleanFieldHelper
    {
        $this->resolveProxy('booleanFieldHelper');

        return $this->booleanFieldHelper;
    }

    public function getDateFieldHelper(): DateFieldHelper
    {
        $this->resolveProxy('dateFieldHelper');

        return $this->dateFieldHelper;
    }

    public function getFileFieldHelper(): FileFieldHelper
    {
        $this->resolveProxy('fileFieldHelper');

        return $this->fileFieldHelper;
    }

    public function getImageFieldHelper(): ImageFieldHelper
    {
        $this->resolveProxy('imageFieldHelper');

        return $this->imageFieldHelper;
    }

    public function getKeyValueFieldHelper(): KeyValueFieldHelper
    {
        $this->resolveProxy('keyValueFieldHelper');

        return $this->keyValueFieldHelper;
    }

    public function getLandingpageFieldHelper(): LandingpageFieldHelper
    {
        $this->resolveProxy('landingpageFieldHelper');

        return $this->landingpageFieldHelper;
    }

    public function getLinkFieldHelper(): LinkFieldHelper
    {
        $this->resolveProxy('linkFieldHelper');

        return $this->linkFieldHelper;
    }

    public function getMatrixFieldHelper(): MatrixFieldHelper
    {
        $this->resolveProxy('matrixFieldHelper');

        return $this->matrixFieldHelper;
    }

    public function getMediaFieldHelper(): MediaFieldHelper
    {
        $this->resolveProxy('mediaFieldHelper');

        return $this->mediaFieldHelper;
    }

    public function getNumberFieldHelper(): NumberFieldHelper
    {
        $this->resolveProxy('numberFieldHelper');

        return $this->numberFieldHelper;
    }

    public function getRelationFieldHelper(): RelationFieldHelper
    {
        $this->resolveProxy('relationFieldHelper');

        return $this->relationFieldHelper;
    }

    public function getRichtextFieldHelper(): RichtextFieldHelper
    {
        $this->resolveProxy('richtextFieldHelper');

        return $this->richtextFieldHelper;
    }

    public function getSelectionFieldHelper(): SelectionFieldHelper
    {
        $this->resolveProxy('selectionFieldHelper');

        return $this->selectionFieldHelper;
    }

    public function getTagsFieldHelper(): TagsFieldHelper
    {
        $this->resolveProxy('tagsFieldHelper');

        return $this->tagsFieldHelper;
    }

    public function getTextFieldHelper(): TextFieldHelper
    {
        return $this->getFieldHelper(TextFieldHelper::class);
    }

    public function getUrlFieldHelper(): UrlFieldHelper
    {
        $this->resolveProxy('urlFieldHelper');

        return $this->urlFieldHelper;
    }

    protected function resolveProxy(string $propertyName): void
    {
        // Resolve proxy
        if ($this->$propertyName instanceof VirtualProxyInterface) {
            $this->$propertyName->initializeProxy();
            $this->$propertyName = $this->$propertyName->getWrappedValueHolderValue();
        }
    }
}
