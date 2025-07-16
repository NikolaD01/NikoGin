<?php

namespace NikoGin\Services\Logic;

class MiddlewareLogicGenerator
{
    public static function generateBasicAuth(string $pluginPrefix): string
    {
        $upper = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $pluginPrefix));

        return <<<PHP
<?php

namespace {$pluginPrefix}\\Http\\Middlewares;

use {$pluginPrefix}\\Core\\Contracts\\MiddlewareInterface;
use WP_Error;
use WP_REST_Request;

final class BasicAuth implements MiddlewareInterface
{
    /**
     * Verifies Basic Auth credentials from config constants.
     *
     * @param WP_REST_Request \$request
     * @return bool|WP_Error
     */
    public static function verify(WP_REST_Request \$request): bool|WP_Error
    {
        \$authHeader = \$request->get_header('authorization');
        if (!\$authHeader || !preg_match('/Basic\s+(.*)$/i', \$authHeader, \$matches)) {
            return new WP_Error('unauthorized', 'Authorization header is missing or malformed.', ['status' => 401]);
        }

        \$decoded = base64_decode(\$matches[1]);
        if (!\$decoded || !str_contains(\$decoded, ':')) {
            return new WP_Error('unauthorized', 'Invalid authorization format.', ['status' => 401]);
        }

        [\$username, \$password] = explode(':', \$decoded, 2);

        // Build and verify that the constants actually exist
        \$userConst = '{$upper}_BASIC_AUTH_USER';
        \$passConst = '{$upper}_BASIC_AUTH_PASSWORD';

        if (! defined(\$userConst) || ! defined(\$passConst)) {
            return new WP_Error(
                'server_error',
                'Basic auth credentials are not configured.',
                ['status' => 500]
            );
        }

        \$validUser = constant(\$userConst);
        \$validPass = constant(\$passConst);

        if (\$username !== \$validUser || \$password !== \$validPass) {
            return new WP_Error('forbidden', 'Invalid credentials.', ['status' => 403]);
        }

        return true;
    }
}
PHP;
    }

    public static function generateBearerTokenAuth(string $pluginPrefix): string
    {

        $upper = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $pluginPrefix));

        return <<<PHP
<?php

namespace {$pluginPrefix}\Http\Middlewares;

use {$pluginPrefix}\Core\Contracts\MiddlewareInterface;
use WP_Error;
use WP_REST_Request;

final class BearerTokenAuth implements MiddlewareInterface
{

    /**
     * Validates the Bearer token from the Authorization header.
     *
     * @return bool|WP_Error  Returns true if valid, or WP_Error on failure.
     */
    public static function verify(WP_REST_Request \$request): bool|WP_Error
    {
        \$authHeader = \$request->get_header('authorization');

        if (!\$authHeader || !preg_match('/Bearer\s+(\S+)/', \$authHeader, \$matches)) {
            return new WP_Error(
                'unauthorized',
                'Authorization header is missing or malformed.',
                ['status' => 401]
            );
        }

        \$token = \$matches[1];
        \$expectedToken = '{$upper}_BEARER_TOKEN';

        if (! defined(\$expectedToken)) {
            return new WP_Error(
                'server_error',
                'Basic auth credentials are not configured.',
                ['status' => 500]
            );
        }
        


        if (\$token !== \$expectedToken) {
            return new WP_Error(
                'unauthorized',
                'Invalid token.',
                ['status' => 403]  // On Orion server with need to set everything 200 or 404, because server doesn't handle other status messages
            );
        }

        return true;
    }
} 
PHP;
    }
}