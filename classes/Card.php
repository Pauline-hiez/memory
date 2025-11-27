<?php

class Card
{
    private int $value;
    private bool $reveled = false;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    // :int -> Doit retourner un entier
    public function getValue(): int
    {
        return $this->value;
    }

    public function isReveled(): bool
    {
        return $this->reveled;
    }

    // : void -> Ne doit rien retourner / Cache les cartes
    public function hide(): void
    {
        $this->reveled = false;
    }

    public function reveal(): void
    {
        $this->reveled = true;
    }

    public function getImagePath(): string
    {
        return "/memory/assets/img/vikings/" . $this->getImageName();
    }

    private function getImageName()
    {
        $map = [
            1 => "bjorn.jpg",
            2 => "ragnar.jpg",
            3 => "lagertha.jpg",
            4 => "ivar.jpg",
            5 => "floki.jpg"
        ];
        return $map[$this->value] ?? "carte.jpg";
    }
}
