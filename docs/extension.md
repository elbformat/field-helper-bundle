Extend the field helpers

## Create your own field helper
It's quite easy to create an own field helper for your project. 
You just need to implement the FieldHelperInterface by exposing the name of the field helper. 
Usualy the class name will be taken here.
```php
class MyFieldHelper extends AbstractFieldHelper
{
    public static function getName(): string
    {
        return self::class;
    }
```
Then you have to set the tag `elbformat_field_helper.field_helper` to the service definition. The registry will automatically known this helper then
```yml
services
    App\MyFieldHelper:
        tags: ['elbformat_field_helper.field_helper']
```
Afterwards you can simple fetch it via the registry
```php
$myFieldHelper = $fieldHelperRegistry->getFieldHelper(App\MyFieldHelper::class);
```

## Extend an existing field helper
Field helpers are of course extensible in symfony style, by decorating the according service. 
If you do not override the `getName()` method, it will still be available under the old name but with your overridden implementation
```php
class BetterTextFieldHelper extends AbstractFieldHelper
{
    ...
}
```
```yml
services
    App\BetterTextFieldHelper:
        decorates: 'elbformat_field_helper.field_helper.text'
```