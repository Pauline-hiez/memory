<?php
session_start();
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Card.php';
require_once __DIR__ . '/../classes/Game.php';
require_once __DIR__ . '/../classes/GameManager.php';
require_once __DIR__ . '/../classes/Player.php';
require_once __DIR__ . '/../classes/GameRepository.php';

header('Content-Type: application/json');

if (!isset($_SESSION['player'])) {
    echo json_encode(['error' => 'not_logged_in']);
    exit;
}

$game = GameManager::getGame();

if (isset($_POST['card_index'])) {
    GameManager::revealCard((int)$_POST['card_index']);
}
if (isset($_POST['action']) && $_POST['action'] == 'continue') {
    GameManager::restTemp();
}

$deck = $game->getDeck();
$cards = [];
foreach ($deck as $i => $card) {
    $showFace = $card->isReveled() || (isset($_SESSION['temps_reveal']) && in_array($i, $_SESSION['temps_reveal']));
    $cards[] = [
        'revealed' => $card->isReveled(),
        'image' => $showFace ? $card->getImagePath() : '/memory/assets/img/vikings/carte.jpg',
    ];
}

$response = [
    'cards' => $cards,
    'moves' => $game->getMoves(),
    'duration' => $game->getDuration(),
    'disableClick' => isset($_SESSION['temps_reveal']) && count($_SESSION['temps_reveal']) === 2 && (!isset($_SESSION['last_pair']) || $_SESSION['last_pair'] === false),
    'needContinue' => isset($_SESSION['temps_reveal']) && count($_SESSION['temps_reveal']) === 2,
    'isEnd' => $game->isEnd(),
];

echo json_encode($response);
