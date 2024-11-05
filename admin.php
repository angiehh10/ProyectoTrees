<?php
include 'utils/functions.php';

// Obtener estadísticas
$totalAmigos = countAmigos();
$totalArbolesDisponibles = countArbolesDisponibles();
$totalArbolesVendidos = countArbolesVendidos();
$arboles = obtenerArboles();

// Variables para mensajes
$mensaje = '';
$error = '';

// Lógica para manejar formularios y acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Acción para especies
    if (isset($_POST['accion_especie'])) {
        if ($_POST['accion_especie'] === 'crear') {
            $nombre_comercial = $_POST['nombre_comercial'];
            $nombre_cientifico = $_POST['nombre_cientifico'];
            if (crearEspecie($nombre_comercial, $nombre_cientifico)) {
                $mensaje = "Especie creada exitosamente.";
            } else {
                $error = "Error al crear la especie.";
            }
        } elseif ($_POST['accion_especie'] === 'actualizar') {
            $id = $_POST['especie_id'];
            $nombre_comercial = $_POST['nombre_comercial'];
            $nombre_cientifico = $_POST['nombre_cientifico'];
            if (actualizarEspecie($id, $nombre_comercial, $nombre_cientifico)) {
                $mensaje = "Especie actualizada exitosamente.";
            } else {
                $error = "Error al actualizar la especie.";
            }
        } elseif ($_POST['accion_especie'] === 'eliminar') {
            $id = $_POST['especie_id'];
            if (eliminarEspecie($id)) {
                $mensaje = "Especie eliminada exitosamente.";
            } else {
                $error = "Error al eliminar la especie.";
            }
        }
    }
// Acción para árboles
elseif (isset($_POST['accion_arbol']) && $_POST['accion_arbol'] === 'crear') {
    $especie_id = $_POST['especie_id'];
    $ubicacion_geografica = $_POST['ubicacion_geografica'];
    $estado = $_POST['estado'];
    $precio = $_POST['precio'];
    $tamano = $_POST['tamano'];

    // Manejo de la imagen: archivo subido
    $foto = null;
    if (isset($_FILES['foto_upload']) && $_FILES['foto_upload']['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = basename($_FILES['foto_upload']['name']);
        $directorioDestino = 'uploads/' . $nombreArchivo;

        // Crear el directorio si no existe
        if (!file_exists('uploads')) {
            mkdir('uploads', 0777, true);
        }

        // Verificar si la imagen ya existe en el directorio
        if (file_exists($directorioDestino)) {
            $foto = $directorioDestino; // Usa la imagen existente
        } else {
            // Mover el archivo subido a la carpeta de destino
            if (move_uploaded_file($_FILES['foto_upload']['tmp_name'], $directorioDestino)) {
                $foto = $directorioDestino; // Guarda la ruta del archivo en la base de datos
            } else {
                $error = "Error al cargar la imagen.";
            }
        }
    }
        // Crear el árbol con la información procesada
        if (crearArbol($especie_id, $ubicacion_geografica, $estado, $precio, $tamano, $foto)) {
            $mensaje = "Árbol creado exitosamente.";
        } else {
            $error = "Error al crear el árbol.";
        }
    }
   // Acción para árboles
   elseif (isset($_POST['accion_arbol']) && $_POST['accion_arbol'] === 'crear') {
    $especie_id = $_POST['especie_id'];
    $ubicacion_geografica = $_POST['ubicacion_geografica'];
    $estado = $_POST['estado'];
    $tamano = $_POST['tamano'];

    // Manejo de la imagen: archivo subido
    $foto = null;
    if (isset($_FILES['foto_upload']) && $_FILES['foto_upload']['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = basename($_FILES['foto_upload']['name']);
        $directorioDestino = 'uploads/' . $nombreArchivo;

        // Crear el directorio si no existe
        if (!file_exists('uploads')) {
            mkdir('uploads', 0777, true);
        }

        // Verificar si la imagen ya existe en el directorio
        if (file_exists($directorioDestino)) {
            $foto = $directorioDestino; // Usa la imagen existente
        } else {
            // Mover el archivo subido a la carpeta de destino
            if (move_uploaded_file($_FILES['foto_upload']['tmp_name'], $directorioDestino)) {
                $foto = $directorioDestino; // Guarda la ruta del archivo en la base de datos
            } else {
                $error = "Error al cargar la imagen.";
            }
        }
    }

    // Crear el árbol con la información procesada
    if (crearArbol($especie_id, $ubicacion_geografica, $estado, $tamano, $foto)) {
        $mensaje = "Árbol creado exitosamente.";
    } else {
        $error = "Error al crear el árbol.";
    }
}

// Acción para actualizar el árbol
    elseif (isset($_POST['accion_arbol']) && $_POST['accion_arbol'] === 'actualizar') {
        $arbol_id = $_POST['arbol_id'];
        $especie_id = $_POST['especie_id'];
        $ubicacion_geografica = $_POST['ubicacion_geografica'];
        $estado = $_POST['estado'];
        $tamano = $_POST['tamano'];

        // Manejo de la imagen en actualización
        $foto = null;
        if (isset($_FILES['foto_upload']) && $_FILES['foto_upload']['error'] === UPLOAD_ERR_OK) {
            $nombreArchivo = basename($_FILES['foto_upload']['name']);
            $directorioDestino = 'uploads/' . $nombreArchivo;

            if (!file_exists('uploads')) {
                mkdir('uploads', 0777, true);
            }

            // Verificar si la imagen ya existe en el directorio
            if (file_exists($directorioDestino)) {
                $foto = $directorioDestino; // Usa la imagen existente
            } else {
                // Mover el archivo subido a la carpeta de destino
                if (move_uploaded_file($_FILES['foto_upload']['tmp_name'], $directorioDestino)) {
                    $foto = $directorioDestino;
                } else {
                    $error = "Error al cargar la imagen.";
                }
            }
        }

        // Actualizar el árbol con los datos y la foto
        if (actualizarArbol($arbol_id, $especie_id, $ubicacion_geografica, $estado, $tamano, $foto)) {
            $mensaje = "Árbol actualizado exitosamente.";
        } else {
            $error = "Error al actualizar el árbol.";
        }
    }

    // Acción para registrar actualización de árbol
    elseif (isset($_POST['accion_actualizacion']) && $_POST['accion_actualizacion'] === 'registrar') {
        $arbol_id = $_POST['arbol_id'];
        $tamano = $_POST['tamano'];
        $estado = $_POST['estado'];

        // Procesar la fecha de actualización
        $fecha_actualizacion = date('Y-m-d'); // Fecha de hoy como valor predeterminado
        if ($_POST['fecha_opcion'] === 'manual' && !empty($_POST['fecha_manual_input'])) {
            $fecha_actualizacion = $_POST['fecha_manual_input'];
        }

        // Llamar a la función para registrar la actualización
        if (registrarActualizacion($arbol_id, $tamano, $estado, $fecha_actualizacion)) {
            $mensaje = "Actualización registrada exitosamente.";
        } else {
            $error = "Error al registrar la actualización.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - MyTrees</title>
    
    <!-- Vincula el CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/estilos.css" rel="stylesheet">
</head>
<body>
    <?php include 'inc/header.php'; ?>

    <div class="container my-5">
    <h1 class="text-center mb-4">Dashboard de Administración</h1>
    <div class="row text-center">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Amigos Registrados</h5>
                    <p class="card-text display-4"><?php echo $totalAmigos; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Árboles Disponibles</h5>
                    <p class="card-text display-4"><?php echo $totalArbolesDisponibles; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Árboles Vendidos</h5>
                    <p class="card-text display-4"><?php echo $totalArbolesVendidos; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Mostrar mensajes de éxito o error -->
    <?php if ($mensaje): ?>
        <div class="alert alert-success text-center"><?php echo $mensaje; ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger text-center"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Menú de Pestañas -->
    <ul class="nav nav-tabs" id="adminTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link <?php echo (!isset($_GET['tab']) || $_GET['tab'] == 'especies') ? 'active' : ''; ?>" href="admin.php?tab=especies">Administrar Especies</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'arboles') ? 'active' : ''; ?>" href="admin.php?tab=arboles">Administrar Árboles</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'actualizacion') ? 'active' : ''; ?>" href="admin.php?tab=actualizacion">Registrar Actualización</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'amigos') ? 'active' : ''; ?>" href="admin.php?tab=amigos">Ver Amigos</a>
        </li>
    </ul>

    <!-- Contenido de Pestañas -->
    <div class="tab-content" id="adminTabContent">
        <!-- Pestaña Administrar Especies -->
        <div class="tab-pane fade <?php echo (!isset($_GET['tab']) || $_GET['tab'] == 'especies') ? 'show active' : ''; ?>" id="especies">
            <div class="card my-4">
                <div class="card-body">
                    <h4>Crear Nueva Especie</h4>
                    <form action="admin.php" method="POST">
                        <input type="hidden" name="accion_especie" value="crear">
                        <div class="mb-3">
                            <label for="nombre_comercial" class="form-label">Nombre Comercial</label>
                            <input type="text" name="nombre_comercial" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="nombre_cientifico" class="form-label">Nombre Científico</label>
                            <input type="text" name="nombre_cientifico" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success">Crear Especie</button>
                    </form>

                    <h5 class="mt-4">Especies Existentes</h5>
                    <?php
                    $especies = obtenerEspecies();
                    foreach ($especies as $especie): ?>
                        <form action="admin.php" method="POST" class="d-flex justify-content-between align-items-center mt-2">
                            <input type="hidden" name="accion_especie" value="actualizar">
                            <input type="hidden" name="especie_id" value="<?php echo $especie['id']; ?>">
                            <input type="text" name="nombre_comercial" value="<?php echo $especie['nombre_comercial']; ?>" class="form-control me-2">
                            <input type="text" name="nombre_cientifico" value="<?php echo $especie['nombre_cientifico']; ?>" class="form-control me-2">
                            <button type="submit" class="btn btn-primary">Actualizar</button>
                            <button type="submit" formaction="admin.php" name="accion_especie" value="eliminar" class="btn btn-danger">Eliminar</button>
                        </form>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
       <!-- Pestaña Administrar Árboles -->
<div class="tab-pane fade <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'arboles') ? 'show active' : ''; ?>" id="arboles">
    <div class="card my-4">
        <div class="card-body">
            <form action="admin.php?tab=arboles" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="accion_arbol" value="crear">
                <div class="mb-3">
                    <label for="especie_id" class="form-label">Especie</label>
                    <select name="especie_id" class="form-control" required>
                        <?php foreach ($especies as $especie): ?>
                            <option value="<?php echo $especie['id']; ?>"><?php echo $especie['nombre_comercial']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="ubicacion_geografica" class="form-label">Ubicación Geográfica</label>
                    <input type="text" name="ubicacion_geografica" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select name="estado" class="form-control" required>
                        <option value="Disponible">Disponible</option>
                        <option value="Vendido">Vendido</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="precio" class="form-label">Precio</label>
                    <input type="number" step="0.01" name="precio" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="foto" class="form-label">Foto del Árbol</label>
                    <input type="file" name="foto_upload" class="form-control mt-2" accept="image/*">
                </div>
                <div class="mb-3">
                    <label for="tamano" class="form-label">Tamaño</label>
                    <input type="text" name="tamano" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success">Crear Árbol</button>
            </form>
        </div>
    </div>
</div>
     <!-- Pestaña Registrar Actualización de Árbol -->
<div class="tab-pane fade <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'actualizacion') ? 'show active' : ''; ?>" id="actualizacion">
    <div class="card my-4">
        <div class="card-body">
            <form action="admin.php" method="POST">
                <input type="hidden" name="accion_actualizacion" value="registrar">
                
                <div class="mb-3">
                    <label for="arbol_id" class="form-label">Seleccionar ID del Árbol</label>
                    <select name="arbol_id" id="arbol_id" class="form-control" required onchange="cargarDatosArbol(this.value)">
                        <option value="">Seleccione un árbol</option>
                        <?php foreach ($arboles as $arbol): ?>
                            <option value="<?php echo $arbol['id']; ?>"><?php echo $arbol['id']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="tamano" class="form-label">Tamaño Actual</label>
                    <input type="text" name="tamano" id="tamano" class="form-control" required>
                </div>
                
                <div class="mb-3">
                    <label for="estado" class="form-label">Estado Actual</label>
                    <select name="estado" id="estado" class="form-control" required>
                        <option value="Disponible">Disponible</option>
                        <option value="Vendido">Vendido</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="fecha_opcion" class="form-label">Fecha de Actualización</label>
                    <select name="fecha_opcion" id="fecha_opcion" class="form-control" onchange="toggleFechaManual(this.value)">
                        <option value="hoy">Usar fecha de hoy</option>
                        <option value="manual">Ingresar fecha manual</option>
                    </select>
                </div>
                
                <div class="mb-3" id="fecha_manual" style="display: none;">
                    <label for="fecha_manual_input" class="form-label">Fecha Manual</label>
                    <input type="date" name="fecha_manual_input" id="fecha_manual_input" class="form-control">
                </div>
                
                <button type="submit" class="btn btn-success">Registrar Actualización</button>
            </form>
        </div>
    </div>
</div>

<script>
// Mostrar u ocultar campo de fecha manual
function toggleFechaManual(opcion) {
    document.getElementById('fecha_manual').style.display = (opcion === 'manual') ? 'block' : 'none';
}

// Cargar datos del árbol seleccionado
function cargarDatosArbol(arbol_id) {
    const arboles = <?php echo json_encode($arboles); ?>;
    const arbol = arboles.find(a => a.id == arbol_id);

    if (arbol) {
        document.getElementById('tamano').value = arbol.tamano;
        document.getElementById('estado').value = arbol.estado;
    } else {
        document.getElementById('tamano').value = '';
        document.getElementById('estado').value = 'Disponible';
    }
}
</script>
       <!-- Pestaña Ver Amigos -->
<div class="tab-pane fade <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'amigos') ? 'show active' : ''; ?>" id="amigos">
    <div class="card my-4">
        <div class="card-body">
            <h5 class="mt-4">Lista de Amigos</h5>
            <?php
            // Obtener lista de amigos
            $amigos = obtenerAmigos();
            foreach ($amigos as $amigo): ?>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span><?php echo $amigo['nombre']; ?></span>
                    <a href="admin.php?tab=amigos&amigo_id=<?php echo $amigo['id']; ?>" class="btn btn-info">Ver Árboles</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<!-- Árboles del Amigo Seleccionado -->
<?php if (isset($_GET['amigo_id'])): ?>
    <div class="card my-4">
        <div class="card-body">
            <h5 class="mt-4">Árboles de <?php echo obtenerNombreAmigo($_GET['amigo_id']); ?></h5>
            <?php
            $arboles = obtenerArbolesPorAmigo($_GET['amigo_id']);
            $especies = obtenerEspecies(); // Asume que esta función obtiene todas las especies
            foreach ($arboles as $arbol):
            ?>
                <form action="admin.php?tab=amigos&amigo_id=<?php echo $_GET['amigo_id']; ?>" method="POST" enctype="multipart/form-data" class="d-flex justify-content-between align-items-center mt-2">
                    <input type="hidden" name="accion_arbol" value="actualizar">
                    <input type="hidden" name="arbol_id" value="<?php echo $arbol['id']; ?>">

                    <!-- Selección de especie -->
                    <select name="especie_id" class="form-control me-2" required>
                        <?php foreach ($especies as $especie): ?>
                            <option value="<?php echo $especie['id']; ?>" <?php echo ($arbol['especie_id'] == $especie['id']) ? 'selected' : ''; ?>>
                                <?php echo $especie['nombre_comercial']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <!-- Tamaño -->
                    <input type="text" name="tamano" value="<?php echo $arbol['tamano']; ?>" class="form-control me-2" placeholder="Tamaño">

                    <!-- Ubicación geográfica -->
                    <input type="text" name="ubicacion_geografica" value="<?php echo $arbol['ubicacion_geografica']; ?>" class="form-control me-2" placeholder="Ubicación Geográfica">

                    <!-- Estado -->
                    <select name="estado" class="form-control me-2">
                        <option value="Disponible" <?php echo ($arbol['estado'] === 'Disponible') ? 'selected' : ''; ?>>Disponible</option>
                        <option value="Vendido" <?php echo ($arbol['estado'] === 'Vendido') ? 'selected' : ''; ?>>Vendido</option>
                    </select>

                    <!-- Foto -->
                    <input type="file" name="foto_upload" class="form-control me-2" accept="image/*">
                    <?php if (!empty($arbol['foto'])): ?>
                        <a href="<?php echo $arbol['foto']; ?>" target="_blank">Ver Imagen Actual</a>
                    <?php endif; ?>

                    <!-- Botones de acción -->
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </form>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>


    <?php include 'inc/footer.php'; ?>

    <!-- Vincula el JS de Bootstrap al final del body -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>