SET @cat = (SELECT id FROM categories WHERE name = 'Chores');

INSERT IGNORE INTO quest_templates
    (category_id, track, quest_type, title, summary, description, why, difficulty, time_limit_hours)
VALUES
-- Main quests
(@cat,'cleaning','main','Deep clean every room in two weeks','Give each room a thorough clean in 14 days','Over the next 14 days deep clean every room in your home, one or two rooms at a time. Include floors, surfaces, windows and corners. Tick off each room.','A thorough clean resets your home and makes everyday tidying much easier.','hard',336),
(@cat,'organising','main','Declutter your wardrobe','Sort everything you own into keep, donate and recycle','Go through your whole wardrobe, try on anything you are unsure about and sort it into keep, donate and recycle. Put the keep items away neatly. Finish within seven days.','A wardrobe you can see into makes getting dressed faster and calmer.','medium',168),
(@cat,'habits','main','Tidy for ten minutes a day for 21 days','Do a ten-minute daily tidy on 17 of 21 days','Set a timer for ten minutes and tidy one area every day on at least 17 of the next 21 days.','A small daily habit prevents mess from building up.','hard',504),
(@cat,'organising','main','Declutter your whole home in 30 days','Clear one area of clutter every day or two for a month','Go through your home area by area, such as drawers, cupboards, shelves and storage, and remove what you do not need. Complete at least 15 areas within 30 days.','A home with less clutter is easier to clean and feels calmer.','epic',720),
(@cat,'planning','main','Run a weekly chore routine for a month','Follow a set weekly cleaning plan for four weeks','Write a weekly plan of chores, with each one on a set day. Follow it for four weeks, adjusting as you learn what works.','A routine means chores happen without daily decisions.','hard',720),
(@cat,'repairs','main','Fix everything on your repair list','Write a list of small repairs and complete at least six','Walk around your home and write a list of small things that need fixing. Complete at least six of them within 14 days.','Small broken things add up to a lot of everyday annoyance.','medium',336),
(@cat,'kitchen','main','Leave the kitchen clean every night for two weeks','Clear and wipe the kitchen before bed on 12 of 14 nights','Before bed, wash up, wipe the surfaces and clear the sink on at least 12 of the next 14 nights.','Waking up to a clean kitchen makes the whole morning easier.','medium',336),
(@cat,'garden','main','Grow herbs or vegetables for 30 days','Plant something and care for it until it grows','Plant herbs or vegetables in a pot or garden and water and care for them for 30 days. Note how they develop.','Growing something teaches patience and gives you a reward.','hard',720),

-- Side quests: cleaning
(@cat,'cleaning','side','Clean the bathroom','Scrub the sink, toilet, shower and floor','Clean all the surfaces in the bathroom, including the sink, toilet, shower and floor.','A clean bathroom is one of the most satisfying chores.','medium',24),
(@cat,'cleaning','side','Vacuum the whole home','Vacuum every floor and rug','Vacuum all the floors and rugs in your home.','Vacuuming removes the dust and dirt that you cannot see.','easy',24),
(@cat,'cleaning','side','Mop the floors','Wash the hard floors','Sweep and then mop all the hard floors in your home.','Fresh floors make a room feel completely clean.','medium',24),
(@cat,'cleaning','side','Dust every surface','Wipe shelves, tables and frames','Dust all the shelves, tables, frames and ledges in at least three rooms.','Dust builds up quickly and affects your breathing.','easy',24),
(@cat,'cleaning','side','Clean the windows','Wash the glass inside and out','Clean the windows in at least two rooms, inside and out where you can reach safely.','Clean windows make your home brighter.','medium',48),
(@cat,'cleaning','side','Wipe door handles and switches','Clean the surfaces everyone touches','Wipe door handles, light switches and remote controls with a suitable cloth.','These are the places germs collect most.','easy',24),
(@cat,'cleaning','side','Clean the inside of the microwave','Remove splashes and stains','Clean the inside of your microwave, including the plate and door.','A clean microwave also smells better.','easy',24),
(@cat,'cleaning','side','Empty all the bins and recycling','Take out rubbish and recycling from every room','Empty every bin in your home and take the rubbish and recycling out.','Clearing bins prevents smells and pests.','easy',24),

-- Side quests: laundry
(@cat,'laundry','side','Wash and dry a load of laundry','Do one full load from start to finish','Wash, dry and bring in a full load of laundry.','Small regular loads stop laundry piling up.','easy',24),
(@cat,'laundry','side','Fold and put away all clean clothes','Clear the pile of clean clothes','Fold all your clean clothes and put them away in their places.','Clothes that are put away stay clean and easy to find.','easy',24),
(@cat,'laundry','side','Wash and replace the bedding','Put fresh sheets on your bed','Wash your sheets and pillowcases and make the bed with fresh ones.','Fresh bedding feels good and helps you sleep.','easy',48),
(@cat,'laundry','side','Iron the clothes for the week','Press clothes you plan to wear','Iron the clothes you plan to wear in the coming week.','Pressed clothes make a good first impression.','medium',48),

-- Side quests: kitchen
(@cat,'kitchen','side','Wash up straight after a meal','Clean up before you relax','Wash up and clear the table straight after a meal, before doing anything else.','Washing up right away takes far less effort.','easy',24),
(@cat,'kitchen','side','Clean the fridge shelves','Wipe the shelves and drawers inside','Empty the fridge shelf by shelf, wipe each one and put the food back neatly.','A clean fridge keeps food fresher and safer.','medium',24),
(@cat,'kitchen','side','Wipe every kitchen surface','Clean worktops, hob and sink','Wipe all the surfaces in your kitchen, including the worktops, hob and sink.','A wiped kitchen feels ready for anything.','easy',24),
(@cat,'kitchen','side','Clean the hob and oven door','Remove grease and baked-on food','Clean the hob and the outside and glass of the oven door.','Grease builds up and becomes harder to remove if you wait.','medium',24),
(@cat,'kitchen','side','Clear out expired food','Check dates and throw away what is past it','Go through your fridge and cupboards and throw out anything that is past its date.','Clearing out old food avoids nasty surprises.','easy',24),
(@cat,'kitchen','side','Descale the kettle','Remove limescale from your kettle','Clean limescale out of your kettle using vinegar or a descaler and rinse well.','A clean kettle works better and tastes better.','easy',24),

-- Side quests: organising
(@cat,'organising','side','Declutter one drawer','Empty a drawer and keep only what you use','Empty one drawer, throw away or donate what you do not need and put the rest back neatly.','A single drawer is a small, satisfying win.','easy',24),
(@cat,'organising','side','Tidy your desk','Clear and organise your workspace','Clear your desk, wipe it and put back only what you need.','A tidy desk makes starting work easier.','easy',24),
(@cat,'organising','side','Sort items to donate','Put aside clothes or things you no longer use','Go through your things and put aside at least ten items to donate.','What you do not use may help someone else.','medium',24),
(@cat,'organising','side','Organise your bookshelf','Sort books so you can find them','Sort your books by type or author and put them back neatly.','An organised shelf is easy to enjoy.','easy',48),
(@cat,'organising','side','Clear a pile of paperwork','Sort letters and documents','Sort a pile of paperwork into keep, shred and recycle, and file what you keep.','Paper piles cause worry, and clearing them brings relief.','medium',24),
(@cat,'organising','side','Sort out one cupboard','Empty a cupboard and put things back in order','Empty one cupboard, sort what is inside and put back only what belongs.','Sorting one space gives you the push to do more.','medium',24),
(@cat,'organising','side','Make your bed first thing','Start the day with one small job done','Make your bed straight after you get up.','A made bed gives you an easy first win.','easy',24),
(@cat,'organising','side','Do a ten-minute tidy of one room','Set a timer and tidy as much as you can','Set a ten-minute timer and tidy one room as much as possible before it rings.','A short timer makes tidying feel doable.','easy',24),

-- Side quests: repairs
(@cat,'repairs','side','Fix something that has been broken a while','Mend one thing you keep ignoring','Choose something that has been broken for a while and fix it.','The things you keep ignoring weigh on you more than you realise.','medium',48),
(@cat,'repairs','side','Replace a bulb or battery that needs it','Swap out a worn-out part','Replace a light bulb or a battery in a device that needs it.','Small replacements keep your home working properly.','easy',24),
(@cat,'repairs','side','Tighten loose screws and handles','Check the fixings around your home','Walk around your home and tighten any loose screws, handles and hinges.','Loose fixings wear out and can break.','easy',24),

-- Side quests: planning
(@cat,'planning','side','Write a weekly chore list','List what needs doing this week','Write a list of every chore that needs doing this week.','Seeing the whole list makes the work feel smaller.','easy',24),
(@cat,'planning','side','Plan a cleaning schedule','Assign chores to days of the week','Create a simple schedule that gives each chore a day.','A schedule spreads the work out.','medium',24),
(@cat,'planning','side','Make a list of household supplies you need','Check what is running low','Check what you are low on, such as cleaning products and bin bags, and write a shopping list.','Running out in the middle of a chore is frustrating.','easy',24),
(@cat,'planning','side','Set a daily ten-minute tidy timer','Choose a time each day to tidy','Pick a time of day, set a ten-minute reminder and tidy when it rings.','A fixed time turns tidying into a habit.','easy',24),

-- Side quests: garden
(@cat,'garden','side','Water and care for your plants','Check the soil and water as needed','Check all your plants, water those that need it and remove dead leaves.','Plants reward regular care.','easy',24),
(@cat,'garden','side','Repot a plant','Give a plant a bigger pot and fresh soil','Repot a plant that has outgrown its pot, using fresh soil.','A bigger pot gives roots room to grow.','medium',48),
(@cat,'garden','side','Tidy a balcony, garden or windowsill','Clear and refresh an outdoor or plant space','Tidy a balcony, garden area or windowsill, clearing dead plants and dust.','A tidy outdoor space invites you to use it.','medium',48),

-- Bonus quests
(@cat,'cleaning','bonus','Clean while listening to music','Make a chore fun with a playlist','Put on your favourite music and clean at least one room.','Music makes chores feel quicker.','easy',24),
(@cat,'cleaning','bonus','Do a chore alongside someone','Work together on a household task','Do a chore with a friend or family member so it goes twice as fast.','Shared chores are lighter and more sociable.','easy',48),
(@cat,'organising','bonus','Upcycle an old item','Give something old a new purpose','Turn an item you would throw away into something useful, such as a jar into a pot.','Upcycling saves money and makes unique things.','medium',48),
(@cat,'cleaning','bonus','Learn a new cleaning trick','Find a better way to do a chore','Look up a cleaning trick, such as cleaning a kettle or removing a stain, and try it.','A good trick saves time every time you use it.','easy',24),
(@cat,'organising','bonus','Rearrange a room','Change the layout of a room you use daily','Move the furniture in one room into a new arrangement and see how it feels.','A new layout can make a familiar room feel fresh.','medium',48),
(@cat,'cleaning','bonus','Thank someone for a chore they do','Say thank you to the person who keeps things running','Thank someone for a chore they do, big or small.','Appreciated work feels lighter.','easy',24),
(@cat,'organising','bonus','Clear digital clutter','Delete old files, photos and apps','Delete at least 30 old files, photos or apps you no longer need.','Digital clutter slows you down just like physical clutter.','easy',24),
(@cat,'planning','bonus','Swap chores with someone for a day','Take on a chore you usually do not do','Swap a chore with a housemate or family member for a day.','Trying someone elses job builds understanding.','easy',48),
(@cat,'cleaning','bonus','Make a natural cleaning spray','Mix a simple cleaner from household items','Make a simple cleaning spray from vinegar and water and use it on a surface.','Homemade cleaners are cheap and gentle.','easy',24),
(@cat,'cleaning','bonus','Clean a spot you usually skip','Tackle the place you always avoid','Clean somewhere you usually skip, such as behind the sofa or on top of a cupboard.','The forgotten spots are where the dust hides.','easy',24),
(@cat,'organising','bonus','Race the clock in a 15-minute tidy','Tidy as much as you can in 15 minutes','Set a timer for 15 minutes and tidy as much as you can before it ends.','A race makes the work playful.','easy',24),
(@cat,'organising','bonus','Donate or recycle unused items','Pass on things you no longer use','Take a bag of things you no longer use to a donation point or recycling.','Giving things away clears space and helps others.','easy',48),
(@cat,'cleaning','bonus','Wash your bike or car','Clean a vehicle inside and out','Wash your bike or car, including the wheels and windows.','A clean vehicle is nicer to use and lasts longer.','medium',48),
(@cat,'cleaning','bonus','Help a neighbour with a chore','Offer a hand to someone nearby','Offer to help a neighbour with a chore, such as carrying shopping or taking out bins.','Small kindnesses make a neighbourhood friendlier.','easy',48),
(@cat,'organising','bonus','Take before and after photos of a tidy','Show your progress','Take a photo before you tidy a space and another afterwards, and compare them.','Seeing the difference shows how much you did.','easy',24),
(@cat,'planning','bonus','Treat yourself after finishing your chores','Plan a small reward','Plan a small reward for when you finish your chores today and enjoy it.','A reward makes it easier to start next time.','easy',24);