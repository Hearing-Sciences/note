# Adding password protection to a survey data page

This is all done on the server, so you will need access to the server's terminal. **If you are not comfortable editing text files on a Linux server please ask someone to complete this task.**

## Htpasswd

The password files are stored in a dedicated folder within the nginx configuration directory. You will need to make a password file on the server (or use an existing one) in a format that the web server can understand. There is a command to achieve this:

```text
sudo htpasswd -c /etc/nginx/htpass/.{name of file} {name of admin user}
```

To break this command down:

* sudo - to perform the task as the root user (you will need to enter your password to confirm this)
* htpasswd - the command for managing web server password files
* -c - for creating a file
* /etc/nginx/htpass/.{name of file} - replace {name of file} with something appropriate. You can use *htpasswd_data_* and the name of your study (without spaces). Not that the file is preceded by a dot - this is intentional. It hides the file from general view.
* {name of admin user} - this is the admin username you will use with your study later. It can be almost anything you like but don't include any spaces. I have tended to use the initials of the study followed by *_admin* such as *ssq12_admin*

You will be asked for your password to perform the admin task, then asked to set a password for this admin user.

## Password best practice

At the moment I would recommend that you set the password to be 20 random characters (letters and numbers). You can download random character generators as browser plugins or you could write your own in Matlab, Python, Rust etc. (Or you could use one included with this package - see below.) You can copy the password you have created and usually paste it into the terminal using a right mouse button click.

Store the password files in */etc/nginx/htpass* to keep things tidy.

### Linux Random Password Generator

There is a password generator available in Linux called *pwgen*. You can generate ten 20-character passwords from the NOTE server terminal using `pwgen -1 -s 20 10` then pick one to use as the password.

The `-1` option presents the strings in a single column and the `-s` option generates 'completely random passwords.' The default options choose randomly selected alphanumeric characters (i.e. A-Za-z0-9). It is possible to request symbols but strongly discouraged in this case as it can cause unintended consequences such as early string termination. Without symbols there are 704423425546998022968330264616370176 20-character combinations that could be returned, but if that is not sufficiently random then 30 characters could be used instead (giving 591222134364399413463902591994678504204696392694759424 combinations).

### Windows Random Password Generator

Andrew has written a (very) simple password generator in Rust also called *pwgen*. This is run from the **Windows** command line and outputs ten 20-character strings using randomly selected alphanumeric characters (i.e. A-Za-z0-9). No symbols are used to prevent unintended consequences such as early string termination.

You can find the program in *utilities/pwgen/target/release/pwgen.exe*. To run the program, open a command line window, *cd* to that folder and run *pwgen.exe*. You can copy one of the strings to the clipboard by double-clicking to select it and right-clicking to copy it.

The source code is included in case you want to improve it. The code is extremely simple so there will be plenty of scope for improvements!

## Nginx

You will need to edit /etc/nginx/sites-available/surveys.hearing.nottingham.ac.uk.conf with information about your password file.
Here is a template of the configuration you can edit:

```text
location /data/{{survey folder}} {
    try_files $uri $uri/ =404 /data.php?$query_string;
    auth_basic      "Only authorised users may continue";
    auth_basic_user_file    /etc/nginx/htpass/.{{name of the file you created earlier}};
}
```

This configuration goes in the *server { ... }* section. There will be other location statements already so just add yours to the list.

Replace {{survey folder}} with the real folder name of your survey and {{name of the file you created earlier}} with, er, the name of the file you created earlier.

Then reload the configuration with `sudo service nginx reload`.

## Help - I've forgotten the password (or never knew it)

If you can't remember a password but don't want to delete it, you can create a new user. Just run the *htpasswd* command again but without the *-c* switch:

```text
sudo htpasswd /etc/nginx/.{name of file} {name of a new user}
```

You will be asked to create a password.

If you want to delete the existing password and start again, delete the password file and create a new one with the same name.

This all sounds a bit poor from a security perspective, but remember that (a) you have already logged into the server and (b) you have rights to do things thanks to *sudo*. So the security checks have already been passed to allow you this far.
