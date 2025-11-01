<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Router.php';
require_once __DIR__ . '/../src/Controllers/UserController.php';

$router = new Router('/api-php-native1/public');
$database = Database::connection();
$userController = new UserController($database);

$router->add('GET', '/api/v1/users', [$userController, 'index']);
$router->add('GET', '/api/v1/users/{id}', [$userController, 'show']);
$router->add('GET', '/api-php-native1/public/api/v1/users', [$userController, 'index']);
$router->add('GET', '/api-php-native1/public/api/v1/users/{id}', [$userController, 'show']);

$router->run();
?>
