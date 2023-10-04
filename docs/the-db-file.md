# *db.php* - talking to the database

This file is included within the survey *index.php* and allows you to write functions for saving survey data.

There isn't a standard way to save data for the simple reason that the data can vary wildly from one survey to another. The main code looks for a function called *function db_store_page_data* and calls it if found. This function should be kept within *db.php* and contain the following arguments:

```php
function db_store_page_data($page, &$post, $uid, &$db, &$_NOTE_MODULE) 
{
    // ...
}
```

| argument | type   | comments |
| -------- | ----   | -------- |
| $page    | String | The name of the page will be passed to the function. *page_xxx.php* will be passed as the string *xxx* |
| &$post   | Array  | All of the data sent using PHP's $_POST superglobal |
| $uid     | String | The user's UID (generated at the start of the survey) |
| &$db     | PHP::PDO object | The active database connection |
| &$_NOTE_MODULE | Array | The array created within *config.php* |

How you write the function is entirely your own business. People have tended to use a ```switch ... case``` structure to test the value of $page and run a page-specific save to the database.

## Here be dragons! Some warnings about saving data

When writing your save function(s) be aware of the following:

- **There is no guarantee that the data provided by *$post* has been sanitised or is safe to use.** Run sanity checks and make sure that rogue or even malicious data is not present.
- **It is a very good idea to use prepared statements when saving to the database.** [Prepared statements](https://www.w3schools.com/php/php_mysql_prepared_statements.asp) help to prevent SQL injection attacks that could wreck or even disclose your data.
- If an error occurs when the function tries to save data **the survey will not advance and will show the same page again.** If you repeatedly see the same page in your survey it is likely that data is not being saved.
- **NOTE does not check that data is being saved.** For example, if you forget to write a save routine for a particular page, NOTE will assume that you meant it and will just continue without warnings.
