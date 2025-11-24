<?php

session_start();

require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Card.php';
require_once __DIR__ . '/../classes/Game.php';
require_once __DIR__ . '/../classes/GameManager.php';
require_once __DIR__ . '/../classes/Player.php';
require_once __DIR__ . '/../classes/GameRepository.php';

// Si non connecté
if (!isset($_SESSION['player'])) {
    header('Location: index.php');
    exit;
}

// Initialise une nouvelle partie si besoin
$player = unserialize($_SESSION['player']);
if (!isset($_SESSION['game']) || isset($_POST['restart'])) {
    GameManager::startNew();
}

//Retourne une carte si on clique dessus
if (isset($_POST['card_index'])) {
    GameManager::revealCard((int)$_POST['card_index']);
}

//Si partie terminée
if ($game->isEnd()) {
    header('Location: finish.php');
    exit;
}

$pageTitle = 'Jeu';
ob_start();

?>

<div class="container">
    <div class="stat-bulle">
        <p>Nombre de coups : </p><?= (int)$moves ?>
        <p>Temps : </p> <?= gmdate('i:s', (int)$elapsed) ?>
    </div>

    <div class="center">
        <div class="btn-center">
            <form method="post">
                <button class="btn" name="restart" type="submit">Recommencer</button>
            </form>
        </div>
    </div>

    <div class="game-grid">
        <?php
        for ($i = 0; $i < count($deck); $i++):
            $card = $deck[$i];
            $showFace = $card->isReveled() || in_array($i, $temps);
            $imagePath = $showFace ? $card->getImagePath() : "/memory/assets/img/carte.jpg";
        ?>

            <div>
                <form method="post">
                    <input type="hidden" name="card_index" value="<?= $i ?>">
                    <button class="card" type="submit" <?= $card->isReveled() ? 'disabled' : '' ?>>
                        <img src="<?= $imagePath ?>" alt="carte">
                    </button>
                </form>
            </div>
        <?php endfor; ?>
    </div>

    <div class="center">
        <?php if (isset($temps) && count($temps) === 2): ?>
            <form id="auto-form" method="post">
                <input type="hidden" name="action" value="continue">
            </form>
            <!-- Retournement automatique des cartes si paire non trouvée -->
            <script>
                setTimeout(function() {
                    document.getElementById('auto-form').submit();
                }, 1200);
            </script>
        <?php endif; ?>
    </div>
</div>

<?php

$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>