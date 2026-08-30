<?php

namespace Config;

use CodeIgniter\Database\Config;

/**
 * Database Configuration
 */
class Database extends Config
{
    /**
     * The directory that holds the Migrations and Seeds directories.
     */
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;

    /**
     * Lets you choose which connection group to use if no other is specified.
     */
    public string $defaultGroup = 'default';

    /**
     * The default database connection.
     *
     * @var array<string, mixed>
     */
    public array $default = [
        'DSN'          => '',
        'hostname'     => '',
        'username'     => '',
        'password'     => '',
        'database'     => '',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => (ENVIRONMENT != 'production'),
        'charset'      => 'utf8mb4',
        'DBCollat'     => 'utf8mb4_general_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => 3306,
        'numberNative' => false,
        'foundRows'    => false,
        'dateFormat'   => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    //    /**
    //     * Sample database connection for SQLite3.
    //     *
    //     * @var array<string, mixed>
    //     */
    //    public array $default = [
    //        'database'    => 'database.db',
    //        'DBDriver'    => 'SQLite3',
    //        'DBPrefix'    => '',
    //        'DBDebug'     => true,
    //        'swapPre'     => '',
    //        'failover'    => [],
    //        'foreignKeys' => true,
    //        'busyTimeout' => 1000,
    //        'synchronous' => null,
    //        'dateFormat'  => [
    //            'date'     => 'Y-m-d',
    //            'datetime' => 'Y-m-d H:i:s',
    //            'time'     => 'H:i:s',
    //        ],
    //    ];

    //    /**
    //     * Sample database connection for Postgre.
    //     *
    //     * @var array<string, mixed>
    //     */
    //    public array $default = [
    //        'DSN'        => '',
    //        'hostname'   => 'localhost',
    //        'username'   => 'root',
    //        'password'   => 'root',
    //        'database'   => 'ci4',
    //        'schema'     => 'public',
    //        'DBDriver'   => 'Postgre',
    //        'DBPrefix'   => '',
    //        'pConnect'   => false,
    //        'DBDebug'    => true,
    //        'charset'    => 'utf8',
    //        'swapPre'    => '',
    //        'failover'   => [],
    //        'port'       => 5432,
    //        'dateFormat' => [
    //            'date'     => 'Y-m-d',
    //            'datetime' => 'Y-m-d H:i:s',
    //            'time'     => 'H:i:s',
    //        ],
    //    ];

    //    /**
    //     * Sample database connection for SQLSRV.
    //     *
    //     * @var array<string, mixed>
    //     */
    //    public array $default = [
    //        'DSN'        => '',
    //        'hostname'   => 'localhost',
    //        'username'   => 'root',
    //        'password'   => 'root',
    //        'database'   => 'ci4',
    //        'schema'     => 'dbo',
    //        'DBDriver'   => 'SQLSRV',
    //        'DBPrefix'   => '',
    //        'pConnect'   => false,
    //        'DBDebug'    => true,
    //        'charset'    => 'utf8',
    //        'swapPre'    => '',
    //        'encrypt'    => false,
    //        'failover'   => [],
    //        'port'       => 1433,
    //        'dateFormat' => [
    //            'date'     => 'Y-m-d',
    //            'datetime' => 'Y-m-d H:i:s',
    //            'time'     => 'H:i:s',
    //        ],
    //    ];

    //    /**
    //     * Sample database connection for OCI8.
    //     *
    //     * You may need the following environment variables:
    //     *   NLS_LANG                = 'AMERICAN_AMERICA.UTF8'
    //     *   NLS_DATE_FORMAT         = 'YYYY-MM-DD HH24:MI:SS'
    //     *   NLS_TIMESTAMP_FORMAT    = 'YYYY-MM-DD HH24:MI:SS'
    //     *   NLS_TIMESTAMP_TZ_FORMAT = 'YYYY-MM-DD HH24:MI:SS'
    //     *
    //     * @var array<string, mixed>
    //     */
    //    public array $default = [
    //        'DSN'        => 'localhost:1521/XEPDB1',
    //        'username'   => 'root',
    //        'password'   => 'root',
    //        'DBDriver'   => 'OCI8',
    //        'DBPrefix'   => '',
    //        'pConnect'   => false,
    //        'DBDebug'    => true,
    //        'charset'    => 'AL32UTF8',
    //        'swapPre'    => '',
    //        'failover'   => [],
    //        'dateFormat' => [
    //            'date'     => 'Y-m-d',
    //            'datetime' => 'Y-m-d H:i:s',
    //            'time'     => 'H:i:s',
    //        ],
    //    ];

    /**
     * This database connection is used when running PHPUnit database tests.
     *
     * @var array<string, mixed>
     */
    public array $tests = [
        'DSN'         => '',
        'hostname'    => '127.0.0.1',
        'username'    => 'root',
        'password'    => '',
        'database'    => 'cva_muebles_test',
        'DBDriver'    => 'MySQLi',
        'DBPrefix'    => '',
        'pConnect'    => false,
        'DBDebug'     => (ENVIRONMENT != 'production'),
        'charset'     => 'utf8mb4',
        'DBCollat'    => 'utf8mb4_general_ci',
        'swapPre'     => '',
        'encrypt'     => false,
        'compress'    => false,
        'strictOn'    => false,
        'failover'    => [],
        'port'        => 3306,
        'foreignKeys' => true,
        'busyTimeout' => 1000,
        'dateFormat'  => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    public function __construct()
    {
        parent::__construct();

        $this->applyConnectionUrl();
        $this->applyDiscreteEnvVars();

        $this->default['DBDriver'] = 'MySQLi';
        $this->default['DSN']      = '';

        if (ENVIRONMENT === 'testing') {
            $this->defaultGroup = 'tests';

            return;
        }

        $this->reportIncompleteConfig();
    }

    /**
     * Applies a full connection URL such as mysql://user:pass@host:3306/database.
     */
    private function applyConnectionUrl(): void
    {
        $url = $this->readEnv(['DATABASE_URL', 'MYSQL_ADDON_URI', 'MYSQL_URL', 'CLEVER_DATABASE_URL']);

        if ($url === null) {
            return;
        }

        $parts = parse_url($url);

        if ($parts === false || ! isset($parts['host'])) {
            return;
        }

        $this->default['hostname'] = $parts['host'];
        $this->default['port']     = (int) ($parts['port'] ?? 3306);

        if (isset($parts['user'])) {
            $this->default['username'] = rawurldecode($parts['user']);
        }
        if (isset($parts['pass'])) {
            $this->default['password'] = rawurldecode($parts['pass']);
        }
        if (isset($parts['path'])) {
            $this->default['database'] = ltrim($parts['path'], '/');
        }
    }

    /**
     * Applies individual environment variables, overriding any connection URL.
     */
    private function applyDiscreteEnvVars(): void
    {
        $map = [
            'hostname' => ['database.default.hostname', 'database_default_hostname', 'DATABASE_DEFAULT_HOSTNAME', 'MYSQL_ADDON_HOST'],
            'database' => ['database.default.database', 'database_default_database', 'DATABASE_DEFAULT_DATABASE', 'MYSQL_ADDON_DB'],
            'username' => ['database.default.username', 'database_default_username', 'DATABASE_DEFAULT_USERNAME', 'MYSQL_ADDON_USER'],
            'password' => ['database.default.password', 'database_default_password', 'DATABASE_DEFAULT_PASSWORD', 'MYSQL_ADDON_PASSWORD'],
            'port'     => ['database.default.port', 'database_default_port', 'DATABASE_DEFAULT_PORT', 'MYSQL_ADDON_PORT'],
        ];

        foreach ($map as $key => $names) {
            $value = $this->readEnv($names);

            if ($value !== null) {
                $this->default[$key] = $key === 'port' ? (int) $value : $value;
            }
        }
    }

    /**
     * Returns the first non-empty value among the given environment variable names.
     */
    private function readEnv(array $names): ?string
    {
        foreach ($names as $name) {
            $value = $_ENV[$name] ?? $_SERVER[$name] ?? getenv($name);

            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        return null;
    }

    /**
     * Logs which connection settings are missing, listing the environment variable
     * names visible to PHP so a misnamed variable can be spotted. Names only —
     * values are never logged.
     */
    private function reportIncompleteConfig(): void
    {
        $missing = [];

        foreach (['hostname', 'database', 'username'] as $key) {
            if ($this->default[$key] === '') {
                $missing[] = $key;
            }
        }

        if ($missing === []) {
            return;
        }

        $env     = getenv();
        $names   = array_unique(array_merge(
            array_keys(is_array($env) ? $env : []),
            array_keys($_ENV),
            array_keys($_SERVER),
        ));
        $related = array_values(array_filter(
            $names,
            static fn ($name) => preg_match('/database|mysql|cloudinary|encryption|baseurl|CI_ENV/i', (string) $name) === 1,
        ));

        sort($related);

        error_log(sprintf(
            '[DB CONFIG] Faltan: %s. Variables de entorno visibles relacionadas (solo nombres): %s',
            implode(', ', $missing),
            $related === [] ? '(ninguna)' : implode(' | ', $related),
        ));
    }
}
