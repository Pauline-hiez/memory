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
$game = GameManager::getGame();

if (!isset($_SESSION['game']) || isset($_POST['restart'])) {
    GameManager::startNew();
    $game = GameManager::getGame();
}

//Retourne une carte si on clique dessus
if (isset($_POST['card_index'])) {
    GameManager::revealCard((int)$_POST['card_index']);
}

// Retourne carte si non trouvée
if (isset($_POST['action']) && $_POST['action'] == 'continue') {
    echo "action";
    GameManager::restTemp();
}

//Si partie terminée
if ($game->isEnd()) {
    header('Location: finish.php');
    exit;
}

$pageTitle = 'Jeu';
ob_start();


$disableClick = (
    isset($_SESSION['temps_reveal']) &&
    count($_SESSION['temps_reveal']) === 2 &&
    (
        !isset($_SESSION['last_pair']) ||
        $_SESSION['last_pair'] === false
    )
);

?>

<div class="container">
    <div class="login">
        <h2>Votre partie</h2>
        <div class="stat-bulle">
            <p>Nombre de coups : <?= (int)$game->getMoves() ?></p>
            <p>Temps : <?= gmdate('i:s', (int)$game->getDuration()) ?></p>
        </div>
        <form method="post" class="form-pseudo">
            <button class="btn" name="restart" type="submit">Recommencer</button>
        </form>
    </div>

    <div class="game-grid" id="game-grid">
        <?php
        $card = $game->getDeck();
        for ($i = 0; $i < count($card); $i++):

            $showFace = $card[$i]->isReveled() || in_array($i, $_SESSION['temps_reveal']);
            $imagePath = $showFace ? $card[$i]->getImagePath() : "/memory/assets/img/carte.jpg";
        ?>

            <div>
                <button class="card" data-index="<?= $i ?>" <?= $card[$i]->isReveled() || $disableClick ? 'disabled' : '' ?>>
                    <img src="<?= $imagePath ?>" alt="carte">
                </button>
            </div>
        <?php endfor; ?>
    </div>

    <div class="center"></div>
</div>

<?php

$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
?>
<script>
    // Gestion AJAX des clics sur les cartes
    document.querySelectorAll('.card').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (btn.disabled) return;
            const index = btn.getAttribute('data-index');
            fetch('ajax_game.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'card_index=' + index
                })
                .then(res => res.json())
                .then(updateGame)
                .catch(console.error);
        });
    });

    function updateGame(data) {
        if (data.isEnd) {
            window.location.href = 'finish.php';
            return;
        }
        // Met à jour les cartes
        document.querySelectorAll('.card').forEach(function(btn, i) {
            btn.disabled = data.cards[i].revealed || data.disableClick;
            btn.querySelector('img').src = data.cards[i].image;
        });
        // Met à jour les stats
        document.querySelector('.stat-bulle p:nth-child(1)').textContent = 'Nombre de coups : ' + data.moves;
        document.querySelector('.stat-bulle p:nth-child(2)').textContent = 'Temps : ' + new Date(data.duration * 1000).toISOString().substr(14, 5);
        // Si besoin d'auto-continuer
        if (data.needContinue) {
            setTimeout(function() {
                fetch('ajax_game.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'action=continue'
                    })
                    .then(res => res.json())
                    .then(updateGame)
                    .catch(console.error);
            }, 800);
        }
    }
</script>