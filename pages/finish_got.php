<?php
session_start();
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Player.php';
require_once __DIR__ . '/../classes/Game.php';
require_once __DIR__ . '/../classes/GameManager.php';
require_once __DIR__ . '/../classes/GameRepository.php';

if (!isset($_SESSION['player'])) {
    header('Location: ../index.php');
    exit;
}

$player = unserialize($_SESSION['player']);
$game = isset($_SESSION['game_got']) ? unserialize($_SESSION['game_got']) : null;

if (!$game) {
    header('Location: game_got.php');
    exit;
}

// Enregistre la partie
try {
    GameRepository::recordGame($player, $game);
} catch (Exception $e) {
    echo "Erreur";
}

// Nettoie la partie GOT en gardant le joueur connecté
unset($_SESSION['game_got']);
unset($_SESSION['temps_reveal_got']);

$pageTitle = 'Fin de partie GOT';
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
            <a class="btn" href="/memory/pages/game_got.php">Rejouer</a>
            <a class="btn" href="/memory/pages/top10.php">Top 10</a>
            <a class="btn" href="/memory/pages/profil.php">Mon profil</a>
        </div>
    </div>
</div>

<div class="game-grid">
    <?php
    // Affiche toutes les cartes GOT face visible
    $gotImages = [
        'arya.jpg',
        'daenerys.jpg',
        'drogo.jpg',
        'hodor.jpg',
        'jon.jpg',
        'ned.jpg',
        'ramsay.jpg',
        'samwell.png',
        'tyrion.jpg',
        'walder.jpg'
    ];
    $deck = [];
    foreach ($gotImages as $img) {
        $deck[] = $img;
        $deck[] = $img;
    }
    $i = 0;
    foreach ($deck as $img):
    ?>
        <div>
            <button class="card" disabled>
                <img src="/memory/assets/img/got/<?= $img ?>" alt="carte GOT">
            </button>
        </div>
    <?php $i++;
    endforeach; ?>
</div>
<?php $content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>