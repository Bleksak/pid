Requirements
------------

PHP 8.3
MySQL 8.0.29

Installation
------------

Ensure the `temp/` and `log/` directories are writable.
run `composer install`
Create the config/config.local.neon file, with the database credentials:

```yaml
database:
	dsn: 'mysql:host=127.0.0.1;dbname=pid'
	user: user
	password: password
```

Web Server Setup
----------------

To quickly dive in, use PHP's built-in server:

	php -S localhost:8000 -t www

Then, open `http://localhost:8000` in your browser to view the welcome page.

