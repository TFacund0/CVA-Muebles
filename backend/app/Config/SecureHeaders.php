<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class SecureHeaders extends BaseConfig
{
    /**
     * X-Frame-Options
     */
    public string $XFrameOptions = 'DENY';

    /**
     * X-Content-Type-Options
     */
    public string $XContentTypeOptions = 'nosniff';

    /**
     * X-Permitted-Cross-Domain-Policies
     */
    public string $XPermittedCrossDomainPolicies = 'none';

    /**
     * Referrer-Policy
     */
    public string $referrerPolicy = 'no-referrer-when-downgrade';

    /**
     * Content-Security-Policy
     */
    public bool $CSPEnabled = false; // Lo mantenemos desactivado por ahora para no romper scripts inline, pero activamos las otras cabeceras.
}
