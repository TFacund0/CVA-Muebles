<?php

if (!function_exists('money')) {
    /**
     * Formatea un monto con separador de miles '.' y decimales ',' (es-AR).
     * No incluye el símbolo '$': eso queda a criterio de cada vista.
     */
    function money(float|int|string|null $value, int $decimals = 0): string
    {
        return number_format((float) $value, $decimals, ',', '.');
    }
}
