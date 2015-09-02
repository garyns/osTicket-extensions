# osTicket-extensions
Extensions and Scripts to complement self-hosted [OSTicket.](http://www.osticket.com)

### Ticket Automation

In the "automation" directory are 2 php scripts for automating ticket creating.

Creating a ticket from the command line:

```
./createTicket.php subject [message]
```

Automatically scheduling ticket creation:
```
./createTicketPeriod.php <period>
```
This script is called from CRON to create tickets automatically on a schedule (eg daily, weekly, monthly, etc). Ticket subjects and messges are maintained in OSTicket's Knowledge-Base as an FAQ.

[See her for further information and setup tutorial.](http://smart-itc.com.au/osticket-automatic-scheduled-tickets/)

### Simple HTML Reporting

A simple HTML managerial report to report on open and closed tickets.

*Coming soon.*