<?php
class Router {
    private array $routes = [];
    private string $basePath;

    public function __construct(?string $basePath = null) {
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $detectedBasePath = rtrim(dirname($scriptName), '/');

        if ($basePath === null) {
            $basePath = $detectedBasePath;
        }

        if ($basePath === null || $basePath === '' || $basePath === '/' || $basePath === '.') {
            $this->basePath = '';
        } else {
            $this->basePath = '/' . trim($basePath, '/');
        }
    }

    public function add(string $method, string $route, callable $callback): void {
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $this->compileRoute($route),
            'callback' => $callback
        ];
    }

    public function run(): void {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $uri = $this->normalizePath($uri);

        if ($this->basePath !== '' && strpos($uri, $this->basePath) === 0) {
            $uri = substr($uri, strlen($this->basePath)) ?: '/';
        }

        if (strpos($uri, '/index.php') === 0) {
            $uri = substr($uri, strlen('/index.php')) ?: '/';
        }

        $uri = $this->normalizePath($uri);
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $uri, $matches)) {
                array_shift($matches);
                call_user_func_array($route['callback'], $matches);
                return;
            }
        }

        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Route not found', 'debug' => [$method, $uri]]);
    }

    private function compileRoute(string $route): string {
        $normalized = $this->normalizePath($route);
        $pattern = preg_replace('#\{[^/]+\}#', '([^/]+)', $normalized);
        return '#^' . $pattern . '/?$#';
    }

    private function normalizePath(string $path): string {
        $normalized = '/' . ltrim($path, '/');
        return $normalized === '/' ? '/' : rtrim($normalized, '/');
    }
}
?>