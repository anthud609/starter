<?php
namespace App\Core\Error\Handler;

class ShutdownHandler
{
    public function register(): void
    {
        register_shutdown_function([$this, 'handleShutdown']);
    }

    public function handleShutdown(): void
    {
        $err = error_get_last();
        if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
            // convert to ErrorException or log directly
            throw new \ErrorException(
                $err['message'],
                0,
                $err['type'],
                $err['file'],
                $err['line']
            );
        }
    }
}
