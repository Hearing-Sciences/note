rem start nginx -c nginx.conf
rem start php-cgi -b 127.0.0.1:9000 -c php.ini
php -S localhost:8000 -t ../ router.php