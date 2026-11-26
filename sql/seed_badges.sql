SET @fit   = (SELECT id FROM categories WHERE name = 'Fitness');
SET @study = (SELECT id FROM categories WHERE name = 'Study');
SET @heal  = (SELECT id FROM categories WHERE name = 'Health');
SET @soc   = (SELECT id FROM categories WHERE name = 'Social');
SET @crea  = (SELECT id FROM categories WHERE name = 'Creativity');
SET @chor  = (SELECT id FROM categories WHERE name = 'Chores');
SET @nutr  = (SELECT id FROM categories WHERE name = 'Nutrition');
SET @mind  = (SELECT id FROM categories WHERE name = 'Mindfulness');
SET @fin   = (SELECT id FROM categories WHERE name = 'Finance');
SET @car   = (SELECT id FROM categories WHERE name = 'Career');
SET @out   = (SELECT id FROM categories WHERE name = 'Outdoors');
SET @lang  = (SELECT id FROM categories WHERE name = 'Languages');

INSERT IGNORE INTO badges (code, name, description, icon, category_id, track, metric, threshold, tier, sort_order) VALUES
-- Fitness
('cat_fitness_1','Rookie Athlete','Earn 500 XP from Fitness quests','128170',@fit,NULL,'category_xp',500,1,10),
('cat_fitness_2','Iron Regular','Earn 2,500 XP from Fitness quests','128170',@fit,NULL,'category_xp',2500,2,20),
('cat_fitness_3','Peak Performer','Earn 10,000 XP from Fitness quests','128170',@fit,NULL,'category_xp',10000,3,30),
('cat_fitness_4','Olympian','Earn 50,000 XP from Fitness quests','128170',@fit,NULL,'category_xp',50000,4,40),
-- Study
('cat_study_1','Curious Mind','Earn 500 XP from Study quests','128218',@study,NULL,'category_xp',500,1,10),
('cat_study_2','Dedicated Scholar','Earn 2,500 XP from Study quests','128218',@study,NULL,'category_xp',2500,2,20),
('cat_study_3','Professor','Earn 10,000 XP from Study quests','128218',@study,NULL,'category_xp',10000,3,30),
('cat_study_4','Einstein','Earn 50,000 XP from Study quests','128218',@study,NULL,'category_xp',50000,4,40),
-- Health
('cat_health_1','Healthy Start','Earn 500 XP from Health quests','128154',@heal,NULL,'category_xp',500,1,10),
('cat_health_2','Balanced Body','Earn 2,500 XP from Health quests','128154',@heal,NULL,'category_xp',2500,2,20),
('cat_health_3','Picture of Health','Earn 10,000 XP from Health quests','128154',@heal,NULL,'category_xp',10000,3,30),
('cat_health_4','Vitality Legend','Earn 50,000 XP from Health quests','128154',@heal,NULL,'category_xp',50000,4,40),
-- Social
('cat_social_1','Friendly Face','Earn 500 XP from Social quests','129309',@soc,NULL,'category_xp',500,1,10),
('cat_social_2','Connector','Earn 2,500 XP from Social quests','129309',@soc,NULL,'category_xp',2500,2,20),
('cat_social_3','Community Pillar','Earn 10,000 XP from Social quests','129309',@soc,NULL,'category_xp',10000,3,30),
('cat_social_4','Heart of the Guild','Earn 50,000 XP from Social quests','129309',@soc,NULL,'category_xp',50000,4,40),
-- Creativity
('cat_creativity_1','Doodler','Earn 500 XP from Creativity quests','127912',@crea,NULL,'category_xp',500,1,10),
('cat_creativity_2','Maker','Earn 2,500 XP from Creativity quests','127912',@crea,NULL,'category_xp',2500,2,20),
('cat_creativity_3','Visionary','Earn 10,000 XP from Creativity quests','127912',@crea,NULL,'category_xp',10000,3,30),
('cat_creativity_4','Da Vinci','Earn 50,000 XP from Creativity quests','127912',@crea,NULL,'category_xp',50000,4,40),
-- Chores
('cat_chores_1','Tidy Beginner','Earn 500 XP from Chores quests','129529',@chor,NULL,'category_xp',500,1,10),
('cat_chores_2','Household Hero','Earn 2,500 XP from Chores quests','129529',@chor,NULL,'category_xp',2500,2,20),
('cat_chores_3','Domestic Champion','Earn 10,000 XP from Chores quests','129529',@chor,NULL,'category_xp',10000,3,30),
('cat_chores_4','Master of the Manor','Earn 50,000 XP from Chores quests','129529',@chor,NULL,'category_xp',50000,4,40),
-- Nutrition
('cat_nutrition_1','Fresh Starter','Earn 500 XP from Nutrition quests','129367',@nutr,NULL,'category_xp',500,1,10),
('cat_nutrition_2','Home Cook','Earn 2,500 XP from Nutrition quests','129367',@nutr,NULL,'category_xp',2500,2,20),
('cat_nutrition_3','Gourmet','Earn 10,000 XP from Nutrition quests','129367',@nutr,NULL,'category_xp',10000,3,30),
('cat_nutrition_4','Grand Chef','Earn 50,000 XP from Nutrition quests','129367',@nutr,NULL,'category_xp',50000,4,40),
-- Mindfulness
('cat_mindfulness_1','Calm Beginner','Earn 500 XP from Mindfulness quests','129496',@mind,NULL,'category_xp',500,1,10),
('cat_mindfulness_2','Steady Mind','Earn 2,500 XP from Mindfulness quests','129496',@mind,NULL,'category_xp',2500,2,20),
('cat_mindfulness_3','Inner Peace','Earn 10,000 XP from Mindfulness quests','129496',@mind,NULL,'category_xp',10000,3,30),
('cat_mindfulness_4','Enlightened','Earn 50,000 XP from Mindfulness quests','129496',@mind,NULL,'category_xp',50000,4,40),
-- Finance
('cat_finance_1','Penny Saver','Earn 500 XP from Finance quests','128176',@fin,NULL,'category_xp',500,1,10),
('cat_finance_2','Budget Keeper','Earn 2,500 XP from Finance quests','128176',@fin,NULL,'category_xp',2500,2,20),
('cat_finance_3','Money Wise','Earn 10,000 XP from Finance quests','128176',@fin,NULL,'category_xp',10000,3,30),
('cat_finance_4','Tycoon','Earn 50,000 XP from Finance quests','128176',@fin,NULL,'category_xp',50000,4,40),
-- Career
('cat_career_1','Intern','Earn 500 XP from Career quests','128188',@car,NULL,'category_xp',500,1,10),
('cat_career_2','Rising Star','Earn 2,500 XP from Career quests','128188',@car,NULL,'category_xp',2500,2,20),
('cat_career_3','Professional','Earn 10,000 XP from Career quests','128188',@car,NULL,'category_xp',10000,3,30),
('cat_career_4','Industry Leader','Earn 50,000 XP from Career quests','128188',@car,NULL,'category_xp',50000,4,40),
-- Outdoors
('cat_outdoors_1','Trail Starter','Earn 500 XP from Outdoors quests','127794',@out,NULL,'category_xp',500,1,10),
('cat_outdoors_2','Wanderer','Earn 2,500 XP from Outdoors quests','127794',@out,NULL,'category_xp',2500,2,20),
('cat_outdoors_3','Pathfinder','Earn 10,000 XP from Outdoors quests','127794',@out,NULL,'category_xp',10000,3,30),
('cat_outdoors_4','Wild Legend','Earn 50,000 XP from Outdoors quests','127794',@out,NULL,'category_xp',50000,4,40),
-- Languages
('cat_languages_1','Phrasebook','Earn 500 XP from Languages quests','127757',@lang,NULL,'category_xp',500,1,10),
('cat_languages_2','Conversationalist','Earn 2,500 XP from Languages quests','127757',@lang,NULL,'category_xp',2500,2,20),
('cat_languages_3','Fluent Speaker','Earn 10,000 XP from Languages quests','127757',@lang,NULL,'category_xp',10000,3,30),
('cat_languages_4','Polyglot','Earn 50,000 XP from Languages quests','127757',@lang,NULL,'category_xp',50000,4,40),
-- Tracks
('track_running_1','Road Runner','Earn 1,500 XP from running quests','127939',@fit,'running','track_xp',1500,2,51),
('track_running_2','Marathon Runner','Earn 7,500 XP from running quests','127939',@fit,'running','track_xp',7500,3,52),
('track_reading_1','Bookworm','Earn 1,500 XP from reading quests','128214',@study,'reading','track_xp',1500,2,51),
('track_reading_2','Grand Librarian','Earn 7,500 XP from reading quests','128214',@study,'reading','track_xp',7500,3,52),
('track_writing_1','Wordsmith','Earn 1,500 XP from writing quests','128221',@study,'writing','track_xp',1500,2,53),
('track_writing_2','Novelist','Earn 7,500 XP from writing quests','128221',@study,'writing','track_xp',7500,3,54),
('track_meditation_1','Mindful Sitter','Earn 1,500 XP from meditation quests','127807',@mind,'meditation','track_xp',1500,2,51),
('track_meditation_2','Meditation Master','Earn 7,500 XP from meditation quests','127807',@mind,'meditation','track_xp',7500,3,52),
('track_cooking_1','Kitchen Regular','Earn 1,500 XP from cooking quests','127859',@nutr,'cooking','track_xp',1500,2,51),
('track_cooking_2','Head Chef','Earn 7,500 XP from cooking quests','127859',@nutr,'cooking','track_xp',7500,3,52),
('track_budgeting_1','Careful Spender','Earn 1,500 XP from budgeting quests','128181',@fin,'budgeting','track_xp',1500,2,51),
('track_budgeting_2','Budget Boss','Earn 7,500 XP from budgeting quests','128181',@fin,'budgeting','track_xp',7500,3,52),
('track_hiking_1','Trail Walker','Earn 1,500 XP from hiking quests','129406',@out,'hiking','track_xp',1500,2,51),
('track_hiking_2','Summit Seeker','Earn 7,500 XP from hiking quests','129406',@out,'hiking','track_xp',7500,3,52),
('track_vocabulary_1','Word Collector','Earn 1,500 XP from vocabulary quests','128292',@lang,'vocabulary','track_xp',1500,2,51),
('track_vocabulary_2','Walking Dictionary','Earn 7,500 XP from vocabulary quests','128292',@lang,'vocabulary','track_xp',7500,3,52);