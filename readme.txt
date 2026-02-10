Info
System : Generate Schedule System

Stack: PHP + MySQL (XAMPP), OpenAI API (gpt-4.1-mini), lightweight frontend and CSS.

University scheduling system that generates conflict-free timetables for sections using course-year curricula, faculty availability, and room constraints. 
It balances assignments across faculty and rooms, groups multiple subjects per section per day, and enforces hard rules for reliability.
This note will be guide for the troubleshooting and details of each files and what it does.

You need to install composer first

Manual Configuration:
- in php.ini: (ctrl + F)
     max_execution_time = 300 // must be 300
