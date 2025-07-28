<?php

namespace NikoGin\Services\Logic;

class SupportLogicGenerator
{

    public static function generateContainerLogic(string $pluginPrefix): string
    {
        return "<?php\n\nnamespace {$pluginPrefix}\\Core\\Support;\n\nuse Exception;\n\nclass Container\n{\n    private static array \$instances = [];\n\n    public static function bind(string \$key, callable \$resolver): void\n    {\n        self::\$instances[\$key] = \$resolver;\n    }\n\n    /**\n     * @throws Exception\n     */\n    public static function get(string \$key)\n    {\n        if (isset(self::\$instances[\$key])) {\n            return call_user_func(self::\$instances[\$key]);\n        }\n        throw new Exception(\"Service not found: {\$key}\");\n    }\n}";
    }

    public static function generateRouterLogic(string $pluginPrefix, string $pluginName): string
    {
        $constantBase = strtoupper(str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9 ]/', '', $pluginName)));
        $apiNamespace = strtolower($pluginPrefix) . '/v1';
        $constantNamespace = "{$constantBase}_NAMESPACE";

        return <<<PHP
<?php

namespace {$pluginPrefix}\\Core\\Support;

use {$pluginPrefix}\\Core\\Contracts\\MiddlewareInterface;
use {$pluginPrefix}\\Core\\Support\\Container;
use InvalidArgumentException;
use WP_REST_Server;

class Router
{
    /** @var string REST namespace (e.g. 'myplugin/v1') */
    private static string \$namespace = {$constantNamespace};

    /**
     * Register a REST route.
     */
    public static function add(string \$route, array \$args = []): void
    {
        register_rest_route(self::\$namespace, \$route, \$args);
    }

    /**
     * Register multiple routes under a common prefix.
     */
    public static function group(string \$prefix, callable \$routesRegistrar): void
    {
        \$orig = self::\$namespace;
        self::\$namespace = rtrim(self::\$namespace, '/') . \$prefix;
        \$routesRegistrar();
        self::\$namespace = \$orig;
    }

    /**
     * Register a route with a middleware/permission callback.
     */
    public static function middleware(string \$middlewareClass, string \$route, array \$args = []): void
    {
        \$middleware = Container::get(\$middlewareClass);
        if (! \$middleware instanceof MiddlewareInterface) {
            throw new InvalidArgumentException("\$middlewareClass must implement MiddlewareInterface");
        }
        \$args['permission_callback'] = [ \$middleware, 'verify' ];
        self::add(\$route, \$args);
    }

    /**
     * Register a full set of RESTful routes for a resource.
     *
     * @param string \$baseRoute       e.g. '/items'
     * @param string \$controllerClass Fully‑qualified controller class with methods:
     *                                  index, show, store, update, destroy
     */
    public static function resource(string \$baseRoute, string \$controllerClass): void
    {
        // index (READABLE)
        self::add(\$baseRoute, [
            'methods'  => WP_REST_Server::READABLE,
            'callback' => [\$controllerClass, 'index'],
        ]);

        // show (READABLE)
        self::add(\$baseRoute . '/(?P<id>\\d+)', [
            'methods'  => WP_REST_Server::READABLE,
            'callback' => [\$controllerClass, 'show'],
            'args'     => ['id' => ['validate_callback' => 'is_numeric']],
        ]);

        // store (CREATABLE)
        self::add(\$baseRoute, [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [\$controllerClass, 'store'],
            'permission_callback' => [Container::get(\$controllerClass), 'storePermission'] ?? '__return_true',
        ]);

        // update (EDITABLE)
        self::add(\$baseRoute . '/(?P<id>\\d+)', [
            'methods'             => WP_REST_Server::EDITABLE,
            'callback'            => [\$controllerClass, 'update'],
            'permission_callback' => [Container::get(\$controllerClass), 'updatePermission'] ?? '__return_true',
            'args'                => ['id' => ['validate_callback' => 'is_numeric']],
        ]);

        // destroy (DELETABLE)
        self::add(\$baseRoute . '/(?P<id>\\d+)', [
            'methods'             => WP_REST_Server::DELETABLE,
            'callback'            => [\$controllerClass, 'destroy'],
            'permission_callback' => [Container::get(\$controllerClass), 'destroyPermission'] ?? '__return_true',
            'args'                => ['id' => ['validate_callback' => 'is_numeric']],
        ]);
    }

    /**
     * Change the REST namespace (e.g. 'myplugin/v2').
     */
    public static function setNamespace(string \$namespace): void
    {
        self::\$namespace = \$namespace;
    }
}
PHP;
    }

    public static function generateHTTPLogic(string $pluginPrefix): string
    {
        return <<<PHP
<?php
namespace {$pluginPrefix}\\Core\\Support;

class HTTP
{
    /**
     * Send a GET request
     */
    public static function get(string \$url, array \$headers = []): array
    {
        return self::request('GET', \$url, [], \$headers);
    }

    /**
     * Send a POST request
     */
    public static function post(string \$url, array \$data = [], array \$headers = []): array
    {
        return self::request('POST', \$url, \$data, \$headers);
    }

    /**
     * Send a PUT request
     */
    public static function put(string \$url, array \$data = [], array \$headers = []): array
    {
        return self::request('PUT', \$url, \$data, \$headers);
    }

    /**
     * Send a DELETE request
     */
    public static function delete(string \$url, array \$headers = []): array
    {
        return self::request('DELETE', \$url, [], \$headers);
    }

    /**
     * Generic request handler
     */
    protected static function request(string \$method, string \$url, array \$data = [], array \$headers = []): array
    {
        \$args = [
            'method'  => strtoupper(\$method),
            'headers' => array_merge([
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ], \$headers),
        ];

        if (!empty(\$data)) {
            \$args['body'] = json_encode(\$data);
        }

        \$response = wp_remote_request(\$url, \$args);

        if (is_wp_error(\$response)) {
            return [
                'success' => false,
                'error'   => \$response->get_error_message(),
            ];
        }

        \$code = wp_remote_retrieve_response_code(\$response);
        \$body = wp_remote_retrieve_body(\$response);

        return [
            'success' => \$code >= 200 && \$code < 300,
            'code'    => \$code,
            'body'    => json_decode(\$body, true),
            'raw'     => \$body,
        ];
    }
}
PHP;

    }

}