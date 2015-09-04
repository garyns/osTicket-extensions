<?php
  // These variables are available: $link (mysql link), $ticketId, $threadId, $hashtag, $body, $body2 (body with hashtag removed and html stripped);
  // These functions are available: update_thread($ticketId, $threadId, 'text')  post_ticket_note($ticketId, 'text');

  // TicketStatus is a custom column added in OSTicket.

  $sql = "UPDATE ost_ticket__cdata SET TicketStatus='Wait On Client' WHERE ticket_id=$ticketId;";
  $result = mysql_query($sql, $link);

  $newBody = str_ireplace("#$hashtag", "<strike>$hashtag</strike> TicketStatus Now 'Wait On Client'", $body);
  echo $result ? "OK" : "ERROR: " . mysql_error($link);
  
  update_thread($ticketId, $threadId, $newBody);
