<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Jwt extends BaseConfig
{
    public string $secret;
    public int $accessTtl;
    public int $refreshTtl;

    public function __construct()
    {
        parent::__construct();

        $this->secret     = (string) env('JWT_SECRET', '');
        $this->accessTtl  = (int) env('JWT_ACCESS_TTL', 3600);
        $this->refreshTtl = (int) env('JWT_REFRESH_TTL', 604800);
    }
}
