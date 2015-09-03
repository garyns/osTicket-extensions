<?php

  $sql = "UPDATE ost_ticket__cdata SET TicketStatus=NULL WHERE ticket_id=$ticketId;";
  $result = mysql_query($sql, $link);

  $newBody = str_ireplace("#$hashtag", "<strike>$hashtag</strike> TicketStatus Reset.", $body);
  update_thread($ticketId, $threadId, $newBody);

  echo $result ? "OK" : "ERROR: " . mysql_error($link);
