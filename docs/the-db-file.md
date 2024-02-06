# *db.php* - talking to the database

This file should be created and stored in the survey directory. It handles survey-specific database operations.

For someone new to PHP, this will probably be the most complicated file to create as NOTE doesn't provide much assistance. There are two very important things to know:

## Some warnings about saving data

When writing your save function(s) be aware of the following:

- **There is no guarantee that the data provided by *$post* has been sanitised or is safe to use.** Run sanity checks and make sure that rogue or even malicious data is not present.
- **It is a very good idea to use prepared statements when saving to the database.** [Prepared statements](https://www.w3schools.com/php/php_mysql_prepared_statements.asp) help to prevent SQL injection attacks that could wreck or even disclose your data.
- If an error occurs when the function tries to save data **the survey will not advance and will show the same page again.** If you repeatedly see the same page in your survey it is likely that data is not being saved.
- **NOTE does not check that data is being saved.** For example, if you forget to write a save routine for a particular page, NOTE will assume that you meant it and will just continue without warnings.

Here is a sample *db.php* file:

```php
<?php

//  My survey
//
// AL 2022-03-17

// table creation scripts
function db_create_tables()
{
    $db->exec('CREATE TABLE IF NOT EXISTS visitors (
                    id INTEGER PRIMARY KEY,
                    uniqueID TEXT,
                    ipaddr TEXT,
                    lastPage INTEGER,
                    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP)');
}

//========================================================================================================
// Store page data

function db_store_page_data($page, &$post, $uid, &$db, &$_NOTE_MODULE) 
{
    switch($_NOTE_MODULE['PAGES'][$page]) {

        case 'name_of_a_page':
        $qry    =     $db->prepare('
                        INSERT INTO my_table 
                        (field1, field2)
                        VALUES
                        (:value1, :value2)
                    ');
        $result = $qry->execute(array(
                ':value1' => $post['value1'],
                ':value2' => $post['value2']
            )
        );
        break;        
    }
}


//========================================================================================================

function display_module_data(&$db, $_NOTE, $_NOTE_MODULE) {
    include $_NOTE['MODULE_DIR'].DIRECTORY_SEPARATOR.'data.php';
}


?>
```

There are three sections to the file.

## db_create_tables: Table Creation

The function *db_create_tables()* contains one or more lines of database execution functions ('*$db->exec*') that create the tables you require for the survey. The single example above creates the *visitors* table that NOTE must have to manage participants. This function is optional but be aware of the following whether you include it or not:

- These commands are run to create and populate a database if an existing database can't be found. This means that if your database disappears for some reason the survey can re-create it and continue storing data. Although you may be more keen to find out why the database has disappeared!
- These commands are run **every time a page is loaded**. Therefore, to prevent the scripts overwriting your database you **must** add *IF NOT EXISTS* to every SQL statement that creates a table. Such as in the example above; 'CREATE TABLE **IF NOT EXISTS** visitors'.

## db_store_page_data: Saving survey data

This function will be called by NOTE and must contain the code for saving data to the database. **NOTE will not save your data automatically** so you must include something here.

## Function variables

The function declaration must be:

```php
function db_store_page_data($page, &$post, $uid, &$db, &$_NOTE_MODULE) 
```

The variables are as follows:
|Variable|Purpose|
|---|---|
|\$page|The ID of the page that has just been visited|
|\&\$post|The data that was saved in a form on that page|
|\$uid|The unique ID of the participant (which is independent of any survey-specific participant ID you may wish to use)|
|\&\$db|The database represented as an object|
|\&\$_NOTE_MODULE|Module variables and values created in *config.php*|

I will briefly mention that some of these variables are passed by reference (they begin with '\&\\$' rather than just '\\$'). Please ask if you want to know more.

### A recipe for getting started with saving data

There are a couple of programming structures that you can draw from to save data. The function itself can use a *switch ... case* structure to check the name of the page and decide how to proceed.

```php
switch($_NOTE_MODULE['PAGES'][$page]) {

    case 'name_of_a_page':
    ...
    break;
}
```

You will have created **$_NOTE_MODULE\['PAGES'\]** in *config.php* and populated it with page names. Here you can draw from that array and save data appropriate for that page. In the example above, a case has been created for the page name *name_of_a_page*, so if that page has just been viewed and used to create data, the data will be passed here for saving.

The other structure to be aware of is the *prepared statement*.

```php
$qry    =     $db->prepare('
                INSERT INTO my_table 
                (field1, field2)
                VALUES
                (:value1, :value2)
            ');
$result = $qry->execute(array(
        ':value1' => $post['value1'],
        ':value2' => $post['value2']
    )
);
```

Rather than executing a database command directly, the INSERT command is prepared using the *$db->prepare* method. Rather than pasting values in directly, use *placeholders* consisting of a colon followed by a name you will refer to later (in the example, *:value1* and *:value2*). Once the statement is prepared, you can execute it by feeding in an array of placeholders and their corresponding values.

This seems like an over-complicated way of using the database, but provides essential protection against some forms of hacking.

## Show survey-specific data

Depending on how you are planning to present the result data, you can use the following code or add lines for analysis, retrieval etc.

```php
function display_module_data(&$db, $_NOTE, $_NOTE_MODULE) {
    include $_NOTE['MODULE_DIR'].DIRECTORY_SEPARATOR.'data.php';
}
```

This will call the file *data.php* in the survey directory.

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
