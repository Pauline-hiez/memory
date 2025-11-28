<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memory</title>
    <link rel="stylesheet" href="/memory/assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Caesar+Dressing&family=Fascinate+Inline&family=Gabriela&family=Imperial+Script&family=League+Script&family=Mea+Culpa&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100..900;1,100..900&family=Story+Script&display=swap" rel="stylesheet">
</head>

<body>
    <header class="main-header">
        <div>
            <nav class="main-nav">
                <a href="/memory/index.php">Accueil</a>
                <a href="/memory/pages/top10.php">Top 10</a>
                <a href="/memory/pages/profil.php">Profil</a>
            </nav>
        </div>
    </header>

    <main class="page-content">
        <?= $content ?>
    </main>

    <footer class="main-footer">
        <div class="center">© Memory — Pop's</div>
    </footer>

</body>

</html>