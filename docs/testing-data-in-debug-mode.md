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

Before the data is handed to the user function db_save_page_data(), NOTE checks for the presence of a *response* key in the $_POST data and the database. If either are missing, an error is displayed.

An example warning message is shown below. The field *items* is present on the page but not in the *config.php* definition or the database, and the field *response* **isn't** on the page but **is** in the definition and the database. This implies that the field on the page has been mis-named, but of course in all instances of alerts **the messages should be checked.**

![Example of an error message](img/example-debug-save-error.png "Example of an error message")