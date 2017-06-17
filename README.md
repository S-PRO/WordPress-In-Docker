# Wordpress in Docker

> The whole idea was to make starting new (or not new) WordPress project as easy as possible. **Wordpress in Docker** is a set of files to easily run WordPress in an isolated environment built with Docker containers. If you ever need something improved, added, or bugfixed, do not hesitate contacting me at [egolovan@s-pro.io](mailto:egolovan@s-pro.io).

## What is inside:

- WordPress:
[http://localhost:8000](http://localhost:8000)
[http://localhost:8000/wp-admin/](http://localhost:8000/wp-admin/)
Username: user
Password: password

- phpMyAdmin:
[http://localhost:8001](http://localhost:8001)

- MySQL Server:
Container has 3307 port bound to the host. Inside Docker network, containers connect to MySQL on standard 3306 port. Outside 3307 port was chosen to avoid conflicts if host has its own MySQL server running.

## Installation 

### Case 1: Setup new project
```docker-compose up``` starts everything you require for new project. Once up, you'll get ready WordPress website on [http://localhost:8000](http://localhost:8000) and phpMyAdmin running on [http://localhost:8001](http://localhost:8001). 
*Note: Once you login in WordPress admin I highly recommend you to update WordPress and all plugins to latest versions if any available.*

WordPress is the latest official 4.8 version extended with following:
- Included [Bones theme](https://github.com/eddiemachado/bones).
- Included some essential, but not activated, plugins (Advanced Contact Fields, Contact Form 7, WP Mail SMTP, WP Migrate DB). Please let me know if you need more.
- Included ```.gitignore``` file.
- Removed all default themes and plugins.

### Case 2: Setup existing project which was not under docker previously
Tricky part starts when you already have a project and want to start it and not default wordpress installation. To achieve this, do the following (the easiest way):
- Replace ```db_dump.sql``` file with your database dump file. ```USE `wordpress`;``` must exist in your file!!! Otherwise docker has no idea where to import this dump. If not there, please add this row at a very top of file.
- Replace all files in ```./wordpress/``` folder with your files.
- Open ```wp-config.php``` file in your text editor and replace following rows:

```php
/** The name of the database for WordPress */
define('DB_NAME', '**********');
/** MySQL database username */
define('DB_USER', '**********');
/** MySQL database password */
define('DB_PASSWORD', '**********');
/** MySQL hostname */
define('DB_HOST', '**********');
```

with these rows:

```php
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress');
/** MySQL database username */
define('DB_USER', 'user');
/** MySQL database password */
define('DB_PASSWORD', 'password');
/** MySQL hostname */
define('DB_HOST', 'wordpress_db:3306');
```
- Start your project with ```docker-compose up```.

### Case 2: Setup existing project which was under docker previously

- Just pull all files from git and run setup from **Case 1**.

## Useful commands

### Database Backup
```sh
docker exec wordpress_db /usr/bin/mysqldump -uuser -ppassword -B wordpress wordpress > db_dump.sql
```

### Database Restore
```sh
cat db_dump.sql | docker exec -i wordpress_db /usr/bin/mysql -uuser -ppassword wordpress
```

### Accessing containers shell
```sh
docker exec -it wordpress bash
docker exec -it wordpress_db bash
```

### Accessing MySQL server inside container from host
```sh
mysql -h0.0.0.0 -P3307 -uroot -proot
```

## Tech

**Wordpress in Docker** uses a number of open source projects to work properly:

- [Docker](https://www.docker.com/) Amazing tool which does the whole magic.
- [WordPress](https://www.wordpress.org/) Open source software you can use to create a beautiful website, blog, or app.
- [phpMyAdmin](https://www.phpmyadmin.net/) Although it's not essential here for things to work, I decided to include it as this is amazing free software tool, intended to handle the administration of MySQL over the Web.
- Official Docker Repositories [WordPress](https://hub.docker.com/_/wordpress/), [MySQL](https://hub.docker.com/_/mysql/), [phpMyAdmin](https://hub.docker.com/r/phpmyadmin/phpmyadmin/)

Licenses
----

Wordpress in Docker: MIT
WordPress: GPL 

@2017 [Eugene Golovan](mailto:egolovan@s-pro.io) at [S-PRO](http://s-pro.io)
