## Usage
The best practice is, to inject the Field Helper Registry into your service. 
You can then fetch a helper for each field you need.
NOTE: You need to inject the interface, when you want to use autowiring. For type-hinting, the real implementation is recommended. 
```php
use Elbformat\FieldHelperBundle\Registry\RegistryInterface;
use Elbformat\FieldHelperBundle\Registry\Registry;
class MyClass {

    private Registry $fieldHelper;
    
    public __construct(RegistryInterface $fieldHelper) {
        $this->fieldHelper = $fieldHelper;
    }
    
    public function readData(Content $content) {
        $text $this->fieldHelper
    }
}
```

Depending on the type, the method returns a different scalar type (string, bool, int) or complex objects (like links).
You can take different methods, depending on whether you want to have an exception thrown, when no data could be returned (strong contract) or you return null.
```php
use Elbformat\FieldHelperBundle\Registry\RegistryInterface;
use Elbformat\FieldHelperBundle\Registry\Registry;
use eZ\Publish\API\Repository\Values\Content\Content;

class MyClass {

    private Registry $fieldHelpers;
    
    public __construct(RegistryInterface $fieldHelpers) {
        $this->fieldHelpers = $fieldHelpers;
    }
    
    public function readData(Content $content) {
        // Will throw a FieldNotSetException, when no string is saved yet
        $text = $this->fieldHelpers->getTextFieldelper()->getString($content, 'my_field');
        
        // Will return null, when no string is set yet
        $text = $this->fieldHelpers->getTextFieldelper()->getOptionalString($content, 'my_field');
    }
}
```

When you are updating a field, you can also take the helper to set structured data.
In return it will tell you, if there were changes made, or if the content object already had this value
```php
$changed = $this->fieldHelpers->getTextFieldelper()->updateString($struct, 'my_field', 'new value', $content);
```
