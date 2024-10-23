<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokedex</title>
    <link rel="stylesheet" href="./styles/styles.css">
    <link rel="stylesheet" href="./styles/index.css">
</head>

<body>
    <section class="hero-section">
        <header>

            <div class="box-menu">

                <div class="logo">
                    <img src="pokeball.png" alt="Pokeball" class="logo-image">
                </div>
                <div class="content-container">
                    <div class="title">
                        <h1>Poke<span>dex</span></h1>
                    </div>

                    <nav class="toggle-nav">

                        <ul class="toggle-nav_list">
                            <li class="toggle-nav_item animation1">

                                <a href="">Home</a>
                                <div class="animation-underline"></div>


                            </li>
                            <li class="toggle-nav_item animation2">

                                <a href="pokemons">Pokemons</a>
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
            <div class="section"><h1>home</h1></div>
            <div class="container">
                <?php
                if(isset($_GET['op']) && isset($_GET['result'])) {
                    ?>
                    <div class="message">
                        result: <?= $_GET['op'] . ' ' . $_GET['result'] ?>
                    </div>
                    <?php
                }
                ?>
                <div class="row">
                    <h3>products, etc.</h3>
                </div>
                <div class="row">
                    <?php
                    if (isset($_SESSION['user'])) {
                        ?>
                        <a href="user/logout.php">log out</a>
                        <?php
                    } else {
                        ?>
                        <a href="user/login.php">log in</a>
                        <?php
                    }
                    ?>
                    &nbsp;
                    <a href="pokemons/" class="btn btn-success">pokemons</a>
                </div>
            </div>
            </div>
        </main>
    </section>
</body>

</html>