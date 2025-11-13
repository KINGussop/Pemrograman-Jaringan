<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Router.php';
require_once __DIR__ . '/../src/Controllers/UserController.php';

// Buat koneksi database
$database = new Database();
$db = $database->connect();

// Inisialisasi controller
$userController = new UserController($db);

// Buat router instance (tanpa base path panjang)
$router = new Router();

// Daftarkan route CRUD
$router->add('GET', '/api/v1/users', [$userController, 'getAllUsers']);  // Read all
$router->add('GET', '/api/v1/users/{id}', [$userController, 'getUserById']); // Read by ID
$router->add('POST', '/api/v1/users', [$userController, 'createUser']); // Create
$router->add('PUT', '/api/v1/users/{id}', [$userController, 'updateUser']); // Update
$router->add('DELETE', '/api/v1/users/{id}', [$userController, 'deleteUser']); // Delete

// Jalankan router
$router->run();