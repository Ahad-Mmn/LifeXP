<?php
declare(strict_types=1);

namespace LifeXP\Services;

final class XpService
{
    private const BASE = 50;

    public static function levelForXp(int $xp): int
    {
        return (int) floor(sqrt($xp / self::BASE)) + 1;
    }

    /** Total XP needed to reach a given level */
    public static function xpForLevel(int $level): int
    {
        return self::BASE * (($level - 1) ** 2);
    }

    public static function title(int $level): string
    {
        return match (true) {
            $level >= 12 => 'Legend',
            $level >= 8  => 'Hero',
            $level >= 5  => 'Adventurer',
            $level >= 3  => 'Apprentice',
            default      => 'Novice',
        };
    }

    /** Everything the dashboard progress bar needs */
    public static function progress(int $xp): array
    {
        $level  = self::levelForXp($xp);
        $start  = self::xpForLevel($level);
        $next   = self::xpForLevel($level + 1);
        $inLvl  = $xp - $start;
        $needed = $next - $start;

        return [
            'level'       => $level,
            'title'       => self::title($level),
            'xp_in_level' => $inLvl,
            'xp_needed'   => $needed,
            'percent'     => (int) floor($inLvl / $needed * 100),
        ];
    }

    /** +10% per streak day after the first, maximum +50% */
    public static function streakBonusPercent(int $streak): int
    {
        return min(max($streak - 1, 0), 5) * 10;
    }

    /** A streak only counts if the last activity was today or yesterday */
    public static function activeStreak(int $streak, ?string $lastActive): int
    {
        if ($lastActive === null) {
            return 0;
        }
        $today     = (new \DateTimeImmutable('today'))->format('Y-m-d');
        $yesterday = (new \DateTimeImmutable('yesterday'))->format('Y-m-d');

        return ($lastActive === $today || $lastActive === $yesterday) ? $streak : 0;
    }

        /** XP by difficulty and time limit, keyed by days */
    private const TIME_XP = [
        'easy'   => [1 => 10, 2 => 15, 3 => 20, 7 => 30,  14 => 45,  21 => 55,  30 => 70],
        'medium' => [1 => 20, 2 => 30, 3 => 35, 7 => 60,  14 => 90,  21 => 110, 30 => 140],
        'hard'   => [1 => 35, 2 => 50, 3 => 65, 7 => 105, 14 => 160, 21 => 195, 30 => 245],
        'epic'   => [1 => 50, 2 => 70, 3 => 90, 7 => 150, 14 => 225, 21 => 275, 30 => 350],
    ];

    /** Reward for a quest, from its difficulty and time limit in hours */
    public static function questXp(string $difficulty, int $hours): int
    {
        $table = self::TIME_XP[$difficulty] ?? self::TIME_XP['easy'];
        $days  = max(1, (int) ceil($hours / 24));
        $xp    = reset($table);

        foreach ($table as $limit => $value) {
            if ($days >= $limit) {
                $xp = $value;
            }
        }
        return $xp;
    }
}