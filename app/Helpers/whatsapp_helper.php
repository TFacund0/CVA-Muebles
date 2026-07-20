<?php

if (!function_exists('wa_link')) {
    /**
     * Arma un link de WhatsApp (wa.me) a partir de un teléfono y un mensaje.
     * Limpia el teléfono de caracteres no numéricos y, si no trae ya el
     * prefijo de país, se lo antepone (por defecto '54', Argentina).
     * Pasar $countryPrefix = null para no tocar el número (por si ya viene
     * garantizado en el formato correcto desde el origen de datos).
     */
    function wa_link(?string $phone, string $message = '', ?string $countryPrefix = '54'): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone ?? '');

        if ($countryPrefix !== null && $digits !== '' && !str_starts_with($digits, $countryPrefix)) {
            $digits = $countryPrefix . $digits;
        }

        $url = 'https://wa.me/' . $digits;

        return $message !== '' ? $url . '?text=' . urlencode($message) : $url;
    }
}
