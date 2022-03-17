# db.php - database management
This file should be created and stored in the survey directory. It handles survey-specific database operations.

For someone new to PHP, this will probably be the most complicated file to create as NOTE doesn't provide much assistance. There are two very important things to know:
1. **NOTE will not provide much assistance for saving data **- you must create most of the code yourself.
2. **NOTE will not check the data is correct, or even that it is 'safe.'** You will need to ensure that the data is what you are expecting and not a hacking attempt. 

Here is a sample *db.php* file:
```php
<?php

//  My survey
//
// AL 2022-03-17

// table creation scripts
$db->exec('CREATE TABLE IF NOT EXISTS visitors (
                id INTEGER PRIMARY KEY,
                uniqueID TEXT,
                ipaddr TEXT,
                lastPage INTEGER,
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP)');

//========================================================================================================
// Store page data

function db_store_page_data($page, &$post, $uid, &$db, &$_NOTE_MODULE) 
{
	switch($_NOTE_MODULE['PAGES'][$page]) {

		case 'screening_questions':
			// Process date of birth
			$post['date_of_birth'] = $post['dob']['year'].'-'.$post['dob']['month'].'-'.$post['dob']['day'];
			// Process fitting dates (now with 'N/A' to totally mess things up)
			if (is_numeric($post['leff']['year']) || is_numeric($post['leff']['month'])) {
				$post['left_ear_first_fitted'] = $post['leff']['year'].'-'.$post['leff']['month'].'-01';
			}
			if (is_numeric($post['reff']['year']) || is_numeric($post['reff']['month'])) {
				$post['right_ear_first_fitted'] = $post['reff']['year'].'-'.$post['reff']['month'].'-01';
			}
			// Set the HHIE/A according to year of birth
			if ($post['dob']['year'] < (date('Y')-65)) {
				$_SESSION['HHI'] = 'hhie';
			} else {
				$_SESSION['HHI'] = 'hhia';
			}
			
			$qry    = 	$db->prepare('
							INSERT INTO screening_questions 
							(uid, prolific_pid, gender, date_of_birth, country, aided_ear, aid_type_left_ear, aid_model_left_ear, left_ear_first_fitted, how_often_use_left_aid, aid_type_right_ear, aid_model_right_ear, right_ear_first_fitted, how_often_use_right_aid, uses_smartphone )
							VALUES
							(:uid, :prolific_pid, :gender, :date_of_birth, :country, :aided_ear, :aid_type_left_ear, :aid_model_left_ear, :left_ear_first_fitted, :how_often_use_left_aid, :aid_type_right_ear, :aid_model_right_ear, :right_ear_first_fitted, :how_often_use_right_aid, :uses_smartphone)
						');
			foreach ( ['prolific_pid', 'gender', 'date_of_birth', 'aided_ear', 'country', 'aid_type_left_ear', 'aid_model_left_ear', 'left_ear_first_fitted', 'how_often_use_left_aid', 'aid_type_right_ear', 'aid_model_right_ear', 'right_ear_first_fitted', 'how_often_use_right_aid', 'uses_smartphone'] as $field) {
				$fields[':'.$field] = $post[$field];
				$fields[':uid'] = $uid;
			}
			$result = $qry->execute($fields);
			if (!$result) {
				print_r($qry->errorInfo()); die;
			}
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
## Table Creation Scripts
The first section contains one or more lines of database execution functions ('*$db->exec*') that create the tables you require for the survey. The single example above creates the *visitors* table that NOTE must have to manage participants. You can skip these commands, but be aware of the following whether you include them or not:
* These commands are run to create and populate a database if an existing database can't be found. This means that if your database disappears for some reason the survey can re-create it and continue storing data. Although you may be more keen to find out why the database has disappeared!
* These commands are run **every time a page is loaded**. Therefore, to prevent the scripts overwriting your database you **must** add *IF NOT EXISTS* to every SQL statement that creates a table. Such as in the example above; 'CREATE TABLE **IF NOT EXISTS** visitors'.
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
|$page|The ID of the page that has just been visited|
|&$post|The data that was saved in a form on that page|
|$uid|The unique ID of the participant (which is independent of any survey-specific participant ID you may wish to use)|
|&$db|The database represented as an object|
|&$_NOTE_MODULE|Module variables and values created in *config.php*|
I will briefly mention that some of these variables are passed by reference (they begin with '&\\$' rather than just '\\$'). Please ask if you want to know more.
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
	$qry    = 	$db->prepare('
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