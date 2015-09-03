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

Parts of this script were inspired from jared@osTicket.com / ntozier@osTicket / tmib.net (http://tmib.net/using-osticket-1812-api)
*/

$settings = array(
  'dbHost' => 'localhost',
  'dbTable' => 'ost.ost_faq', // Database.Table where FAQs are stored.
  'dbUser' => 'root',
  'dbPass' => '',
  'categoryId' => 16, // The Category ID where Automator tickete FAQs are kept
  'topicId' => 8, // Created tickets are assigned to this topic.
  'subjectPrefix' => '[',
  'subjectSuffix' => ']',
  'reporterEmail' => 'automator@domain.com',
  'reporterName' => 'Automator',
  'apiURL' => 'http://ost.domain.com/api/http.php/tickets.json',
  'apiKey' => 'your-api-key'
);

if (!isset($settings)) {
  die ('$settings is not set. Aborting.');
}

$period = isset($argv[1]) ? $argv[1] : "daily";

$tks = findTicketsToCreate($period);

foreach($tks as $t) {
  createTicket($t->subject, $t->message);
}



// $period to match FAQ Question field.
function findTicketsToCreate($period = "daily") {  
    global $settings;
    
    $link = mysql_connect($settings['dbHost'], $settings['dbUser'], $settings['dbPass']);

    if (!$link) {
        die('Could not connect: ' . mysql_error());
    }
    
    $sql_query = "select faq_id, answer from " . $settings['dbTable'] . " WHERE category_id=" . $settings['categoryId'] . " AND UPPER(question) LIKE UPPER('%$period%')";
    
    $sql_result = mysql_query($sql_query, $link);
    
    $rowsFound = false;
    $results = array();
    
    while ($row = mysql_fetch_array($sql_result,MYSQL_ASSOC)) {
      $rowsFound = true;
    
      $lines = explode("\n", trim(br2nl($row['answer']))) ;
    
      foreach($lines as $line) {

        $line = trim($line);
   
        // Skip empty lines or lines starting with a comment (-- or #) 
        if (empty($line) || (strpos($line, "--") === 0) || (strpos($line, "#") === 0)) {
          continue;
        }

        $subject = $line;
        $message = null;

        if (strpos($line, "|") !== FALSE) {
          list($subject, $message) = explode("|", $line);
        }

        $subject = decode($subject);
        $message = decode($message);

        if (empty($message)) {
          $message = $subject;
        }

        $tmp = new stdClass();
        $tmp->faqId = $row['faq_id'];
        $tmp->subject = $subject;
        $tmp->message = $message . "\n\nPeriod: $period";
        $results[] = $tmp;
      }
    }
    
    mysql_free_result($sql_result);
    mysql_close($link);

    if (!$rowsFound) {
      $msg = "Automator called for Period '$period' but found no tickets to create";
      echo $msg . "\n";
      createTicket($msg);
    }

    return $results;
} // findTicketsToCreate()


function createTicket($subject, $message = null) {
  global $settings;

  $topicId = $settings['topicId']; 
  $reporterEmail = $settings['reporterEmail'];
  $reporterName = $settings['reporterName'];
  $reporterIP = gethostbyname(gethostname());

  if (empty($subject)) {
    echo ("No Subject provided. Not creating ticket.\n");
    return false;
  };

  if (empty($message)) {
    $message = $subject;
  }

  $subject = $settings['subjectPrefix'] . $subject . $settings['subjectSuffix'];

  $data = array(
    'name'      =>      $reporterName, 
    'email'     =>      $reporterEmail, 
    'phone' 	=>      '',  
    'subject'   =>      $subject,  
    'message'   =>      $message,  
    'ip'        =>      $reporterIP,
    'topicId'   =>      $topicId
  );

  function_exists('curl_version') or die('CURL support required');
  function_exists('json_encode') or die('JSON support required');

  set_time_limit(30);
  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $settings['apiURL']);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
  curl_setopt($ch, CURLOPT_USERAGENT, 'osTicket API Client v1.8');
  curl_setopt($ch, CURLOPT_HEADER, FALSE);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Expect:', 'X-API-Key: '.$settings['apiKey']));
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  $result=curl_exec($ch);
  $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  if ($code != 201) {
    echo "Unable to create ticket with subject $subject: " .$result . "\n";
    return false;
  }

  $ticketId = (int)$result;

  echo "Ticket '$subject' created with id $ticketId\n";

  return $ticketId;

} // createTicket()

function br2nl($string) {
    return preg_replace('/\<br(\s*)?\/?\>/i', PHP_EOL, $string);
}

function decode($s) {
  if (empty($s)) {
    return $s;
  }

  $s = str_ireplace("&nbsp;", " ", $s);
  return trim($s);
}
