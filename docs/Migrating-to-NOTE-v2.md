# Migrating to NOTE v2

I have tried to keep the changes to a minimum and marked features as 'deprecated' rather than 'take action now.' However there are a few things that have to be changed and some that will need to be changed soon.

## NOTE specific changes

### SQL Statements for creating tables - take action now

In the past the file *db.php* contained statements for recreating the database alongside required functions for saving data. Mixing statements and functions in a file isn't the best idea so the creation statements must now be included in a function called *db_create_cables.php*.

```php
    function db_create_tables($db)
    {
        // Put all your statements here

        // $db->exec('CREATE TABLE IF NOT EXISTS ...');
    }
```

NOTE will attempt to run this function just after a database connection has been made.

### db_store_page_data() now mandatory

The function ```db_store_page_data``` must be present in ```db.php``` even if it is empty. In reality almost every survey contains this function anyway as they all need to save data.

### $_NOTE - deprecated

The ```$_NOTE``` array is now deprecated and will be removed in a future version. It has been replaced by a ```$noteSurvey``` object containing similar variables (and others).

```$_NOTE``` **should have been treated as read-only in your surveys** so the changes should be easy to implement. Some variables have been replaced with getter functions to underline that they can't be changed.

It is unlikely that you will have used much of the ```$_NOTE``` array apart from possibly ```PAGE```. However a complete list is given below.

| Old variable            | New variable                   | Notes               |
| ----------------------- | ------------------------------ | ------------------- |
| $_NOTE['PAGE']          | $noteSurvey->getPage()         | Use this getter function rather than a variable |
| $_NOTE['MODULE_ID']           | $noteSurvey->moduleId    | |
| $_NOTE['UID']                 | $noteSurvey->participant->getUid()    | Use this getter function rather than a variable |
| $_NOTE['RAW_URI'] | $noteSurvey->rawUri | |
| $_NOTE['BASE_URI'] | $noteSurvey->baseUri | |
| $_NOTE['REQUEST_URI'] | $noteSurvey->requestUri | |
| $_NOTE['MODULE_ID'] | $noteSurvey->moduleId | |
| $_NOTE['DISPLAY_ID'] | $noteSurvey->mode | |
| $_NOTE['OTHER_ID'] | $noteSurvey->otherId | |
| $_NOTE['MODULE_DIR'] | $noteSurvey->moduleDir | |
| $_NOTE['MODULE_PATH'] | $noteSurvey->modulePath | |
| $_NOTE['FS_MODULE_PATH'] | $noteSurvey->fsModulePath | |
| $_NOTE['PART_ID'] | $noteSurvey->partId | |

This is not indended to be an exhaustive list of $noteSurvey properties and just includes those converted for $_NOTE.

## Hearing Sciences specific changes

### File and folder ownership

Surveys (including the survey folders) should be owned by *www-data*.

### Service management

Nginx can be reloaded, restarted and checked using:

```bash
sudo service nginx reload
sudo service nginx restart
sudo service nginx status
```

PHP can be reloaded, restarted and checked using:

```bash
sudo service php8.1-fpm reload
sudo service php8.1-fpm restart
sudo service php8.1-fpm status
```
