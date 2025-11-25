<?php


session_start();
$message = '';

require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/Player.php';

$db = Database::getConnexion();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['username'])) {
    $username = trim($_POST['username']);

    $stmt = $db->prepare("
        SELECT id FROM players WHERE username = ?
    ");

    $stmt->execute([$username]);
    $row = $stmt->fetch();

    if ($row) {
        $playerId = (int)$row['id'];
    } else {
        $ins = $db->prepare("
            INSERT INTO players (username)
            VALUES (?)
        ");

        $ins->execute([$username]);
        $playerId = (int)$db->lastInsertId();
    }

    //Instancie Player
    $player = new Player($playerId, $username);

    //Sauvegarde en session
    $_SESSION['player'] = serialize($player);

    // Redirection vers le jeu
    header('Location: pages/game.php');
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
            <button class="btn" type="submit">Jouer</button>
        </form>
    </div>

    <div class="game-grid">
        <?php
        require_once __DIR__ . '/classes/Card.php';
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
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/layout.php';
?>