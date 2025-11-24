<?php

session_start();

require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Player.php';
require_once __DIR__ . '/../classes/GameRepository.php';

if (!isset($_SESSION['player'])) {
    header('Location: index.php');
    exit;
}

$player = unserialize($_SESSION['player']);
$history = GameRepository::getHistory($player);
$best = GameRepository::getBestForPlayer($player) ?? null;

$pageTitle = 'Profil';
ob_start();

?>

<div class="container">
    <div class="bulle">
        <h3>Profil de <?= htmlspecialchars($player->getLogin()) ?></h3>
        <p>Meilleur nombre de coups : <?= $best && isset($best['best_moves']) ? (int)$best['best_moves'] : '-' ?></p>
        <p>Meilleur temps : <?= $best && isset($best['best_time']) ? gmdate('i:s', (int)$best['best_time']) : '-' ?></p>
    </div>

    <div class="profil">
        <h4>Historique des parties</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Coups</th>
                    <th>Durée</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($history)): ?>
                    <tr>
                        <td colspan="3" class="center">Aucune partie</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($history as $g): ?>
                        <tr>
                            <td><?= (int)$g['moves'] ?></td>
                            <td><?= isset($g['time_seconds']) ? gmdate('i:s', (int)$g['time_seconds']) : '-' ?></td>
                            <td><?= htmlspecialchars($g['played_at'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
