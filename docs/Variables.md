# Variables

## Current

### $noteSurvey - a *Survey* object defining the environment

$noteSurvey **should be treated as read-only in your surveys**.

It is unlikely that you use much of the ```$noteSurvey``` object apart from possibly ```getPage()```. However a complete list is given below.

| Property                   | Value | Notes               |
| ------------------------------ | -------- | ------------------- |
| participant | The participant object | for its properties see below |
| page   | (PRIVATE) The current page number | This returns the current pointer position in the ```$_NOTE_MODULE['PAGES']``` array. The first page will therefore be 'page zero.' |
| moduleId  | The folder name (without slashes) used to access this survey | |
| rawUri | The folder name (including a trailing slash) | |
| baseUri | The base name of the website, ignoring server host name and folder name | In reality this is likely to be blank. |
| requestUri | The full request (including a trailing slash) | |
| mode | The current NOTE 'mode' | This will be either *dev*, *run* or *data* |
| otherId | Any information following the survey name in *moduleId* | |
| moduleDir | The module directory on the server, starting at the web root | This is likely to be ```modules/<moduleId>``` |
| modulePath | The module directory on the server, starting at the web root | This is likely to be ```modules/<moduleId>``` |
| fsModulePath | The module directory on the server **including** the web root | E.g. ```/var/www/html/modules/<moduleId>``` |

| Method                   | Value | Notes               |
| ------------------------------ | -------- | ------------------- |
| getPage()   | Getter for the current page number | This returns the current pointer position in the ```$_NOTE_MODULE['PAGES']``` array. The first page will therefore be 'page zero.' |

### $participant - a *Participant* object defining the participant

| Property                   | Value | Notes               |
| ------------------------------ | -------- | ------------------- |
| db | The SQLite database for this survey | This is the PHP PDO object for the database. This should be unset before the survey page is loaded. |
| dbInfo | An array of participant information | This contains all of the information retrieved from the *visitors* table. |
| uid | (PRIVATE) The participant unique ID | Generated as the *moduleId* value plus a random *UUID* string. |
| isNew | If the participant is new to the survey | Likely to be *1* on the first page and *0* on other pages. |
| lastPage | The last page visited by this participant, as an integer representing a pointer to ```$_NOTE_MODULE['PAGES']``` | This includes the page currently being viewed. |

| Method                   | Value | Notes               |
| ------------------------------ | -------- | ------------------- |
| getUid() | Getter for the participant unique ID |  |

### $_NOTE_MODULE

Array of survey-specific options (see the [configuration file](./the-conf-file.md) for more details)

## Deprecated

### $_NOTE

```$_NOTE``` is now deprecated and will be removed in a future version. It has been replaced by ```$noteSurvey```.
