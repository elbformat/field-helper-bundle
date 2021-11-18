## Extend an existing field helper
Existing field helpers are of course extensible in symfony style, by decorating the according service. 
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