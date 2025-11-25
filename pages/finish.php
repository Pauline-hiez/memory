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
    <div class="bulle-victory">
        <div class="victory-flex">
            <img src="/memory/assets/img/winner.png" alt="winner" class="winner-icon">
            <div class="victory-content">
                <h3 class="victory-title">Victoire !</h3>
                <p>Joueur : <?= htmlspecialchars($player->getUsername()) ?></p>
                <p>Coups : <?= (int)$game->getMoves() ?></p>
                <p>Durée : <?= gmdate('i:s', $game->getDuration()) ?></p>
            </div>
            <img src="/memory/assets/img/winner.png" alt="winner" class="winner-icon">
        </div>
        <div class="victory-links">
            <a class="btn" href="/memory/pages/game.php">Rejouer</a>
            <a class="btn" href="/memory/pages/top10.php">Top 10</a>
            <a class="btn" href="/memory/pages/profil.php">Mon profil</a>
        </div>
    </div>
</div>

<div class="game-grid">
    <?php
    require_once __DIR__ . '/../classes/Card.php';
    for ($i = 1; $i <= 10; $i++):
        $card = new Card(($i % 5) + 1);
        $imgPath = $card->getImagePath();
    ?>
        <div>
            <button class="card" disabled>
                <img src="<?= $imgPath ?>" alt="carte">
            </button>
        </div>
    <?php endfor; ?>
</div>

<?php $content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
