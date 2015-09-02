# osTicket-extensions
Extensions and Scripts to complement self-hosted [OSTicket.](http://www.osticket.com)

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

## Troubleshooting


**Unable to create ticket with subject: [_subject_]**

Ticket creation has failed. The likely cause is because the script could not reach your OSTicket API URL. Check the  **$settings array** key **apiURL**.

**Unable to create ticket with subject [_subject_]: Valid API key required**

The script found the OSTicket API URL but failed because the provided API Key was incorrect. Check the  **$settings array** key **apiKey**. Also check the API Keys page in OSTicket for the correct APIKey and IP Address.


## Simple HTML Reporting

A simple HTML managerial report to report on open and closed tickets.

*Coming soon.*