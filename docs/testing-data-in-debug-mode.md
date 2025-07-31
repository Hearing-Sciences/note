# Testing data on survey pages and the database

There are two definitions of data in a survey: forms within the survey pages and the columns that make up database tables. In order to test that these definitions are accurate and correctly sending and receiving data, there needs to be a single 'source of truth' for the data being used. As of NOTE 2.1 this can be managed within *config.php*.

The ```$_NOTE_MODULE['PAGES']``` array can contain arrays describing each page. By defining the data fields contained in the document, and the database table into which they should be saved, NOTE can check that all are present and that 'page fields' match 'database fields.'

This depends on form fields being given the same names as database fields (maybe something even more flexible will appear in NOTE v3?).

For example:

```php
$_NOTE_MODULE['PAGES'][] = [
    'PAGE' => 'sortable',
    'TABLE' => 'sortables', 
    'FIELDS' => [
        'response' => 'TEXT'
    ]
];
```

This array entry shows that the page *page_sortable.php* will contain the data field *response* and the table *sortables* should also contain a column called *response.*

Fields are described as an indexed array where the key is the name of the field and the value is an SQLite column definition (usually TEXT, INTEGER or DATETIME).

Before the data is handed to the user function db_save_page_data(), NOTE checks for the presence of a *response* key in the $_POST data and the database. If either are missing, an error is displayed.

An example warning message is shown below. The field *items* is present on the page but not in the *config.php* definition or the database, and the field *response* **isn't** on the page but **is** in the definition and the database. This implies that the field on the page has been mis-named, but of course in all instances of alerts **the messages should be checked.**

![Example of an error message](img/example-debug-save-error.png "Example of an error message")

Yes, the messages are intrusive and annoying. That's the point! Don't forget that they won't be displayed when you switch off debug mode.

## Fields common to all pages

If all pages contain a particular field, it could be tiresome to add the information to every page definition in *config.php*. So there is another way to define a 'common' field: the 'FIELDS' key.

```php
$_NOTE_MODULE['FIELDS'] = [
    'COMMON' => [
        'participant_id' => 'TEXT'
    ]
];
```

The FIELDS key can contain a 'COMMON' subkey defining fields that will be present on every page.

Fields are described as an indexed array where the key is the name of the field and the value is an SQLite column definition (usually TEXT, INTEGER or DATETIME).

## Fields that should be ignored

A form may also contain fields that should be ignored because they don't contain experiment data or will never be present in the $_POST data. These can be described in the ['IGNORE'] key:

```php
$_NOTE_MODULE['FIELDS'] = [
    'IGNORE' => [
        'id' => 'INTEGER',
        'uid' => 'TEXT', 
        'timestamp' => 'DATETIME',
        'page' => 'TEXT',
        'nextpage' => 'TEXT'
    ]
];
```

Fields are described as an indexed array where the key is the name of the field and the value is an SQLite column definition (usually TEXT, INTEGER or DATETIME).
