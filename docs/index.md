## What are "Field helpers"?
Field helpers are intended to easily and safely access fields from content objects in a typed manner. 
With this you can make your project safe for static code analysis without adding too much boilerplate code everywhere.
Especially for importer scripts, that create content, there is an update method which helps you to track changes.
With thism, you can speed up the update process by not publishing it, when no change was made at all. 

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

## Field Types
On https://doc.ibexa.co/en/latest/api/field_type_reference/ is a list with different field types. 
There may not be helpers for all types, so feel free to make a Pull Request.
Some field types may also have multiple helpers, depending on what you want to extract from it.
Implemented types are:

| Field Type   | Internal Name | Available Helpers |
| ------------ | ------------- | --- |
| Checkbox     | ezboolean     | TODO |
| DateAndTime  | 	
| Date         |
| EmailAddress | ezemail | TextFieldHelper |
| Float        |
| Integer      |
| Matrix       |
| Relation     |
| RelationList |
| RichText     |
| Selection    |
| TextBlock    | eztext       | TextFieldHelper |
| TextLine     | ezstring     | TextFieldHelper |
| Time         |
| Url          |

## Advanced topics
* Learn how to [extend](docs/extension.md) the field helpers
* 