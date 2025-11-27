<?php

class GameManager
{
    public static function startNew(): void
    {
        unset($_SESSION['game']);
        unset($_SESSION['temps_reveal']);
        unset($_SESSION['last_pair']);
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

        if ($deck[$index]->isReveled()) return;
        if (in_array($index, $_SESSION['temps_reveal'])) return;

        $_SESSION['temps_reveal'][] = $index;



        if (count($_SESSION['temps_reveal']) === 2) {
            $a = $_SESSION['temps_reveal'][0];
            $b = $_SESSION['temps_reveal'][1];

            $game->incrementMoves();

            if ($deck[$a]->getValue() === $deck[$b]->getValue()) {
                $deck[$a]->reveal();
                $deck[$b]->reveal();
                $_SESSION['last_pair'] = true;
            } else {
                $_SESSION['last_pair'] = false;
            }
        }

        self::saveGame($game);
    }

    // Reinitialise le temps
    public static function restTemp()
    {
        if (!isset($_SESSION['temps_reveal']) || count($_SESSION['temps_reveal']) !== 2) {
            return;
        }
        $game = self::getGame();
        $deck = $game->getDeck();
        $a = $_SESSION['temps_reveal'][0];
        $b = $_SESSION['temps_reveal'][1];

        // On ne cache les cartes que si ce n'était pas une paire
        if (count($_SESSION['temps_reveal']) === 2 && $deck[$a]->getValue() != $deck[$b]->getValue()) {
            $deck[$a]->hide();
            $deck[$b]->hide();
        }
        // }
        // On vide la sélection temporaire et le flag
        $_SESSION['temps_reveal'] = [];
        unset($_SESSION['last_pair']);
        self::saveGame($game);
    }
}
