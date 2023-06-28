# Sending files to the NOTE server

WinSCP is the best program to transfer files to and from the NOTE server. The settings are:

- File Protocol: SFTP
- Server: mslapmedbrc01.nottingham.ac.uk
- Port number: 22
- User name: your University account probably starting with *ms*
- Password: leave this blank and supply it each time you want to connect

Trasfer the files to your home area using WinSCP then move them to the correct directory later. It's safer and you may have to change some file attributes anyway.

## Copying the files within the NOTE server

The following directories are used for storing NOTE surveys ('modules'):

- Development: ```/var/www/surveys.hearing.nottingham.ac.uk/dev/modules/```
- Live: ```/var/www/surveys.hearing.nottingham.ac.uk/modules/```

These locations contain one directory per survey and the directory names form part of the URL you would use to complete the survey online.

Use *PuTTY* to connect to the server with the following settings:

- Host name: mslapmedbrc01.nottingham.ac.uk
- Port: 22
- Connection type: SSH

When the terminal window opens you can use your windows account (starting with *ms*) and password to log in.

You have very few permissions as a regular user and any privileged actions require the use of *sudo*. To copy the files to the dev area for the survey called *test_survey* you would run the following commands:

|Command|Purpose|
|---|---|
|```cd ~/test_survey```|Change directory to the survey directory|
|```sudo mkdir /var/www/surveys.hearing.nottingham.ac.uk/dev/test_survey```|Create a *test_survey* folder|
|```sudo cp -r * /var/www/surveys.hearing.nottingham.ac.uk/dev/test_survey/```|Copy all files and folders (and their contents) to the folder you just created|
|```cd /var/www/surveys.hearing.nottingham.ac.uk/dev/```|Change directory to the *dev* area|
|```sudo chown -R nginx:nginx /var/www/surveys.hearing.nottingham.ac.uk/dev/test_survey```|Change the owner and group of the survey folder and contents to the *nginx* user|
|```sudo chmod -R u+w /var/www/surveys.hearing.nottingham.ac.uk/dev/test_survey```|Make sure that the *nginx* user has read and write permissions to the *test_survey* folder and files within|

Other ways of transferring files are available: this is just one way. 

## SQLite Permissions

One commonly encountered trap is for everything to look OK but for NOTE to refuse to save any data. It should be immediately apparent because clicking the *Next* button will cause the same page to reload rather than letting you continue. The most likely cause for this is the permissions attached to the database *and the containing folder*. The *nginx* user must have read and write permissions to the database *database.sqlite3* and the parent folder */var/www/surveys.hearing.nottingham.ac.uk/dev/test_survey* (in this case). The commands above will set those permissions but it's easy to forget.
