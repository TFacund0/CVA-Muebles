<?php

if (!function_exists('fecha_local')) {
    /**
     * Formatea una fecha (string de BDD) al formato pedido.
     * Envoltorio de date(format, strtotime($valor)) para no repetir
     * el strtotime() en cada vista.
     */
    function fecha_local(?string $valor, string $formato = 'd/m/Y'): string
    {
        if (empty($valor)) {
            return '';
        }

        return date($formato, strtotime($valor));
    }
}
