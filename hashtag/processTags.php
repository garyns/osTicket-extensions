#!/usr/bin/php -q
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

##
## IMPORTANT - This script relies on a custom view "tickets_last_entry". See reports/viewsCreate.sql for SQL to create this view.
##
*/

$settings = array(
  'dbHost' => 'localhost',
  'dbUser' => 'root',
  'dbPass' => '',
  'dbDatabase' => 'ost',
  'dbTablePrefix' => 'ost_'
);

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to localhost @ port 3306
$link = mysql_connect($settings['dbHost'], $settings['dbUser'], $settings['dbPass']); 
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

// Select database 
$db = mysql_select_db($settings['dbDatabase'], $link);

if (!$db) { 
    die('Could not open database: ' . mysql_error());
}

$sql_query = "SELECT ticket_id, thread_id, body FROM tickets_last_entry WHERE status='open' AND UPPER(body) LIKE UPPER('%#%');";

$sql_result = mysql_query($sql_query, $link); 

if (($sql_result)||(mysql_errno == 0)) {        

   while ($row = mysql_fetch_array($sql_result,MYSQL_ASSOC)) { 

     $ticketId = $row['ticket_id'];
     $threadId = $row['thread_id'];
     $body = $row['body'];
     $hashtags = get_hash_tags($row['body']);

     if (count($hashtags) > 0) {

       echo "Ticket $ticketId: #" . implode(",#", $hashtags) . "\n";

       foreach($hashtags as $hashtag) {
         process_hash_tag($ticketId, $threadId, $body, $hashtag);
       }
     }
     
   } // white
    
    mysql_free_result($sql_result);
} // if result

function get_hash_tags($text) {
  $tags = array();

  preg_match_all('/(^|[^a-z0-9_])#([a-z0-9_]+)/i', $text, $matches); 
  $hashtag = '';

  if (!empty($matches[0])) {
     foreach($matches[0] as $match) {
        
          $hashtag = trim(preg_replace("/[^a-z0-9]+/i", "", $match));

          $isColor = preg_match('/#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?\b/', $match);

          if ($isColor || is_numeric($hashtag) || empty($hashtag)) {
            // Skip certain hashtags what we find in HTML tickets.
            continue;
          }

          $tags[] = $hashtag;
     }
  }

  return $tags;
}


/**
 * Find hashtags in $body.
 * @return Array of tags found (without # prefixed);
 */
function process_hash_tag($ticketId, $threadId, $body, $hashtag) {
  global $link;

  echo "Ticket $ticketId #$hashtag: ";

  $dir = dirname(__FILE__);
  $file = "$dir/tag-" . strToUpper($hashtag) . ".php";

  // body2 is body with the hasttag removed and all html tags removed.
  $body2 = trim(strip_tags(str_ireplace("#$hashtag", "", $body)));

  if (file_exists("$file")) {
    ob_start();
    include "$file";
    $t = ob_get_clean();
    echo $t;
  }  else {
    echo "No processing file $file found.";
  }

  echo "\n";
}

/**
 * Add a new internal note to a ticket.
 */
function post_ticket_note($ticketId, $note) {
  global $link, $settings;

  $sql = "INSERT INTO " . $settings['dbTablePrefix'] . "ticket_thread (ticket_id, pid, staff_id, user_id, thread_type, source, title, body, format, ip_address, created, updated) VALUES ($ticketId, 0, 0, 0, 'N', 'TagProcessor', NULL, '$note', 'html', '', NOW(), NOW());";

  $result = mysql_query($sql, $link);

  if ($result === FALSE) {
    echo  "Ticket $ticketId ERROR posting note '$note': " . mysql_error($link) . "\n";
    return false;
  }

  return true;
}

/**
 * Replace the body text on an existing ticket thread.
 */
function update_thread($ticketId, $threadId, $note) {
  global $link, $settings;

  $sql = "UPDATE " . $settings['dbTablePrefix'] . "ticket_thread SET body='" . nl2br($note) . "', updated=NOW() WHERE id=$threadId;";

  $result = mysql_query($sql, $link);

  if ($result === FALSE) {
    echo  "Ticket $ticketId ERROR Updating Thread $threadId '$note': " . mysql_error($link) . "\n";
    return false;
  }

 return true;

}
