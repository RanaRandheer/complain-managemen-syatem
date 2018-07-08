# Email Parser
Introduction:

Basic of an email parser is to automate data extraction from incoming email.  This software extracts data from a mail, checks if it is a new issue or is related to already registered issue.  If issue is new one data is stored in database and an acknowledgement is sent to user. Else add new data to respective cell in database. To make system modular code is divided into function.

Features:

•	Extracting Data: system automats data extraction from massages and store data in database.<br>•	Reply Massage: an auto generated email is sent to user with allotted issue id.<br>•	Error Massage: in case something goes wrong an error massage is sent to user.<br>•	Categorising Massages: system differentiate between massages related to registered issue and massage raising new issue.<br>•	Identifying category of issue: system automatically assign category to fresh issue.

Basic steps:
1.	Get email
2.	Extract data
3.	Push data into database
4.	Reply

Software required:

Apache<br>
MySQL<br>
PHPMailer<br>

Language and Libraries:

Imap <br>
PHP 5<br>
mySQLi<br>

Database:
1.	Parsingtable{issue_id, user_id, category_id, description, status}
2.	member {id, name, phone_no, email, department}

Basic functions:

reply(): send reply massage to user with allotted issue id.<br>
errormsg(): send error massage to user.<br><br>
Update(): appends description cell of a particular row with given information<br>
Verify_issue_id(): if provided issue id is valid returns true else returns false <br>
Pushdata(): append database with new row with provided information and return allotted issue id<br>
