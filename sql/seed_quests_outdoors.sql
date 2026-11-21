SET @cat = (SELECT id FROM categories WHERE name = 'Outdoors');

INSERT IGNORE INTO quest_templates
    (category_id, track, quest_type, title, summary, description, why, difficulty, time_limit_hours)
VALUES
-- Main quests
(@cat,'hiking','main','Walk or hike 50 km in three weeks','Reach a combined 50 km of outdoor walking in 21 days','Add up your outdoor walks and hikes over the next 21 days until you reach a total of 50 km. Log each walk so you can see the total grow.','Steady distance over several weeks builds real endurance and a love of the outdoors.','hard',504),
(@cat,'exploring','main','Visit five new outdoor places in 30 days','Discover five parks, trails or viewpoints you have never been to','Over the next 30 days visit five outdoor places you have never been to, such as a park, trail, beach or viewpoint. Note what you liked about each.','Exploring close to home often reveals places you never knew were there.','hard',720),
(@cat,'general','main','Spend 30 minutes outside every day for two weeks','Go outdoors for at least half an hour on 12 of 14 days','Spend at least 30 minutes outside on 12 of the next 14 days, whatever the weather.','Daily time outdoors lifts mood and sleep.','medium',336),
(@cat,'hiking','main','Complete a 15 km day hike','Plan and finish a long walk in a single day','Plan a route of around 15 km, pack water, food and the right clothes, and finish it within 14 days.','A long day on the trail is a real achievement and a test of planning.','hard',336),
(@cat,'camping','main','Plan and take an overnight trip outdoors','Spend a night camping or in an outdoor shelter','Plan a safe overnight trip, such as camping or a hut stay, with a checklist of what to bring. Complete it within 30 days.','A night outdoors is a different experience of the natural world.','epic',720),
(@cat,'wildlife','main','Learn to identify 20 plants or birds','Learn and recognise twenty species in 21 days','Use a guide or an app to learn to identify 20 plants or birds near you. By the end you should name them without help.','Knowing names changes how you see every walk.','hard',504),
(@cat,'nature','main','Watch eight sunrises or sunsets in 30 days','Spend time outdoors for eight sunrises or sunsets','Watch at least eight sunrises or sunsets over the next 30 days, outdoors and away from screens.','Marking the start and end of the day outdoors resets your sense of time.','medium',720),
(@cat,'sports','main','Try a new outdoor sport three times','Give an outdoor activity a proper chance','Choose a new outdoor activity, such as climbing, paddling or orienteering, and try it three times within 21 days.','You need a few tries to know whether you love something.','hard',504),

-- Side quests: hiking
(@cat,'hiking','side','Take a one-hour walk in a park','Walk in green space for an hour','Spend a full hour walking in a park or green space.','Time in green space calms and clears the mind.','easy',48),
(@cat,'hiking','side','Hike a trail you have never done','Follow a new path','Choose a trail you have never walked and follow it.','New trails keep walking exciting.','medium',48),
(@cat,'hiking','side','Walk 5 km outdoors','Cover five kilometres on foot','Walk 5 km outdoors at a comfortable pace.','Five kilometres is a satisfying distance for a regular walk.','easy',24),
(@cat,'hiking','side','Climb a hill and enjoy the view','Reach a high point nearby','Find a hill or high point nearby, climb it and spend a few minutes taking in the view.','Looking out from a height gives a sense of perspective.','medium',48),
(@cat,'hiking','side','Walk uphill for 30 minutes','Take a route that climbs','Choose a route with hills and walk uphill for 30 minutes in total.','Hills build strength and stamina quickly.','medium',24),
(@cat,'hiking','side','Take a long weekend walk','Walk for two hours or more at a relaxed pace','Take a relaxed walk of at least two hours at the weekend.','A long walk gives your thoughts room to wander.','medium',48),

-- Side quests: nature
(@cat,'nature','side','Sit outside for twenty minutes','Do nothing but be outdoors','Sit somewhere outside for 20 minutes without your phone and just watch and listen.','Stillness outdoors lowers stress.','easy',24),
(@cat,'nature','side','Find five signs of the season','Notice what is changing outdoors','Go outside and find five signs of the current season, such as leaves, flowers or weather.','Noticing the seasons connects you to the year.','easy',24),
(@cat,'nature','side','Walk through woodland','Spend time among trees','Walk through a wood or forest and notice the sounds and smells.','Woodland air and quiet have a calming effect.','easy',48),
(@cat,'nature','side','Watch the clouds for ten minutes','Lie back and look at the sky','Lie on the grass or sit and watch the clouds for ten minutes.','Cloud watching is free relaxation.','easy',24),
(@cat,'nature','side','Stargaze for twenty minutes','Look up on a clear night','On a clear night go somewhere dark and look at the stars for 20 minutes.','Looking at the night sky puts everyday worries in perspective.','easy',48),
(@cat,'nature','side','Watch the sunset','Take in the end of the day','Go outdoors and watch the sunset from start to finish.','A sunset is a daily event that costs nothing.','easy',24),

-- Side quests: wildlife
(@cat,'wildlife','side','Spot five different birds','Find and name five birds','Find five different birds and write down what each one looks like and its name.','Birdwatching sharpens your attention.','easy',24),
(@cat,'wildlife','side','Learn to identify three trees','Find and name three trees near you','Use a guide or app to identify three different trees near you.','Trees become friends when you know their names.','easy',24),
(@cat,'wildlife','side','Put up a bird feeder','Feed the birds near you','Put up a bird feeder or a dish of water and watch who visits.','Feeding birds brings nature to your window.','medium',48),
(@cat,'wildlife','side','Photograph a wild plant or animal','Take a good picture of something living outdoors','Take a clear photo of a wild plant or animal, without disturbing it.','Photography makes you patient and observant.','easy',24),
(@cat,'wildlife','side','Learn about local wildlife','Find out what lives near you','Read about the wildlife that lives in your area and note three species you would like to see.','Knowing what lives nearby makes every walk an adventure.','easy',24),

-- Side quests: exploring
(@cat,'exploring','side','Visit a local park you have never been to','Discover green space nearby','Visit a park or green space in your area that you have never been to.','There is often a good place just around the corner.','easy',48),
(@cat,'exploring','side','Find a viewpoint in your area','Look for somewhere with a good view','Find and visit a viewpoint within an hour of home.','A good view is worth the effort.','medium',48),
(@cat,'exploring','side','Walk a new route home','Take a different path','Choose a route home you have never taken and walk it.','A new route changes how you see a familiar place.','easy',24),
(@cat,'exploring','side','Visit a nature reserve','Spend time somewhere protected for wildlife','Visit a nature reserve or protected area near you.','Reserves show nature at its best.','medium',48),
(@cat,'exploring','side','Explore a nearby village or town on foot','Wander through somewhere new','Visit a nearby village or town and explore it on foot.','Exploring on foot shows you details you miss from a car.','medium',48),
(@cat,'exploring','side','Plan a walk from a map','Choose a route and note landmarks','Look at a map, choose a walking route and write down the landmarks you will pass.','Planning makes the walk part of the adventure.','easy',24),

-- Side quests: water
(@cat,'water','side','Visit a beach, lake or river','Spend time by the water','Visit a beach, lake or river and spend at least an hour there.','Water has a calming effect on the mind.','easy',48),
(@cat,'water','side','Paddle a canoe or kayak','Try paddling on calm water','Try paddling a canoe or kayak on calm water with proper safety kit.','Paddling gives you a new view of familiar places.','medium',48),
(@cat,'water','side','Watch the water for ten minutes','Sit by a river, lake or sea and just watch','Sit beside water for ten minutes and watch how it moves.','Watching water is a natural form of meditation.','easy',24),

-- Side quests: skills
(@cat,'skills','side','Learn to read a map','Practise with a paper or digital map','Learn to read the symbols and contours on a map and trace a route.','Map skills help you feel confident outdoors.','medium',24),
(@cat,'skills','side','Pack a day-hike bag','Prepare the essentials for a day outdoors','Pack a bag with water, snacks, a first aid kit and a layer for the weather.','Good preparation is the difference between a good and a bad day.','easy',24),
(@cat,'skills','side','Learn three useful knots','Practise tying knots with a piece of rope','Learn to tie three useful knots, such as a reef knot and a bowline.','Knots are a skill you use again and again outdoors.','easy',24),

-- Side quests: seasons
(@cat,'seasons','side','Take a walk in light rain','Go out with the right clothes','Dress for the weather and take a walk in light rain.','Rain makes everything fresh and quiet.','easy',24),
(@cat,'seasons','side','Enjoy a frosty or misty morning outdoors','Get out on a cold, clear day','Go outdoors on a frosty or misty morning and walk for at least 20 minutes.','Cold mornings are beautiful and wake you up.','easy',48),
(@cat,'seasons','side','Eat a meal outdoors','Take your food outside','Eat a meal outdoors, in a garden, park or balcony.','Food tastes different in the open air.','easy',24),

-- Side quests: general
(@cat,'general','side','Do a workout outside','Exercise in the open air','Take your exercise outdoors in a park or garden.','Outdoor exercise often feels easier and more enjoyable.','easy',24),
(@cat,'general','side','Read a book outdoors','Take your book outside','Read outdoors for at least 30 minutes.','A good book and fresh air is a perfect mix.','easy',24),
(@cat,'general','side','Pick up litter on a walk','Leave a place cleaner than you found it','Take a bag and gloves on a walk and collect litter, taking care with anything sharp.','A small effort makes a place nicer for everyone.','easy',48),
(@cat,'general','side','Take a break outdoors during your workday','Step outside in your breaks','Take your lunch or a break outside for 15 minutes during your working or study day.','Fresh air and daylight improve afternoon focus.','easy',24),

-- Bonus quests
(@cat,'nature','bonus','Go stargazing with a friend','Share the night sky with someone','Take a friend outside on a clear night to look at the stars.','Shared wonder makes a good memory.','easy',48),
(@cat,'general','bonus','Have a picnic','Eat outdoors with food you prepared','Prepare food and enjoy a picnic outdoors, alone or with others.','A picnic turns a meal into an event.','easy',48),
(@cat,'exploring','bonus','Visit somewhere with a great view','Find a place worth the journey','Visit a place known for a great view, such as a hill, a bridge or a tower.','Views are good for the soul.','medium',48),
(@cat,'exploring','bonus','Try geocaching','Use an app to find hidden containers','Use a free geocaching app to find at least one hidden cache near you.','Geocaching turns walking into a treasure hunt.','easy',48),
(@cat,'general','bonus','Show a friend your favourite outdoor spot','Share a place you love','Take a friend to your favourite outdoor place and tell them why you like it.','Sharing a place makes it mean more.','easy',48),
(@cat,'wildlife','bonus','Plant a tree or flower','Add something living to the world','Plant a tree, a flower or a seed in the ground or a pot.','Planting is an act of hope.','medium',48),
(@cat,'hiking','bonus','Join a group walk or hiking club','Walk with a group','Join a group walk or a hiking club for one outing.','Groups introduce you to new routes and new friends.','medium',48),
(@cat,'nature','bonus','Watch a nature documentary','Learn about the natural world','Watch a nature documentary from start to finish.','Documentaries show wonders you might not otherwise see.','easy',24),
(@cat,'wildlife','bonus','Take photos of nature','Capture what you find outdoors','Take ten photos of nature, such as plants, insects or landscapes.','Photography helps you notice more.','easy',24),
(@cat,'sports','bonus','Try a new outdoor activity','Do something outside you have not done before','Try an outdoor activity that is new to you, such as orienteering or slacklining.','New activities bring new interests.','medium',48),
(@cat,'general','bonus','Leave a place cleaner than you found it','Tidy a spot outdoors','Pick up litter from a park, beach or path and dispose of it properly.','Leaving a place better than you found it is a good habit.','easy',24),
(@cat,'general','bonus','Write down what you saw on a walk','Record the details of a walk','After a walk, write down what you saw, heard and felt.','Writing makes you remember and notice more next time.','easy',24),
(@cat,'exploring','bonus','Walk without a destination','Wander and see where you end up','Go for a walk with no plan and follow whatever interests you.','Wandering is the oldest form of exploration.','easy',24),
(@cat,'exploring','bonus','Read about a national park or trail','Find a place you would like to visit one day','Read about a national park or long-distance trail and note what you would like to see.','Dreaming about a trip makes it more likely to happen.','easy',24),
(@cat,'general','bonus','Spend an evening in the garden or on a balcony','Enjoy the outdoors at home','Spend an evening outside at home, with a drink or a book.','Your own outdoor space is a place to relax.','easy',24),
(@cat,'general','bonus','Cook a meal outdoors','Prepare food outside safely','Cook a meal outdoors, using a barbecue or camping stove, taking proper fire safety care.','Food cooked outdoors always tastes better.','medium',48);