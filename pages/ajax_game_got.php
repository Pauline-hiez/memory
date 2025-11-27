<?php
session_start();
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/CardGot.php';
require_once __DIR__ . '/../classes/Game.php';
require_once __DIR__ . '/../classes/GameManager.php';
require_once __DIR__ . '/../classes/Player.php';
require_once __DIR__ . '/../classes/GameRepository.php';

header('Content-Type: application/json');

if (!isset($_SESSION['player'])) {
    echo json_encode(['error' => 'not_logged_in']);
    exit;
}

$game = isset($_SESSION['game_got']) ? unserialize($_SESSION['game_got']) : null;

if (isset($_POST['card_index'])) {
    GameManager::revealCard((int)$_POST['card_index'], 'got');
}
if (isset($_POST['action']) && $_POST['action'] == 'continue') {
    GameManager::restTemp('got');
}

$deck = $game ? $game->getDeck() : [];
$cards = [];
foreach ($deck as $i => $card) {
    $showFace = $card->isReveled() || (isset($_SESSION['temps_reveal_got']) && in_array($i, $_SESSION['temps_reveal_got']));
    $cards[] = [
        'revealed' => $card->isReveled(),
        'image' => $showFace ? $card->getImagePath() : '/memory/assets/img/got/carte.jpg',
    ];
}

$response = [
    'cards' => $cards,
    'moves' => $game ? $game->getMoves() : 0,
    'duration' => $game ? $game->getDuration() : 0,
    'disableClick' => isset($_SESSION['temps_reveal_got']) && count($_SESSION['temps_reveal_got']) === 2 && (!isset($_SESSION['last_pair_got']) || $_SESSION['last_pair_got'] === false),
    'needContinue' => isset($_SESSION['temps_reveal_got']) && count($_SESSION['temps_reveal_got']) === 2,
    'isEnd' => $game ? $game->isEnd() : false,
];

echo json_encode($response);
