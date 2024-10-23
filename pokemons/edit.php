<?php
require('Class/Connection.php');

// Configuración de errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Iniciar sesión
session_start();

// Redirigir a la página anterior si no hay un usuario en sesión
if (!isset($_SESSION['user'])) {
    header('Location:..');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

// Obtener instancia de conexión
Connection::GetInstance();

// Consulta para obtener las columnas de la tabla 'pokemon'
$showColumnsSQL = 'SHOW COLUMNS FROM pokemon';
$param = Connection::ExecuteSentence($showColumnsSQL);

$selectSQL = 'SELECT * FROM pokemon WHERE id=:id';
$select = Connection::PrepareStatements($selectSQL);
$select->bindValue(':id', $id);
$select->execute();

// Inicializar arreglos para parámetros y tipos
$parameter = [];
$type = [];
$value = [];

//No obtenemos el primer valor id
$fila = $param->fetch();

$pokemon = $select->fetch();

// Recoger información de las columnas
while ($fila = $param->fetch(PDO::FETCH_ASSOC)) {
    $parameter[] = $fila['Field']; // Nombre de la columna
    $type[] = CheckType($fila['Type']); // Tipo de dato

    if(isset($_SESSION['old'][$fila['Field']])){
        $value[] = $_SESSION['old'][$fila['Field']];
        unset($_SESSION['old'][$fila['Field']]);
    }else{
        $value[] = $pokemon[$fila['Field']];
    }
}

// Función para verificar el tipo de datos
function CheckType($string)
{
    if (strpos($string, 'int') !== false) {
        return 'int';
    } elseif (strpos($string, 'char') !== false) {
        return 'text';
    } elseif (strpos($string, 'decimal') !== false) {
        return 'float';
    } elseif (strpos($string, 'enum') !== false) {
        preg_match('/enum\((.*)\)/', $string, $matches);
        if (isset($matches[1])) {
            $string = preg_replace("/'/", "", $matches[1]); // Eliminar comillas
            $valores = explode(',', $string);
            return array_map('trim', $valores); // Limpiar espacios
        }
    }
    return null; // Retornar null si no se encuentra el tipo
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokedex</title>
    <link rel="stylesheet" href="../styles/create_edit.css">
    <link rel="stylesheet" href="../styles/styles.css">
</head>

<body>
    <section>
        <header>
            <div class="box-menu">
                <div class="logo">
                    <img src="../pokeball.png" alt="Pokeball" class="logo-image">
                </div>
                <div class="content-container">
                    <div class="title">
                        <h1>Poke<span>dex</span></h1>
                    </div>
                    <nav class="toggle-nav">
                        <ul class="toggle-nav_list">
                            <li class="toggle-nav_item animation1">
                                <a href="..">Home</a>
                                <div class="animation-underline"></div>
                            </li>
                            <li class="toggle-nav_item animation2">
                                <a href="index.php">Pokemons</a>
                                <div class="animation-underline"></div>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <div class="box-triangle">
                <div class="triangle"></div>
            </div>
        </header>

        <main>
            <div class="section">
                <h1>Pokemon</h1>
            </div>
            <div class="container">

                <!-- Mensaje de operación -->
                <?php if (isset($_GET['op']) && isset($_GET['result'])): ?>
                    <div class="<?= $_GET['result'] > 0 ? 'message' : 'danger' ?>">
                        Result: <?= htmlspecialchars($_GET['op']) . ' ' . htmlspecialchars($_GET['result']) ?>
                    </div>
                <?php endif; ?>

                <div>
                    <form action="update.php" method="post" style="width: 100%;">
                        <?php
                        $isEnum = false;
                        for ($i = 0; $i < count($parameter); $i++): // Iterar sobre columnas
                            ?>
                            <div class="field">
                                <label for="<?= $parameter[$i]; ?>">Pokemon <?= htmlspecialchars($parameter[$i]); ?></label>
                                <?php
                                // Determinar el tipo de entrada según el tipo de datos
                                if (is_array($type[$i])) {
                                    $isEnum = true;
                                }

                                if (!$isEnum): // Si no es enum
                                    ?>
                                    <input value="<?= htmlspecialchars($value[$i]) ?>" required type="<?= getInputType($type[$i]) ?>" class="form-control" id="<?= $parameter[$i]; ?>" name="<?= $parameter[$i]; ?>"
                                        <?php if ($type[$i] === 'int'): ?>
                                            step="1" placeholder="0"
                                        <?php elseif ($type[$i] === 'float'): ?>
                                            step="0.01" placeholder="0.00"
                                        <?php else: ?>
                                            placeholder="Pokemon <?= htmlspecialchars($parameter[$i]); ?>">
                                        <?php endif; ?>
                                <?php else: // Si es enum ?>
                                    <select id="<?= $parameter[$i]; ?>" name="<?= $parameter[$i]; ?>">
                                        <?php foreach ($type[$i] as $val): ?>
                                            <option value="<?= htmlspecialchars($val) ?>" <?php if (!empty($value[$i]) && $value[$i] == $val) echo 'selected'; ?>>
                                                <?= htmlspecialchars($val) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php $isEnum = false; ?>
                                <?php endif; ?>
                            </div>
                        <?php endfor; ?>
                        <input value="<?= $id ?>" type="hidden" name="id" id="id">

                        <button type="submit" class="btn btn-primary">Edit</button> 
                    </form>
                </div>
            </div>
        </main>
    </section>
</body>

</html>
<?php
// Limpiar la conexión al finalizar
Connection::ClearConnection();

// Función para determinar el tipo de entrada
function getInputType($type)
{
    switch ($type) {
        case 'int':
        case 'float':
            return 'number';
        case 'text':
            return 'text';
        default:
            return 'text'; // Por defecto
    }
}