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
*/

$settings = array(
  'dbHost' => 'localhost',
  'dbUser' => 'root',
  'dbPass' => '',
  'dbDatabase' => 'ost'
);


error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to localhost @ port 3306
$link = mysql_connect($settings['dbHost'], $settings['dbUser'], $settings['dbPass']); 
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

linebreak();
    
// Select database 
$db = mysql_select_db($settings['dbDatabase'], $link);

if (!$db) { 
    die('Could not open database: ' . mysql_error());
} 

/**
 * Wraps text with HTML tag STRONG.
 */
function strong($text) {
    return "<STRONG>$text</STRONG>\n";
}

/**
 * Ends text with HTML tag BR.
 * Default return just BR.
 */
function linebreak($text="\n") {
    echo nl2br( $text );
}

/**
 * Create a dynamic table with headers based on the column names
 * from a query. It automatically creates the table and the correct
 * number of columns.
 */
function createDynamicHTMLTable($table_name, $sql_query) {
    global $link;
    $counter=0;

    // execute SQL query and get result
    $sql_result = mysql_query($sql_query, $link); 
    if (($sql_result)||(mysql_errno == 0)) {        
        echo "<DIV>\n";
        linebreak( strong( sprintf("%s", $table_name) ) );
        echo "<TABLE borderColor=#000000 cellSpacing=0 cellPadding=6 border=1>\n";
        echo "<TBODY>\n";
        if (mysql_num_rows($sql_result)>0) 
        { 
            //loop thru the field names to print the correct headers 
            $i = 0; 
            echo "<TR vAlign=top bgColor=silver>\n";
            echo "<TH bgColor=silver>#</TH>\n";

            while ($i < mysql_num_fields($sql_result)) 
            { 
                echo "<TH>". mysql_field_name($sql_result, $i) . "</TH>\n"; 
                $i++; 
            } 
            echo "</TR>\n"; 


            //display The data 
            while ($rows = mysql_fetch_array($sql_result,MYSQL_ASSOC)) { 
                echo "<TR>\n"; 
                echo "<TD valign=top>". ++$counter ."</TD>";

                foreach ($rows as $data) 
                { 

                    $align = (is_numeric($data)) ? "center": "left";
                    echo "<TD valign=top align='$align'>". $data . "</TD>\n"; 
                } 
                echo "</TR>\n"; 
            } 
        } else { 
            echo "<TR>\n<TD colspan='" . ($i+1) . "'>No Results found!</TD></TR>\n"; 
        } 
        
        echo "</TBODY>\n</TABLE>\n";
        echo "</DIV>\n";
    } else { 
        echo nl2br( sprintf( "Error in running query: %s\n", mysql_error()) ); 
    }
    
    mysql_free_result($sql_result);
    
    linebreak();
}


