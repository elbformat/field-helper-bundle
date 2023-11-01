## Field Types
On https://doc.ibexa.co/en/latest/api/field_type_reference/ is a list with different field types. 
There may not be helpers for all types, so feel free to make a Pull Request.
Some field types may also have multiple helpers, depending on what you want to extract from it.
Implemented types are:

| Field Type   | Internal Name        | Available Helpers    |
|--------------|----------------------|----------------------|
| Author       | ezauthor             | AuthorFieldHelper    |
| BinaryFile   | ezbinaryfile         | FileFieldHelper      |
| Checkbox     | ezboolean            | BoolFieldHelper      |
| ContentQuery |                      |                      |
| Country      |                      |                      |
| DateAndTime  | ezdatetime           | DateTimeFieldHelper  |
| Date         | ezdate               | DateTimeFieldHelper  |
| EmailAddress | ezemail              | TextFieldHelper      |
| Float        | ezfloat              | NumberFieldHelper    |
| Form         |                      |                      |
| Image        | ezimage              | ImageFieldHelper     |
| ImageAsset   | ezimageasset         | ImageFieldHelper     |
| Integer      | ezinteger            | NumberFieldHelper    |
| ISBN         |                      |                      |
| Keyword      |                      |                      |
| MapLocation  |                      |                      |
| Matrix       | ezmatrix             | MatrixFieldHelper    | 
| Media        |                      |                      |
| Null         |                      |                      |
| Page         |                      |                      |
| Relation     | ezobjectrelation     | RelationFieldHelper  |
| RelationList | ezobjectrelationlist | RelationFieldHelper  |
| RichText     | ezrichtext           | RichtextFieldHelper  |
| Selection    | ezselection          | SelectionFieldHelper |
| TextBlock    | eztext               | TextFieldHelper      |
| TextLine     | ezstring             | TextFieldHelper      |
| Time         | eztime               | DateTimeFieldHelper  |
| Url          | ezurl                | UrlFieldHelper       |
| User         |                      |                      |
