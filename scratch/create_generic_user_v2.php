<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'arce_acevedo');
$check = $conn->query("SELECT id_usuario FROM usuarios WHERE usuario = 'cliente_whatsapp'");
if ($check->num_rows == 0) {
    // Note: 'pass' instead of 'password', and 'imagen' must be provided as it is NOT NULL
    $conn->query("INSERT INTO usuarios (nombre, apellido, email, usuario, pass, imagen, perfil_id, baja) 
                  VALUES ('Cliente', 'Externo', 'whatsapp@cva.com', 'cliente_whatsapp', 'manual_order_only', '', 2, 'NO')");
    echo "Generic WhatsApp user created successfully.";
} else {
    echo "Generic WhatsApp user already exists.";
}
$conn->close();
?>
