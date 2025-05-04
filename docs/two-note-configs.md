* Running two NOTE configurations

You can test another version of NOTE without harming your development environment. To achieve this, put the versions of NOTE in separate folders and modify your Nginx configuration.

In the file _nginx.conf_ add another `server` block. In the example below the newer NOTE files have been stored in C:\NOTEv21 although you could put them wherever you liked.

```
    server {
      ... original server configuration
	}

    server {
        listen       9100;
        server_name  localhost;

		root c:/NOTEv21;

		location / {
			try_files $uri =404 /index.php?$query_string;
		}
		
		location /data/ {
			try_files $uri =404 /data.php?$query_string;
		}

		location ~ \.php$ {
			fastcgi_pass   127.0.0.1:9000;
			fastcgi_index  index.php;
			fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
			include        fastcgi_params;
		}
	}

```

Then start or restart Nginx and PHP-FPM (in whatever way you usually start these programs). You will find your orignal NOTE setup in the usual place and the newer NOTE setup at `localhost:9100`.
