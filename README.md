# CHalloMates


## Requirements
- Your PHP version must be >= 8.1+ 
- Redis Server


## Installation Instructions

- Clone the repository with git
- Run `composer install`
- Run `cp .env.example .env`
- Run `php artisan key:generate`
- Set your database configration to .env 
- Set your email configration to .env 
- Run `php artisan migrate --seed`
- Run `php artisan jwt:secret`
- Run `npm install`
- Run `sudo npm install -g echo-socketio-v4`
- Run `laravel-echo-server init`
- Run `npm run dev` or ` npm run production`


### Set ENV
REDIS_CLIENT=predis

PUSHER_APP_ID=appid
PUSHER_APP_KEY=websoket_app_key
PUSHER_APP_SECRET=websocket_app_secret
PUSHER_APP_CLUSTER=mt1
PUSHER_HOST=challomates.com
PUSHER_PORT=6001
PUSHER_SCHEME=https


MIX_SOCKET_IO_HOST=challomates.com:3001
LARAVEL_ECHO_SERVER_HOST=challomates.com
LARAVEL_ECHO_SERVER_PORT=3001
LARAVEL_ECHO_SERVER_PROTO=https
LARAVEL_ECHO_SERVER_SSL_KEY="/home/ubuntu/letsencrypt/live/challomates.com/privkey.pem"
LARAVEL_ECHO_SERVER_SSL_CERT="/home/ubuntu/letsencrypt/live/challomates.com/fullchain.pem"

LARAVEL_WEBSOCKETS_SSL_LOCAL_CERT="/home/ubuntu/letsencrypt/live/challomates.com/fullchain.pem"
LARAVEL_WEBSOCKETS_SSL_LOCAL_PK="/home/ubuntu/letsencrypt/live/challomates.com/privkey.pem"


### SET configuration (optional)
APP_TIMEZONE=
FILESYSTEM_DISK=


### Set API Credential
FIREBASE_SERVER_KEY=
FIREBASE_SERVER_KEY=


### Set SSL Certificate
- Run `sudo apt install certbot python3-certbot-nginx`
- Run `sudo nano /etc/nginx/sites-available/challomates`
- Set `server_name challomates.com;`
- Set `sudo systemctl reload nginx`
- Run 
```sudo ufw allow 'Nginx Full'
   sudo ufw delete allow 'Nginx HTTP'
```
- Run `sudo certbot --nginx --config-dir="/home/ubuntu/letsencrypt/ -d challomates.com`


### Set NGINX
- Run `sudo nano /etc/nginx/sites-available/challomates`
- Add this line 
```
    map $http_upgrade $type {
        default "web";
        websocket "ws";
    }


    server {
        server_name challomates.com; # managed by Certbot
        root /var/www/challomates/public/;
    
        add_header X-Frame-Options "SAMEORIGIN";
        add_header X-Content-Type-Options "nosniff";
    
        index index.php;
    
        charset utf-8;
        
        #web
        location @web  {
            try_files $uri $uri/ /index.php?$query_string;
        }
        
        #websocket
        location @ws  {
            proxy_pass             https://127.0.0.1:6001;
            proxy_set_header Host  $host;
            proxy_read_timeout     60;
            proxy_connect_timeout  60;
            proxy_redirect         off;

            # Allow the use of websockets
            proxy_http_version 1.1;
            proxy_set_header Upgrade $http_upgrade;
            proxy_set_header Connection 'upgrade';
            proxy_set_header Host $host;
            proxy_cache_bypass $http_upgrade;
        }

    
        location / {
            try_files /nonexistent @$type;
        }

    
        #   location / {
        #      try_files $uri $uri/ /index.php?$query_string;
        # }
    
        location = /favicon.ico { access_log off; log_not_found off; }
        location = /robots.txt  { access_log off; log_not_found off; }
    
        error_page 404 /index.php;
    
        location ~ \.php$ {
            fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
            fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
            include fastcgi_params;
        }
    
        location ~ /\.(?!well-known).* {
            deny all;
        }

        #include snippets/phpmyadmin.conf;
        
        #snippets/phpmyadmin.conf;


        listen [::]:443 ssl ipv6only=on; # managed by Certbot
        listen 443 ssl; # managed by Certbot
        ssl_certificate /home/ubuntu/letsencrypt/live/challomates.com/fullchain.pem; # managed by Certbot
        ssl_certificate_key /home/ubuntu/letsencrypt/live/challomates.com/privkey.pem; # managed by Certbot
        include /home/ubuntu/letsencrypt/options-ssl-nginx.conf; # managed by Certbot
        ssl_dhparam /home/ubuntu/letsencrypt/ssl-dhparams.pem; # managed by Certbot

    }

    server {
        if ($host = challomates.com) {
            return 301 https://$host$request_uri;
        } # managed by Certbot


        listen 80 ;
        listen [::]:80 ;
        server_name challomates.com;
        return 404; # managed by Certbot


    }

```



### Set Cronjob
- Run `crontabb -e`
- add this line end of file `* * * * * cd /var/www/challomates && php artisan schedule:run >> /dev/null 2>&1`
- Save and run `sudo service cron restart`

## Install supervisor
- Run `sudo apt-get install supervisor`

### Set supervisor for queue
- Run `sudo nano /etc/supervisor/conf.d/laravel-worker.conf`
- Add this line 
```
    [program:laravel-worker]
    process_name=%(program_name)s_%(process_num)02d
    command=php /var/www/challomates/artisan queue:work --sleep=3 --tries=3 --max-time=3600
    autostart=true
    autorestart=true
    stopasgroup=true
    killasgroup=true
    numprocs=8
    redirect_stderr=true
    stopwaitsecs=3600
```
### Set supervisor for laravel echo
- Run `sudo nano /etc/supervisor/conf.d/laravel-echo.conf`
- Add this line 
```
    [program:laravel-echo]
    directory=/var/www/challomates
    process_name=%(program_name)s_%(process_num)02d
    command=laravel-echo-server start
    autostart=true
    autorestart=true
    user=ubuntu
    numprocs=1
    redirect_stderr=true
    stdout_logfile=/var/www/challomates/storage/logs/echo.log
```
### Set supervisor for laravel web socket
- Run `sudo nano /etc/supervisor/conf.d/laravel-echo.conf`
- Add this line 
```
    [program:websockets]
    process_name=%(program_name)s_%(process_num)02d
    command=php /var/www/challomates/artisan websockets:serve
    autostart=true
    autorestart=true
    numprocs=1
    user=ubuntu
    stderr_logfile=/var/log/supervisor-laravel.websocket.err.log
    stdout_logfile=/var/log/supervisor-laravel.websocket.out.log
    stderr_logfile_maxbytes=0

```


- Save and run this command one by one
```
sudo supervisorctl reread 
sudo supervisorctl update 
sudo supervisor restart
```


## Run Application
- run `php artisan serve`



## Run Application
### Enjoy!

