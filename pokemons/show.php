<?php
require('Class/Connection.php');

// Configuración de errores
ini_set('display_errors', 1);
error_reporting(E_ALL);



$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: ..');
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

$pokemon = $select->fetch();


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokedex</title>
    <link rel="stylesheet" href="../styles/styles.css">
    <link rel="stylesheet" href="../styles/show.css">
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
            <?php 
                // Iterar sobre las columnas y mostrar los datos del Pokémon
                while ($column = $param->fetch(PDO::FETCH_ASSOC)) {
                    $columnName = $column['Field']; // Obtén el nombre de la columna
                    $columnValue = $pokemon[$columnName] ?? ''; // Obtén el valor correspondiente del Pokémon
                ?>
                    <div class="pokemon-detail">
                        <strong><?php echo htmlspecialchars($columnName); ?>:</strong>
                        <span><?php echo htmlspecialchars($columnValue); ?></span>
                    </div>
                <?php 
                } 
                ?>
            </div>
        </main>
    </section>
</body>

</html>
<?php
// Limpiar la conexión al finalizar
Connection::ClearConnection();

