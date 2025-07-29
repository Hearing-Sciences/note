# Debug Mode

It's possible to run NOTE in *debug mode* where variable information is dumped to the page and additional data checking takes place.

To enable debug mode, add the following line to *config.php*:

```$_NOTE_MODULE['DEBUG'] = true;```

Your survey pages will contain the message *debug mode on*.

![Debug mode activated](img/debug-mode.png "Debug mode activated")

## Variable dump

The ```$noteSurvey``` variable will be shown twice on the page, before and after and checks have been completed. The variable can be revealed by clicking the button *Show / Hide variable info.* The variable is the basis for the ```$_NOTE``` variable and can be checked for errors.

For example, if the page has loaded for the first time you will see that the private ```page``` property increments. If you reload the page, you will see that there is no increment (because the survey has not progressed).

## Testing data on survey pages and the database

This is covered in a separate section.
