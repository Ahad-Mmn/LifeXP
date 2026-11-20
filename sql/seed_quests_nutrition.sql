SET @cat = (SELECT id FROM categories WHERE name = 'Nutrition');

INSERT IGNORE INTO quest_templates
    (category_id, track, quest_type, title, summary, description, why, difficulty, time_limit_hours)
VALUES
-- Main quests
(@cat,'produce','main','Five portions of fruit and veg for a week','Eat five portions of fruit or vegetables every day for seven days','Eat at least five portions of fruit and vegetables on each of seven days in a row. A portion is about a handful. If you miss a day, the week starts again.','Five a day is a simple target backed by a great deal of evidence.','medium',168),
(@cat,'cooking','main','Cook dinner ten times in two weeks','Prepare your own dinner on 10 of 14 days','Cook your own dinner from basic ingredients on at least 10 of the next 14 days.','Cooking for yourself gives you control over what is in your food.','hard',336),
(@cat,'planning','main','Plan and cook meals for a full week','Write a meal plan and cook what you planned','Write a meal plan for seven days, shop for it and cook the meals as planned, with room to swap days.','Planning saves money, time and stress, and reduces unhealthy last-minute choices.','hard',168),
(@cat,'snacks','main','Three weeks without sugary drinks','Avoid sugary drinks on 18 of 21 days','Avoid soft drinks, sweetened juices and energy drinks on at least 18 of the next 21 days.','Sugary drinks are one of the biggest sources of extra sugar in most diets.','hard',504),
(@cat,'cooking','main','Try ten new healthy recipes in 30 days','Cook ten recipes you have never made before','Find and cook ten new healthy recipes over the next 30 days. Note which ones you want to make again.','Variety keeps healthy eating interesting and widens the range of nutrients you get.','epic',720),
(@cat,'protein','main','Protein with every meal for two weeks','Include a good protein source at every main meal for 14 days','Include a protein source such as eggs, fish, beans, lentils, meat or tofu at every main meal on at least 12 of 14 days.','Protein keeps you full and helps muscle repair.','medium',336),
(@cat,'planning','main','Eat breakfast every day for two weeks','Have a proper breakfast on 12 of 14 days','Eat a real breakfast on at least 12 of the next 14 days.','A steady breakfast helps many people avoid mid-morning snacking.','medium',336),
(@cat,'mindful','main','Eat mindfully for three weeks','Eat at least one meal a day slowly and without screens for 21 days','Eat one meal each day slowly, without a screen, noticing taste and fullness. Do it on at least 17 of 21 days.','Eating slowly helps you notice fullness and enjoy food more.','hard',504),

-- Side quests: cooking
(@cat,'cooking','side','Cook a meal from scratch','Make a full meal from basic ingredients','Cook one full meal from basic ingredients without using ready-made sauces or packaged meals.','Cooking from scratch shows you exactly what you are eating.','easy',24),
(@cat,'cooking','side','Make a recipe you have not tried','Cook something new from a recipe','Find a recipe you have never made and cook it.','Trying new recipes stops healthy eating from getting boring.','medium',24),
(@cat,'cooking','side','Prepare a healthy lunch to take with you','Pack your own lunch for tomorrow','Prepare and pack a balanced lunch to take to work or study.','A packed lunch is cheaper and usually healthier than the alternatives.','easy',24),
(@cat,'cooking','side','Make homemade soup','Cook a soup with fresh ingredients','Make a pot of soup using fresh vegetables and a simple stock.','Soup is an easy way to eat several vegetables at once.','medium',24),
(@cat,'cooking','side','Roast a tray of vegetables','Roast vegetables with oil and seasoning','Chop vegetables, toss them in a little oil and seasoning and roast until golden.','Roasting brings out flavour and makes vegetables much more appealing.','easy',24),
(@cat,'cooking','side','Make a healthy breakfast from scratch','Prepare a breakfast with whole ingredients','Make a breakfast such as porridge with fruit or eggs with vegetables.','A good breakfast sets up steady energy for the morning.','easy',24),
(@cat,'cooking','side','Cook a one-pan meal','Make a full meal in a single pan or tray','Cook a complete meal in one pan or one tray, with protein, vegetables and a carbohydrate.','Easy meals make it far more likely you will cook on a busy day.','easy',24),
(@cat,'cooking','side','Batch cook two portions for tomorrow','Cook extra and store it for another meal','Cook double and keep the extra portions for tomorrow, so another meal is already done.','Batch cooking is the best defence against takeaway on a tired evening.','medium',24),

-- Side quests: produce
(@cat,'produce','side','Eat five portions of fruit and vegetables','Reach five portions today','Count your portions of fruit and vegetables today and reach at least five.','Five portions a day is a proven target for good health.','medium',24),
(@cat,'produce','side','Add a vegetable to every meal','Include at least one vegetable in each meal today','Add at least one vegetable to every meal you eat today, including breakfast.','Making vegetables routine is easier than treating them as a special effort.','easy',24),
(@cat,'produce','side','Eat a four-ingredient salad','Make a salad with at least four different ingredients','Make a salad with at least four different ingredients and a simple dressing.','A varied salad covers several kinds of nutrients at once.','easy',24),
(@cat,'produce','side','Eat fruit instead of a sweet snack','Swap a sweet snack for fruit','Choose a piece of fruit instead of a biscuit or sweets.','Fruit gives sweetness along with fibre and vitamins.','easy',24),
(@cat,'produce','side','Try a vegetable you rarely eat','Buy and eat a vegetable you usually skip','Choose a vegetable you rarely eat and cook it in a way you think you might enjoy.','Tastes change, and cooking method makes a huge difference.','easy',48),
(@cat,'produce','side','Make a real-fruit smoothie','Blend fruit with no added sugar','Make a smoothie from whole fruit, yoghurt or milk, with no added sugar.','A smoothie can pack in fruit when you are short on time.','easy',24),

-- Side quests: planning
(@cat,'planning','side','Plan three dinners','Decide your next three dinners','Write down what you will cook for the next three dinners.','Deciding in advance removes the nightly what-shall-we-eat stress.','easy',24),
(@cat,'planning','side','Write a weekly meal plan','Plan your meals for seven days','Write a plan covering breakfast, lunch and dinner for the whole week.','A weekly plan makes shopping and cooking far simpler.','medium',24),
(@cat,'planning','side','Plan around what is in your fridge','Check your fridge and cupboards and plan meals from them','Check what you already have and plan meals that use it up before it spoils.','Using what you have cuts food waste and saves money.','easy',24),
(@cat,'planning','side','Prep ingredients for tomorrow','Chop, wash or pre-cook for the next day','Prepare ingredients for tomorrow, such as chopping vegetables or cooking rice.','Prep done tonight makes tomorrow meal much quicker.','easy',24),
(@cat,'planning','side','Prepare breakfast the night before','Make overnight oats or something similar','Prepare overnight oats or another breakfast the night before so it is ready to eat.','A ready breakfast takes away the morning excuse for skipping it.','easy',24),

-- Side quests: protein
(@cat,'protein','side','Protein in every meal','Include a protein source in each meal today','Add a protein source to breakfast, lunch and dinner today.','Protein keeps you fuller for longer and supports your muscles.','easy',24),
(@cat,'protein','side','Cook a bean or lentil meal','Make a meal built around pulses','Cook a meal where beans, lentils or chickpeas are the main protein.','Pulses are cheap, filling and full of fibre.','medium',24),
(@cat,'protein','side','Cook fish or a lean protein meal','Prepare fish, chicken or tofu for dinner','Cook a meal built around fish, lean meat or tofu.','Lean protein supports muscle and is satisfying without being heavy.','medium',24),
(@cat,'protein','side','Eat a high-protein breakfast','Start the day with a protein-rich meal','Eat a breakfast containing protein, such as eggs, yoghurt or beans.','A protein breakfast helps keep hunger away until lunch.','easy',24),

-- Side quests: snacks
(@cat,'snacks','side','Swap a processed snack for a whole food','Choose nuts, fruit or yoghurt instead of a packaged snack','Replace one packaged snack with a whole food such as nuts, fruit or plain yoghurt.','Whole foods give nutrients instead of just sugar and salt.','easy',24),
(@cat,'snacks','side','Go a day without sugary drinks','Avoid sweet drinks for a full day','Avoid soft drinks, sweetened juices and energy drinks for the full day.','Cutting sugary drinks is one of the simplest nutrition wins.','medium',24),
(@cat,'snacks','side','Prepare a healthy snack box','Pack snacks to take with you','Pack a small box with healthy snacks, such as fruit and nuts, to take with you today.','Having a good snack with you stops you buying a poor one.','easy',24),
(@cat,'snacks','side','Read three food labels','Check the labels on three foods you eat','Read the ingredient list and nutrition label of three foods you regularly eat.','Labels tell you what you are really eating.','easy',24),

-- Side quests: mindful eating
(@cat,'mindful','side','Eat one meal slowly without screens','Take your time over one meal','Eat one meal slowly, without a phone, TV or laptop.','Eating slowly lets you notice fullness before you overeat.','easy',24),
(@cat,'mindful','side','Stop when comfortably full','Pay attention to fullness at one meal','At one meal stop eating when you feel comfortably full rather than finishing everything.','Listening to hunger and fullness is a skill that gets easier with practice.','medium',24),
(@cat,'mindful','side','Chew each bite slowly','Put your fork down between bites for one meal','During one meal, put your fork down between bites and chew slowly.','Slowing down gives your body time to register fullness.','easy',24),
(@cat,'mindful','side','Sit at a table for every meal','Avoid eating on the move or on the sofa today','Eat every meal today sitting at a table.','Eating at a table makes meals feel like meals and not an afterthought.','easy',24),

-- Side quests: shopping
(@cat,'shopping','side','Shop with a list','Make a list before shopping and stick to it','Write a shopping list based on your meal plan and buy only what is on it.','A list protects both your budget and your diet.','easy',48),
(@cat,'shopping','side','Read the ingredients on five products','Check ingredient lists while shopping','Read the ingredient lists on five products in the shop and put back any you do not want.','Shorter ingredient lists usually mean less processed food.','easy',48),
(@cat,'shopping','side','Buy one new whole food','Pick up a whole ingredient you have never cooked with','Buy one whole food ingredient you have never used before, such as a grain, a vegetable or a pulse.','New ingredients widen your menu and your nutrition.','easy',48),
(@cat,'shopping','side','Visit a fresh produce market','Shop at a market or fresh produce shop','Buy fruit and vegetables from a market or a fresh produce shop.','Fresh local produce is usually cheaper and tastier.','easy',48),
(@cat,'shopping','side','Compare unit prices','Pick the better value item on three products','Compare the price per kilogram or litre on three products and choose the better value one.','Unit prices show you which option is really cheaper.','easy',48),

-- Bonus quests
(@cat,'cooking','bonus','Cook for a friend or family member','Prepare a meal for someone else','Cook a meal for a friend or family member and eat it together.','Cooking for others makes healthy eating a shared pleasure.','easy',48),
(@cat,'cooking','bonus','Try a cuisine you have never eaten','Cook or order food from a cuisine new to you','Cook or order a dish from a cuisine you have never tried.','New cuisines open up a lot of healthy ideas.','medium',48),
(@cat,'produce','bonus','Grow herbs on your windowsill','Plant a herb pot','Buy or plant a pot of herbs and keep it on a windowsill.','Fresh herbs make simple meals taste much better.','easy',48),
(@cat,'mindful','bonus','Eat dinner with someone, no screens','Share a meal with another person and talk','Share a dinner with someone and keep every screen away.','Shared meals are good for both eating habits and relationships.','easy',24),
(@cat,'cooking','bonus','Learn a new knife skill','Practise a chopping technique','Learn and practise a new knife skill, such as dicing an onion properly.','Good knife skills make cooking quicker and more enjoyable.','easy',24),
(@cat,'protein','bonus','Cook a plant-based meal','Make a meal with no meat or fish','Cook a full meal that uses only plant-based ingredients.','Plant-based meals add variety and are often cheaper.','easy',24),
(@cat,'cooking','bonus','Make your own dressing or sauce','Prepare a sauce from basic ingredients','Make a dressing or sauce from basic ingredients instead of using a bottle.','Homemade sauces usually have far less sugar and salt.','easy',24),
(@cat,'snacks','bonus','Drink herbal tea instead of a sweet drink','Replace a sugary drink with tea','Choose herbal tea or another unsweetened drink instead of a sweet one.','Warm unsweetened drinks can satisfy a craving without sugar.','easy',24),
(@cat,'shopping','bonus','Read a nutrition article from a trusted source','Learn one new fact about food','Read one article on nutrition from a trusted health source.','Good information makes good choices easier.','easy',24),
(@cat,'cooking','bonus','Pack a picnic','Prepare food to eat outdoors','Prepare food and eat it outdoors, at a park or anywhere you like.','Meals outside are a simple pleasure that gets you outdoors too.','easy',48),
(@cat,'cooking','bonus','Ferment or pickle something simple','Make quick pickles from vegetables','Make a jar of quick pickled vegetables or something similar.','Pickling is easy and gives a lasting source of flavour.','medium',48),
(@cat,'planning','bonus','Share a recipe with someone','Pass on a recipe you like','Share a favourite healthy recipe with a friend or family member.','Sharing recipes spreads good habits.','easy',48),
(@cat,'cooking','bonus','Taste five spices in one meal','Use several spices in a dish','Cook a dish that uses at least five different spices and notice the flavours.','Spices add flavour without adding salt or sugar.','easy',24),
(@cat,'produce','bonus','Cook with seasonal ingredients','Use fruit or vegetables that are in season','Cook a meal using fruit or vegetables that are in season now.','Seasonal produce is fresher, cheaper and tastier.','easy',24),
(@cat,'produce','bonus','Try a new fruit','Taste a fruit you have never eaten','Buy and eat a fruit you have never tried before.','Variety in fruit means variety in nutrients.','easy',24),
(@cat,'cooking','bonus','Make a healthy dessert','Prepare a sweet treat with real ingredients','Make a dessert based on fruit, yoghurt or oats, with little added sugar.','A good homemade dessert makes healthy eating feel like a treat.','medium',24);