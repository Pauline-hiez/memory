<?php

class GameManager
{
    public static function startNew(): void
    {
        $_SESSION['game'] = serialize(new Game());
        $_SESSION['temps_reveal'] = [];
    }

    public static function getGame(): ?Game
    {
        if (!isset($_SESSION['game']) || empty($_SESSION['game'])) {
            return null;
        }
        return unserialize($_SESSION['game']);
    }

    public static function saveGame(Game $game): void
    {
        $_SESSION['game'] = serialize($game);
    }

    public static function revealCard(int $index): void
    {
        $game = self::getGame();
        $deck = $game->getDeck();

        if ($deck[$index]->isReveal()) return;
        if (in_array($index, $_SESSION['temps_reveal'])) return;

        $_SESSION['temps_reveal'][] = $index;

        if (count($_SESSION['temps_reveal']) === 2) {
            $a = $_SESSION['temps_reveal'][0];
            $b = $_SESSION['temps_reveal'][1];

            $game->incrementMoves();

            if ($deck[$a]->getValue() === $deck[$b]->getValue()) {
                $deck[$a]->reveal();
                $deck[$b]->reveal();
                $_SESSION['temps_reveal'] = [];
            }
        }
        self::saveGame($game);
    }

    // Reinitialise le temps
    public static function restTemp(): void
    {
        $game = self::getGame();
        $deck = $game->getDeck();

        if (count($_SESSION['temps_reveal']) === 2) {
            $a = $_SESSION['temps_reveal'][0];
            $b = $_SESSION['temps_reveal'][1];
            if ($deck[$a]->getValue() !== $deck[$b]->getValue()) {
                $deck[$a]->hide();
                $deck[$b]->hide;
            }
        }
        $_SESSION['temp_reveal'] = [];
        self::saveGame($game);
    }
}
