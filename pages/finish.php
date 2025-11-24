<?php

session_start();

require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Player.php';
require_once __DIR__ . '/../classes/Game.php';
require_once __DIR__ . '/../classes/GameManager.php';
require_once __DIR__ . '/../classes/GameRepository.php';

if (!isset($_SESSION['player'])) {
    header('Location: index.php');
    exit;
}

$player = unserialize($_SESSION['player']);
$game = GameManager::getGame();

if (!$game) {
    header('Location: game.php');
    exit;
}

// Enregistre la partie
try {
    GameRepository::recordGame($player, $game);
} catch (Exception $e) {
    echo "Erreur";
}

// Nettoie la partie en gardant le joueur connecté
unset($_SESSION['game']);
unset($_SESSION['temps_reveal']);

// Contenu
$pageTitle = 'Fin de partie';
ob_start();

?>

<div class="center">
    <div class="bulle">
        <h3>Victoire !</h3>
        <p>Joueur : <?= htmlspecialchars($player->getUsername()) ?></p>
        <p>Coups : <?= (int)$game->getMoves() ?></p>
        <p>Joueur : <?= gmdate('i:s', $game->getDuration()) ?></p>
    </div>

    <div>
        <a class="btn" href="/memory/pages/game.php">Rejouer</a>
        <a class="btn" href="/memory/pages/top10.php">Top 10</a>
        <a class="btn" href="/memory/pages/profil.php">Mon profil</a>
    </div>
</div>

<?php $content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
