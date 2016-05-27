<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    $view = new Slim\Views\PhpRenderer($settings['template_path']);
    return $view;
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));
    return $logger;
};

// db config
$container['pdo'] = function ($c) {
    $config = $c->get('settings')['dbconfig'];
    if (!isset($config)) {
        $pdo = new PDO(
            'mysql:dbname=vd_test;host=127.0.0.1',
            'root',
            ''
        );
    } else {
        $pdo = new PDO(
            'mysql:dbname=' . $config['schema'] . ';host=' . $config['host'],
            $config['user'],
            $config['pass']
        );
    }

    try {
        return $pdo;

    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    }

};

// Default Action
$container['App\Controller\DefaultAction'] = function ($c) {
    return new App\Controller\DefaultAction($c->get('renderer'), $c->get('logger'), $c->get('pdo'));
};