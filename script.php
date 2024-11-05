<?php
// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$database = "mytrees";

// Configuración del correo
$admin_email = "proyectoka123@gmail.com";
$subject = "Recordatorio: Actualización de árboles desactualizados";

// Conectar a la base de datos
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Calcular la fecha límite (1 mes atrás)
$fecha_limite = date("Y-m-d H:i:s", strtotime("-1 month"));

// Consultar árboles desactualizados
$sql = "
    SELECT a.id, a.ubicacion_geografica 
    FROM arboles a
    LEFT JOIN actualizaciones act ON a.id = act.arbol_id
    WHERE (SELECT MAX(fecha_actualizacion) FROM actualizaciones WHERE arbol_id = a.id) < '$fecha_limite'
";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Generar la lista de árboles desactualizados
    $lista_arboles = "Los siguientes árboles no han sido actualizados desde hace 1 mes:\n";
    while ($row = $result->fetch_assoc()) {
        $lista_arboles .= "Arbol " . $row["id"] . "\n";
    }

    // Configuración del correo
    $headers = "From: angiehh1724@gmail.com\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Enviar el correo
    if (mail($admin_email, $subject, $lista_arboles, $headers)) {
        echo "Correo enviado al administrador.";
    } else {
        echo "Error al enviar el correo.";
    }
} else {
    echo "No hay árboles desactualizados.";
}

// Cerrar conexión
$conn->close();
?>
