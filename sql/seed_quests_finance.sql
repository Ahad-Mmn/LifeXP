SET @cat = (SELECT id FROM categories WHERE name = 'Finance');

INSERT IGNORE INTO quest_templates
    (category_id, track, quest_type, title, summary, description, why, difficulty, time_limit_hours)
VALUES
-- Main quests
(@cat,'budgeting','main','Track every expense for 30 days','Record all your spending for a month','Write down every purchase for 30 days, large or small, using a notebook, app or spreadsheet. At the end, add up the totals by category.','You cannot manage money you cannot see, and one month of data usually surprises people.','hard',720),
(@cat,'saving','main','Save something every week for a month','Put money aside in each of four weeks','Choose an amount you can afford and move it into savings in each of four consecutive weeks.','Regular small amounts matter more than occasional big ones.','medium',720),
(@cat,'budgeting','main','Build a monthly budget and follow it','Plan your income and spending, then stick to it for a month','List your income and all regular costs, set limits for the rest, and track how well you keep to them over 30 days.','A budget is a plan for your money rather than a list of things you cannot do.','epic',720),
(@cat,'spending','main','Two weeks without impulse purchases','Buy only planned items for 14 days','For 14 days buy only things that were on a list or are real needs. Wait 24 hours before any other purchase.','Pausing before you buy shows how many purchases you did not actually want.','hard',336),
(@cat,'saving','main','Start a small emergency fund','Set a target and save toward it over 30 days','Choose a target amount for a first emergency fund and save toward it over the next 30 days, setting it aside in a separate account.','A cushion turns an emergency from a crisis into an inconvenience.','epic',720),
(@cat,'learning','main','Learn the basics of personal finance','Read a book or finish a short course on money','Finish a beginner book or short course on personal finance within 21 days, noting three things you will change.','Understanding the basics is the best long-term investment in yourself.','hard',504),
(@cat,'spending','main','Seven days with no unnecessary spending','Spend only on essentials for a week','For seven days spend only on essentials such as rent, food, transport and bills. Note how you feel by the end.','A short break from extras shows what you really value.','hard',168),
(@cat,'planning','main','Review all your subscriptions and bills','Check every regular payment and cancel or reduce what you can','Over the next 14 days list every subscription and regular bill, check whether each is worth it, and cancel or reduce at least one.','Regular payments quietly drain money, and a review often finds easy savings.','medium',336),

-- Side quests: budgeting
(@cat,'budgeting','side','Write down everything you spend today','Record every purchase for one day','Record every purchase you make today, however small.','Awareness is the first step to control.','easy',24),
(@cat,'budgeting','side','Check your bank balance and note it','Look at your balance and write it down','Open your banking app, check your balance and note it with the date.','Knowing where you stand removes a lot of money worry.','easy',24),
(@cat,'budgeting','side','List your fixed monthly costs','Write down every regular bill','List every regular monthly cost, such as rent, bills and subscriptions, with the amounts.','Fixed costs form the base of any budget.','medium',24),
(@cat,'budgeting','side','List your income and expenses for the month','Put the whole picture on one page','Write your monthly income and all expenses and see what is left.','The balance shows whether you are saving or slipping.','medium',24),
(@cat,'budgeting','side','Sort last weeks spending into categories','Group recent purchases','Look at last weeks spending and sort it into groups such as food, transport and fun.','Groups show where your money really goes.','medium',24),
(@cat,'budgeting','side','Set a spending limit for the week','Choose a flexible spending cap','Choose a limit for non-essential spending this week and write it down.','A limit makes everyday choices simpler.','easy',24),
(@cat,'budgeting','side','Check one bill for errors','Read a bill carefully','Check one recent bill for mistakes or charges you do not recognise.','Errors are more common than people think and are often easy to fix.','easy',48),
(@cat,'budgeting','side','Plan the weeks food spending','Decide how much you will spend on food','Decide on a food budget for the week and base your shopping list on it.','Food is one of the easiest costs to bring down.','easy',24),

-- Side quests: saving
(@cat,'saving','side','Move a small amount into savings','Transfer any amount you can spare','Transfer a small amount into a savings account today.','The habit matters more than the amount.','easy',24),
(@cat,'saving','side','Round up a purchase and save the difference','Save the change from a purchase','Round up one purchase to the next pound and move the difference into savings.','Small round-ups add up faster than you expect.','easy',24),
(@cat,'saving','side','Set a savings goal with a figure and a date','Write a clear target','Write down one savings goal with an amount and a date.','A goal with a number and a date is far more likely to be reached.','medium',24),
(@cat,'saving','side','Cut one cost and save the money','Find a small saving and move it into savings','Find one regular cost you can reduce and move the saving into savings.','Each saving you find keeps working for you.','medium',48),
(@cat,'saving','side','Sell or donate something you do not use','Clear something out and turn it into cash or good','Sell or donate an item you no longer use, and save any money you get.','Unused things are idle money, or help for someone else.','medium',48),
(@cat,'saving','side','Review your savings account','Check the rate and terms of your account','Look at the interest rate and terms on your savings account and compare it with one alternative.','A better rate means more money for the same effort.','medium',48),

-- Side quests: spending
(@cat,'spending','side','Wait 24 hours before a non-essential purchase','Delay one purchase for a day','Pick one non-essential purchase and wait 24 hours before you decide.','Many wants fade when you wait.','easy',24),
(@cat,'spending','side','Compare prices on one purchase','Check at least three sellers','Compare prices from at least three sellers before buying something.','A few minutes of checking can save a lot.','easy',24),
(@cat,'spending','side','Cook instead of buying a meal out','Make food at home for one meal','Cook at home for one meal you would usually buy.','Home cooking is almost always cheaper.','easy',24),
(@cat,'spending','side','Cancel or pause one unused subscription','Stop paying for something you do not use','Find a subscription you rarely use and cancel or pause it.','Unused subscriptions are money for nothing.','medium',24),
(@cat,'spending','side','Go a whole day without spending','Spend nothing at all for 24 hours','Avoid spending any money for the whole day, apart from bills already due.','A no-spend day shows how much of your spending is automatic.','medium',24),
(@cat,'spending','side','Unsubscribe from shopping emails','Remove temptation from your inbox','Unsubscribe from at least five shopping or promotional emails.','Fewer ads mean fewer impulse buys.','easy',24),

-- Side quests: learning
(@cat,'learning','side','Read an article about budgeting','Learn one new budgeting idea','Read an article about budgeting from a trusted source and note one idea to try.','Good ideas only help if you act on them.','easy',24),
(@cat,'learning','side','Watch a video on how interest works','Understand compound interest','Watch a short video explaining interest and compound growth.','Interest works for you when you save and against you when you borrow.','easy',24),
(@cat,'learning','side','Learn what a credit score is','Find out how credit scores work','Read about how credit scores work and what affects yours.','A good score can save you real money over the years.','easy',24),
(@cat,'learning','side','Learn the difference between saving and investing','Compare the two','Read a guide explaining the difference between saving and investing, including the risks.','Knowing the difference stops you making costly mistakes.','medium',24),
(@cat,'learning','side','Learn how your pension or savings plan works','Find out the basics of your retirement plan','Look up how your pension or long-term savings plan works and what you pay in.','Understanding your plan helps you make better use of it.','medium',48),
(@cat,'learning','side','Use a budgeting calculator','Try an online calculator','Use a free online budgeting calculator to see where your money goes.','A calculator turns vague worry into numbers.','easy',24),

-- Side quests: planning
(@cat,'planning','side','Plan for an upcoming expense','Prepare for a cost you know is coming','List an upcoming expense, estimate the cost and decide how you will cover it.','Planned costs are far less stressful than surprises.','medium',48),
(@cat,'planning','side','Write a shopping list and stick to it','Buy only what is on the list','Write a shopping list before you go, and buy only what is on it.','A list protects your budget and your time.','easy',24),
(@cat,'planning','side','Set calendar reminders for your bills','Never miss a payment date','Add your regular bill due dates to your calendar with reminders.','Late fees are money wasted.','easy',24),
(@cat,'planning','side','Organise your financial documents','Put your paperwork in order','Gather your important financial documents and file them in one place, paper or digital.','Organised records save time and stress when you need them.','medium',48),
(@cat,'planning','side','Choose one money goal for the month','Pick a single focus','Choose one specific money goal for this month and write it where you will see it.','One clear goal beats five vague ones.','easy',24),

-- Side quests: security
(@cat,'security','side','Check your accounts for unfamiliar transactions','Scan recent transactions for anything odd','Look through the last month of transactions for anything you do not recognise.','Spotting fraud early limits the damage.','easy',24),
(@cat,'security','side','Update an important password','Change the password on a key account','Change the password on your bank, email or another key account to a strong, unique one.','Your email is the key to everything else, so protect it well.','easy',24),
(@cat,'security','side','Turn on two-step verification','Add a second lock to your bank or email','Switch on two-step verification for your bank or email account.','A second step blocks most account takeovers.','medium',24),
(@cat,'security','side','Learn to spot a scam message','Read about common scams','Read about common scam messages and write down three warning signs.','Scammers rely on speed and fear, and knowing the signs slows you down.','easy',24),
(@cat,'security','side','Check your credit report','Look at your report for mistakes','Get a free copy of your credit report and check it for errors.','Mistakes on your report can cost you money, and they are free to fix.','medium',48),

-- Bonus quests
(@cat,'saving','bonus','Start a savings challenge with a friend','Save together toward a goal','Agree a small savings challenge with a friend and check in with each other.','Having someone to answer to keeps you going.','easy',48),
(@cat,'spending','bonus','Find a free activity instead of a paid one','Enjoy something that costs nothing','Choose a free activity such as a walk, a park or a library visit in place of a paid one.','Good times do not have to cost money.','easy',24),
(@cat,'learning','bonus','Listen to a personal finance podcast','Hear a new idea about money','Listen to a full episode of a personal finance podcast.','Passive time can become learning time.','easy',24),
(@cat,'spending','bonus','Skip buying coffee out today','Make a drink at home instead','Make your drinks at home today instead of buying them out.','Small daily purchases add up to large yearly totals.','easy',24),
(@cat,'planning','bonus','Write a financial goal for next year','Describe what you want money to do for you','Write one goal for your money over the next year and why it matters to you.','Goals give your spending a direction.','easy',24),
(@cat,'learning','bonus','Share a money tip with someone','Teach a friend one thing you learned','Share one useful money tip you have learned with a friend or family member.','Teaching helps you remember and helps them too.','easy',48),
(@cat,'saving','bonus','Start a loose change jar','Collect spare coins','Set up a jar for spare coins and put in whatever you have today.','Small habits can grow into real savings.','easy',24),
(@cat,'spending','bonus','Borrow something instead of buying it','Use a library, a friend or a loan option','Borrow something you need once instead of buying it.','Not owning everything is one of the cheapest ways to live well.','easy',48),
(@cat,'budgeting','bonus','Try a budgeting app','Test a free budgeting tool','Try a free budgeting app for a day and see if it suits you.','Finding the right tool makes budgeting easier.','medium',48),
(@cat,'learning','bonus','Read a finance article from a trusted source','Learn one thing from a reliable site','Read an article from a trusted personal finance source and note one thing to remember.','Reliable sources protect you from bad advice.','easy',24),
(@cat,'spending','bonus','Mend something instead of replacing it','Repair an item you would normally throw away','Fix a broken item, such as sewing a seam or tightening a screw, instead of replacing it.','Repairing saves money and builds a useful skill.','medium',48),
(@cat,'planning','bonus','Set up a small automatic transfer','Save without having to remember','Set up a small automatic transfer into savings each month.','Automation makes saving effortless.','medium',48),
(@cat,'spending','bonus','Swap something with a friend','Trade books, clothes or tools','Swap something you no longer use with a friend for something they no longer use.','Swapping gives things a second life at no cost.','easy',48),
(@cat,'learning','bonus','Learn one new money word','Look up a term you do not understand','Look up a money word you do not fully understand, such as APR or ISA, and explain it in your own words.','Plain understanding beats pretending you know.','easy',24),
(@cat,'saving','bonus','Write what you would do with an extra hundred pounds','Imagine a small windfall','Write down what you would do with an extra hundred pounds and why.','It shows you what you really value.','easy',24),
(@cat,'planning','bonus','Look up your banks help page','Find out what your bank offers','Visit your banks website or branch and read about the tools and accounts it offers.','Many people miss helpful features they already have.','easy',48);