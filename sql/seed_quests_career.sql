SET @cat = (SELECT id FROM categories WHERE name = 'Career');

INSERT IGNORE INTO quest_templates
    (category_id, track, quest_type, title, summary, description, why, difficulty, time_limit_hours)
VALUES
-- Main quests
(@cat,'skills','main','Finish a course that helps your career','Complete every lesson of a course within 30 days','Choose a course that matches where you want your career to go, and complete every lesson, quiz and exercise within 30 days.','Finishing a course gives you a new skill and proof that you can follow through.','epic',720),
(@cat,'networking','main','Reach out to ten new professional contacts','Message ten people in your field in two weeks','Send personal, polite messages to ten people in your field over 14 days. Ask a question or offer something useful.','Most opportunities come from people, not job boards.','hard',336),
(@cat,'jobsearch','main','Update your CV and apply to five roles','Refresh your CV, then send five applications','Rewrite your CV, update your online profile, and apply to five roles that suit you within 14 days.','Applying regularly is the only way to find out what is possible.','hard',336),
(@cat,'skills','main','Build a portfolio piece','Create a project that shows your skills','Plan, build and finish one piece of work, such as a project, report or design, that you could show to an employer within 30 days.','A finished piece of work speaks louder than a list of skills.','epic',720),
(@cat,'productivity','main','Two weeks of daily priority planning','Choose your top three tasks every morning for 12 of 14 days','Each morning for two weeks, write your three most important tasks and finish them before lower-priority work. Do this on at least 12 of the 14 days.','Daily priorities stop busy work from crowding out important work.','medium',336),
(@cat,'networking','main','Three conversations with people in roles you admire','Talk to three people about their careers','Arrange and have three conversations with people in roles you admire, asking about their path and advice. Complete them within 21 days.','Listening to real careers is more useful than guessing.','hard',504),
(@cat,'growth','main','Set career goals and review them weekly','Write goals, then review them each week for a month','Write three career goals, and review your progress every week for four weeks, adjusting your plan each time.','Reviewing turns goals from wishes into plans.','hard',720),
(@cat,'skills','main','Learn a new tool used in your field','Learn enough of one tool to use it in real work','Choose a tool or programme used in your field, and learn it well enough to use it in a real task within 21 days.','Tool skills are one of the quickest ways to add value.','medium',504),

-- Side quests: CV and profile
(@cat,'cv','side','Update your CV','Add your latest work and skills','Update your CV with your most recent work, skills and achievements.','A current CV means you are ready when opportunity arrives.','medium',48),
(@cat,'cv','side','Write a short professional summary','Describe yourself in three sentences','Write a three-sentence summary of who you are professionally and what you want to do.','A clear summary helps everyone understand you quickly.','easy',24),
(@cat,'cv','side','Update your professional profile','Refresh your online profile','Update your photo, headline and summary on your professional profile.','Your profile is often seen before your CV.','easy',48),
(@cat,'cv','side','Ask someone for a recommendation','Request a reference or a written recommendation','Ask a manager, tutor or colleague to write a short recommendation for you.','Endorsements from others carry weight that self-praise cannot.','medium',48),
(@cat,'cv','side','List your achievements','Write down what you have achieved','Write a list of your achievements at work or study, with figures where possible.','Achievements are easy to forget and powerful in interviews.','easy',24),

-- Side quests: networking
(@cat,'networking','side','Message a former colleague','Say hello to someone from your past work','Send a friendly message to a former colleague or classmate.','Old contacts are often the easiest to reconnect with.','easy',24),
(@cat,'networking','side','Connect with a professional in your field','Send one thoughtful connection request','Send a connection request to someone in your field with a short, personal note.','A personal note makes a request much more likely to be accepted.','easy',24),
(@cat,'networking','side','Comment on a post by someone in your field','Add a useful comment to a professional post','Write a thoughtful comment on a post by someone in your field.','Visible engagement builds your name.','easy',24),
(@cat,'networking','side','Ask someone for a short advice chat','Request ten minutes of someones time','Ask someone you respect for ten minutes to talk about their work.','Most people are glad to share what they know.','medium',48),
(@cat,'networking','side','Attend an online talk or event','Join a webinar or event in your field','Attend an online talk, webinar or event related to your career.','Events are the easiest place to meet new people.','medium',48),
(@cat,'networking','side','Introduce two people who could help each other','Connect two people you know','Introduce two people in your network who could benefit from knowing each other.','Helping others is the best way to be remembered.','medium',48),
(@cat,'networking','side','Thank someone who helped your career','Say thanks to someone who gave you a chance','Send a thank-you message to someone who helped your career.','Gratitude strengthens the relationships that help you most.','easy',24),

-- Side quests: skills
(@cat,'skills','side','Practise a work skill for 30 minutes','Spend half an hour improving one skill','Choose a work-related skill and practise it deliberately for 30 minutes.','Regular short practice beats occasional long practice.','easy',24),
(@cat,'skills','side','Watch a tutorial on a tool in your field','Learn something new from a video','Watch a tutorial about a tool or technique used in your field.','Tutorials are the fastest way to learn a feature you do not know.','easy',24),
(@cat,'skills','side','Read an industry article','Keep up with your field','Read an article about trends or news in your field and note one idea.','Knowing your industry helps you make better decisions.','easy',24),
(@cat,'skills','side','Complete one lesson of a career course','Finish a single lesson','Complete one full lesson of a course related to your career.','One lesson at a time is how courses get finished.','easy',24),
(@cat,'skills','side','Do a small project to practise a skill','Apply a skill to a real task','Create something small that uses a skill you want to improve.','Making something is the real test of whether you learned it.','medium',48),
(@cat,'skills','side','Write three skills to learn this year','Plan your learning','Write down three skills you want to learn this year and why each matters.','A short list keeps your learning focused.','easy',24),
(@cat,'skills','side','Learn five shortcuts in a tool you use daily','Save time with keyboard shortcuts','Learn five keyboard shortcuts for a tool you use every day and use them for the day.','Small speed gains add up over thousands of uses.','easy',24),
(@cat,'skills','side','Practise a presentation out loud','Rehearse a short talk','Practise a short presentation aloud, ideally to someone or in front of a mirror.','Speaking aloud shows you what needs work before it counts.','medium',24),

-- Side quests: job search
(@cat,'jobsearch','side','Search for roles that interest you','Browse openings for 30 minutes','Spend 30 minutes searching for roles that interest you.','Looking shows you what is actually out there.','easy',24),
(@cat,'jobsearch','side','Save three roles and note what they need','Find common requirements','Save three roles and write down the skills each one asks for.','Patterns in job ads show you what to learn next.','easy',24),
(@cat,'jobsearch','side','Tailor your CV for one role','Adapt your CV to a specific job','Choose one role and adjust your CV to match what the employer is asking for.','A tailored CV is much more likely to get noticed.','medium',24),
(@cat,'jobsearch','side','Prepare answers to three interview questions','Practise common questions','Write answers to three common interview questions and practise saying them aloud.','Prepared answers calm nerves on the day.','medium',24),
(@cat,'jobsearch','side','Research a company you would like to work for','Learn about one employer','Spend time learning what a company does, its values and recent news.','Knowing a company makes your application stand out.','easy',48),

-- Side quests: productivity
(@cat,'productivity','side','Plan your day in the morning','Decide your tasks before starting','Spend five minutes each morning deciding what you will do and when.','A plan stops the day from running you.','easy',24),
(@cat,'productivity','side','Choose your three most important tasks','Prioritise before you begin','Write down the three most important tasks for today and do them first.','Doing important work first protects it from distractions.','easy',24),
(@cat,'productivity','side','Clear your inbox','Sort and reply to what is waiting','Go through your inbox, reply to what needs it, and file or delete the rest.','A clear inbox clears your mind too.','medium',24),
(@cat,'productivity','side','Do one task you have been avoiding','Face something you keep putting off','Choose a task you keep postponing and spend 30 minutes on it.','Avoided tasks grow heavier the longer you wait.','medium',24),
(@cat,'productivity','side','Work in two focused blocks','Do two 45-minute blocks of focused work','Work in two 45-minute blocks with a short break between and no distractions.','Focused blocks get more done than scattered hours.','medium',24),
(@cat,'productivity','side','Organise your files and folders','Put your work files in order','Tidy and name your files and folders so you can find things quickly.','Time lost searching for files adds up quickly.','easy',48),

-- Side quests: reflection
(@cat,'reflection','side','Write what went well this week','Note your wins','Write down three things that went well in your work or study this week.','Recording wins helps in reviews and interviews.','easy',24),
(@cat,'reflection','side','Ask for feedback on something you did','Request honest comments','Ask someone you trust for honest feedback on a piece of work.','Feedback shows you what you cannot see yourself.','medium',48),
(@cat,'reflection','side','Write a career goal for next year','Describe where you want to be','Write down one clear goal for your career over the next year.','A written goal is much more likely to be reached.','easy',24),
(@cat,'reflection','side','Note one lesson from a mistake','Turn a mistake into a lesson','Write down a recent mistake and the lesson you will take from it.','Mistakes teach most when you stop and look at them.','easy',24),
(@cat,'reflection','side','List what energises and drains you at work','Spot patterns in your work','Make two lists of the work tasks that give you energy and those that drain it.','These lists point you toward the work you will enjoy most.','easy',24),

-- Bonus quests
(@cat,'networking','bonus','Have a coffee with someone in your field','Meet someone for a relaxed chat','Meet someone in your field for a coffee or call and ask about their work.','Casual chats often lead to the best advice.','medium',48),
(@cat,'skills','bonus','Listen to a podcast about your industry','Learn something new on the move','Listen to a full episode of a podcast related to your field.','Passive time can become learning time.','easy',24),
(@cat,'networking','bonus','Offer help to a colleague','Give someone a hand with a task','Offer to help a colleague or classmate with something they are working on.','Helping others builds trust and reputation.','easy',24),
(@cat,'growth','bonus','Find a mentor to ask a question','Ask an experienced person for advice','Choose someone with more experience and ask them one thoughtful question.','A good mentor can save you years of trial and error.','medium',48),
(@cat,'skills','bonus','Read a book chapter on your field','Read one chapter from a professional book','Read a chapter from a book about your field.','Books go deeper than articles.','easy',24),
(@cat,'growth','bonus','Visit a workplace or open day','See a working environment first hand','Visit a workplace, university department or open day that interests you.','Seeing a real place makes a career feel concrete.','medium',48),
(@cat,'networking','bonus','Share a useful article','Pass on something helpful','Share a useful article with a friend or colleague who would benefit.','Sharing good information makes you a valuable contact.','easy',24),
(@cat,'skills','bonus','Teach a colleague something','Share a skill you have','Teach a colleague or classmate something you know well.','Teaching deepens your own understanding.','easy',48),
(@cat,'productivity','bonus','Declutter your workspace','Clear and clean your desk','Clear your desk of everything you do not need and wipe it down.','A clean space makes it easier to start.','easy',24),
(@cat,'reflection','bonus','Write a note of appreciation to a manager or tutor','Say thank you for guidance','Write a short, honest note of appreciation to a manager or tutor.','Appreciation strengthens the relationships that support you.','easy',24),
(@cat,'skills','bonus','Watch a talk from an expert','Learn from someone with experience','Watch a talk or lecture by an expert in your field.','Hearing experts is a quick way to raise your sights.','easy',24),
(@cat,'growth','bonus','Set up a free profile on a learning platform','Prepare for future learning','Create a free account on a learning platform and bookmark two courses.','Having options ready keeps your momentum going.','easy',24),
(@cat,'networking','bonus','Join an online community in your field','Find people who share your interests','Join an online community or forum in your field and read it for ten minutes.','Communities are great places to learn and ask questions.','easy',48),
(@cat,'reflection','bonus','Write your ideal workday','Imagine what you want your days to look like','Write a description of your ideal working day, in as much detail as you can.','Knowing what you want helps you choose your next step.','easy',24),
(@cat,'jobsearch','bonus','Browse roles one level above yours','See what the next step looks like','Browse job listings one level above your current one and note the requirements.','Seeing the next step shows what to work on.','easy',24),
(@cat,'productivity','bonus','Try a new productivity method','Test a technique you have not used','Try a technique such as time blocking or the Pomodoro method for one day.','Finding a method that suits you can transform your output.','medium',48);