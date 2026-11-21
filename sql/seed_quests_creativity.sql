SET @cat = (SELECT id FROM categories WHERE name = 'Creativity');

INSERT IGNORE INTO quest_templates
    (category_id, track, quest_type, title, summary, description, why, difficulty, time_limit_hours)
VALUES
-- Main quests
(@cat,'drawing','main','Draw every day for two weeks','Draw for 15 minutes on 12 of 14 days','Draw for at least 15 minutes a day on 12 of the next 14 days. Subjects can be anything from objects to faces. Keep your drawings together so you can compare the first and last.','Daily practice improves your hand and eye faster than occasional long sessions.','medium',336),
(@cat,'writing','main','Write a short story of 2000 words','Plan, draft and finish a complete short story','Plan a beginning, middle and end, write a full draft of at least 2000 words, then edit it once. Finish within 14 days.','Finishing a whole story teaches you more than starting ten.','hard',336),
(@cat,'music','main','Learn a whole song on an instrument','Play one song from start to finish without stopping','Choose a song you love and learn it on an instrument you have or are learning. By the end you should play it through without stopping. Finish within 21 days.','Learning a real song keeps practice fun and gives you something to share.','hard',504),
(@cat,'photography','main','A 30-day photo challenge','Take and choose one good photo a day on 25 of 30 days','Take at least one photo a day on 25 of the next 30 days, choose your best each day, and keep them in one folder. Pick your favourite five at the end.','Taking a photo every day trains you to notice things you would normally walk past.','medium',720),
(@cat,'craft','main','Finish a handmade project','Plan and complete one project by hand','Choose a project such as knitting, sewing, woodwork or model making. Plan it, gather materials and finish it within 14 days.','Finishing something real with your hands is deeply satisfying.','medium',336),
(@cat,'design','main','Create a small design portfolio','Design three pieces and present them together','Design three related pieces, such as posters, logos or illustrations, and put them together in a neat portfolio. Finish within 21 days.','A small portfolio shows your style and your ability to finish.','hard',504),
(@cat,'music','main','Compose an original tune','Create and record a short piece of your own music','Write a short original tune or beat of at least one minute, and record it so you can listen back. Finish within 30 days.','Making your own music is very different from playing someone elses and teaches you how music works.','epic',720),
(@cat,'general','main','Complete a 30-day creative challenge','Make something creative on 25 of 30 days','Choose any creative challenge and make something on at least 25 of the next 30 days. Share your favourite at the end.','Thirty days of making is enough to see real growth in your skill and ideas.','epic',720),

-- Side quests: drawing
(@cat,'drawing','side','Sketch for 15 minutes','Draw anything you like for a quarter of an hour','Set a timer for 15 minutes and draw without worrying about the result.','Short, relaxed sketching builds the habit of drawing.','easy',24),
(@cat,'drawing','side','Draw an object from real life','Draw something in front of you','Choose an object nearby and draw it carefully, looking at it more than at your paper.','Drawing from real life trains you to see accurately.','easy',24),
(@cat,'drawing','side','Draw your hand five times','Make five drawings of your own hand in different poses','Draw your own hand five times in different positions, spending a few minutes on each.','Hands are hard to draw, and practising them improves everything else.','easy',24),
(@cat,'drawing','side','Copy a drawing you admire','Study another artist by copying a small part','Choose a drawing you admire and copy a small part of it carefully to learn how it was made.','Copying is a traditional way to learn technique.','medium',24),
(@cat,'drawing','side','Fill a page with doodles','Doodle freely until the page is full','Fill a whole page with doodles, patterns and shapes, with no plan.','Doodling lets ideas flow without pressure.','easy',24),
(@cat,'drawing','side','Draw a self-portrait','Draw yourself from a mirror or photo','Draw your own face from a mirror or a photo, aiming to capture what makes it yours.','A self-portrait is a classic test of observation.','medium',48),

-- Side quests: writing
(@cat,'writing','side','Write a 300-word scene','Describe one moment in a story','Write a scene of at least 300 words that takes place in one location over a few minutes.','Scenes are the building blocks of every story.','medium',24),
(@cat,'writing','side','Write a poem of twelve lines','Write a poem about something you saw today','Write a poem of about twelve lines about something you noticed today.','Poetry teaches you to choose words with care.','easy',24),
(@cat,'writing','side','Write a six-word story','Tell a whole story in six words','Write a complete story using exactly six words, then write two more.','Tight limits make you creative.','easy',24),
(@cat,'writing','side','Describe a place in detail','Write about somewhere using all five senses','Pick a place you know well and describe it using sight, sound, smell, taste and touch.','Detail makes writing feel real.','easy',24),
(@cat,'writing','side','Write a conversation between two people','Write dialogue that sounds natural','Write a short conversation between two characters, where each wants something different.','Good dialogue shows character without explaining it.','medium',24),
(@cat,'writing','side','Rewrite a paragraph in a new voice','Change the style of something you wrote','Take a paragraph you wrote earlier and rewrite it in a very different voice or style.','Experimenting with voice helps you find your own.','medium',24),

-- Side quests: music
(@cat,'music','side','Practise an instrument for 20 minutes','Play and practise for 20 minutes','Practise any instrument for 20 minutes, working on one thing that is hard.','Short regular sessions beat occasional long ones.','easy',24),
(@cat,'music','side','Learn four chords or a scale','Learn something new on your instrument','Learn four new chords or one new scale and play it until it feels comfortable.','Chords and scales are the vocabulary of music.','medium',24),
(@cat,'music','side','Sing for ten minutes','Sing along to songs you love','Sing for ten minutes, in the shower, in the car or in your room.','Singing is good for your mood and your breathing.','easy',24),
(@cat,'music','side','Make a short beat or loop','Create a few bars of music on an app','Use a free app to make a short beat or loop of a few bars.','Making a loop is the quickest way to start creating music.','medium',24),
(@cat,'music','side','Listen closely to an album','Listen to a whole album and note the instruments','Listen to an album from start to finish with no distractions and note the instruments you hear.','Careful listening makes you a better musician.','easy',24),

-- Side quests: craft
(@cat,'craft','side','Fold an origami figure','Follow a guide to make a paper model','Follow a tutorial to fold a paper figure, such as a crane or a box.','Origami is a calm, small project you can finish in one sitting.','easy',24),
(@cat,'craft','side','Customise or mend a piece of clothing','Add a patch, a stitch or a new detail','Mend a tear, add a patch or decorate something you already own.','Mending and customising give your things new life.','medium',48),
(@cat,'craft','side','Make a handmade card','Create a card for someone','Make a card by hand for a friend or family member and write a message inside.','A handmade card shows real thought.','easy',24),
(@cat,'craft','side','Build something from recycled materials','Make something useful from things you would throw away','Use boxes, jars or other recycled materials to make something useful or decorative.','Working with limits is great for creativity.','medium',48),
(@cat,'craft','side','Knit, crochet or sew for 30 minutes','Work on a textile project for half an hour','Spend 30 minutes knitting, crocheting or sewing something.','Repeated hand movements are relaxing and productive.','easy',24),

-- Side quests: photography
(@cat,'photography','side','Take ten photos of one subject','Photograph one thing from ten angles','Pick one subject and take ten photos of it from different angles and distances.','Looking again and again is how photographers train their eye.','easy',24),
(@cat,'photography','side','Photograph in golden hour','Take photos in the hour after sunrise or before sunset','Go out in the hour after sunrise or before sunset and take at least ten photos in the warm light.','Golden light makes almost everything look better.','easy',24),
(@cat,'photography','side','Take a photo from an unusual angle','Find a surprising point of view','Take a photo from very high, very low or very close, so it looks different from the usual.','A new angle can turn a plain subject into something special.','easy',24),
(@cat,'photography','side','Edit one of your photos','Improve a photo with crop, light and colour','Edit a photo using a free app, adjusting the crop, brightness and colour.','Editing shows you what makes a photo work.','medium',24),
(@cat,'photography','side','Tell the story of your day in five photos','Take five photos that show your day','Take five photos that together tell the story of your day.','Telling a story in pictures builds your sense of sequence.','medium',24),

-- Side quests: design
(@cat,'design','side','Design a poster','Create a poster for an event or idea','Design a poster for a real or imaginary event, using a free tool or paper.','Posters teach hierarchy, colour and layout.','medium',48),
(@cat,'design','side','Redesign an everyday object','Imagine a better version of something you use','Pick an everyday object and sketch an improved design, noting what you changed and why.','Design thinking starts by noticing what is annoying.','medium',24),
(@cat,'design','side','Make a colour palette from nature','Pick five colours from something you see outdoors','Take a photo of something in nature and pick five colours from it to make a palette.','Nature gives you colours that always work together.','easy',24),
(@cat,'design','side','Sketch a logo for an idea','Design a simple logo','Think of a made-up project and sketch three logo ideas, then pick your favourite.','Logos teach you to say a lot with a little.','medium',24),

-- Side quests: general
(@cat,'general','side','Brainstorm twenty ideas','Write down twenty ideas on any topic','Choose a topic and write twenty ideas in ten minutes, without judging any of them.','Quantity first is how good ideas appear.','easy',24),
(@cat,'general','side','Make something from only three materials','Limit yourself and see what you create','Choose three materials and make something using only those.','Limits push you to be inventive.','medium',48),
(@cat,'general','side','Spend 30 minutes making without judging','Create freely with no inner critic','Spend 30 minutes making anything you like, with no plan and no judging.','Enjoying the process matters more than the result.','easy',24),
(@cat,'general','side','Respond to a creative prompt','Use a prompt from a book or website','Find a creative prompt and make something in response within 30 minutes.','Prompts remove the pain of a blank page.','easy',24),
(@cat,'general','side','Finish a small unfinished project','Complete something you left half done','Choose a small unfinished project and finish it.','Finishing builds confidence and clears your mind.','medium',48),

-- Bonus quests
(@cat,'general','bonus','Visit a gallery or exhibition','See art in person','Visit a gallery, museum or exhibition and spend time with a few pieces.','Seeing art in person shows you things no screen can.','easy',48),
(@cat,'craft','bonus','Make a gift by hand','Create something for someone you care about','Make a gift by hand for someone you care about.','A handmade gift carries far more feeling than a bought one.','medium',48),
(@cat,'general','bonus','Share your work with someone','Show a friend something you made','Show something you made to a friend or family member and ask what they think.','Sharing takes courage and rewards it.','easy',24),
(@cat,'general','bonus','Take a creative class or workshop','Try a class in a creative skill','Join a class or online workshop in a creative skill.','Learning from others opens up new techniques.','medium',48),
(@cat,'general','bonus','Collaborate with a friend','Make something together','Make a drawing, song or story together with a friend.','Collaboration brings ideas you would never have alone.','easy',48),
(@cat,'drawing','bonus','Try a new art medium','Use watercolour, charcoal or something new to you','Try an art medium you have not used before, such as watercolour or charcoal.','A new medium shows you new ways of seeing.','easy',24),
(@cat,'drawing','bonus','Draw while listening to music','Let the music guide your pencil','Put on some music and draw whatever it makes you feel.','Music can free your hand.','easy',24),
(@cat,'general','bonus','Watch a documentary about an artist','Learn how another creator works','Watch a documentary about an artist, musician or designer.','Seeing how others work is a great spark.','easy',24),
(@cat,'design','bonus','Make a mood board','Collect images that fit a theme','Collect images, colours and words that fit a theme or feeling and arrange them together.','A mood board gives a project a clear direction.','easy',24),
(@cat,'writing','bonus','Read about a writer or poet you admire','Learn the story behind the work','Read about the life and working habits of a writer or poet you admire.','Creative lives are full of ideas to borrow.','easy',24),
(@cat,'photography','bonus','Take a photo walk','Walk with the purpose of finding pictures','Go for a walk and take photos of anything interesting you find along the way.','A photo walk turns a familiar place into something new.','easy',48),
(@cat,'drawing','bonus','Keep a sketchbook with you for a day','Draw whenever you have a spare moment','Carry a sketchbook all day and draw whenever you have a spare moment.','Having the tool to hand is half the battle.','easy',24),
(@cat,'writing','bonus','Write a poem for someone','Write something just for them','Write a short poem for someone you care about and give it to them.','A personal poem is a rare and lasting gift.','easy',48),
(@cat,'craft','bonus','Redecorate a corner of your room','Make one corner feel more like you','Pick a corner of your room and redecorate it with things that make you happy.','Surroundings shape your mood and your ideas.','medium',48),
(@cat,'general','bonus','Learn a technique from a tutorial','Pick up a new trick and try it','Watch a short tutorial on a creative technique and try it straight away.','Learning and using it at once makes it stick.','easy',24),
(@cat,'music','bonus','Make a playlist for a mood','Choose songs that fit a feeling','Build a playlist of songs that fit a particular mood and listen to it from start to finish.','Curating music trains your ear for what works together.','easy',24);