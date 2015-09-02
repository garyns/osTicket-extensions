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

This report reports on tickets real time.
For instance, if ran on 1 September, it reports for August.

################################################################################
*/

  // Database settings are defined in report.inc.php
  include "report.inc.php";
 
  createDynamicHTMLTable("Ticket Report At", "SELECT NOW()");
  
  createDynamicHTMLTable("Tickets", "SELECT d as Month, opened as 'Opened', closed as 'Closed', (opened - closed) as Diff  FROM tickets_by_month");
  
  createDynamicHTMLTable("Tickets Open", "SELECT (sum(opened) - sum(closed)) AS 'Open' FROM tickets_by_month");
  
  createDynamicHTMLTable("Tickets Open", "SELECT ticket_id as 'Ticket', subject as 'Subject', DATEDIFF(now(), created) as Age, DATEDIFF(now(), threadUpdated) 'Last Update', body as 'Last Comment' FROM tickets_last_entry WHERE status='open';");

