# Server Installation

**You are responsible for the safety and security of any setup you create yourself. The authors accept no responsiblity for any environment you create and it must not be assumed that following the information given below will result in a secure environment.** 

### APT-compatible Linux

Install the base packages.

```bash
apt install -y nginx php-fpm php-sqlite3
```

Create the folder to host files

```bash
mkdir /var/www/note
```

In your ```nginx.conf``` file configure the NOTE folder. **This configuration will not work on your server and/or create a secure environment - make sure the settings are correct for your server and security requirements.**

```/etc/nginx/sites-available/note-server.conf```

```conf
server {
    listen 443 ssl http2;
    server_name  note.nowhere.com;
    ssl_certificate <<ADD YOUR CERTIFICATE HERE>>;
    ssl_certificate_key <<ADD YOUR KEY HERE>>;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_prefer_server_ciphers on;
    ssl_ciphers ECDH+AESGCM:ECDH+AES256-CBC:ECDH+AES128-CBC:DH 3DES:!ADH:!AECDH:!MD5;

    root   /var/www/note;
    index  index.php index.html;

    client_max_body_size 256M;

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

    location ~ /modules/.*?/data\.php {
        return 404;
    }

    location /dev/ {
        try_files $uri $uri/ =404 /dev/index.php?$query_string;
        auth_basic      "Development area";
        auth_basic_user_file    /etc/nginx/htpass/.htpasswd_dev;
    }

    location /dev/data/ {
        try_files $uri $uri/ =404 /dev/data.php?$query_string;
        auth_basic      "Development area";
        auth_basic_user_file    /etc/nginx/htpass/.htpasswd_dev;
    }

    # Pass PHP requests to PHP-FPM, assuming that it is listening on a socket
    location ~ [^/]\.php(/|$) {
        fastcgi_split_path_info ^(.+?\.php)(/.*)$;
        if (!-f $document_root$fastcgi_script_name) {
            return 404;
        }
        fastcgi_index   index.php;
        fastcgi_pass    unix:/var/run/php/php-fpm.sock;
        include         fastcgi_params;
        fastcgi_param   SCRIPT_FILENAME    $document_root$fastcgi_script_name;
        fastcgi_param   SCRIPT_NAME        $fastcgi_script_name;
    }
}

# Bounce insecure requests to the server listening on HTTPS
server {
    listen       80;
    server_name  surveys.hearing.nottingham.ac.uk;
    return 301 https://surveys.hearing.nottingham.ac.uk$request_uri;
}
```

Link the configuration so that it is available

```bash
ln -s /etc/nginx/sites-available/note-server.conf /etc/nginx/sites-enabled/note-server.conf
```

Start the server

```bash
systemctl start php8.4-fpm nginx
```

Troubleshoot as required - remember it's **your** server!
