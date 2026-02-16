## Field Types

On https://doc.ibexa.co/en/latest/api/field_type_reference/ is a list with different field types.
There may not be helpers for all types, so feel free to make a Pull Request.
Some field types may also have multiple helpers, depending on what you want to extract from it.
Implemented types are:

| Field Type   | Internal Name        | Internal Name Ibexa 5      | Available Helpers    |
|--------------|----------------------|----------------------------|----------------------|
| Author       | ezauthor             | ibexa_author               | AuthorFieldHelper    |
| BinaryFile   | ezbinaryfile         | ibexa_binaryfile           | FileFieldHelper      |
| Checkbox     | ezboolean            | ibexa_boolean              | BoolFieldHelper      |
| ContentQuery |                      |                            |                      |
| Country      |                      |                            |                      |
| DateAndTime  | ezdatetime           | ibexa_datetime             | DateTimeFieldHelper  |
| Date         | ezdate               | ibexa_date                 | DateTimeFieldHelper  |
| EmailAddress | ezemail              | ibexa_email                | TextFieldHelper      |
| Float        | ezfloat              | ibexa_float                | NumberFieldHelper    |
| Form         |                      |                            |                      |
| Image        | ezimage              | ibexa_image                | ImageFieldHelper     |
| ImageAsset   | ezimageasset         | ibexa_image_asset          | ImageFieldHelper     |
| Integer      | ezinteger            | ibexa_integer              | NumberFieldHelper    |
| ISBN         |                      |                            |                      |
| Keyword      |                      |                            |                      |
| MapLocation  |                      |                            |                      |
| Matrix       | ezmatrix             | ibexa_matrix               | MatrixFieldHelper    | 
| Media        |                      |                            |                      |
| Null         |                      |                            |                      |
| Page         |                      |                            |                      |
| Relation     | ezobjectrelation     | ibexa_object_relation      | RelationFieldHelper  |
| RelationList | ezobjectrelationlist | ibexa_object_relation_list | RelationFieldHelper  |
| RichText     | ezrichtext           | ibexa_richtext             | RichtextFieldHelper  |
| Selection    | ezselection          | ibexa_selection            | SelectionFieldHelper |
| TextBlock    | eztext               | ibexa_text                 | TextFieldHelper      |
| TextLine     | ezstring             | ibexa_string               | TextFieldHelper      |
| Time         | eztime               | ibexa_time                 | DateTimeFieldHelper  |
| Url          | ezurl                | ibexa_url                  | UrlFieldHelper       |
| User         |                      |                            |                      |    
