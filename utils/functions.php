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
    $sql = "SELECT a.* FROM arboles a 
            JOIN amigo_arbol aa ON a.id = aa.arbol_id 
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

function registrarActualizacion($arbol_id, $tamano, $estado) {
    $conn = getConnection();
    $sql = "INSERT INTO actualizaciones (arbol_id, tamano, estado, fecha) VALUES (?, ?, ?, NOW())";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $arbol_id, $tamano, $estado);
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

?>
