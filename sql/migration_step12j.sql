UPDATE quests SET difficulty = 'hard'
WHERE quest_type = 'custom' AND difficulty = 'epic';

UPDATE quests
SET xp_reward = CASE difficulty WHEN 'easy' THEN 5 WHEN 'medium' THEN 12 ELSE 25 END
WHERE quest_type = 'custom' AND status = 'active';