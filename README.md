# Slim Framework 3 Skeleton Application

Use this skeleton application to quickly setup and start working on a new Slim Framework 3 application. This application uses the latest Slim 3 with the PHP-View template renderer. It also uses the Monolog logger.

This skeleton application was built for Composer. This makes setting up a new Slim Framework application quick and easy.

## Install the Application

Run this command from the directory in which you want to install your new Slim Framework application.

    php composer.phar create-project slim/slim-skeleton [my-app-name]

Replace `[my-app-name]` with the desired directory name for your new application. You'll want to:

* Point your virtual host document root to your new application's `public/` directory.
* Ensure `logs/` is web writeable.

That's it! Now go build something cool.

---

# Sample Code for VD Coding Test

##### Default database schema included:
MySQL dump available at `/vd_test.sql`

##### Default db config file included:
`/src/dbconfig.php` configuration file is required but NOT under version control

Sample structure of `/src/dbconfig.php` is as follows:

    <?php
    return [
        'host'   => '127.0.0.1',
        'user'   => 'root',
        'pass'   => '',
        'schema' => 'vd_test'
    ];