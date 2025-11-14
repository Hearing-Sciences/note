# *config.php* - your experiment outline

This is the most important file in the survey directory. It sets the variables and values that NOTE will need to display your survey.

Here's a very basic configuration file:

```php
<?php

// My example survey
//
// Andrew Lavens, March 2022

//==============================================================================
// Overall
//==============================================================================

$_NOTE_MODULE['TITLE'] = 'My Example Survey';
$_NOTE_MODULE['PAGETITLE'] = 'My Example Survey';
$_NOTE_MODULE['PAGES'] = [];
$_NOTE_MODULE['CSS_LIBRARIES'] = [];
$_NOTE_MODULE['JS_LIBRARIES'] = [];
$_NOTE_MODULE['FOOTER_COPYRIGHT'] = 'The information and materials on this website are the property of the University of Nottingham and/or Nottingham University Hospitals NHS Trust';
$_NOTE_MODULE['STUDY_TITLE'] = 'My Example Survey';
$_NOTE_MODULE['GOLIVE_DATE'] = '2024-04-25';

//==============================================================================
// Order of pages - the pages below are just an example
//==============================================================================

$_NOTE_MODULE['PAGES'][] = 'welcome';
$_NOTE_MODULE['PAGES'][] = 'survey';
$_NOTE_MODULE['PAGES'][] = 'thanks';
$_NOTE_MODULE['PAGES'][] = 'goodbye';

?>
```

$_NOTE_MODULE is an array of module-specific options including:

| Key             | Required | Description | Default |
| --------------- | -------- | ----------- | ------- |
| TITLE*          | Yes      | The title of your survey | NULL |
| PAGETITLE*      | Yes      | The title of your survey | NULL |
| STUDY_TITLE*    | Yes      | The title of your survey | NULL |
| GOLIVE_DATE     | No       | The date it will be released to participants in the format *yyyy-mm-dd* | 1900-01-01 |
| CSS_LIBRARIES   | No       | An array of libraries. At present the only value available is *bootstrap5* which causes NOTE to load the Bootstrap 5 library. | If *bootstrap5* is missing, Bootstrap 4 is used instead for backwards compatibility. |
| JS_LIBRARIES    | No       | An array of libraries to load from jsDelivr. Current options include *bootstrap5* and *p5*. | If *bootstrap5* is missing, Bootstrap 4 is used instead for backwards compatibility. |
| FOOTER_COPYRIGHT | Yes      | The copyright message | "*The information and materials on this website are the property of the University of Nottingham and/or Nottingham University Hospitals NHS Trust*" |
| PAGES           | Yes      | An array of either page names or arrays of page information (see below). In order for everything to work, the page name must relate to a file name. The file name will be *page_{page name}.php* where *{page_name}* is the page name in the array. For example: the value *welcome* would cause *page_welcome.php* to be loaded and *survey* would case *page_survey.php* to be loaded. Values must not include spaces. | If a file cannot be found, the NOTE code will crash and the participant will probably see a mostly-empty web page. |
| FIELDS | No | This should contain two indexed arrays (see below) | NULL |
| FIELDS['COMMON'] | No | An indexed array of FORM field / column names that are present on every page and should *always* be included in automated testing. The array is populated using ```'name' => 'SQLite type'``` e.g. ```'uid' => 'TEXT'```. | NULL |
| FIELDS['IGNORE'] | No | An indexed array of FORM field / column names that should **not** be checked as part of any automated testing. This will be useful for form fields such as 'nextpage' that are functional but never contain data. The array is populated using ```'name' => 'SQLite type'``` e.g. ```'uid' => 'TEXT'```. | NULL |

***The three 'title' variables are confusing and likely to be reduced to just one value in the future.**

## Creating an array of pages

NOTE uses an indexed array to define the order of pages. If a page should be shown more than once (e.g. for multiple trials) then it would need to be added to the array multiple times. The *PAGES* array can be declared at once in its entirety or a page at a time using the shortcut '[]' syntax as in the example above.

### Page information as arrays - the next dimension

From NOTE version 2.1, entries in the *PAGES* array can themselves be arrays. In this case, one of the keys must be *PAGE* referring to the name of the page in the same way as before. However, other keys can be defined which are passed into the survey as *$_NOTE_MODULE['PAGE_INFO']*. For example:

```php
$_NOTE_MODULE['PAGES'][] = [
    'PAGE' => 'task',
    'TRIAL_ID' => 1
];
```

This would add a page to the survey. The *PAGE* key determines that the survey page would be called *page_task.php* and the page would receive the following array:

```php
$_NOTE_MODULE['PAGE_INFO'] = [
    'TRIAL_ID' => 1
]
```

You can add as many keys as you like. It will be useful if you need to add a large number of repeating task pages. The trial ID could be generated as part of a loop:

```php
for ( $trial = 1; $trial < 101; $trial++) {
    $_NOTE_MODULE['PAGES'][] = [
        'PAGE' => 'task',
        'TRIAL_ID' => $trial
    ];
}
```

On the task page you could use the value of ```$_NOTE_MODULE['PAGE_INFO']['TRIAL_ID']``` to record progess ('Trial n of 100'), determine which stimulus to present etc. It could also be added to a hidden FORM field to send back to the server when the form is submitted:

```php
<input type="hidden" name="trial_id" value="<?= $_NOTE_MODULE['PAGE_INFO']['TRIAL_ID'] ?>">
```

### 'PAGE' array

If a page entry is itself an array, there are some required and optional keys:

|Key|Required?|Description|
|---|---|---|
|PAGE|Yes|The name of the page *n* in *page_n.php*|
|TABLE|No|The table that will receive data from the page|
|FIELDS|No|An indexed array of field names and SQLite types: data that will be sent from the page for saving or processing.|

The FIELDS array should be set as ```'field' => 'type'```, e.g. ```'name' => 'TEXT'```. The types are from SQLite: INTEGER, TEXT and DATETIME are the ones you are most likely to need. The types aren't used in NOTE yet but may be in a later version.

### Setting 'bookmarks' (DEPRECATED)

#### (This is the old method for maintaining trial IDs etc. It still works but check out the newer method above that uses the PAGES entries as arrays.)

If pages are appended to the *PAGES* array individually there is an opportunity to add code between statements. For example, it may be important to know the page number where a particular part of the survey begins. That information could be hard-coded but if you decide to re-order your pages this can cause bugs. A better way would be to do this:

```php
$_NOTE_MODULE['PAGES'][] = 'welcome';
$_NOTE_MODULE['PAGES'][] = 'page';
$_NOTE_MODULE['PAGES'][] = 'another_page';
$_NOTE_MODULE['IMPORTANT_START_PAGE'] = count($_NOTE_MODULE['PAGES']) - 1;
$_NOTE_MODULE['PAGES'][] = 'survey';
```

You can see that an additional value 'IMPORTANT_START_PAGE' is created, and the value is set to the current count of pages (3). Note that PHP arrays are indexed from zero so the count of pages (3) is different from the current index (2). The ```- 1``` can be used to correct for this if necessary.

You can make use of this feature when a survey contains many instances of the same page:

```php
$_NOTE_MODULE['PAGES'][] = 'welcome';
$_NOTE_MODULE['PAGES'][] = 'page';
$_NOTE_MODULE['PAGES'][] = 'another_page';
$_NOTE_MODULE['IMPORTANT_START_PAGE'] = count($_NOTE_MODULE['PAGES']) - 1;
foreach(range(1,10)) {
    $_NOTE_MODULE['PAGES'][] = 'survey_trial';
}
$_NOTE_MODULE['PAGES'][] = 'goodbye';
```

On the *survey_trial* page you might want to show 'trial *x*' where *x* would be the trial number (from 1 to 10 in this example). You could achieve this by comparing the current trial page to the *IMPORTANT_START_PAGE*:

```php
<?php
$trial_number = $noteSurvey->getPage() - $_NOTE_MODULE['IMPORTANT_START_PAGE'];
?>

<h3>Trial number <?php echo $trial_number; ?></h3>
```

```$noteSurvey->getPage()``` retrieves the current page number and ```$_NOTE_MODULE['IMPORTANT_START_PAGE']``` retrieves the start page defined earlier. For the first time that *survey_trial* is called ```$trial_number``` will equal 1. On the second call it will equal 2 and so on.
