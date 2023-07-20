# 🐳 Docker + PHP 8.2 + MySQL + Nginx + Symfony 6.2 Boilerplate

## Description

Test job for systeme company. Senior PHP developer position

## Installation

2. If you are working with Docker Desktop for Mac, ensure **you have enabled `VirtioFS`**.

3. Create the file `./.docker/.env.nginx.local` using `./.docker/.env.nginx` as template. The value of the variable `NGINX_BACKEND_DOMAIN` should be `localhost`.

4. Go inside folder `./docker` and run `docker compose up -d` to start containers.


6. Inside the `php` container, run `composer install` to install dependencies from `/var/www/symfony` folder.
   Run `docker exec  systeme_test_job-php-1 composer install`

7. Use the following value for the DATABASE_URL environment variable:

```
DATABASE_URL=mysql://systeme_user:systeme_pass@db:3306/systeme?serverVersion=8.0.33
```
