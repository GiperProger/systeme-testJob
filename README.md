## Description

Test job for systeme company. Senior PHP developer position

## Installation

1. If you are working with Docker Desktop for Mac, ensure **you have enabled `VirtioFS`**.

2. Create the file `./.docker/.env.nginx.local` using `./.docker/.env.nginx` as template. The value of the variable `NGINX_BACKEND_DOMAIN` should be `localhost`.

3. Go inside folder `./docker` and run `make start` command.

4. Use the following value for the DATABASE_URL environment variable:

```
DATABASE_URL=mysql://systeme_user:systeme_pass@db:3306/systeme?serverVersion=8.0.33
```

9. Visit `http://localhost:8000/`
