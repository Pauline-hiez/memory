<?php

class GameRepository
{
    public static function recordGame(Player $player, Game $game): void
    {
        $pdo = Database::getConnexion();
        $stmt = $pdo->prepare("
            INSERT INTO games (player_id, moves, time_seconds)
            VALUES (?, ?, ?)
        ");

        $stmt->execute([
            $player->getId(),
            $game->getMoves(),
            $game->getDuration()
        ]);
    }

    // Appelle le top10 en fonction du temps et du nombre de coups
    public static function top10(): array
    {
        $pdo = Database::getConnexion();
        return $pdo->query("
            SELECT p.username, g.moves, g.time_seconds, g.played_at
            FROM games g
            JOIN players p ON g.player_id = p.id
            ORDER BY g.moves ASC, g.time_seconds ASC
            LIMIT 10
        ")->fetchAll();
    }

    // Récupère l'historique de jeu
    public static function getHistory(Player $p): array
    {
        $pdo = Database::getConnexion();
        $stmt = $pdo->prepare("
            SELECT * FROM games
            WHERE player_id = ?
            ORDER BY played_at DESC
        ");

        $stmt->execute([$p->getID()]);
        return $stmt->fetchAll();
    }

    public static function getBestForPlayer(Player $p): ?array
    {
        $pdo = Database::getConnexion();
        $stmt = $pdo->prepare("
            SELECT MIN(moves) as best_moves, MIN(time_seconds) as best_time
            FROM games
            WHERE player_id = ?
        ");

        $stmt->execute([$p->getId()]);
        $result = $stmt->fetch();

        return ($result && $result['best_moves']) ? $result : null;
    }
}
