<?php
class Router {
    private $routes = [];

    public function add($method, $path, $handler) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function run() {
        // Ambil URL yang diminta
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        // Hilangkan base path dengan lebih robust
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        if ($basePath !== '/' && strpos($requestUri, $basePath) === 0) {
            $requestUri = substr($requestUri, strlen($basePath));
        }
        
        // Pastikan path dimulai dengan /
        $requestUri = '/' . ltrim($requestUri, '/');

        // Loop semua route yang terdaftar
        foreach ($this->routes as $route) {
            // Ganti parameter dinamis {id} menjadi regex
            $pattern = "@^" . preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_]+)', $route['path']) . "$@";

            // Cek apakah cocok method dan URI-nya
            if ($requestMethod === $route['method'] && preg_match($pattern, $requestUri, $matches)) {
                array_shift($matches); // hapus match pertama (path penuh)
                return call_user_func_array($route['handler'], $matches);
            }
        }

        // Jika tidak ada route yang cocok
        http_response_code(404);
        echo json_encode(["message" => "Endpoint not found"]);
    }
}

