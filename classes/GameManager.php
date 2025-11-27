<?php

class GameManager
{
    public static function startNew(string $theme = 'vikings', array $deck = null): void
    {
        if ($theme === 'got') {
            unset($_SESSION['game_got']);
            unset($_SESSION['temps_reveal_got']);
            unset($_SESSION['last_pair_got']);
            $_SESSION['game_got'] = isset($deck) ? serialize(new Game($deck)) : serialize(new Game());
            $_SESSION['temps_reveal_got'] = [];
        } else {
            unset($_SESSION['game']);
            unset($_SESSION['temps_reveal']);
            unset($_SESSION['last_pair']);
            $_SESSION['game'] = isset($deck) ? serialize(new Game($deck)) : serialize(new Game());
            $_SESSION['temps_reveal'] = [];
        }
    }

    public static function getGame(string $theme = 'vikings'): ?Game
    {
        if ($theme === 'got') {
            if (!isset($_SESSION['game_got']) || empty($_SESSION['game_got'])) {
                return null;
            }
            return unserialize($_SESSION['game_got']);
        } else {
            if (!isset($_SESSION['game']) || empty($_SESSION['game'])) {
                return null;
            }
            return unserialize($_SESSION['game']);
        }
    }

    public static function saveGame(Game $game, string $theme = 'vikings'): void
    {
        if ($theme === 'got') {
            $_SESSION['game_got'] = serialize($game);
        } else {
            $_SESSION['game'] = serialize($game);
        }
    }

    public static function revealCard(int $index, string $theme = 'vikings'): void
    {
        $game = self::getGame($theme);
        $deck = $game->getDeck();
        $temps_reveal = $theme === 'got' ? 'temps_reveal_got' : 'temps_reveal';
        $last_pair = $theme === 'got' ? 'last_pair_got' : 'last_pair';

        if ($deck[$index]->isReveled()) return;
        if (in_array($index, $_SESSION[$temps_reveal])) return;

        $_SESSION[$temps_reveal][] = $index;



        if (count($_SESSION[$temps_reveal]) === 2) {
            $a = $_SESSION[$temps_reveal][0];
            $b = $_SESSION[$temps_reveal][1];

            $game->incrementMoves();

            if ($deck[$a]->getValue() === $deck[$b]->getValue()) {
                $deck[$a]->reveal();
                $deck[$b]->reveal();
                $_SESSION[$last_pair] = true;
            } else {
                $_SESSION[$last_pair] = false;
            }
        }

        self::saveGame($game, $theme);
    }

    // Reinitialise le temps
    public static function restTemp(string $theme = 'vikings')
    {
        $temps_reveal = $theme === 'got' ? 'temps_reveal_got' : 'temps_reveal';
        $last_pair = $theme === 'got' ? 'last_pair_got' : 'last_pair';
        if (!isset($_SESSION[$temps_reveal]) || count($_SESSION[$temps_reveal]) !== 2) {
            return;
        }
        $game = self::getGame($theme);
        $deck = $game->getDeck();
        $a = $_SESSION[$temps_reveal][0];
        $b = $_SESSION[$temps_reveal][1];

        // On ne cache les cartes que si ce n'était pas une paire
        if (count($_SESSION[$temps_reveal]) === 2 && $deck[$a]->getValue() != $deck[$b]->getValue()) {
            $deck[$a]->hide();
            $deck[$b]->hide();
        }
        // }
        // On vide la sélection temporaire et le flag
        $_SESSION[$temps_reveal] = [];
        unset($_SESSION[$last_pair]);
        self::saveGame($game, $theme);
    }
}
