# Making a new NOTE server

Here are some tips for creating a new server, if manually installing NGINX or using the packaged Vagrant server aren't for you. I'm going to assume that a Debian family distribution will be used (Debian, Ubuntu, Mint etc.) but try to offer instructions for Nginx and Apache.

## Extreme warning

This is a work in progress. Do not follow these instructions and expect to have a stable, secure NOTE setup at the end.

## 1. Install a server

This isn't a guide for installing servers as plenty of guides exist on the web. A virtual server might even be provided for you which means that you can skip this step.

Don't forget to update packages before starting:

```bash
apt-get update
apt-get upgrade -y
apt-get autoremove -y
```

## 1a. Install required packages (Nginx)

Assuming that you're working with Nginx, you will need:

- Nginx
- PHP - FastCGI
- Sqlite

These can be installed in Debian family distributions using

```bash
sudo apt install -y nginx php-cgi sqlite3 php-sqlite3 
```

## 1b. Install required packages (Apache)

Assuming that you're working with Apache, you will need:

- Apache
- PHP
- Sqlite

These can be installed using:

```bash
sudo apt install -y apache2 libapache2-mod-php sqlite3 php-sqlite3 
```

## 2. Configure the web server

The packages are in place so it's time to configure them. Broadly, the server will need to:

- Serve pages from a directory
- Always open *index.php* and then feed other directory names as arguments ...
- ... apart from *data.php* which follows a similar system ...
- ... and files inside *libs* which should be served as they are
- **not** serve anything else

## 2a. Configure the web server (Nginx)

The following Nginx configuration will serve pages from ```/var/www/surveys.hearing.nottingham.ac.uk```

```conf
server {
    listen       443    ssl http2;
    server_name  surveys.hearing.nottingham.ac.uk mslapmedbrc01.nottingham.ac.uk;
    ssl_certificate  /etc/ssl/2022-10-certs/surveys_hearing_nottingham_ac_uk-bundle.crt;
    ssl_certificate_key  /etc/ssl/2021-12-certs/surveys_hearing_nottingham_ac_uk.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_prefer_server_ciphers on;
    ssl_ciphers ECDH+AESGCM:ECDH+AES256-CBC:ECDH+AES128-CBC:DH+3DES:!ADH:!AECDH:!MD5;

    root   /var/www/surveys.hearing.nottingham.ac.uk;
    index  index.php index.html;

    location / {
        try_files $uri $uri/ =404 /index.php?$query_string;
    }

    location ~\.(sqlite3)$ {
        deny all;
        error_page 403 =404 / ;
    }

    location /data {
            return 404;
    }

    location /data/<somewhere> {
        try_files $uri $uri/ =404 /index.php?$query_string;
            auth_basic  "Data";
            auth_basic_user_file        /etc/nginx/.htpasswd_<somewhere>;
    }
}

server {
    listen       80;
    server_name  surveys.hearing.nottingham.ac.uk;
    return 301 https://surveys.hearing.nottingham.ac.uk$request_uri;
}

server {
    listen       80 default_server;
    listen       [::]:80 default_server;
    server_name  mslapmedbrc01.nottingham.ac.uk;
    return 301 https://mslapmedbrc01.nottingham.ac.uk$request_uri;
}

```

## 2b. Configure the web server (Apache)

```conf
<IfModule mod_ssl.c>
        <VirtualHost *:443>
                ServerAdmin <your email address>
                ServerName surveys.hearing.nottingham.ac.uk
                DocumentRoot /var/www/surveys.hearing.nottingham.ac.uk
                ErrorLog ${APACHE_LOG_DIR}/error.log
                CustomLog ${APACHE_LOG_DIR}/access.log combined
                SSLEngine on
                SSLCertificateFile      /etc/ssl/selfsigned/apache-selfsigned.crt
                SSLCertificateKeyFile /etc/ssl/selfsigned/apache-selfsigned.key
                <FilesMatch "\.(cgi|shtml|phtml|php)$">
                                SSLOptions +StdEnvVars
                </FilesMatch>
                <Directory /usr/lib/cgi-bin>
                                SSLOptions +StdEnvVars
                </Directory>
                <directory "/var/www/surveys.hearing.nottingham.ac.uk">
                        RewriteEngine On
                        RewriteBase "/"
                        RewriteRule "^data/([a-zA-Z0-9\-_]+)$" "/index.php?data/$1" [END]
                        RewriteRule "^([\/a-zA-Z0-9\-_]+)$" "/index.php?$1" [END]
                </directory>
                <directory "/var/www/surveys.hearing.nottingham.ac.uk/libs">
                        Options -Indexes
                        RewriteEngine Off
                </directory>
        </VirtualHost>
</IfModule>
```
