<?php

session_start();

require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/GameRepository.php';
require_once __DIR__ . '/../classes/Player.php';

$top = GameRepository::top10();

$pageTitle = 'Top 10';
ob_start();

?>

<div class="container">
    <div class="bulle">
        <div class="top-10-container">
            <div class="top-10">
                <img src="/memory/assets/img/winner.png" alt="winner" class="winner-icon">
                <h3 class="top-10-title">Top 10</h3>
                <img src="/memory/assets/img/winner.png" alt="winner" class="winner-icon">
            </div>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Joueur</th>
                <th>Coups</th>
                <th>Durée</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($top)): ?>
                <tr>
                    <td colspan="5" class="center">Aucune partie enregistrée</td>
                </tr>
            <?php else: ?>
                <?php $i = 1;
                foreach ($top as $row): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= (int)$row['moves'] ?></td>
                        <td><?= isset($row['time_seconds']) ? gmdate('i:s', (int)$row['time_seconds']) : '-' ?></td>
                        <td><?= htmlspecialchars($row['played_at'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout.php';
