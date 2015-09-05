# osTicket-extensions
Extensions and Scripts to complement self-hosted [OSTicket.](http://www.osticket.com) They include:

* Automatically schedule and create tickets.
* Process #hashtags in ticket notes and comments.
* Simple managerial ticket report

Tested against OSTicket 1.8

## Ticket Automation

In the "automation" directory are 2 php scripts for automating ticket creation.

Creating a ticket from the command line:

```
./createTicket.php subject [message]
```

Automatically scheduling ticket creation:
```
./createTicketPeriod.php <period>
```
This script is called from CRON to create tickets automatically on a schedule (eg daily, weekly, monthly, etc). Ticket subjects and messges are maintained in OSTicket's Knowledge-Base as an FAQ.

[See here for further information and setup tutorial.](http://smart-itc.com.au/osticket-automatic-scheduled-tickets/)

### Troubleshooting


**Unable to create ticket with subject: [_subject_]**

Ticket creation has failed. The likely cause is because the script could not reach your OSTicket API URL. Check the  **$settings array** key **apiURL**.

**Unable to create ticket with subject [_subject_]: Valid API key required**

The script found the OSTicket API URL but failed because the provided API Key was incorrect. Check the  **$settings array** key **apiKey**. Also check the API Keys page in OSTicket for the correct APIKey and IP Address.


## Simple HTML Reporting

A simple HTML managerial report to report on open and closed tickets. [Here is a screen shot](http://smart-itc.com.au/wp-content/uploads/2015/09/GITHubOSTicketReport.jpg) Yep, it's not pretty!


#### Background

These scripts were created to provide a client with some basic managerial reporting of tickets. There are 2 scripts:

**reportrt.php** *Internal* real-time reporting from the OSTicket database. Used to help manage and report tickets internally and prepair the managerial report. It reports on all tickets.

**report.php** *Managerial* report for last month. It contains less information than the internal report, plus tickets from the current month are excluded. So, to generate a managerial report for August, the report is generated in September.

The last comment/post in a ticket is what appears in the "Last Comment" column. The process adopted was for staff to review open tickets at the end of the month (or early in the next month), and post an internal 1 line progress comment for the report (a convention was adopted to suffix the comment with '(eom)' as seen in the the [screen shot](http://smart-itc.com.au/wp-content/uploads/2015/09/GITHubOSTicketReport.jpg)).

#### Setup

* The code is contained in the reports directory. This directory needs to be uploaded to your web server and made accessable via a URL.
* Edit **report.inc.php** to setup database connection details.
* Import the SQL in **viewsCreate.sql** into your OSTicket database. The PHP scripts use these views. Note that the SQL assumes an **ost_** prefix on tables. Edit the SQL to match your deployment.
* You can add your own SQL to the report.php, etc scripts to suite your needs.

#### Using
* Browse to a report. Eg http://ost.yourdomain.com/reports/report.php

## Ticket #hashtags

Process #hashtags in ticket notes/comments.

The script **hashtag/processTags.php** processes the last thread entry for open tickets and looks for hashtags, eg "#WOC" (WOC = Wait On Client).

Typically you would call this script from CRON:
```
* * * * * php /path-to-script/processTags.php > /dev/null
```

**processTags.php** then looks for an external PHP file to process the ticket. Eg, **tag-WOC.php**

In the hasthag folder there are some example processing scripts:

* **tag-WOC.php** update a custom OSTicket Field "TicketStatus" to read "Wait On Client".
* **tag-NORMAL.php** update a custom OSTicket Field "TicketStatus" to be null.
* **tag-STATUS.php** update a custom OSTicket Field "TicketStatus" to arbitary text.
* **tag-SUBJECT.php** update the ticket subject.

---
**Here is tag-STATUS.php in action**

**tag-STATUS.php** uses a custom ticket field called "Ticket Status", set up like this in osTicket:

![image](http://take.ms/H8ClQ)


1 User posts a note with #STATUS and text to a ticket.

![](http://take.ms/Db0R3)

2 After note added

![](http://take.ms/e9eSe)

3 Moments later (when processTags.php called by CRON)

![](http://take.ms/kQZTh)

4 Ticket List (*this is a customisation to show Ticket Status on the ticket list*)

![](http://take.ms/xApAv)

Note: **tag-SUBJECT.php** works similarly, excepts it updates the ticket subject field, which is a core osTicket field, not a custom field.

#### Setup

* Update the variable **$settings** in **processTags.php** with your database settings.
* **processTags.php** needs a custom view called "tickets_last_entry". The SQL to create this view is contained in the file **reports/viewsCreate.sql**
