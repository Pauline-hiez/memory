<?php

class Game
{
    private array $deck = [];
    private int $moves = 0;
    private int $startTime;

    public function __construct()
    {
        $this->startTime = time();
        $this->initializeDeck();
    }

    public function initializeDeck(): void
    {
        $this->deck = [];
        $values = [1, 1, 2, 2, 3, 3, 4, 4, 5, 5]; //Tableau des cartes
        shuffle($values); //Mélange les cartes

        foreach ($values as $value) {
            if ($value >= 1 && $value <= 5) {
                $this->deck[] = new Card($value);
            }
        }
    }

    public function getDeck(): array
    {
        return $this->deck;
    }

    public function incrementMoves(): void
    {
        $this->moves++; //Incrémente le nombre de coups
    }

    public function getMoves(): int
    {
        return $this->moves; //Récupère le nombre de coups
    }

    public function getDuration(): int
    {
        return time() - $this->startTime; //Récupère le temps de jeu
    }

    public function isEnd(): bool
    {
        foreach ($this->deck as $d) {
            if (!$d->isReveled())
                return false; //Fin de partie
        }
        return true;
    }
}
