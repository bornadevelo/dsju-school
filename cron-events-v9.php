<?php
include_once("wp-load.php");

global $wpdb;

function eburza_html_special_chars($my_value) {
    $my_value = trim($my_value);
    $my_value = @preg_replace(
        array('/&/', '/</', '/>/', '/"/', "/'/"),
        array('&amp;', '&#60;', '&#62;', '&#34;', '&#39;'),
        $my_value
    );
    return $my_value;
}

function limit_text($text, $limit) {
    if (str_word_count($text, 0) > $limit) {
        $words = str_word_count($text, 2);
        $pos   = array_keys($words);
        $text  = substr($text, 0, $pos[$limit]) . '...';
    }
    return $text;
}

function eburza_make_seo($myValue) {
    $pattern     = array("&amp;","&#034;","&#039;","&quot;","&lt;","&gt;","\r","\n","\t","\"","<",">","(",")","'",".",",",";",":","!","#","$","&","%","=","*","?","/","\\"," ","__","__","--","-_","_-","--");
    $replacement = array("","","","","","","","","","","","","","","","_","","","","","","","","","","","_","/","","-","_","_","-","-","-","-");

    $myValue = mb_strtolower($myValue);
    $myValue = str_replace($pattern, $replacement, $myValue);

    $pattern     = array("Č","Ć","Š","Ž","Đ","č","ć","š","ž","đ");
    $replacement = array("c","c","s","z","d","c","c","s","z","d");
    $myValue = str_replace($pattern, $replacement, $myValue);

    return $myValue;
}

$json = file_get_contents('http://lmstest.dsju.hr/webservice/rest/server.php?wstoken=58c7a863ef504cf072a551d1ccb0910e&wsfunction=mod_timetable_get_all_relevant_timetables_data_expanded&moodlewsrestformat=json');
$json_data = json_decode($json, true);

foreach($json_data as $key => $value) {
    $timetableid        = $value['timetableid'];
    $current_date       = date("Y-m-d h:i:s");
    $course_name        = $value['course_name'];
    $course_name_url    = $value['course_name'] . '-' . rand(1, 15);
    $goals_and_purpose  = $value['goals_and_purpose'];
    $content            = $value['content'];
    $status             = $value['status'];
    $category_name      = $value['category_name'];
    $admin_full_name    = $value['admin_full_name'];
    $admin_email        = $value['admin_email'];
    $admin_phone        = $value['admin_phone'];
    $realisation_type   = $value['realisation_type'];
    $details_page_link  = $value['details_page_link'];
    $in_house           = $value['in_house'];
    $target_groups      = $value['target_groups'];
    $trainers           = $value['trainers'];
    $start_date         = $value['start_date'];
    $end_date           = $value['end_date'];
    $institutions       = $value['institutions'];
    $post_content = $content;

    $query = $wpdb->prepare("
        SELECT post_content, post_title
        FROM wp_posts
        WHERE post_content = %s
        AND post_title = %s,
        [
            $post_content,
            $course_name
        ]
    ");
    $postQuery = $wpdb->get_results($query);

    $event_write = $postQuery === null;

    if ($event_write === true) {
        $datetime1 = strtotime($start_date);
        $datetime2 = strtotime($end_date);

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

        $query = $wpdb->prepare("
            INSERT INTO wp_posts
            (
                post_author, 
                post_date, 
                post_date_gmt, 
                post_content, 
                post_title, 
                post_excerpt, 
                post_status, 
                ping_status, 
                post_name, 
                to_ping, 
                pinged, 
                post_modified, 
                post_modified_gmt, 
                post_content_filtered, 
                post_parent, 
                guid, 
                menu_order, 
                post_type
             )
            VALUES (
                %d,
                %s,
                %s,
                %s,
                %s,
                %s,
                %s,
                %s,
                %s,
                %s,
                %s,
                %s,
                %s,
                %d,
                %s,
                %d,
                %s,    
            )
            [
                2,
                $current_date,
                $current_date,
                $post_content,
                $course_name
                limit_text(strip_tags($goals_and_purpose), 30),
                'publish',
                'closed',
                eburza_make_seo($course_name_url),
                '',
                '',
                $_start_date,
                $_start_date,
                '',
                0,
                '',
                0,
                'tribe_events'
            ]
    ");

        $wpdb->show_errors();
    $postInsert = $wpdb->query($query);
    $wpdb->print_error();

    }
}

