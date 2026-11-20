SET @cat = (SELECT id FROM categories WHERE name = 'Health');

INSERT IGNORE INTO quest_templates
    (category_id, track, quest_type, title, summary, description, why, difficulty, time_limit_hours)
VALUES
-- Main quests
(@cat,'sleep','main','Seven hours of sleep for seven nights','Sleep at least seven hours every night for a week','Aim to be asleep for at least seven hours on each of seven nights in a row. If you miss one night, the week restarts. Note your bedtime and wake time each day.','A full week of enough sleep shows how much better you can feel and think.','medium',168),
(@cat,'sleep','main','Keep a fixed bedtime for two weeks','Go to bed within 30 minutes of the same time for 14 days','Choose a bedtime and stick to it, within 30 minutes either way, on 12 of the next 14 days.','A steady schedule is the strongest sleep habit you can build.','hard',336),
(@cat,'hydration','main','Two litres of water for two weeks','Drink around two litres of water on 12 of 14 days','Track your water and reach about two litres on at least 12 of the next 14 days. Tea and other drinks do not count.','Good hydration supports energy, skin, digestion and concentration.','medium',336),
(@cat,'screens','main','Screens off an hour before bed for three weeks','No screens for the hour before bed on 18 of 21 days','Stop using phones, tablets and TV one hour before your bedtime on at least 18 of the next 21 days.','Screen light and scrolling delay sleep and keep your mind active.','hard',504),
(@cat,'prevention','main','Book and attend a health check','Schedule and attend a routine appointment within 14 days','Book a routine check-up, such as a dentist, optician or doctor, and attend it within 14 days.','Prevention is much easier and cheaper than treatment.','medium',336),
(@cat,'habits','main','Build a morning routine for 30 days','Follow the same morning routine on 25 of 30 days','Design a short morning routine of about 15 minutes and follow it on at least 25 of the next 30 days.','A steady start gives the whole day a calmer shape.','epic',720),
(@cat,'posture','main','Improve your posture for three weeks','Do daily posture exercises and checks for 21 days','Do a short posture routine and set up your workspace properly. Complete it on at least 17 of 21 days.','Better posture reduces pain and helps you breathe and concentrate.','medium',504),
(@cat,'habits','main','Track one health habit for 30 days','Choose a habit and log it daily for a month','Choose one habit, such as stretching, vitamins or walking after meals, and log whether you did it each day for 30 days. Aim for at least 24 days.','What you measure improves, and a month of data shows what really works for you.','epic',720),

-- Side quests: sleep
(@cat,'sleep','side','Go to bed 30 minutes earlier','Move your bedtime earlier tonight','Go to bed 30 minutes earlier than usual tonight.','Extra sleep helps your mood, focus and immune system.','easy',24),
(@cat,'sleep','side','Wind down for 20 minutes before bed','Do something calm before sleeping','Spend 20 minutes before bed reading, stretching or doing something quiet and calm.','A wind-down tells your body it is time to sleep.','easy',24),
(@cat,'sleep','side','Make your bedroom cool and dark','Prepare your bedroom for better sleep','Close the curtains, lower the temperature and remove light sources from your bedroom.','A dark, cool room helps you fall asleep faster and sleep deeper.','easy',24),
(@cat,'sleep','side','No caffeine after 2pm','Skip coffee, tea and energy drinks after 2pm','Avoid all caffeine from 2pm until you go to sleep.','Caffeine stays in your body for hours and can ruin sleep.','easy',24),
(@cat,'sleep','side','Wake up at the same time as yesterday','Keep a steady wake-up time','Get up at the same time as yesterday, without extra snoozing.','A steady wake-up time sets your body clock.','easy',24),
(@cat,'sleep','side','Take a ten-minute afternoon rest','Rest quietly in the afternoon for ten minutes','Lie down or sit quietly for ten minutes in the afternoon, without your phone.','A short rest restores focus without hurting your night sleep.','easy',24),

-- Side quests: hydration
(@cat,'hydration','side','Drink a glass of water on waking','Start the day with water','Drink a full glass of water within 15 minutes of waking up.','You wake up mildly dehydrated, and water is the fastest fix.','easy',24),
(@cat,'hydration','side','Drink two litres of water','Reach two litres of water today','Track your water and reach about two litres before the day ends.','Reaching a clear number is the easiest way to stay hydrated.','medium',24),
(@cat,'hydration','side','Swap one sugary drink for water','Replace a sweet drink with water today','Pick one sugary or fizzy drink you would normally have and drink water instead.','Sugary drinks add a lot of calories without keeping you full.','easy',24),
(@cat,'hydration','side','Carry a water bottle all day','Keep a bottle with you everywhere today','Take a refillable water bottle with you all day and refill it at least once.','A bottle within reach makes drinking automatic.','easy',24),
(@cat,'hydration','side','Drink water with every meal','Have a glass of water with each meal today','Drink a glass of water with every meal you eat today.','Linking water to meals is an easy way to remember it.','easy',24),

-- Side quests: prevention
(@cat,'prevention','side','Book a dental or doctor appointment','Schedule one overdue check-up','Book a dental or doctor appointment you have been putting off.','Booking is the hardest part, and it can catch problems early.','easy',48),
(@cat,'prevention','side','Check your medicine cabinet','Remove expired items and restock basics','Go through your medicine cabinet, throw away expired items and note anything you need.','Expired or missing medicine is a problem you only notice when you need it.','easy',48),
(@cat,'prevention','side','Write down questions for your next appointment','Prepare questions before a health visit','Write down any questions or concerns for your next health appointment.','Prepared questions make a short appointment far more useful.','easy',48),
(@cat,'prevention','side','Check your vaccination record','Find out which vaccinations are up to date','Look up or ask for your vaccination record and note anything that is due.','Knowing what you are missing is the first step to staying protected.','easy',48),

-- Side quests: posture
(@cat,'posture','side','Adjust your desk and chair','Set up your workspace to suit your body','Adjust your chair height, screen level and keyboard position so your back and wrists are comfortable.','A good setup prevents back, neck and wrist pain over time.','easy',24),
(@cat,'posture','side','Five posture resets during the day','Check and fix your posture five times','Five times today, sit or stand tall with relaxed shoulders and a neutral spine.','Frequent resets stop slouching from becoming a habit.','easy',24),
(@cat,'posture','side','Ten minutes of back care','Do gentle back exercises for ten minutes','Do ten minutes of gentle exercises for your back, such as cat-cow and child pose.','Keeping your back mobile helps prevent stiffness and pain.','easy',24),
(@cat,'posture','side','Work standing for an hour','Stand while working or studying for an hour','Work at a high surface or counter for at least an hour today.','Alternating sitting and standing reduces strain on your back.','easy',24),
(@cat,'posture','side','Neck and shoulder release routine','Release tension in your neck and shoulders','Do a five to ten minute routine of gentle neck rolls and shoulder stretches.','Most desk tension builds up in the neck and shoulders.','easy',24),

-- Side quests: recovery
(@cat,'recovery','side','Take a full rest hour','One hour with no tasks or obligations','Take one full hour with no work, chores or errands, and do something calming.','Rest is part of staying healthy, not a reward for finishing everything.','easy',24),
(@cat,'recovery','side','Take a warm shower or bath to unwind','Use warm water to relax in the evening','Take a warm shower or bath in the evening to help your body wind down.','Warmth relaxes muscles and helps you fall asleep.','easy',24),
(@cat,'recovery','side','Spend 20 minutes outside in daylight','Get daylight for twenty minutes','Spend at least 20 minutes outdoors in natural light.','Daylight regulates your sleep cycle and lifts your mood.','easy',24),
(@cat,'recovery','side','Gentle recovery stretch','Do a slow, gentle stretch routine for ten minutes','Do ten minutes of slow, gentle stretching with deep breathing.','Gentle movement helps your body recover and relax.','easy',24),
(@cat,'recovery','side','Have a no-work evening','Switch off work or study for the whole evening','Stop all work and study at a set time and do not return to it until tomorrow.','A clear end to the working day helps you actually recover.','medium',24),

-- Side quests: habits
(@cat,'habits','side','Track your mood today','Note how you feel at three points in the day','Write down how you feel in the morning, afternoon and evening, with a word or two each time.','Noticing patterns in your mood helps you understand what affects it.','easy',24),
(@cat,'habits','side','Plan one health goal for the week','Choose a single health goal and write it down','Write down one specific health goal for this week and how you will do it.','A specific goal is far more likely to happen than a vague wish.','easy',24),
(@cat,'habits','side','Five-minute morning routine','Do a short morning routine from start to finish','Do a five-minute routine such as water, stretching and a few deep breaths.','A small routine is the easy way to begin a bigger one.','easy',24),
(@cat,'habits','side','Replace one unhealthy habit','Choose a better action for one habit today','Pick one habit you want to change and do a healthier action in its place today.','Replacing a habit works better than just trying to stop it.','medium',24),
(@cat,'habits','side','Air out your room','Open the windows and refresh your space','Open your windows for at least ten minutes to let fresh air in.','Fresh air lowers stuffiness and helps you think clearly.','easy',24),

-- Side quests: screens
(@cat,'screens','side','No screens for 30 minutes after waking','Start the morning without your phone','Leave your phone alone for the first 30 minutes after you wake up.','A calm start stops the day from beginning with other peoples demands.','medium',24),
(@cat,'screens','side','Take a one-hour screen break','Stay off screens for an hour','Choose an hour today to stay away from all screens and do something else.','Breaks from screens rest your eyes and your attention.','easy',24),
(@cat,'screens','side','Turn off non-essential notifications','Silence unnecessary alerts for a day','Switch off all notifications except calls and messages from important people for the whole day.','Fewer interruptions mean less stress and better focus.','medium',24),

-- Side quests: hygiene
(@cat,'hygiene','side','Brush properly morning and night','Brush for two minutes, twice today','Brush your teeth for a full two minutes in the morning and again before bed.','Good brushing prevents decay and gum disease.','easy',24),
(@cat,'hygiene','side','Clean your phone and keyboard','Wipe down the things you touch most','Clean your phone, keyboard and mouse with a suitable wipe.','These items collect more germs than most people realise.','easy',24),
(@cat,'hygiene','side','Change your bed sheets','Put fresh sheets on your bed','Wash and replace your bed sheets and pillowcases.','Fresh sheets feel good and support better sleep.','easy',48),

-- Bonus quests
(@cat,'recovery','bonus','Try a new relaxing activity','Do something calming you have never tried','Try an activity such as colouring, puzzles, journaling or gentle music.','Finding what truly relaxes you helps you recover faster.','easy',48),
(@cat,'screens','bonus','Spend an evening without your phone','Stay away from your phone from dinner until bed','Put your phone away after dinner and leave it alone until the morning.','An evening without your phone often feels longer and calmer.','medium',24),
(@cat,'habits','bonus','Have a screen-free meal','Eat one meal without any screens','Eat one meal today without a phone, TV or laptop.','You taste more and notice when you are full.','easy',24),
(@cat,'recovery','bonus','Get sunlight within an hour of waking','Step outside soon after you wake up','Spend at least five minutes in natural light within an hour of waking up.','Morning light sets your body clock and improves sleep that night.','easy',24),
(@cat,'recovery','bonus','Try a gentle breathing exercise','Breathe slowly for five minutes','Breathe in for four seconds, hold for four and breathe out for six, for five minutes.','Slow breathing calms your nervous system within minutes.','easy',24),
(@cat,'recovery','bonus','Book a massage or take a long bath','Treat your body to some real recovery','Book a massage or take a long, unhurried bath.','Treating your body well is part of caring for your health.','easy',48),
(@cat,'habits','bonus','Check in on a friend','Ask someone how they are really doing','Message or call a friend and ask how they are, then listen.','Good relationships are strongly linked to better health.','easy',48),
(@cat,'sleep','bonus','Read about sleep','Read one article on healthy sleep habits','Read one article from a trusted health source about sleep.','Knowing why sleep matters makes protecting it easier.','easy',24),
(@cat,'sleep','bonus','Tidy your bedroom for better sleep','Declutter the space where you sleep','Tidy your bedroom, clearing surfaces and putting things away.','A calm room helps a calm mind.','easy',24),
(@cat,'sleep','bonus','Try a warm caffeine-free drink before bed','Have a calming drink in the evening','Have a warm caffeine-free drink, such as herbal tea or warm milk, about an hour before bed.','A warm drink makes a simple, relaxing bedtime ritual.','easy',24),
(@cat,'screens','bonus','Take a day off social media','Avoid social media for a whole day','Stay off all social media apps for the full day.','A break shows how much time and energy social media takes.','medium',24),
(@cat,'sleep','bonus','Stretch before bed','Do five minutes of gentle stretching before sleep','Spend five minutes stretching gently before you go to bed.','Stretching releases the tension you carried through the day.','easy',24),
(@cat,'screens','bonus','Follow the 20-20-20 rule','Rest your eyes regularly while working','Every 20 minutes look at something 20 feet away for 20 seconds, for the whole day.','Regular eye breaks reduce strain and headaches.','easy',24),
(@cat,'habits','bonus','Write three things you are grateful for','Note three good things from your day','Write down three things you are grateful for today.','Gratitude is linked to better mood and better sleep.','easy',24),
(@cat,'prevention','bonus','Make an emergency contact list','Write down key contacts and health information','Write a list of emergency contacts, allergies and any medicines you take, and keep it somewhere handy.','If something goes wrong, this list saves time when it matters most.','easy',48),
(@cat,'sleep','bonus','Sleep with your phone out of the bedroom','Charge your phone in another room tonight','Leave your phone to charge outside the bedroom overnight.','Removing the phone removes the temptation to scroll late at night.','medium',24);