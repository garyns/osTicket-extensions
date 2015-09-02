<?php
/*
The MIT License (MIT)

Copyright (c) 2015 Gary Smart www.smart-itc.com.au

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

Parts of this script have been inspired from Jeff Tanner, jeff_tanner@earthlink.net (https://sites.google.com/site/jeff00coder00seattle/home/coding/php-coding/mysql-php-dynamic-table-creation-from-query-results)


################################################################################

This report reports on the previous month and prior.
For instance, if ran on 1 September, it reports for August.

################################################################################
*/

  // Database settings are defined in report.inc.php
  include "report.inc.php";

  createDynamicHTMLTable("Ticket Report For", "SELECT DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MONTH), '%M %Y') AS Month");

  createDynamicHTMLTable("Tickets", "SELECT d as Month, opened as 'Opened', closed as 'Closed' FROM tickets_by_month WHERE d != DATE_FORMAT(NOW(),'%Y-%m')");

  createDynamicHTMLTable("Tickets Open EOM", "SELECT (sum(opened) - sum(closed)) AS 'Open' FROM tickets_by_month WHERE d < DATE_FORMAT(NOW(),'%Y-%m')");

  createDynamicHTMLTable("Tickets Opened During Month", " SELECT t.ticket_id as Ticket, status as Status,  td.subject as Subject FROM ost_ticket t, ost_ticket__cdata td WHERE t.ticket_id = td.ticket_id AND DATE_FORMAT(created,'%Y-%m') = DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MONTH), '%Y-%m');");

  createDynamicHTMLTable("Tickets Open", "SELECT ticket_id as 'Ticket', subject as 'Subject', body as 'Last Comment' FROM tickets_last_entry WHERE status='open' AND created < DATE_FORMAT(NOW(),'%Y-%m')");
