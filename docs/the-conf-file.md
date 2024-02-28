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
| JS_LIBRARIES    | No       | An array of libraries. At present the only value available is *bootstrap5* which causes NOTE to load the Bootstrap 5 JS library. | If *bootstrap5* is missing, Bootstrap 4 is used instead for backwards compatibility. |
| FOOTER_COPYRIGHT | Yes      | The copyright message | "*The information and materials on this website are the property of the University of Nottingham and/or Nottingham University Hospitals NHS Trust*" |
| PAGES           | Yes      | An array of page names. In order for everything to work, the page name must relate to a file name. The file name will be *page_{page name}.php* where *{page_name}* is the page name in the array. For example: the value *welcome* would cause *page_welcome.php* to be loaded and *survey* would case *page_survey.php* to be loaded. Values must not include spaces. | If a file cannot be found, the NOTE code will crash and the participant will probably see a mostly-empty web page. |

***The three 'title' variables are confusing and likely to be reduced to just one value in the future.**

## Creating arrays of pages - a handy tip

PHP arrays can be built an item at a time using the shortcut '[]' syntax as in the example above. This means that you can sneak extra in in during the creation process. For example, maybe you want to know the page number where a particular part of the survey begins. You could hard-code that information, but if you decide to re-order your pages this can cause bugs. A better way would be to do this:

```php
$_NOTE_MODULE['PAGES'][] = 'welcome';
$_NOTE_MODULE['PAGES'][] = 'page';
$_NOTE_MODULE['PAGES'][] = 'another_page';
$_NOTE_MODULE['SURVEY_START_PAGE'] = count($_NOTE_MODULE['PAGES']);
$_NOTE_MODULE['PAGES'][] = 'survey';
```

You can see that an additional value 'SURVEY_START_PAGE' is created, and the value is set to the current count of pages (3). This exposes a key feature of arrays in many programming languages. The value returned is '3' but it might be expected that the survey page is actually page four. This all works because array indexes begin at zero, and from NOTE's point of view the survey page is page three. (And yes, the welcome page is page zero!).
