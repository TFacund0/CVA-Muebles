<?php

namespace App\Services;

/**
 * Class EmailService
 *
 * Servicio centralizado para el envío de correos transaccionales vía SMTP.
 * Encapsula la lógica de configuración y ensamble de plantillas HTML.
 */
class EmailService
{
    protected $email;

    public function __construct()
    {
        $this->email = \Config\Services::email();
    }

    /**
     * Enviar correo genérico usando una vista predefinida.
     *
     * @param string $to       Correo del destinatario.
     * @param string $subject  Asunto del correo.
     * @param string $viewPath Ruta de la vista HTML (ej. 'emails/bienvenida').
     * @param array  $viewData Datos para inyectar en la vista.
     * @param array  $cc       (Opcional) Array o string de correos para enviar con copia.
     * 
     * @return bool True si el correo se envió correctamente, false en caso contrario.
     */
    public function enviarCorreo($to, $subject, $viewPath, $viewData = [], $cc = null)
    {
        // Cargar y renderizar la plantilla HTML
        $htmlContent = view($viewPath, $viewData);

        $this->email->clear();
        
        $fromEmail = env('email.fromEmail', env('email.SMTPUser', ''));
        $fromName  = env('email.fromName', 'CVA Muebles');
        
        $this->email->setFrom($fromEmail, $fromName);
        $this->email->setTo($to);

        if (!empty($cc)) {
            $this->email->setCC($cc);
        }

        $this->email->setSubject($subject);
        $this->email->setMessage($htmlContent);

        // Envío
        if ($this->email->send()) {
            return true;
        } else {
            // Log de errores para diagnóstico
            log_message('error', '[EmailService] Fallo al enviar correo a ' . $to . '. Error: ' . $this->email->printDebugger(['headers']));
            return false;
        }
    }

    /**
     * Envía correo de bienvenida al nuevo usuario.
     */
    public function enviarBienvenida($to, $nombre)
    {
        return $this->enviarCorreo($to, '¡Bienvenido a CVA Muebles!', 'emails/bienvenida', ['nombre' => $nombre]);
    }

    /**
     * Envía confirmación de compra al usuario y copia al administrador.
     */
    public function enviarConfirmacionPedido($to, $nombre, $pedidoId, $total, $articulos)
    {
        // Si el administrador quiere recibir copia
        $ccAdmin = env('email.fromEmail', '');
        
        return $this->enviarCorreo(
            $to, 
            'Confirmación de Pedido #' . $pedidoId . ' - CVA Muebles', 
            'emails/pedido_confirmado', 
            [
                'nombre' => $nombre,
                'pedidoId' => $pedidoId,
                'total' => $total,
                'articulos' => $articulos
            ],
            $ccAdmin
        );
    }

    /**
     * Notifica al usuario cuando su pedido cambia de estado.
     */
    public function enviarActualizacionEstado($to, $nombre, $pedidoId, $nuevoEstado)
    {
        return $this->enviarCorreo(
            $to, 
            'Actualización de Pedido #' . $pedidoId . ' - CVA Muebles', 
            'emails/estado_actualizado', 
            [
                'nombre' => $nombre,
                'pedidoId' => $pedidoId,
                'estado' => $nuevoEstado
            ]
        );
    }
}
