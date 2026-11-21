SET @cat = (SELECT id FROM categories WHERE name = 'Languages');

INSERT IGNORE INTO quest_templates
    (category_id, track, quest_type, title, summary, description, why, difficulty, time_limit_hours)
VALUES
-- Main quests
(@cat,'vocabulary','main','Learn 100 new words in 30 days','Learn and remember 100 words in your target language','Choose 100 useful words and learn about four of them every day. Review them regularly. By day 30 you should recognise at least 80 without help.','A solid vocabulary is the fastest way to start understanding and speaking.','hard',720),
(@cat,'habits','main','Study a language for 15 minutes a day for two weeks','Study for at least 15 minutes on 12 of 14 days','Study your chosen language for at least 15 minutes a day on 12 of the next 14 days, using any method you like.','Small daily sessions beat long irregular ones.','medium',336),
(@cat,'speaking','main','Have a ten-minute conversation','Talk with a speaker of your language for ten minutes','Arrange a conversation with a tutor, language partner or native speaker and keep it going in the target language for ten minutes. Complete it within 14 days.','Speaking is the skill that matters most and the one people avoid most.','hard',336),
(@cat,'listening','main','Watch three films or episodes in the language','Watch three pieces of video with subtitles and note new words','Watch three films or episodes in your target language, using subtitles if you need them, and note at least ten new words from each. Finish within 21 days.','Real speech is faster and more natural than any course.','medium',504),
(@cat,'reading','main','Read a short book or collection of stories','Finish a short book in the language','Choose a short book written for learners or a collection of short stories, and read it from start to finish within 21 days.','Reading builds vocabulary and grammar together.','hard',504),
(@cat,'writing','main','Write a 300-word piece','Write a text in the language and have it corrected','Write a text of at least 300 words about a subject you like, then ask a tutor, partner or tool to check it. Finish within 14 days.','Writing shows you exactly what you can and cannot say yet.','hard',336),
(@cat,'course','main','Finish a beginner course or textbook','Complete every unit of a beginner course','Choose a beginner course or textbook and complete every unit within 30 days.','A structured course gives you the foundations.','epic',720),
(@cat,'speaking','main','Speak a little every day for three weeks','Speak aloud in the language on 17 of 21 days','Speak aloud in the language for at least five minutes a day, to yourself, a partner or a recording, on at least 17 of 21 days.','Speaking daily is the best way to stop being afraid of it.','hard',504),

-- Side quests: vocabulary
(@cat,'vocabulary','side','Learn ten new words','Learn and use ten new words','Choose ten new words and learn them well enough to use each in a sentence.','Ten words a day adds up to hundreds in a few weeks.','easy',24),
(@cat,'vocabulary','side','Review 30 flashcards','Go through thirty words and sort them','Review 30 flashcards and sort them into those you knew and those you did not.','Reviewing what you got wrong is where the learning happens.','easy',24),
(@cat,'vocabulary','side','Learn twenty new words','Take on a bigger set of words','Learn 20 new words and test yourself at the end.','A bigger set pushes you to remember better.','medium',24),
(@cat,'vocabulary','side','Label ten objects in your home','Put words on things around you','Write the target language word for ten objects in your home and stick it on.','Seeing words in daily life makes them stick.','easy',24),
(@cat,'vocabulary','side','Learn the days, months and numbers','Master the basics of time and counting','Learn the days of the week, the months and numbers up to 20.','These are used in almost every conversation.','easy',24),
(@cat,'vocabulary','side','Learn ten useful travel phrases','Learn phrases for a trip','Learn ten phrases you would need on a trip, such as asking for directions.','Travel phrases are practical and motivating.','medium',24),
(@cat,'vocabulary','side','Revise last weeks words','Test yourself on older vocabulary','Go back over the words you learned last week and test yourself.','Revisiting words is how they move into long-term memory.','easy',24),

-- Side quests: speaking
(@cat,'speaking','side','Say twenty sentences aloud','Practise out loud','Say 20 sentences aloud in the language, about anything you can think of.','Saying words aloud trains your mouth as well as your memory.','easy',24),
(@cat,'speaking','side','Describe your day aloud','Say what you did in the language','At the end of the day, describe what you did aloud in the language.','Describing your own life is the most useful practice.','medium',24),
(@cat,'speaking','side','Record yourself speaking','Listen to your own pronunciation','Record yourself speaking for two minutes, listen back and note what to improve.','Hearing yourself shows what you cannot hear while speaking.','medium',24),
(@cat,'speaking','side','Practise a greeting and an introduction','Introduce yourself in the language','Practise greeting someone and introducing yourself, with your name, where you live and what you do.','Introductions are the start of every conversation.','easy',24),
(@cat,'speaking','side','Practise ordering food','Rehearse a restaurant conversation','Practise ordering a meal and asking for the bill in the language.','Food is one of the first things you will need.','easy',24),
(@cat,'speaking','side','Shadow a native speaker for ten minutes','Repeat after a recording right away','Play a recording of a native speaker and repeat each sentence immediately after for ten minutes.','Shadowing improves rhythm and pronunciation quickly.','medium',24),

-- Side quests: listening
(@cat,'listening','side','Listen to a podcast for 15 minutes','Tune your ear to the language','Listen to a podcast in the language for 15 minutes, even if you do not understand everything.','Regular listening trains your ear.','easy',24),
(@cat,'listening','side','Listen to songs and pick out words','Find familiar words in music','Listen to songs in the language and note any words you recognise.','Music makes words stick.','easy',24),
(@cat,'listening','side','Watch a short video with subtitles','Watch something in the language with help','Watch a short video in the language with subtitles in the same language.','Subtitles in the same language link what you hear to what you read.','easy',24),
(@cat,'listening','side','Watch a 20-minute episode','Follow a short TV episode','Watch a 20-minute episode in the language and try to follow the story.','Longer content trains your attention.','medium',24),
(@cat,'listening','side','Write down what you hear','Do a short dictation','Listen to a short dialogue and write down what you hear, then check it.','Dictation shows exactly what you miss.','medium',24),
(@cat,'listening','side','Listen to slow news in the language','Follow a news broadcast made for learners','Listen to a slow or learner news broadcast and note the main topic.','Real topics keep listening interesting.','medium',48),

-- Side quests: reading
(@cat,'reading','side','Read a short article','Read something in the language','Read a short article in the language and note new words.','Reading widens your vocabulary.','easy',24),
(@cat,'reading','side','Read a short story for children','Start with simple stories','Read a short story written for children in the language.','Children stories use simple, useful language.','easy',24),
(@cat,'reading','side','Read the labels on three products','Learn from everyday packaging','Read the labels on three products written in the language, or look them up online.','Everyday text is great practice.','easy',24),
(@cat,'reading','side','Read a recipe','Follow a recipe in the language','Read a recipe in the language and note the new words.','Recipes are short and full of useful words.','easy',24),
(@cat,'reading','side','Read five headlines and note new words','Skim the news in the language','Read five news headlines and look up any new words.','Headlines are short and show how real language works.','easy',24),

-- Side quests: writing
(@cat,'writing','side','Write five sentences about your day','Describe what you did','Write five sentences in the language about your day.','Short daily writing builds confidence.','easy',24),
(@cat,'writing','side','Write a short message to a language partner','Send a real message in the language','Write and send a short message in the language to a language partner or tutor.','Writing to a real person gives writing a purpose.','medium',24),
(@cat,'writing','side','Describe a picture in the language','Write about what you see','Choose a picture and write a few sentences describing it.','Describing pictures helps you use new words.','easy',24),
(@cat,'writing','side','Write a postcard to yourself','Write a short note in the language','Write a short postcard to yourself in the language, about your plans.','A postcard is short and personal.','easy',24),
(@cat,'writing','side','Write a paragraph about your family','Describe the people close to you','Write a paragraph about your family in the language.','Writing about people you know uses everyday words.','medium',24),

-- Side quests: grammar
(@cat,'grammar','side','Study one grammar point','Learn a single rule and examples','Choose one grammar point and learn it with at least five example sentences.','One rule at a time is easier than all at once.','easy',24),
(@cat,'grammar','side','Do a set of grammar exercises','Practise a rule with exercises','Complete a set of grammar exercises and check your answers.','Exercises make rules automatic.','medium',24),
(@cat,'grammar','side','Learn the present tense of five verbs','Conjugate five common verbs','Learn how to conjugate five common verbs in the present tense.','Verbs are at the heart of every sentence.','easy',24),
(@cat,'grammar','side','Learn five common irregular verbs','Study the verbs that break the rules','Learn five common irregular verbs and use each in a sentence.','Irregular verbs are everywhere because they are common.','medium',24),

-- Side quests: culture
(@cat,'culture','side','Learn about a festival','Find out about a celebration in the culture','Read about a festival or tradition from a country where the language is spoken.','Culture gives language its meaning.','easy',24),
(@cat,'culture','side','Cook a dish from the culture','Make a traditional recipe','Cook a traditional dish from a country where the language is spoken and read the recipe in the language if you can.','Food is a delicious way to learn.','medium',48),
(@cat,'culture','side','Learn a proverb or idiom','Find a saying and its meaning','Learn a proverb or idiom in the language and what it means.','Sayings show how people think.','easy',24),

-- Bonus quests
(@cat,'speaking','bonus','Find a language partner','Meet someone to practise with','Find a language partner online or in person and arrange a first chat.','A partner keeps you speaking and motivated.','medium',48),
(@cat,'speaking','bonus','Chat with a native speaker online','Talk with someone who speaks it daily','Have a short chat with a native speaker through an exchange app or site.','Real conversation is the best teacher.','medium',48),
(@cat,'habits','bonus','Switch your phone to the language for a day','Change your phone language for 24 hours','Change your phone language to your target language for a full day.','Daily tools become free practice.','medium',24),
(@cat,'listening','bonus','Watch a film in the language','Enjoy a whole film with subtitles','Watch a whole film in the language, with subtitles if needed.','A film is a treat and good practice.','easy',48),
(@cat,'listening','bonus','Sing a song in the language','Learn the words to a song and sing along','Learn the words of a song in the language and sing it.','Singing is a fun way to learn.','easy',24),
(@cat,'speaking','bonus','Learn a tongue twister','Practise a tricky phrase','Learn a tongue twister in the language and say it five times fast.','Tongue twisters improve pronunciation.','easy',24),
(@cat,'culture','bonus','Visit a restaurant or shop and use the language','Try the language in real life','Visit a restaurant or shop from the culture and use a few words of the language.','Using the language in real life builds courage.','easy',48),
(@cat,'speaking','bonus','Join a language meetup or class','Practise with a group','Join a language meetup, club or class for one session.','Groups make practice social.','medium',48),
(@cat,'vocabulary','bonus','Count to 100','Learn the numbers up to one hundred','Learn to count to 100 in the language.','Numbers are used constantly.','easy',24),
(@cat,'vocabulary','bonus','Teach someone five words','Share what you have learned','Teach a friend or family member five words in the language.','Teaching helps you remember.','easy',48),
(@cat,'reading','bonus','Read a short poem','Read a poem in the language','Read a short poem in the language, and look up any words you do not know.','Poems use language in a memorable way.','easy',24),
(@cat,'listening','bonus','Learn a nursery rhyme','Learn a simple traditional rhyme','Learn a nursery rhyme in the language.','Rhymes are simple and easy to remember.','easy',24),
(@cat,'listening','bonus','Find a podcast to follow','Choose a show you will enjoy','Find a podcast in the language that you enjoy and subscribe to it.','A show you like makes regular listening easy.','easy',24),
(@cat,'writing','bonus','Write your shopping list in the language','Use the language in everyday life','Write your shopping list in the language for the day.','Daily tasks become practice.','easy',24),
(@cat,'vocabulary','bonus','Name everything you see on a walk','Practise words as you move','On a walk, name the things you see in the language.','Linking words to real objects makes them stick.','easy',24),
(@cat,'culture','bonus','Learn how to say thank you in five languages','Collect a few useful words','Learn how to say thank you in five different languages.','Politeness opens doors everywhere.','easy',24);