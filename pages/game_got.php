<div class="got">

    <?php
    session_start();
    require_once __DIR__ . '/../classes/Database.php';
    require_once __DIR__ . '/../classes/CardGot.php';
    require_once __DIR__ . '/../classes/Game.php';
    require_once __DIR__ . '/../classes/GameManager.php';
    require_once __DIR__ . '/../classes/Player.php';
    require_once __DIR__ . '/../classes/GameRepository.php';

    // Si non connecté
    if (!isset($_SESSION['player'])) {
        header('Location: ../index.php');
        exit;
    }

    // Images GOT (10 paires)
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
    $expectedCardCount = count($gotImages) * 2;

    // Initialise une nouvelle partie GOT si besoin
    if (!isset($_SESSION['game_got']) || isset($_POST['restart']) || (isset($_SESSION['game_got']) && count(unserialize($_SESSION['game_got'])->getDeck()) !== $expectedCardCount)) {
        $deck = [];
        foreach ($gotImages as $img) {
            $deck[] = new CardGot($img);
            $deck[] = new CardGot($img);
        }
        shuffle($deck);
        $game = new Game($deck);
        $_SESSION['game_got'] = serialize($game);
        $_SESSION['temps_reveal_got'] = [];
        $_SESSION['last_pair_got'] = null;
    } else {
        $game = unserialize($_SESSION['game_got']);
    }

    //Retourne une carte si on clique dessus
    if (isset($_POST['card_index'])) {
        GameManager::revealCard((int)$_POST['card_index'], 'got');
    }
    // Retourne carte si non trouvée
    if (isset($_POST['action']) && $_POST['action'] == 'continue') {
        GameManager::restTemp('got');
    }
    //Si partie terminée
    if ($game->isEnd()) {
        header('Location: finish_got.php');
        exit;
    }
    $pageTitle = 'Jeu GOT';
    ob_start();
    $disableClick = (
        isset($_SESSION['temps_reveal_got']) &&
        count($_SESSION['temps_reveal_got']) === 2 &&
        (
            !isset($_SESSION['last_pair_got']) ||
            $_SESSION['last_pair_got'] === false
        )
    );
    ?>
    <div class="container">
        <div class="login">
            <h2>Votre partie Game of Thrones</h2>
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
                $showFace = $card[$i]->isReveled() || (isset($_SESSION['temps_reveal_got']) && in_array($i, $_SESSION['temps_reveal_got']));
                $imagePath = $showFace ? $card[$i]->getImagePath() : "/memory/assets/img/got/carte.jpg";
            ?>
                <div>
                    <button class="card" data-index="<?= $i ?>" <?= $card[$i]->isReveled() || $disableClick ? 'disabled' : '' ?> style="width:100px;height:140px;">
                        <img src="<?= $imagePath ?>" alt="carte GOT" style="width:100%;height:100%;object-fit:cover;">
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
        document.querySelectorAll('.card').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                if (btn.disabled) return;
                const index = btn.getAttribute('data-index');
                fetch('ajax_game_got.php', {
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
                window.location.href = 'finish_got.php';
                return;
            }
            document.querySelectorAll('.card').forEach(function(btn, i) {
                btn.disabled = data.cards[i].revealed || data.disableClick;
                btn.querySelector('img').src = data.cards[i].image;
            });
            document.querySelector('.stat-bulle p:nth-child(1)').textContent = 'Nombre de coups : ' + data.moves;
            document.querySelector('.stat-bulle p:nth-child(2)').textContent = 'Temps : ' + new Date(data.duration * 1000).toISOString().substr(14, 5);
            if (data.needContinue) {
                setTimeout(function() {
                    fetch('ajax_game_got.php', {
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
</div>