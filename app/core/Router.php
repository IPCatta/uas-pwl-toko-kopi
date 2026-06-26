<?php
/**
 * Router — kedai-kopi
 *
 * Memetakan HTTP method + URI path ke Controller@method.
 * Mendukung:
 *   - GET dan POST
 *   - URL dinamis dengan parameter, misal /produk/{id}
 *   - Handler format 'Controller@method' (termasuk subfolder 'api/')
 *   - Halaman 404 bila rute tidak ditemukan
 */
class Router
{
    /**
     * Daftar rute terdaftar.
     * Format: $routes['GET']['/path'] = 'Controller@method'
     *         $routes['POST']['/path'] = 'Controller@method'
     *
     * @var array
     */
    private array $routes = [
        'GET'  => [],
        'POST' => [],
    ];

    /**
     * Daftarkan rute GET.
     *
     * @param string $path    URI path (misal '/produk/{id}')
     * @param string $handler Format 'Controller@method'
     * @return void
     */
    public function get(string $path, string $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    /**
     * Daftarkan rute POST.
     *
     * @param string $path    URI path
     * @param string $handler Format 'Controller@method'
     * @return void
     */
    public function post(string $path, string $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    /**
     * Dispatch — cocokkan request saat ini dengan rute terdaftar,
     * lalu panggil controller+method yang sesuai.
     *
     * @return void
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD']; // GET atau POST
        $uri    = $this->getUri();

        // Cari rute yang cocok
        $matched = $this->matchRoute($method, $uri);

        if ($matched === null) {
            $this->notFound();
            return;
        }

        [$handler, $params] = $matched;

        // Parse handler: 'Controller@method' atau 'api/Controller@method'
        $this->callHandler($handler, $params);
    }

    /**
     * Ambil path URI bersih dari request saat ini.
     * Menghapus BASE_URL prefix, query string, dan trailing slash.
     *
     * @return string  Path bersih, misal '/produk/5' atau '/'
     */
    private function getUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'];

        // Hapus query string (?key=val)
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }

        // Hapus prefix base path (misal /kedai-kopi/public)
        $basePath = parse_url(BASE_URL, PHP_URL_PATH);
        if ($basePath && $basePath !== '/') {
            // Hapus basePath dari awal URI
            if (strpos($uri, $basePath) === 0) {
                $uri = substr($uri, strlen($basePath));
            }
        }

        // Pastikan selalu dimulai dengan /
        if ($uri === '' || $uri === false) {
            $uri = '/';
        }

        // Hapus trailing slash (kecuali root /)
        if ($uri !== '/') {
            $uri = rtrim($uri, '/');
        }

        return $uri;
    }

    /**
     * Cocokkan URI dengan daftar rute yang terdaftar untuk method tertentu.
     * Mendukung parameter dinamis seperti {id}, {slug}, dll.
     *
     * @param string $method  HTTP method (GET/POST)
     * @param string $uri     URI path bersih
     * @return array|null     [handler, params] atau null jika tidak cocok
     */
    private function matchRoute(string $method, string $uri): ?array
    {
        if (!isset($this->routes[$method])) {
            return null;
        }

        foreach ($this->routes[$method] as $routePath => $handler) {
            $params = $this->matchPath($routePath, $uri);

            if ($params !== false) {
                return [$handler, $params];
            }
        }

        return null;
    }

    /**
     * Cocokkan satu pola rute dengan URI aktual.
     *
     * Pola rute bisa mengandung placeholder {name} yang akan dicocokkan
     * dengan segmen URI dan dikembalikan sebagai parameter.
     *
     * Contoh:
     *   matchPath('/produk/{id}', '/produk/5') → ['id' => '5']
     *   matchPath('/admin/kategori/{id}/update', '/admin/kategori/3/update') → ['id' => '3']
     *   matchPath('/', '/') → []
     *   matchPath('/login', '/register') → false
     *
     * @param string $routePath  Pola rute terdaftar
     * @param string $uri        URI aktual
     * @return array|false       Array parameter atau false jika tidak cocok
     */
    private function matchPath(string $routePath, string $uri): array|false
    {
        // Rute statis (tanpa placeholder) — exact match
        if (!str_contains($routePath, '{')) {
            return ($routePath === $uri) ? [] : false;
        }

        // Pecah jadi segmen
        $routeParts = explode('/', trim($routePath, '/'));
        $uriParts   = explode('/', trim($uri, '/'));

        // Jumlah segmen harus sama
        if (count($routeParts) !== count($uriParts)) {
            return false;
        }

        $params = [];

        foreach ($routeParts as $i => $routePart) {
            // Cek apakah segmen ini adalah placeholder {name}
            if (preg_match('/^\{(\w+)\}$/', $routePart, $m)) {
                // Placeholder — tangkap nilainya
                $params[$m[1]] = $uriParts[$i];
            } else {
                // Segmen statis — harus sama persis
                if ($routePart !== $uriParts[$i]) {
                    return false;
                }
            }
        }

        return $params;
    }

    /**
     * Panggil handler Controller@method.
     * Mendukung subfolder (misal 'api/WilayahApiController@kota').
     *
     * @param string $handler Format 'Controller@method' atau 'folder/Controller@method'
     * @param array  $params  Parameter dari URL dinamis
     * @return void
     */
    private function callHandler(string $handler, array $params): void
    {
        // Pisahkan handler menjadi controller dan method
        [$controllerPath, $method] = explode('@', $handler);

        // Tentukan subfolder dan nama class
        $subFolder = '';
        $className = $controllerPath;

        if (str_contains($controllerPath, '/')) {
            $parts     = explode('/', $controllerPath);
            $className = array_pop($parts);
            $subFolder = implode('/', $parts) . '/';
        }

        // Path file controller
        $controllerFile = APP_PATH . '/controllers/' . $subFolder . $className . '.php';

        if (!file_exists($controllerFile)) {
            $this->notFound();
            return;
        }

        require_once $controllerFile;

        if (!class_exists($className)) {
            $this->notFound();
            return;
        }

        $controller = new $className();

        if (!method_exists($controller, $method)) {
            $this->notFound();
            return;
        }

        // Panggil method dengan parameter URL sebagai argumen
        call_user_func_array([$controller, $method], $params);
    }

    /**
     * Tampilkan halaman 404.
     *
     * @return void
     */
    private function notFound(): void
    {
        http_response_code(404);

        $errorPage = APP_PATH . '/views/errors/404.php';

        if (file_exists($errorPage)) {
            require $errorPage;
        } else {
            echo '<h1>404 — Halaman Tidak Ditemukan</h1>';
            echo '<p>Maaf, halaman yang Anda cari tidak ada.</p>';
        }
    }
}
