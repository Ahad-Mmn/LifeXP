SET @cat = (SELECT id FROM categories WHERE name = 'Mindfulness');

INSERT IGNORE INTO quest_templates
    (category_id, track, quest_type, title, summary, description, why, difficulty, time_limit_hours)
VALUES
-- Main quests
(@cat,'meditation','main','Meditate for two weeks','Meditate for 10 minutes on 12 of 14 days','Sit quietly for at least 10 minutes a day, using a guided recording or a simple timer, on 12 of the next 14 days. When your mind wanders, gently return to your breath. Tick off each day.','Regular practice is what changes how you handle stress, not a single long session.','medium',336),
(@cat,'journaling','main','Keep a journal for 21 days','Write for at least 10 minutes on 17 of 21 days','Write in a journal for at least 10 minutes a day on 17 of the next 21 days. Write whatever is on your mind, with no rules about neatness or grammar.','Writing things down turns tangled thoughts into something you can look at and understand.','hard',504),
(@cat,'gratitude','main','Thirty days of gratitude','Write three things you are grateful for on 25 of 30 days','Each day write three specific things you are grateful for, and try not to repeat yourself. Complete this on at least 25 of the next 30 days.','Looking for good things trains your attention to notice them more often.','medium',720),
(@cat,'breathing','main','A week of daily breathing practice','Do a five-minute breathing exercise every day for seven days','Choose a technique such as slow breathing or box breathing and do it for five minutes every day for seven days in a row. If you miss a day, the week restarts.','Breathing is the one part of your nervous system you can control directly.','medium',168),
(@cat,'digital','main','Four screen-free Sunday mornings','Stay away from screens until noon on four Sundays','On four Sundays over the next 30 days, avoid phones, tablets, TV and computers until midday. Plan something else to do in that time.','Slow mornings give your mind a rest that scrolling never does.','hard',720),
(@cat,'nature','main','Time in nature, six times in two weeks','Spend 30 minutes in green space on six days in 14 days','Spend at least 30 minutes in a park, woodland, garden or other green space on six separate days within the next two weeks.','Time among plants and trees lowers stress and improves mood.','medium',336),
(@cat,'meditation','main','Complete a 30-day meditation challenge','Meditate on 26 of 30 days, building from 5 to 20 minutes','Start with five minutes a day and add a little each week until you can sit for 20 minutes. Meditate on at least 26 of the next 30 days.','A full month is long enough to feel the difference in how you react to things.','epic',720),
(@cat,'habits','main','Build a calm evening routine','Follow the same wind-down routine on 17 of 21 evenings','Design a short evening routine, such as tidying, a warm drink and some quiet time, and follow it on at least 17 of the next 21 evenings.','A calm ending to the day makes it easier to switch off and sleep.','hard',504),

-- Side quests: meditation
(@cat,'meditation','side','Five-minute guided meditation','Follow a short guided meditation','Play a five-minute guided meditation and follow it from start to finish.','Guided sessions are the easiest way to begin.','easy',24),
(@cat,'meditation','side','Ten minutes of silent sitting','Sit quietly for ten minutes with a timer','Set a timer for ten minutes and sit quietly, returning to your breath whenever you drift.','Silence teaches you how busy your mind really is.','medium',24),
(@cat,'meditation','side','Body scan meditation','Move your attention slowly through your body','Lie down and move your attention slowly from your feet to your head, noticing each part without judging it.','A body scan releases tension you did not know you were holding.','easy',24),
(@cat,'meditation','side','Fifteen-minute walking meditation','Walk slowly and pay attention to each step','Walk slowly for 15 minutes, noticing the feeling of each step and the sounds around you.','You can practise mindfulness without sitting still.','easy',24),
(@cat,'meditation','side','Meditate before breakfast','Start the day with a short sit','Meditate for at least five minutes before you eat or check your phone.','Starting calm sets the tone for the whole day.','easy',24),
(@cat,'meditation','side','Loving-kindness meditation','Wish good things for yourself and others','Spend ten minutes silently repeating kind wishes for yourself, someone you love and someone you find difficult.','Kindness practice softens self-criticism and irritation.','medium',24),
(@cat,'meditation','side','Meditate for twenty minutes','Sit for a longer session','Sit for 20 minutes, using a guided recording if that helps.','Longer sessions let the mind settle more deeply.','medium',24),

-- Side quests: breathing
(@cat,'breathing','side','Box breathing for five minutes','Breathe in, hold, out, hold, each for four seconds','Breathe in for four seconds, hold for four, breathe out for four and hold for four, repeating for five minutes.','Box breathing is a quick way to calm a racing mind.','easy',24),
(@cat,'breathing','side','Three slow breathing breaks','Pause three times today to take ten slow breaths','At three points in the day, stop and take ten slow, deep breaths.','Small pauses stop stress from building up.','easy',24),
(@cat,'breathing','side','Breathe before bed','Do a slow breathing exercise in bed','In bed, breathe in for four seconds, hold for seven and out for eight, for five rounds.','A slow breath signals your body that it is safe to sleep.','easy',24),
(@cat,'breathing','side','Breathe slowly in a stressful moment','Use slow breathing the next time you feel tense','The next time you feel stressed, take ten slow breaths before you react.','Using the skill in real life is what makes it stick.','medium',48),
(@cat,'breathing','side','A ten-minute breathing session','Practise one breathing technique for ten minutes','Choose a technique and practise it for ten minutes without interruption.','A longer session builds control you can use later in a short moment.','medium',24),

-- Side quests: journaling
(@cat,'journaling','side','Write freely for ten minutes','Write whatever comes into your head','Set a timer for ten minutes and write without stopping or editing.','Free writing clears the mental clutter.','easy',24),
(@cat,'journaling','side','Write about how you feel','Describe your feelings in a paragraph','Write a paragraph about how you feel right now and what might be behind it.','Naming a feeling makes it easier to manage.','easy',24),
(@cat,'journaling','side','Write a worry and a next step','Pick one worry and write one action you can take','Write about something that is worrying you, then write one small step you could take about it.','Turning a worry into a step reduces its power.','medium',24),
(@cat,'journaling','side','Write one page first thing in the morning','Write a page before you start your day','As soon as you wake, write one page about anything at all.','Morning writing clears your head before the day begins.','medium',24),
(@cat,'journaling','side','Write about a happy memory','Describe a good memory in detail','Write about a happy memory with as much detail as you can recall.','Reliving good moments boosts your mood.','easy',24),
(@cat,'journaling','side','Write a letter you will not send','Say what you need to say on paper','Write a letter to someone, or to your past self, about something unresolved. Do not send it.','Getting thoughts out of your head and onto paper can bring relief.','medium',24),

-- Side quests: gratitude
(@cat,'gratitude','side','Write three things you are grateful for','List three specific good things from today','Write three specific things from today you are grateful for.','Specific gratitude works better than general thanks.','easy',24),
(@cat,'gratitude','side','Thank someone for something specific','Tell someone exactly what you appreciate','Tell someone in person or by message exactly what you appreciate about them.','Being specific makes thanks mean far more.','easy',24),
(@cat,'gratitude','side','Notice five good moments','Pay attention to small good things during the day','Watch for five small good moments today, such as a warm drink or a kind word, and write them down.','You find more good when you are looking for it.','easy',24),
(@cat,'gratitude','side','Write about a person who helped you','Describe someone who made a difference to you','Write a paragraph about someone who helped you, and what they did.','Remembering support helps you feel less alone.','medium',24),

-- Side quests: attention
(@cat,'attention','side','Eat one meal mindfully','Taste every bite without distractions','Eat one meal slowly with no screens, noticing the taste, smell and texture.','Paying attention turns an ordinary meal into a calm moment.','easy',24),
(@cat,'attention','side','Do one chore mindfully','Give a chore your full attention','Pick a chore such as washing up and do it slowly, noticing the sensations.','Daily tasks become an easy place to practise.','easy',24),
(@cat,'attention','side','Five senses exercise','Name five things you see, four you feel, three you hear, two you smell and one you taste','Work through the five senses slowly, naming what you notice with each one.','This exercise pulls you back into the present quickly.','easy',24),
(@cat,'attention','side','Do one task at a time for 30 minutes','Single-task without switching','Work on one task for 30 minutes with no phone, tabs or switching.','Doing one thing at a time is calmer and usually quicker.','medium',24),
(@cat,'attention','side','Listen fully in one conversation','Listen without planning your reply','In one conversation, listen closely and wait until the other person has finished before you respond.','Real listening is a form of mindfulness that others notice.','medium',24),

-- Side quests: digital calm
(@cat,'digital','side','No phone during one meal','Keep your phone away while you eat','Put your phone in another room for one whole meal.','Meals without phones are calmer and more enjoyable.','easy',24),
(@cat,'digital','side','One hour with notifications off','Silence all alerts for an hour','Turn off notifications for one full hour and notice how it feels.','Fewer interruptions give your mind room to settle.','easy',24),
(@cat,'digital','side','Delete one app that drains you','Remove an app that wastes your time or mood','Choose an app that leaves you feeling worse and delete it.','Less scrolling means more time for things you value.','medium',24),
(@cat,'digital','side','Check your phone only at set times','Choose set times to check messages','Pick three times today to check messages and stay off your phone in between.','Checking on purpose beats checking from habit.','medium',24),

-- Side quests: nature
(@cat,'nature','side','Twenty minutes outside without headphones','Walk and listen to the world around you','Go outside for 20 minutes with no headphones and notice what you hear and see.','Quiet time outdoors calms the mind.','easy',24),
(@cat,'nature','side','Sit outside and watch for ten minutes','Observe what is going on around you','Sit outdoors for ten minutes and simply watch the sky, trees or people.','Watching without doing anything is a restful skill.','easy',24),
(@cat,'nature','side','Find five plants or birds and learn their names','Identify five living things nearby','Go outside and identify five plants or birds, using a guide or app to learn their names.','Learning names makes you notice more of the world.','medium',48),

-- Side quests: stress
(@cat,'stress','side','Ten-minute stretch to release tension','Stretch your neck, shoulders and back','Stretch slowly for ten minutes, focusing on the places where you hold stress.','Tension in the body feeds tension in the mind.','easy',24),
(@cat,'stress','side','List what is in your control','Separate what you can change from what you cannot','Write two lists: things you can control and things you cannot, about something on your mind.','Focusing on what you can influence reduces worry.','easy',24),

-- Bonus quests
(@cat,'meditation','bonus','Try a meditation class or app','Attend a class or try a new meditation app','Go to a meditation class or try a new app for one session.','Finding the right style makes the habit easier to keep.','easy',48),
(@cat,'journaling','bonus','Journal from a prompt','Use a writing prompt from a book or website','Find a journaling prompt and write for ten minutes in response.','Prompts help when you do not know where to start.','easy',24),
(@cat,'nature','bonus','Watch a sunrise or sunset in silence','Sit and watch the light change','Watch a sunrise or sunset for at least ten minutes without your phone.','Some moments are best simply watched.','easy',24),
(@cat,'gratitude','bonus','Write a full gratitude letter','Write a letter to someone who has shaped your life','Write a detailed letter thanking someone who made a difference, and give it to them if you can.','A heartfelt letter brings joy to both of you.','medium',48),
(@cat,'digital','bonus','Spend an evening offline','Stay offline from dinner until bed','Avoid the internet and social media for the whole evening.','An offline evening feels slower and calmer.','medium',24),
(@cat,'attention','bonus','Try a mindful hobby','Do colouring, knitting or something similar for 30 minutes','Spend 30 minutes on a quiet, hands-on activity such as colouring, knitting or drawing.','Repetitive, gentle activities settle the mind.','easy',48),
(@cat,'meditation','bonus','Visit a quiet place to reflect','Spend time somewhere peaceful','Visit a quiet place such as a church, garden or library and sit there for 20 minutes.','Peaceful surroundings make reflection easier.','easy',48),
(@cat,'breathing','bonus','Teach someone a breathing exercise','Share a technique with someone','Teach a friend or family member a simple breathing technique and practise it together.','Teaching helps you remember and helps them too.','easy',48),
(@cat,'nature','bonus','Walk barefoot on grass','Feel the ground under your feet','Take your shoes off and walk on grass or soil for a few minutes.','It is a simple way to feel connected to the present.','easy',24),
(@cat,'journaling','bonus','Read back through old journal entries','Look back at what you wrote before','Read through some old journal entries and note anything that has changed.','Looking back shows how far you have come.','easy',24),
(@cat,'stress','bonus','Make a calm playlist','Collect songs that relax you','Build a playlist of calm music and listen to it for 20 minutes.','Having a playlist ready gives you a quick way to unwind.','easy',24),
(@cat,'attention','bonus','Listen to one piece of music with full attention','Sit and do nothing but listen','Choose one piece of music and listen from start to finish with no other activity.','Deep listening is meditation with a soundtrack.','easy',24),
(@cat,'habits','bonus','Declutter one small space','Tidy a drawer, shelf or corner','Choose one small space and clear it of things you no longer need.','A tidier space helps a calmer mind.','easy',24),
(@cat,'stress','bonus','Take a shower or bath without your phone','Enjoy a screen-free wash','Take a shower or bath with no phone or music, noticing the water and warmth.','A few minutes with no input is a rare and valuable rest.','easy',24),
(@cat,'attention','bonus','Walk in silence with a friend','Walk together without talking for ten minutes','Take a walk with someone and agree to stay silent for ten minutes of it.','Shared silence can feel surprisingly close.','easy',48),
(@cat,'meditation','bonus','Read about a mindfulness technique','Learn one new technique from a trusted source','Read an article or chapter about a mindfulness technique you have not tried.','Knowing why a practice works makes you more likely to keep it up.','easy',24);