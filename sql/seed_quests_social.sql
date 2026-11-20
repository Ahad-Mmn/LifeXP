SET @cat = (SELECT id FROM categories WHERE name = 'Social');

INSERT IGNORE INTO quest_templates
    (category_id, track, quest_type, title, summary, description, why, difficulty, time_limit_hours)
VALUES
-- Main quests
(@cat,'connection','main','Reach out to a friend every day for a week','Contact a different friend on each of seven days','Over seven days, send a message or call a different friend or family member each day. Keep it genuine, not just a quick greeting.','Friendships fade without small, regular contact.','medium',168),
(@cat,'family','main','A weekly family meal for a month','Share a meal with family on four weekends in 30 days','Share a meal with family, at home or out, once a week for four weeks, with phones away.','Regular shared meals are one of the strongest family habits.','medium',720),
(@cat,'community','main','Volunteer three times in 30 days','Give your time to a cause three times','Volunteer for a local cause, charity or group on three separate occasions within 30 days.','Helping others is good for them and for you.','hard',720),
(@cat,'connection','main','Reconnect with five old friends','Reach out to five people you have lost touch with','Over two weeks, get in touch with five old friends or acquaintances and arrange to catch up with at least two.','Old friends are often only one message away.','hard',336),
(@cat,'communication','main','Practise active listening for three weeks','Listen fully in at least one conversation a day on 17 of 21 days','In at least one conversation each day, listen without interrupting, ask questions and repeat back what you heard. Do it on at least 17 of 21 days.','Being listened to is rare, and people remember who does it.','medium',504),
(@cat,'community','main','Organise a get-together','Plan and host a gathering for friends or neighbours','Plan, invite and host a gathering, such as a games night, a picnic or a meal, within 14 days.','Bringing people together creates connections that outlast the event.','hard',336),
(@cat,'kindness','main','Keep a kindness journal for 30 days','Record an act of kindness on 25 of 30 days','Each day do and note one act of kindness, large or small, on at least 25 of the next 30 days.','Kindness is a habit, and writing it down builds it.','hard',720),
(@cat,'community','main','Join a club or class and attend four times','Commit to a regular group activity','Join a club, class or group and attend at least four sessions within 30 days.','Regular shared activities are the easiest way to make friends.','epic',720),

-- Side quests: connection
(@cat,'connection','side','Message a friend you have not spoken to lately','Send a thoughtful message','Send a friend you have not spoken to in a while a message that goes beyond hello.','One message can restart a friendship.','easy',24),
(@cat,'connection','side','Call a friend instead of texting','Hear their voice','Phone a friend rather than texting.','A call carries more warmth than any message.','easy',24),
(@cat,'connection','side','Send a voice message to someone','Share your voice','Record and send a short voice message to someone you care about.','A voice message feels personal and takes no scheduling.','easy',24),
(@cat,'connection','side','Ask a friend how they really are, and listen','Have a real check-in','Ask a friend how they really are, and listen without trying to fix things.','Being heard is often all someone needs.','medium',24),
(@cat,'connection','side','Plan a meet-up for this week','Arrange to see someone in person','Choose someone you would like to see and set a time and place this week.','Plans only happen when someone makes them.','medium',48),
(@cat,'connection','side','Have a coffee or walk with someone','Spend time together face to face','Meet someone for a coffee or a walk.','Time in person strengthens bonds like nothing else.','medium',48),
(@cat,'connection','side','Share a memory with an old friend','Remind someone of a good time','Message an old friend with a happy memory you share.','Shared memories remind people they matter.','easy',24),
(@cat,'connection','side','Check in on someone who has been quiet','Reach out to someone you have not heard from','Message someone who has been quiet lately to see how they are.','A short check-in can mean a lot to someone struggling.','easy',24),

-- Side quests: family
(@cat,'family','side','Call a family member','Phone a relative','Call a family member and ask about their week.','Regular contact keeps family close across distance.','easy',24),
(@cat,'family','side','Ask a relative about their life story','Learn something new about someone close','Ask a relative about an important time in their life and listen.','Family stories are easy to lose if nobody asks.','medium',48),
(@cat,'family','side','Share a meal with family','Eat together','Share a meal with family members, with phones away.','Shared meals are the heart of family time.','easy',48),
(@cat,'family','side','Help a family member with a task','Lend a hand','Offer to help a family member with something that needs doing.','Practical help is a clear way to show you care.','medium',48),
(@cat,'family','side','Look through old photos with someone','Share a trip down memory lane','Look through old photos with a family member and talk about them.','Photos start stories that might otherwise never be told.','easy',48),
(@cat,'family','side','Message a relative you rarely contact','Reconnect with someone in your wider family','Send a friendly message to a relative you rarely speak to.','Distant relatives often appreciate hearing from you most.','easy',24),

-- Side quests: kindness
(@cat,'kindness','side','Give someone a genuine compliment','Say something kind and true','Give someone a sincere compliment about something specific.','A kind word can brighten a whole day.','easy',24),
(@cat,'kindness','side','Do a small favour without being asked','Help without waiting for a request','Do something helpful for someone without being asked.','Offering help before it is requested is a rare and welcome gift.','easy',24),
(@cat,'kindness','side','Smile and thank three people','Notice the people around you','Make eye contact, smile and say thank you to three people you meet today, such as a driver or shop worker.','Small courtesies make the world feel friendlier.','easy',24),
(@cat,'kindness','side','Write a short thank-you note','Put your thanks in writing','Write a short thank-you note to someone who has helped you.','A written note is kept and reread.','easy',24),
(@cat,'kindness','side','Help a neighbour','Offer a hand to someone nearby','Offer help to a neighbour, such as carrying something, a lift or a favour.','Good neighbours make a place feel like home.','medium',48),
(@cat,'kindness','side','Leave a kind review for a local business','Support somewhere you like','Write a positive review for a local business you value.','Reviews help small businesses far more than people realise.','easy',24),

-- Side quests: communication
(@cat,'communication','side','Practise active listening in one conversation','Give someone your full attention','In one conversation, listen without interrupting and ask a follow-up question.','Good listening is the foundation of every relationship.','medium',24),
(@cat,'communication','side','Start a conversation with someone new','Say hello to someone you do not know','Start a friendly conversation with someone you have not met before.','Every friend was once a stranger.','medium',48),
(@cat,'communication','side','Ask three open questions in a conversation','Use questions that start with how or what','Ask three open questions that need more than a yes or no answer.','Open questions show interest and lead to better conversations.','easy',24),
(@cat,'communication','side','Put your phone away during a conversation','Be fully present','Keep your phone out of sight for a whole conversation.','Giving full attention is a gift that is increasingly rare.','easy',24),
(@cat,'communication','side','Say clearly what you need in one situation','Ask for something directly and kindly','Pick one situation and state clearly and kindly what you need.','Clear requests prevent resentment and confusion.','medium',24),
(@cat,'communication','side','Apologise to someone if you owe it','Repair something that went wrong','If you owe someone an apology, give a sincere one without excuses.','A real apology can heal a rift that silence would widen.','medium',48),

-- Side quests: community
(@cat,'community','side','Attend a local event','Go to something happening nearby','Attend a local event, such as a market, a talk or a gathering.','Local events are the easiest way to feel part of a place.','medium',48),
(@cat,'community','side','Support a local business','Spend your money close to home','Buy something from a local independent business.','Local businesses keep a community alive.','easy',48),
(@cat,'community','side','Donate something you do not use','Give something useful to charity','Donate an item or two you no longer need to a charity shop or food bank.','Things you do not use may be exactly what someone else needs.','easy',48),
(@cat,'community','side','Join an online group about a hobby','Find people who share an interest','Join an online group about a hobby you enjoy and say hello.','Shared interests make talking easy.','easy',48),
(@cat,'community','side','Learn the name of a neighbour or regular face','Turn a familiar face into a name','Find out the name of a neighbour or someone you often see and use it next time.','Names turn strangers into community.','easy',48),

-- Side quests: play
(@cat,'play','side','Play a board game or card game with someone','Enjoy a game together','Play a board or card game with a friend or family member.','Games are one of the oldest ways to bond.','easy',48),
(@cat,'play','side','Watch a film with a friend and talk about it','Share a story and a conversation','Watch a film with a friend and talk about it afterwards.','Talking about a story shows how someone thinks.','easy',48),
(@cat,'play','side','Cook with someone','Make a meal together','Cook a meal together with a friend or family member.','Working side by side makes conversation easy.','medium',48),
(@cat,'play','side','Play a sport with friends','Be active together','Play a sport or active game with friends.','Playing together builds friendship and fitness at once.','medium',48),
(@cat,'play','side','Share a joke or funny moment','Make someone laugh','Share a joke or a funny story with someone today.','Laughter is a fast way to feel close.','easy',24),

-- Bonus quests
(@cat,'community','bonus','Host a small dinner','Invite a few people for a meal','Invite two or three people over for a simple meal.','Hosting is a generous way to build friendships.','medium',48),
(@cat,'connection','bonus','Write a letter by hand to someone','Send something personal in the post','Write a letter by hand to a friend or relative and post it.','A handwritten letter is rare and treasured.','easy',48),
(@cat,'connection','bonus','Introduce two friends','Bring two people together','Introduce two friends who might get on well.','Connecting people strengthens your whole circle.','easy',48),
(@cat,'kindness','bonus','Plan a small surprise for someone','Do something thoughtful and unexpected','Plan a small, thoughtful surprise for someone you care about.','Surprises show someone they have been on your mind.','medium',48),
(@cat,'community','bonus','Try a group activity you have never done','Step outside your comfort zone','Try a class or group activity that is new to you.','New groups mean new people.','medium',48),
(@cat,'communication','bonus','Give someone your full attention for an hour','Be completely present','Spend an hour with someone with no phone and no distractions.','Undivided attention is the best gift you can give.','easy',24),
(@cat,'kindness','bonus','Make a call to say thank you','Phone someone to thank them','Call someone to thank them for something they did for you.','A call carries real feeling.','easy',24),
(@cat,'connection','bonus','Start a group chat for a shared interest','Bring friends together online','Start a group chat with friends around a shared interest.','A shared space makes keeping in touch effortless.','easy',24),
(@cat,'kindness','bonus','Offer a lift or practical help','Make someone elses day easier','Offer a lift or practical help to someone who could use it.','Practical help often means more than kind words.','easy',48),
(@cat,'community','bonus','Share a skill with someone','Teach someone something you know','Teach a friend or family member something you do well.','Sharing a skill makes both of you richer.','easy',48),
(@cat,'community','bonus','Invite someone new to join an activity','Include someone who might be left out','Invite someone new to join something you are already doing.','Being included is a powerful feeling.','easy',48),
(@cat,'connection','bonus','Take a walk with someone you rarely see','Catch up on the move','Take a walk with a friend or relative you do not see often.','Walking side by side makes talking easier.','easy',48),
(@cat,'communication','bonus','Write three qualities you value in a friend','Reflect on what makes friendship work','Write down three qualities you value in a friend, and whether you offer them too.','Knowing what you value helps you be a better friend.','easy',24),
(@cat,'community','bonus','Join a clean-up or community project','Help improve a shared space','Take part in a litter pick, garden project or other community effort.','Shared work builds shared pride.','medium',48),
(@cat,'kindness','bonus','Celebrate a friends achievement','Mark someone elses success','Congratulate a friend on something they achieved, with a message or small gesture.','Celebrating others strengthens friendships.','easy',24),
(@cat,'play','bonus','Spend an evening phone-free with others','Enjoy company without screens','Spend an evening with friends or family with all phones put away.','Screen-free time lets conversations go deeper.','medium',24);