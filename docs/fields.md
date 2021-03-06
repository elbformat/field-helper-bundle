## Field Types
On https://doc.ibexa.co/en/latest/api/field_type_reference/ is a list with different field types. 
There may not be helpers for all types, so feel free to make a Pull Request.
Some field types may also have multiple helpers, depending on what you want to extract from it.
Implemented types are:

| Field Type   | Internal Name        | Available Helpers   |
| ------------ | -------------------- | ------------------- |
| Checkbox     | ezboolean            | BoolFieldHelper     |
| DateAndTime  | ezdatetime	          | DateTimeFieldHelper |
| Date         | ezdate               | DateTimeFieldHelper |
| EmailAddress | ezemail              | TextFieldHelper     |
| Float        | ezfloat              | NumberFieldHelper   |
| Integer      | ezinteger            | NumberFieldHelper   |
| Matrix       |
| Relation     | ezobjectrelation     | RelationFieldHelper |
| RelationList | ezobjectrelationlist | RelationFieldHelper |
| RichText     |
| Selection    |
| TextBlock    | eztext               | TextFieldHelper     |
| TextLine     | ezstring             | TextFieldHelper     |
| Time         | eztime               | DateTimeFieldHelper |
| Url          | ezurl                | UrlFieldHelper      |
