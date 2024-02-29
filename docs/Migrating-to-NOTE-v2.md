# Migrating to NOTE v2

I have tried to keep the changes to a minimum and marked features as 'deprecated' rather than 'take action now.' However there are a few things that have to be changed and some that will need to be changed soon.

## SQL Statements for creating tables - take action now

In the past the file *db.php* contained statements for recreating the database alongside required functions for saving data. Mixing statements and functions in a file isn't the best idea so the creation statements must now be included in a function called *db_create_cables.php*.

```php
    function db_create_tables($db)
    {
        // Put all your statements here

        // $db->exec('CREATE TABLE IF NOT EXISTS ...');
    }
```

NOTE will attempt to run this function just after a database connection has been made.

## $_NOTE - deprecated

The ```$_NOTE``` array is now deprecated and will be removed in a future version. It has been replaced by a ```$noteSurvey``` object containing similar variables (and others).

```$_NOTE``` **should have been treated as read-only in your surveys** so the changes should be easy to implement. Some variables have been replaced with getter functions to underline that they can't be changed.

It is unlikely that you will have used much of the ```$_NOTE``` array apart from possibly ```PAGE```. However a complete list is given below.

| Old variable            | New variable                   | Notes               |
| ----------------------- | ------------------------------ | ------------------- |
| $_NOTE['PAGE']          | $noteSurvey->getPage()         | Use this getter function rather than a variable |
| $_NOTE['MODULE_ID']           | $noteSurvey->moduleId    |  |
| $_NOTE['UID']                 | $noteSurvey->participant->getUid()    |  Use this getter function rather than a variable |
