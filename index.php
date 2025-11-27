<div class="index">
    <?php

    session_start();
    $message = '';

    require_once __DIR__ . '/classes/Database.php';
    require_once __DIR__ . '/classes/Player.php';

    $db = Database::getConnexion();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['username']) && !empty($_POST['theme'])) {
        $username = trim($_POST['username']);
        $theme = $_POST['theme'];

        $stmt = $db->prepare("SELECT id FROM players WHERE username = ?");
        $stmt->execute([$username]);
        $row = $stmt->fetch();

        if ($row) {
            $playerId = (int)$row['id'];
        } else {
            $ins = $db->prepare("INSERT INTO players (username) VALUES (?)");
            $ins->execute([$username]);
            $playerId = (int)$db->lastInsertId();
        }

        //Instancie Player
        $player = new Player($playerId, $username);
        //Sauvegarde en session
        $_SESSION['player'] = serialize($player);

        // Redirection selon le thème
        if ($theme === 'got') {
            header('Location: pages/game_got.php');
        } else {
            header('Location: pages/game.php');
        }
        exit;
    }

    $pageTitle = 'Accueil';
    ob_start();

    ?>

    <div class="container">
        <div class="login">
            <h2>Entrez votre pseudo</h2>
            <?php if ($message): ?>
                <p class="error"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <form action="index.php" method="post" class="form-pseudo">
                <input type="text" name="username" placeholder="Votre pseudo" required>
                <select class="select" name="theme" required>
                    <option value="vikings">5 paires - Vikings</option>
                    <option value="got">10 paires - Game of Thrones</option>
                </select>
                <button class="btn" type="submit">Jouer</button>
            </form>
        </div>

        <div class="game-grid">
            <?php
            require_once __DIR__ . '/classes/Card.php';
            require_once __DIR__ . '/classes/CardGot.php';
            // Vikings
            $vikings = [1, 2, 3, 4, 5];
            // GOT
            $got = [
                "arya.jpg",
                "daenerys.jpg",
                "drogo.jpg",
                "hodor.jpg",
                "jon.jpg",
                "ned.jpg",
                "ramsay.jpg",
                "samwell.png",
                "tyrion.jpg",
                "walder.jpg"
            ];
            // Mélange des deux jeux
            $cards = [];
            foreach ($vikings as $v) {
                $cards[] = new Card($v);
            }
            foreach ($got as $g) {
                $cards[] = new CardGot($g);
            }
            // Mélange aléatoire
            shuffle($cards);
            foreach ($cards as $card):
                $imgPath = $card->getImagePath();
            ?>
                <div>
                    <button class="card" disabled>
                        <img src="<?= $imgPath ?>" alt="carte" style="width:160px;height:220px;border-radius:12px;box-shadow:0 4px 16px #0002;">
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php
    $content = ob_get_clean();
    require_once __DIR__ . '/layout.php';
    ?>
</div>