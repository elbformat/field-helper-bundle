## Usage
The best practice is, to inject the Field Helper Registry into your service. 
You can then fetch a helper for each field you need.
NOTE: You need to inject the interface, when you want to use autowiring. 
```php
use Elbformat\FieldHelperBundle\Registry\RegistryInterface;
use Elbformat\FieldHelperBundle\Registry\Registry;

class MyClass {

    private RegistryInterface $fieldHelper;
    
    public __construct(RegistryInterface $fieldHelper) {
        $this->fieldHelper = $fieldHelper;
    }
}
```

Depending on the type, the method returns a different scalar type (string, bool, int) or complex objects (like links).
```php
// Will return a string or null value
$textOrNull = $this->fieldHelpers->getTextFieldelper()->getString($content, 'my_field');

// If you want to be sure not to have null values, use null coalescing operator
$text = $this->fieldHelpers->getTextFieldelper()->getString($content, 'my_field') ?? '';
```

When you are updating a field, you can also take the helper to set structured data.
In return it will tell you, if there were changes made, or if the content object already had this value and stays unchanged.
```php
$changed = $this->fieldHelpers->getTextFieldelper()->updateString($struct, 'my_field', 'new value', $content);
```
