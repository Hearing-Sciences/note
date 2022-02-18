# Installation
## Getting the files
If you are planning to develop surveys and not work with the source code for note, just download a zip file of the contents. These are available at [https://github.com/Hearing-Sciences/note](https://github.com/Hearing-Sciences/note). If you don't have access, contact Michael or Andrew.
## Getting a web server
You will need a web server in order to view your studies. There are some readily available web servers that can be installed within Windows. I'll include two options below but other web servers are available for local installations in Windows. Whatever you use, you will need to make further modifications explained below.
## NGINX Setup
### NGINX
This is the same program that is used on the NOTE server itself (although it's not vital to use the same program). You can download a windows version from [http://nginx.org/en/docs/windows.html](http://nginx.org/en/docs/windows.html). It is downloaded as a zip archive, so you can extract the contents anywhere. I'd recommend *c:\nginx* as it's outside the areas protected by Windows.
### PHP-FastCGI
You will then need PHP to run the code. A Windows version is available from [https://windows.php.net/download/](https://windows.php.net/download/) and I chose the non-thread safe x64 version. That is also a zip archive so I extracted it to *c:\wamp\php*. Some configuration will be needed to set up sessions, so create a file called *php.ini* in *c:\wamp\php* with the following contents:
```
extension_dir = "ext"
extension=pdo_sqlite
[Session]
session.save_handler = files
session.save_path = "C:\nginx\temp\fastcgi_temp"
session.use_strict_mode = 0
session.use_cookies = 1
session.use_only_cookies = 1
session.name = PHPSESSID
session.auto_start = 0
session.cookie_lifetime = 0
session.cookie_path = /
session.cookie_domain =
```
To run the program you can make a batch file such as *php-fcgi.bat*:
```cmd
@ECHO OFF
set PATH=C:\nginx\PHP;%PATH%
c:\nginx\RunHiddenConsole.exe C:\nginx\php\php-cgi.exe -b 127.0.0.1:9000 -c c:\nginx\php\php.ini
```
This file adds PHP to the path variable and starts the executable, listening on port 9000. Run this file to start PHP.
### Back to NGINX
Now NGINX will need to know how to find the PHP executable. The following configuration file should work:
```
worker_processes  1;

events {
    worker_connections  1024;
}


http {
    include       mime.types;
    default_type  application/octet-stream;

    sendfile        on;
    keepalive_timeout  65;

    server {
        listen       80;
        server_name  localhost;

		root c:/nginx/www;

		location / {
			try_files $uri =404 /index.php?$query_string;
		}

		location /dev/ {
			try_files $uri =404 /dev/index.php?$query_string;
		}

		location ~ \.php$ {
			fastcgi_pass   127.0.0.1:9000;
			fastcgi_index  index.php;
			fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
			include        fastcgi_params;
		}
	}
}
```
Now you can run the nginx.exe executable and browse to http://localhost/(name-of-experiment-folder) to view everything.
## Wampserver Setup
Before you start, you will need the [Visual C++ Redistributable for Visual Studio 2012 Update 4](https://www.microsoft.com/en-us/download/details.aspx?id=30679). The link should work but if not, just search for the title. Then you can download Wampserver [https://www.wampserver.com/en/](https://www.wampserver.com/en/). It's best to install Wampserver using the default options. Once installed, start Wampserver and a green 'W' should eventually appear in the system tray. 

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