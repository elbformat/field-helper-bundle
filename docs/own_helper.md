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

Don't forget to create a Pull Request, when you wrote a helper, that could be useful for others, too!