# note

Nottingham Online Testing and Experiment (NOTE) framework

A lightweight framework for conducting research online. NOTE is intended to make setting up online tests
and experiments more efficient by handling the work of hosting multiple projects at once, parsing URLs,
keeping track of participant progress, handling database setup, and simplifying database interactions. Its
primary purpose is to allow researchers to focus on the design and implementation of their experiment rather
than the core infrastructure needed to do online testing.

## Revision History

## January 10 2024
### index.php
Minor changes have been made to variable checks that remove PHP warnings. For example, if a variable doesn't exist then *array_key_exists* checks will produce a warning so the code now checks that the array exists before checking the key.
### blank.html
A new page has been created containing simple placeholder information to be displayed if a survey is not provided (i.e. someone browses to *https://surveys.hearing.nottingham.ac.uk* without adding /{survey}).

## July 18 2023
### env.php and index.php
*Env.php* has been rewritten as a class *Env* managing the NOTE environment. *Index.php* has been updated to use an instance of Env called *$env* but still generates a *$_NOTE* for backwards compatibility.

## March 21 2023
### env.php and index.php

- Single quotes have replaced double quotes wherever possible. PHP scans for variables inside double quoted strings which is a waste of time if not needed.

### env.php

- The code for parsing a URL has been rewritten.

- `$_NOTE['MODULE_ID']` could contain either the module ID as expected or a page type 'data.' The code has been rewritten so that ['MODULE_ID'] now always contains the name of the module. 
- A new variable `$_NOTE['DISPLAY_ID']` has been created conatining the type of page; either 'exp' 'data' or 'preview'
- A new variable `$_NOTE['FS_MODULE_PATH']` has been created containing the module path from the filesystem point of view (for includes etc.).
- `$_NOTE['PART_ID']` is now deprecated and equal to `$_NOTE['OTHER_ID']`. It will be removed in a future version.

### index.php

* *config.php* is now required rather than just included.
* CSS and JavaScript files are now saved locally rather than being downloaded from external URLs.
* Some if/then code blocks have been changed from `if(condition) { ?> ... <?php } ?>`  to `if(condition): ?> ... <?php endif; ?>` for readability.
* Duplicated code for creating a database connection has been removed and replaced by a function *db_connect*. 
  * The function returns a database object `$db` or *false*.
  * The variable `$db_error` has been removed as it is redundant. `$db` can be checked instead.
* Changes to the `$_NOTE` variable (see above) have been reflected in this file.
* The code relies on functions that are in a required file. Because this file is referred to using a variable, code debuggers are unable to check that the functions exist and always throw errors. To work around this, the code checks for the functions' existence and creates dummy functions if required. This is a crude hack but enough to pass the debug checks.
* The code looks for four new files to include:
  * *experiment.css* and *experiment.js* in the module folder. This is loaded for every page in the experiment and can contain experiment-specific code.
  * *page_xxx.css* and *page_xxx.js* in the module folder, where *page_xxx* is actually the name of a PHP file from the experiment. These files would be loaded for that page only and can contain page-specific code.

