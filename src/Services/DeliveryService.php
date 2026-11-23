<?php
declare(strict_types=1);

namespace LifeXP\Services;

use DateTimeImmutable;
use LifeXP\Core\Session;
use PDO;
use Throwable;

final class DeliveryService
{
    private const MAIN_SLOTS = 1;
    private const MAX_OPEN_BONUS = 2;
    private const BONUS_OFFER_HOURS = 24;
    private const REPEAT_DAYS = 30;

    /** Minimum score => settings. The first band whose minimum the score reaches is used. */
    private const BANDS = [
        90 => ['side' => 5, 'bonus' => 2, 'light' => ['easy' => 20, 'medium' => 80], 'main' => ['medium' => 10, 'hard' => 40, 'epic' => 50]],
        80 => ['side' => 4, 'bonus' => 1, 'light' => ['easy' => 20, 'medium' => 80], 'main' => ['medium' => 10, 'hard' => 40, 'epic' => 50]],
        60 => ['side' => 3, 'bonus' => 0, 'light' => ['easy' => 40, 'medium' => 60], 'main' => ['medium' => 20, 'hard' => 60, 'epic' => 20]],
        30 => ['side' => 2, 'bonus' => 0, 'light' => ['easy' => 60, 'medium' => 40], 'main' => ['medium' => 60, 'hard' => 40]],
        0  => ['side' => 1, 'bonus' => 0, 'light' => ['easy' => 100],                'main' => ['medium' => 100]],
    ];

    /** Brings a player's quests up to date. A failure here is logged and never breaks a page. */
    public static function sync(PDO $pdo, int $userId): void
    {
        try {
            $summary = self::run($pdo, $userId, new DateTimeImmutable('now'));
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('LifeXP delivery sync failed: ' . $e->getMessage());
            return;
        }

        self::announce($summary);
    }

    /** Tells the player about quests that ran out of time */
    private static function announce(array $summary): void
    {
        foreach ($summary['expired'] ?? [] as $event) {
            $text = 'Time ran out on "' . $event['title'] . '".';
            if ($event['penalty'] > 0) {
                $text .= ' You lost ' . $event['penalty'] . ' XP.';
            }
            Session::flash('error', $text);
        }

        $old = (int) ($summary['old_level'] ?? 0);
        $new = (int) ($summary['new_level'] ?? 0);
        if ($new > 0 && $new < $old) {
            Session::flash('error', 'Your level dropped to ' . $new . ' (' . XpService::title($new) . ').');
        }
    }

    private static function run(PDO $pdo, int $userId, DateTimeImmutable $now): array
    {
        $nowStr = $now->format('Y-m-d H:i:s');
        $pdo->beginTransaction();

        // Locking the player's row means two tabs cannot deliver or penalise at the same moment
        $stmt = $pdo->prepare('SELECT onboarded, total_xp, level FROM users WHERE id = :u FOR UPDATE');
        $stmt->execute(['u' => $userId]);
        $row = $stmt->fetch();
        if (!$row || (int) $row['onboarded'] !== 1) {
            $pdo->rollBack();
            return [];
        }

        // Quests past their time limit expire, and main and side quests cost XP
        $stmt = $pdo->prepare(
            "SELECT id, title, quest_type, xp_reward FROM quests
             WHERE user_id = :u AND status = 'active'
               AND quest_type IN ('main','side','bonus')
               AND expires_at IS NOT NULL AND expires_at < :cut FOR UPDATE"
        );
        $stmt->execute(['u' => $userId, 'cut' => $nowStr]);
        $expired = $stmt->fetchAll();

        $oldLevel = (int) $row['level'];
        $newLevel = $oldLevel;
        $events   = [];

        if ($expired) {
            $total  = (int) $row['total_xp'];
            $mark   = $pdo->prepare("UPDATE quests SET status = 'expired', resolved_at = :now WHERE id = :id");
            $ledger = $pdo->prepare(
                'INSERT INTO xp_ledger (user_id, delta, reason, quest_id, created_at)
                 VALUES (:u, :d, :r, :q, :at)'
            );

            foreach ($expired as $q) {
                $mark->execute(['now' => $nowStr, 'id' => $q['id']]);

                $penalty = min(XpService::penaltyFor((string) $q['quest_type'], (int) $q['xp_reward']), $total);
                if ($penalty > 0) {
                    $ledger->execute([
                        'u'  => $userId,
                        'd'  => -$penalty,
                        'r'  => 'quest_expired',
                        'q'  => $q['id'],
                        'at' => $nowStr,
                    ]);
                    $total -= $penalty;
                }
                $events[] = ['title' => $q['title'], 'type' => $q['quest_type'], 'penalty' => $penalty];
            }

            if ($total !== (int) $row['total_xp']) {
                $newLevel = XpService::levelForXp($total);
                $pdo->prepare('UPDATE users SET total_xp = :xp, level = :lvl WHERE id = :u')
                    ->execute(['xp' => $total, 'lvl' => $newLevel, 'u' => $userId]);
            }
        }

        $summary = ['expired' => $events, 'old_level' => $oldLevel, 'new_level' => $newLevel];

        // Offers nobody answered lapse at no cost
        $pdo->prepare(
            "UPDATE quests SET status = 'declined', resolved_at = :now
             WHERE user_id = :u AND status = 'offered'
               AND expires_at IS NOT NULL AND expires_at < :cut"
        )->execute(['now' => $nowStr, 'u' => $userId, 'cut' => $nowStr]);

        // Hidden score and the band it falls into
        $score = ProductivityService::score($pdo, $userId, $now);
        $pdo->prepare('UPDATE users SET productivity_score = :s WHERE id = :u')
            ->execute(['s' => $score, 'u' => $userId]);
        $band = self::band($score);

        // What the player already holds
        $open = ['main' => 0, 'side' => 0, 'bonus' => 0];
        $stmt = $pdo->prepare(
            "SELECT quest_type, status, COUNT(*) AS n FROM quests
             WHERE user_id = :u AND quest_type IN ('main','side','bonus') AND status IN ('active','offered')
             GROUP BY quest_type, status"
        );
        $stmt->execute(['u' => $userId]);
        foreach ($stmt->fetchAll() as $r) {
            if ($r['quest_type'] === 'bonus') {
                $open['bonus'] += (int) $r['n'];
            } elseif ($r['status'] === 'active') {
                $open[$r['quest_type']] += (int) $r['n'];
            }
        }

        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM quests WHERE user_id = :u AND quest_type = 'bonus' AND delivered_at >= :t"
        );
        $stmt->execute(['u' => $userId, 't' => $now->setTime(0, 0)->format('Y-m-d H:i:s')]);
        $offersToday = (int) $stmt->fetchColumn();

        $stmt = $pdo->prepare('SELECT category_id FROM user_interests WHERE user_id = :u');
        $stmt->execute(['u' => $userId]);
        $categoryIds = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
        if (!$categoryIds) {
            $pdo->commit();
            return $summary;
        }

        $context = self::context($pdo, $userId, $now);

        $bonusRoom = min($band['bonus'] - $offersToday, self::MAX_OPEN_BONUS - $open['bonus']);
        $plans = [
            ['main',  self::MAIN_SLOTS - $open['main'],  $band['main']],
            ['side',  $band['side'] - $open['side'],     $band['light']],
            ['bonus', $bonusRoom,                         $band['light']],
        ];

        foreach ($plans as [$type, $count, $weights]) {
            if ($count > 0) {
                self::deliver($pdo, $userId, $type, $count, $categoryIds, $weights, $context, $now);
            }
        }

        $pdo->commit();
        return $summary;
    }

    private static function band(int $score): array
    {
        foreach (self::BANDS as $minimum => $settings) {
            if ($score >= $minimum) {
                return $settings;
            }
        }
        return self::BANDS[0];
    }

    /** Things that tilt the random choice: ticked tracks, recent categories, categories already open */
    private static function context(PDO $pdo, int $userId, DateTimeImmutable $now): array
    {
        $tracks = [];
        $stmt = $pdo->prepare(
            'SELECT t.category_id, t.track
             FROM user_interest_templates uit
             JOIN quest_templates t ON t.id = uit.template_id
             WHERE uit.user_id = :u'
        );
        $stmt->execute(['u' => $userId]);
        foreach ($stmt->fetchAll() as $r) {
            $tracks[(int) $r['category_id'] . '|' . $r['track']] = true;
        }

        $affinity = [];
        $stmt = $pdo->prepare(
            'SELECT category_id, COUNT(*) AS n FROM quest_completions
             WHERE user_id = :u AND category_id IS NOT NULL AND completed_at >= :since
             GROUP BY category_id'
        );
        $stmt->execute(['u' => $userId, 'since' => $now->modify('-30 days')->format('Y-m-d H:i:s')]);
        foreach ($stmt->fetchAll() as $r) {
            $affinity[(int) $r['category_id']] = (int) $r['n'];
        }

        $openCategories = [];
        $stmt = $pdo->prepare(
            "SELECT category_id, COUNT(*) AS n FROM quests
             WHERE user_id = :u AND status IN ('active','offered') AND quest_type IN ('main','side','bonus')
             GROUP BY category_id"
        );
        $stmt->execute(['u' => $userId]);
        foreach ($stmt->fetchAll() as $r) {
            $openCategories[(int) $r['category_id']] = (int) $r['n'];
        }

        return ['tracks' => $tracks, 'affinity' => $affinity, 'open' => $openCategories];
    }

    private static function deliver(
        PDO $pdo,
        int $userId,
        string $type,
        int $count,
        array $categoryIds,
        array $weights,
        array &$context,
        DateTimeImmutable $now
    ): void {
        $candidates = self::candidates($pdo, $userId, $type, $categoryIds, $now);

        for ($i = 0; $i < $count && $candidates; $i++) {
            $index  = self::choose($candidates, $weights, $context);
            $picked = $candidates[$index];
            array_splice($candidates, $index, 1);

            self::insertQuest($pdo, $userId, (int) $picked['id'], $type, $now);

            $categoryId = (int) $picked['category_id'];
            $context['open'][$categoryId] = ($context['open'][$categoryId] ?? 0) + 1;
        }
    }

    /** Active templates of this type in the player's categories, avoiding open and recent ones when possible */
    private static function candidates(PDO $pdo, int $userId, string $type, array $categoryIds, DateTimeImmutable $now): array
    {
        $params = [
            'u1'    => $userId,
            'u2'    => $userId,
            'since' => $now->modify('-' . self::REPEAT_DAYS . ' days')->format('Y-m-d H:i:s'),
            'type'  => $type,
        ];
        $holders = [];
        foreach ($categoryIds as $i => $categoryId) {
            $holders[] = ':c' . $i;
            $params['c' . $i] = $categoryId;
        }

        $stmt = $pdo->prepare(
            "SELECT t.id, t.category_id, t.track, t.difficulty,
                    EXISTS (SELECT 1 FROM quests q
                            WHERE q.user_id = :u1 AND q.template_id = t.id
                              AND q.status IN ('active','offered')) AS is_open,
                    EXISTS (SELECT 1 FROM quests q
                            WHERE q.user_id = :u2 AND q.template_id = t.id
                              AND q.delivered_at >= :since) AS is_recent
             FROM quest_templates t
             WHERE t.is_active = 1 AND t.quest_type = :type
               AND t.category_id IN (" . implode(',', $holders) . ')'
        );
        $stmt->execute($params);

        $usable = array_values(array_filter($stmt->fetchAll(), fn (array $r) => (int) $r['is_open'] === 0));
        $fresh  = array_values(array_filter($usable, fn (array $r) => (int) $r['is_recent'] === 0));

        return $fresh ?: $usable;
    }

    /** Weighted random choice. Returns the index of the chosen candidate. */
    private static function choose(array $candidates, array $weights, array $context): int
    {
        $useBand = false;
        foreach ($candidates as $c) {
            if (($weights[$c['difficulty']] ?? 0) > 0) {
                $useBand = true;
                break;
            }
        }

        $scores = [];
        $total  = 0;
        foreach ($candidates as $i => $c) {
            $categoryId = (int) $c['category_id'];
            $weight = (float) ($useBand ? ($weights[$c['difficulty']] ?? 0) : 1);

            if (isset($context['tracks'][$categoryId . '|' . $c['track']])) {
                $weight *= 3;
            }
            $weight *= 1 + min(10, $context['affinity'][$categoryId] ?? 0) / 10;
            if (($context['open'][$categoryId] ?? 0) > 0) {
                $weight *= 0.4;
            }

            $scores[$i] = max(0, (int) round($weight * 100));
            $total += $scores[$i];
        }

        if ($total <= 0) {
            return 0;
        }

        $roll = random_int(1, $total);
        foreach ($scores as $i => $score) {
            $roll -= $score;
            if ($roll <= 0) {
                return $i;
            }
        }
        return 0;
    }

    private static function insertQuest(PDO $pdo, int $userId, int $templateId, string $type, DateTimeImmutable $now): void
    {
        $stmt = $pdo->prepare('SELECT * FROM quest_templates WHERE id = :id');
        $stmt->execute(['id' => $templateId]);
        $t = $stmt->fetch();
        if (!$t) {
            return;
        }

        $hours   = (int) $t['time_limit_hours'];
        $isBonus = $type === 'bonus';
        $expires = $isBonus
            ? $now->modify('+' . self::BONUS_OFFER_HOURS . ' hours')
            : $now->modify('+' . $hours . ' hours');

        $pdo->prepare(
            'INSERT INTO quests
                (user_id, category_id, quest_type, template_id, title, summary, description, why,
                 difficulty, xp_reward, due_date, expires_at, status, delivered_at)
             VALUES
                (:u, :cat, :type, :tpl, :title, :summary, :descr, :why,
                 :diff, :xp, :due, :exp, :status, :at)'
        )->execute([
            'u'       => $userId,
            'cat'     => (int) $t['category_id'],
            'type'    => $type,
            'tpl'     => $templateId,
            'title'   => $t['title'],
            'summary' => $t['summary'],
            'descr'   => $t['description'],
            'why'     => $t['why'],
            'diff'    => $t['difficulty'],
            'xp'      => XpService::questXp((string) $t['difficulty'], $hours),
            'due'     => $isBonus ? null : $expires->format('Y-m-d'),
            'exp'     => $expires->format('Y-m-d H:i:s'),
            'status'  => $isBonus ? 'offered' : 'active',
            'at'      => $now->format('Y-m-d H:i:s'),
        ]);
    }
}