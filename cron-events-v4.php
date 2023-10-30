<?php

# Configuration file
include_once("wp-config.php");

# Test baza
$MySQL = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME); 

# Live baza
#$MySQL = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME); 

// Check connection
if (mysqli_connect_errno()) { 
	printf("Connect failed: %s\n", mysqli_connect_error());
	exit();
}

// Change character set to utf8 
if (!mysqli_set_charset($MySQL, "utf8")) {
    printf("Error loading character set utf8: %s\n", mysqli_error($MySQL));
    exit();
} else {
    mysqli_character_set_name($MySQL);
    mysqli_set_charset($MySQL, 'utf8');
}


# Functions
function eburza_html_special_chars($my_value) {
    $my_value = trim($my_value);
    $my_value = @preg_replace(
        array('/&/', '/</', '/>/', '/"/', "/'/"),
        array('&amp;', '&#60;', '&#62;', '&#34;', '&#39;'),
        $my_value
    );
    return $my_value;
} # end of the 'eburza_html_special_chars()' function

function limit_text($text, $limit) {
    if (str_word_count($text, 0) > $limit) {
        $words = str_word_count($text, 2);
        $pos   = array_keys($words);
        $text  = substr($text, 0, $pos[$limit]) . '...';
    }
    return $text;
}

# 'eburza_make_seo()' function
function eburza_make_seo($myValue) {
    $pattern     = array("&amp;","&#034;","&#039;","&quot;","&lt;","&gt;","\r","\n","\t","\"","<",">","(",")","'",".",",",";",":","!","#","$","&","%","=","*","?","/","\\"," ","__","__","--","-_","_-","--");
    $replacement = array("","","","","","","","","","","","","","","","_","","","","","","","","","","","_","/","","-","_","_","-","-","-","-");

    $myValue = mb_strtolower($myValue);
    $myValue = str_replace($pattern, $replacement, $myValue);

    $pattern     = array("Č","Ć","Š","Ž","Đ","č","ć","š","ž","đ");
    $replacement = array("c","c","s","z","d","c","c","s","z","d");
    $myValue = str_replace($pattern, $replacement, $myValue);

    return $myValue;
} # end of the 'MakeSEO()' function
 


$json = file_get_contents('https://demo-dsju.ebitsdev.com/webservice/rest/server.php?wstoken=046a91a0eb7e0e3df4a0a4427fffb5b5&wsfunction=mod_timetable_get_all_relevant_timetables&moodlewsrestformat=json&');
$json_data = json_decode($json,true);


foreach($json_data as $key => $value) {
    $timetableid   = $value['timetableid'];

    $current_date = date("Y-m-d h:i:s");

    $start_date    = $value['start_date']; $datetime1 = strtotime($start_date);
    $end_date      = $value['end_date'];   $datetime2 = strtotime($end_date);
    
    $duration      = $datetime2 - $datetime1;

    $start_hour  = substr($start_date, 11, 2);
	$start_min   = substr($start_date, 14, 2);
	$start_sec   = ':00';
	$start_year  = substr($start_date, 6, 4);
	$start_month = substr($start_date, 3, 2);
	$start_day   = substr($start_date, 0, 2);

    $_start_date = $start_year .'-' . $start_month . '-' . $start_day . ' ' . $start_hour . ':' . $start_min . $start_sec;

    $end_hour  = substr($end_date, 11, 2);
	$end_min   = substr($end_date, 14, 2);
    $end_sec   = ':00';
	$end_year  = substr($end_date, 6, 4);
	$end_month = substr($end_date, 3, 2);
	$end_day   = substr($end_date, 0, 2);

    $_end_date = $end_year .'-' . $end_month . '-' . $end_day . ' ' . $end_hour . ':' . $end_min . $end_sec;
    
    $course_name        = $value['course_fullname'];
    $course_name_url    = $value['course_fullname'] . '-' . rand(1, 15);
    $goals_and_purpose  = $value['goals_and_purpose'];
    $content            = $value['content'];
    

    $event_write = false;

    $post_content  = '<!-- wp:tribe/event-datetime-->';
    $post_content .= '<!-- /wp:tribe/event-datetime -->';
    
    $post_content .= '<!-- wp:paragraph -->';
    $post_content .= '<h2 style="font-size:16px;">' . $course_name . '</h2>';
    #$post_content .= '<h3 style="font-size:14px;">' . $category_name . '</h3>';
    $post_content .=  $content . '<!-- /wp:paragraph -->';
    # administrator ime
    #$post_content .= '<p><b>Kontakt:</b> ' . $admin_full_name . ', ' . $admin_email . '</p>';
    # vrijeme održavanja
    $post_content .= '<!-- wp:paragraph -->
    <p><b>Početak:</b> ' . $start_date . '</p>
    <!-- /wp:paragraph -->';
    $post_content .= '<!-- wp:paragraph --><p><b>Kraj:</b> ' . $end_date . '</p><!-- /wp:paragraph -->';
    if (isset($full_name)) {
        # Trener
        $post_content .= '<!-- wp:paragraph --><p><b>Trener:</b> ' . $full_name . '</p><!-- /wp:paragraph -->';
    }
    
    $post_content .= '<!-- /wp:paragraph -->';

    $post_content .= '<!-- wp:tribe/event-price -->';
    $post_content .= '<!-- /wp:tribe/event-price -->';

    $post_content .= '<!-- wp:tribe/event-organizer -->';
    $post_content .= '<!-- /wp:tribe/event-organizer -->';

    $post_content .= '<!-- wp:tribe/event-organizer -->';
    $post_content .= '<!-- /wp:tribe/event-organizer -->';

    $post_content .= '<!-- wp:tribe/event-venue -->';
    $post_content .= '<!-- /wp:tribe/event-venue -->';

    $post_content .= '<!-- wp:tribe/event-website {"urlLabel":"Prijavi se"} /-->';

    $post_content .= '<!-- wp:tribe/event-links -->';
    $post_content .= '<!-- /wp:tribe/event-links -->';

    $query  = "SELECT ID, post_date, post_title FROM wp_posts";
    $query .= " WHERE post_content = '" . $post_content . "'";
    $query .= " AND post_title = '" . $course_name . "'";
    $result = @mysqli_query($MySQL, $query);
    $rowcount = mysqli_num_rows($result);
                
        if ($rowcount == 0) {
            $event_write = true;
           
        } 

            
    if ($event_write == true) {
       

        $_query  = "INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, ping_status, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type)";
        $_query .= " VALUES (2, '" . $current_date . "', '" . $current_date . "', '" . $post_content . "', '" . $course_name . "', '" . limit_text(strip_tags($goals_and_purpose), 30) . "', 'publish', 'closed', '" . eburza_make_seo($course_name_url) . "', '', '', '" . $_start_date . "', '" . $_start_date . "', '', '0', '', '0', 'tribe_events')";
        $_result = @mysqli_query($MySQL, $_query); 
        $ID = mysqli_insert_id($MySQL);

        

        $guid = 'https://dsju.hr/?post_type=tribe_events&#038;p=' . $ID;
        $hash = bin2hex(random_bytes(8));

        $s_query  = "UPDATE wp_posts SET guid='" . $guid . "'";
        $s_query .= " WHERE ID = $ID";
        $s_result = @mysqli_query($MySQL, $s_query); 

        $__query  = "INSERT INTO  wp_tec_events (post_id, start_date, end_date, timezone, start_date_utc, end_date_utc, duration, hash, rset)";
        $__query .= " VALUES ('" . $ID . "', '" . $_start_date . "', '" . $_end_date . "', 'Europe/Zagreb', '" . $_start_date . "', '" . $_end_date . "', '" . $duration . "', '" . $hash . "', '')";
        $__result = @mysqli_query($MySQL, $__query); 
        $_ID = mysqli_insert_id($MySQL);

        $___query  = "INSERT INTO  wp_tec_occurrences (event_id, post_id, start_date, start_date_utc, end_date, end_date_utc, duration, hash)";
        $___query .= " VALUES ('" . $_ID . "', '" . $ID . "', '" . $_start_date . "', '" . $_start_date . "', '" . $_end_date . "', '" . $_end_date . "', '" . $duration . "', '" . $hash . "')";
        $___result = @mysqli_query($MySQL, $___query); 

        $____query  = "INSERT INTO  wp_yoast_indexable (permalink, permalink_hash, object_id, object_type, object_sub_type, author_id, post_parent, title, description, breadcrumb_title, post_status, is_public, is_protected, has_public_posts, number_of_pages, canonical, primary_focus_keyword, primary_focus_keyword_score, readability_score, object_last_modified, object_published_at)";
        $____query .= " VALUES ('" . $guid . "', '" . $hash . "', '" . $ID . "', 'post', 'tribe_venue', '2', '0', '" . $course_name . "', '" . $course_name . "', '" . $course_name . "', 'publish', '0', '0', '0', '', '', '', '', '', '" . $current_date . "', '" . $current_date . "')";
        $____result = @mysqli_query($MySQL, $____query); 

   }

}


mysqli_close($MySQL);




?>