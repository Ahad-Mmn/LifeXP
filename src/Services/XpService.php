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
}