<?php

if (!function_exists('run_async_command')) {
    /**
     * Executes a CLI command in the background without waiting for its completion.
     * Compatible with Windows and Linux.
     *
     * @param string $command The CodeIgniter Spark command string (e.g., 'image:process src dest 800')
     * @return void
     */
    function run_async_command(string $command)
    {
        // Ruta absoluta al binario PHP y al script spark
        $php = PHP_BINARY;
        $spark = ROOTPATH . 'spark';
        
        // Determinar el OS
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Comando asíncrono para Windows
            $cmd = sprintf('start /B "" "%s" "%s" %s 2>nul >nul', $php, $spark, $command);
            pclose(popen($cmd, "r"));
        } else {
            // Comando asíncrono para Linux/Unix
            $cmd = sprintf('"%s" "%s" %s > /dev/null 2>&1 &', $php, $spark, $command);
            exec($cmd);
        }
    }
}
