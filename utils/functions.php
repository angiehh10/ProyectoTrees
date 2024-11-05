<?php
function getConnection() {
    $connection = mysqli_connect('localhost', 'root', '', 'mytrees'); 

    if (!$connection) {
        die('Error de conexión: ' . mysqli_connect_error());
    }

    return $connection;
}

function loginUser($email, $contrasena) {
    $conn = getConnection();
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        // Comparar el hash de la contraseña usando SHA2
        if (hash('sha256', $contrasena) === $user['contrasena']) {
            // Iniciar sesión y guardar datos en $_SESSION
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['rol'] = $user['rol'];
            $_SESSION['estado'] = $user['estado'];

            // Verificar el rol y estado para determinar la redirección
            if ($user['estado'] === 'Activo') {
                if ($user['rol'] === 'Administrador') {
                    // Llamada a la función de verificación y envío de correo
                    verificarYEnviarCorreoSiAdmin(true);
                    return ['status' => 'success', 'redirect' => 'admin.php'];
                } elseif ($user['rol'] === 'Amigo') {
                    return ['status' => 'success', 'redirect' => 'amigo.php'];
                }
            } else {
                return ['status' => 'error', 'message' => 'Acceso denegado o cuenta inactiva.'];
            }
        } else {
            return ['status' => 'error', 'message' => 'Contraseña incorrecta.'];
        }
    } else {
        return ['status' => 'error', 'message' => 'Usuario no encontrado.'];
    }

    // Cerrar la declaración y la conexión
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}


function registerUser($nombre_usuario, $email, $contrasena, $rol, $estado = 'Activo', $nombre = null, $apellidos = null, $telefono = null, $direccion = null, $pais = null) {
    $conn = getConnection();

    // Verificar si el correo electrónico ya existe
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        return "El correo electrónico ya está registrado.";
    }

    // Insertar el nuevo usuario con la contraseña hasheada y los datos adicionales
    $contrasena_hashed = hash('sha256', $contrasena);
    $sql = "INSERT INTO usuarios (nombre_usuario, email, contrasena, rol, estado, nombre, apellidos, telefono, direccion, pais) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssssss", $nombre_usuario, $email, $contrasena_hashed, $rol, $estado, $nombre, $apellidos, $telefono, $direccion, $pais);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return true; // Registro exitoso
    } else {
        return "Error al registrar el usuario.";
    }
}

function countAmigos() {
    $conn = getConnection();
    $sql = "SELECT COUNT(*) AS total FROM usuarios WHERE rol = 'Amigo'";
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);
    mysqli_close($conn);
    return $data['total'];
}

function countArbolesDisponibles() {
    $conn = getConnection();
    $sql = "SELECT COUNT(*) AS total FROM arboles WHERE estado = 'Disponible'";
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);
    mysqli_close($conn);
    return $data['total'];
}

function countArbolesVendidos() {
    $conn = getConnection();
    $sql = "SELECT COUNT(*) AS total FROM arboles WHERE estado = 'Vendido'";
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);
    mysqli_close($conn);
    return $data['total'];
}

function crearEspecie($nombre_comercial, $nombre_cientifico) {
    $conn = getConnection();
    $sql = "INSERT INTO especies (nombre_comercial, nombre_cientifico) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $nombre_comercial, $nombre_cientifico);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $result;
}

function obtenerEspecies() {
    $conn = getConnection();
    $sql = "SELECT * FROM especies";
    $result = mysqli_query($conn, $sql);
    $especies = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_close($conn);
    return $especies;
}

function actualizarEspecie($id, $nombre_comercial, $nombre_cientifico) {
    $conn = getConnection();
    $sql = "UPDATE especies SET nombre_comercial = ?, nombre_cientifico = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $nombre_comercial, $nombre_cientifico, $id);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $result;
}

function eliminarEspecie($id) {
    $conn = getConnection();
    $sql = "DELETE FROM especies WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $result;
}

function crearArbol($especie_id, $ubicacion_geografica, $estado, $precio, $tamano, $foto_url = null, $foto_upload = null) {
    $conn = getConnection();
    
    // Procesar la imagen
    $foto_path = null;
    if ($foto_upload && $foto_upload['error'] === UPLOAD_ERR_OK) {
        $foto_path = 'uploads/' . basename($foto_upload['name']);
        move_uploaded_file($foto_upload['tmp_name'], $foto_path);
    } elseif ($foto_url) {
        $foto_path = $foto_url;
    }
    
    $sql = "INSERT INTO arboles (especie_id, ubicacion_geografica, estado, precio, tamano, foto) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "issdss", $especie_id, $ubicacion_geografica, $estado, $precio, $tamano, $foto_path);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $result;
}

function obtenerArbolesPorAmigo($amigo_id) {
    $conn = getConnection();
    $sql = "SELECT a.id, a.tamano, a.ubicacion_geografica, a.estado, a.precio, a.foto, e.nombre_comercial AS especie, a.especie_id
            FROM arboles a
            JOIN amigo_arbol aa ON a.id = aa.arbol_id
            JOIN especies e ON a.especie_id = e.id
            WHERE aa.amigo_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $amigo_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $arboles = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $arboles;
}

function registrarActualizacion($arbol_id, $tamano, $estado, $fecha_actualizacion) {
    $conn = getConnection();
    $sql = "INSERT INTO actualizaciones (arbol_id, tamano, estado, fecha_actualizacion) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "isss", $arbol_id, $tamano, $estado, $fecha_actualizacion);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $result;
}


function obtenerAmigos() {
    $conn = getConnection();
    $sql = "SELECT * FROM usuarios WHERE rol = 'Amigo'";
    $result = mysqli_query($conn, $sql);
    $amigos = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_close($conn);
    return $amigos;
}

function obtenerArbolesDisponibles() {
    $conn = getConnection();
    $sql = "SELECT a.id, e.nombre_comercial AS especie, a.ubicacion_geografica, a.precio 
            FROM arboles a 
            JOIN especies e ON a.especie_id = e.id 
            WHERE a.estado = 'Disponible'";
    
    $result = mysqli_query($conn, $sql);
    $arboles = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_close($conn);
    return $arboles;
}

function obtenerArbolPorId($arbol_id) {
    $conn = getConnection();
    $sql = "SELECT a.*, e.nombre_comercial 
            FROM arboles a 
            JOIN especies e ON a.especie_id = e.id 
            WHERE a.id = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $arbol_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $arbol = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $arbol;
}

function obtenerArboles() {
    $conn = getConnection();
    $sql = "SELECT id, especie_id, ubicacion_geografica, estado, tamano FROM arboles";
    $result = mysqli_query($conn, $sql);
    $arboles = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_close($conn);
    return $arboles;
}


function registrarCompra($usuario_id, $arbol_id) {
    $conn = getConnection();
    
    // Inserta el registro de compra en la tabla de relación amigo-arbol
    $sql = "INSERT INTO amigo_arbol (amigo_id, arbol_id) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $usuario_id, $arbol_id);
    
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    return $result; // Retorna verdadero si se registró correctamente
}

function actualizarEstadoArbol($arbol_id, $nuevo_estado) {
    $conn = getConnection();
    
    // Actualiza el estado del árbol en la base de datos
    $sql = "UPDATE arboles SET estado = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $nuevo_estado, $arbol_id);
    
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    return $result; // Retorna verdadero si se actualizó correctamente
}

function perteneceAlUsuario($usuario_id, $arbol_id) {
    $conn = getConnection();
    $sql = "SELECT * FROM arboles a 
            JOIN amigo_arbol aa ON a.id = aa.arbol_id 
            WHERE aa.amigo_id = ? AND a.id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $usuario_id, $arbol_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $existe = mysqli_num_rows($result) > 0;
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $existe;
}

function obtenerNombreAmigo($amigo_id) {
    $conn = getConnection();
    $sql = "SELECT nombre FROM usuarios WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $amigo_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $nombre = null;

    if ($row = mysqli_fetch_assoc($result)) {
        $nombre = $row['nombre'];
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $nombre;
}

function actualizarArbol($arbol_id, $especie_id, $ubicacion_geografica, $estado, $tamano, $foto_url = null, $foto_upload = null) {
    $conn = getConnection();
    
    // Procesar la imagen
    $foto_path = null;
    if ($foto_upload && $foto_upload['error'] === UPLOAD_ERR_OK) {
        $foto_path = 'uploads/' . basename($foto_upload['name']);
        move_uploaded_file($foto_upload['tmp_name'], $foto_path);
    } elseif ($foto_url) {
        $foto_path = $foto_url;
    }

    // Si no se carga una nueva imagen, mantener la foto existente en la base de datos
    if ($foto_path) {
        $sql = "UPDATE arboles SET especie_id = ?, ubicacion_geografica = ?, estado = ?, tamano = ?, foto = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "issssi", $especie_id, $ubicacion_geografica, $estado, $tamano, $foto_path, $arbol_id);
    } else {
        $sql = "UPDATE arboles SET especie_id = ?, ubicacion_geografica = ?, estado = ?, tamano = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isssi", $especie_id, $ubicacion_geografica, $estado, $tamano, $arbol_id);
    }

    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $result;
}

function verificarYEnviarCorreoSiAdmin($esAdmin) {
    if (!$esAdmin) {
        return; // Salir si el usuario no es admin
    }

    // Configuración del correo
    $admin_email = "proyectoka123@gmail.com";
    $subject = "Recordatorio: Actualización de árboles desactualizados";
    
    // Conectar a la base de datos usando la función getConnection
    $conn = getConnection();
    
    // Calcular la fecha límite (1 mes atrás)
    $fecha_limite = date("Y-m-d H:i:s", strtotime("-1 month"));
    
    // Consultar árboles desactualizados considerando solo la última fecha de actualización
    $sql = "
        SELECT a.id, a.ubicacion_geografica 
        FROM arboles a
        LEFT JOIN (
            SELECT arbol_id, MAX(fecha_actualizacion) AS ultima_actualizacion
            FROM actualizaciones
            GROUP BY arbol_id
        ) act ON a.id = act.arbol_id
        WHERE act.ultima_actualizacion < '$fecha_limite' OR act.ultima_actualizacion IS NULL
    ";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        // Crear el contenido del correo en formato HTML
        $lista_arboles = "
            <h2>Los siguientes árboles no han sido actualizados desde hace 1 mes:</h2>
            <table style='width:100%; border-collapse: collapse;'>
                <thead>
                    <tr>
                        <th style='border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;'>ID del Árbol</th>
                        <th style='border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;'>Ubicación</th>
                    </tr>
                </thead>
                <tbody>
        ";

        while ($row = mysqli_fetch_assoc($result)) {
            $lista_arboles .= "
                <tr>
                    <td style='border: 1px solid #ddd; padding: 8px; text-align: center;'>{$row['id']}</td>
                    <td style='border: 1px solid #ddd; padding: 8px; text-align: center;'>{$row['ubicacion_geografica']}</td>
                </tr>
            ";
        }

        $lista_arboles .= "
                </tbody>
            </table>
            <p style='font-size: 14px; color: #555;'>Por favor, actualice la información de los árboles desactualizados.</p>
        ";
        
        // Configuración del correo en formato HTML
        $headers = "From: angiehh1724@gmail.com\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
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
    mysqli_close($conn);
}

?>