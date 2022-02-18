# Installation
## Getting the files
If you are planning to develop surveys and not work with the source code for note, just download a zip file of the contents.
## Getting a web server
You will need a web server in order to view your studies. There are some readily available web servers that can be installed within Windows. One of the most well-known is Wampserver available at [https://www.wampserver.com/en/](https://www.wampserver.com/en/). I'll assume that you will download and install this, but if you are using another web server you will need to make further modifications explained below.
## Wampserver Setup
Before you start, you will need the [Visual C++ Redistributable for Visual Studio 2012 Update 4](https://www.microsoft.com/en-us/download/details.aspx?id=30679). The link should work but if not, just search for the title. Then you can install Wampserver using the default options. Once installed, start Wampserver and a green 'W' should eventually appear in the system tray. 

You will need to configure Apache to render the pages correctly. Open the Apache configuration file by **left**-clicking on the green 'W' and selecting *Apache -> httpd-vhosts.conf*. Add the following lines to the 'Directory' section:
```ApacheConf
	RewriteEngine On
    RewriteBase "/"
    RewriteRule "^note/data/([a-zA-Z0-9\-_]+)$" "/note/index.php?data/$1" [END]
    RewriteRule "^note/([\/a-zA-Z0-9\-_]+)$" "/note/index.php?$1" [END]
```
So the complete section of text should look like this:
```ApacheConf
<VirtualHost *:80>
  ServerName localhost
  ServerAlias localhost
  DocumentRoot "${INSTALL_DIR}/www"
  <Directory "${INSTALL_DIR}/www/">
    Options +Indexes +Includes +FollowSymLinks +MultiViews
    AllowOverride All
    Require local
	RewriteEngine On
    RewriteBase "/"
    RewriteRule "^note/data/([a-zA-Z0-9\-_]+)$" "/note/index.php?data/$1" [END]
    RewriteRule "^note/([\/a-zA-Z0-9\-_]+)$" "/note/index.php?$1" [END]
  </Directory>
</VirtualHost>
```
Create a folder in *c:\wamp\www* called *note* and copy all of the note files into this folder. Then reload Apache by left-clicking on the green 'W' and selecting *Apache -> Service administration 'wampapache64' -> Restart Service*. You should now be able to open a browser and visit *http://localhost/note* to see the general welcome page. (You might see some errors too, but that's because we're not finished.)

Now create a folder in *c:\wamp\www\note* called *modules* and copy your study folders into it. You should be able to access your experiments at *http://localhost/note/(name-of-module-folder)* (you **mustn't** include */modules/* as the server handles that itself).