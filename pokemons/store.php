<?php
require('Class/Connection.php');

// Configuración de errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Iniciar sesión
session_start();

// Verificar si hay un usuario en sesión
if (!isset($_SESSION['user'])) {
    header('Location:..');
    exit;
}

// Obtener instancia de conexión
$connection = Connection::getInstance();

// Consulta para obtener las columnas de la tabla 'pokemon'
$showColumnsSQL = 'SHOW COLUMNS FROM pokemon';
$param = Connection::executeSentence($showColumnsSQL);

$values = [];
$columns = [];
$isValid = true;

//Nos saltamos el parametro id
$parameter = $param->fetch(PDO::FETCH_ASSOC);

// Recoger información de las columnas
while ($parameter = $param->fetch(PDO::FETCH_ASSOC)) {
    $columnName = $parameter['Field'];

    // Verificar si los campos han sido enviados por POST
    if (!isset($_POST[$columnName])) {
        Connection::clearConnection();
        header('Location:..');
        exit;
    }

    $value = trim($_POST[$columnName]);
    $values[$columnName] = $value;
    $columns[] = $columnName;

    // Validar si los valores son numéricos y están en el rango permitido
    if (empty($value) || (is_numeric($value) && ($value < 0 || $value > 1000000))) {
        $isValid = false;
    }
}

// Si la validación falla, redirigir y guardar los valores en la sesión
if (!$isValid) {
    $_SESSION['old'] = $values;
    header('Location: create.php?op=insertproduct&result=0');
    exit;
}

// Construcción de la consulta de inserción
$insertSQL = 'INSERT INTO pokemon (' . implode(', ', $columns) . ') VALUES (:' . implode(', :', $columns) . ')';

$insert = Connection::prepareStatements($insertSQL);

try {
    // Asignar los valores a los parámetros de la consulta
    foreach ($values as $column => $value) {
        $insert->bindValue(':' . $column, $value);
    }

    // Ejecutar la consulta
    $insert->execute();

    // Obtener el último ID insertado
    $lastId = $connection->lastInsertId();

    // Redirigir con el resultado de la inserción
    header('Location: index.php?op=insertproduct&result=' . $lastId);
} catch (PDOException $ex) {
    Connection::clearConnection();
    header('Location: ..');
    exit;
}

// Limpiar la conexión
Connection::clearConnection();
