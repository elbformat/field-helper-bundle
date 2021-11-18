# Elbformat Field Helper Bundle for ibexa DXP
This bundle provides helpers to extract and set structured data from and to ibexa content fields.

What are "Field helpers"?
=========================
Field helpers are intended to easily and safely access fields from content objects in a typed manner.
With this you can make your project safe for static code analysis without adding too much boilerplate code everywhere.
Especially for importer scripts, that create content, there is an update method which helps you to track changes.
With thism, you can speed up the update process by not publishing it, when no change was made at all.

Quick usage
===========
Install bundle via composer
```console
$ composer require elbformat/field-helper-bundle
```
Use like this
```php
public function getFields(RegistryInterface $fieldHelperRegistry, Content $content) {
    $myText = $fieldHelperRegistry->getTextFieldHelper()->getString($content, 'text_field');
    $linkObject = $fieldHelperRegistry->getLinkFieldHelper()->getLink($content, 'my_url');
    $linkUrl = $linkObj->getUrl();
    $linkText = $linkObj->getText();
}
```

Further topics
==============
* [Installation](docs/installation.md)
* [How to use the field helpers](docs/usage.md)
* [List of supported fields and their helpers](docs/fields.md)
* [Extending existing helpers](docs/extending.md)
* [Writing your own helpers](docs/own_helper.md)