# File structure

You will need the following files to create a survey:

| File | Required? | Notes |
| ---  | ---       | ----- |
| [config.php](the-conf-file.md) | Yes | This file defines your survey. The name of the survey, pages to use, order etc. |
| [db.php](the-db-file.md) | Yes | This file contains functions for accessing the survey database and saving data. |
| data.php | Yes | This file should contain code for displaying data, analyses etc. |
| README.txt | Yes | This file should contain survey details for administrators browsing the server. |
| page_xxx.php | Yes | Each page in your survey must be in the format *page_xxx.php* where *xxx* is a name unique to the survey. |
| experiment.css | No | If this file exists it is loaded with each of your survey pages and provides a space for 'survey-global' styling. |
| experiment.js | No | If this file exists it is loaded with each of your survey pages and provides a space for 'survey-global' JavaScript functions. |
| page_xxx.css | No | If this file exists it is loaded with the specific survey page *page_xxx.php* and provides a space for page-specific styling. |
| page_xxx.js | No | If this file exists it is loaded with the specific survey page *page_xxx.php* and provides a space for page-specific JavaScript. |
