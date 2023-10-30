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
 
$json = file_get_contents('http://lmstest.dsju.hr/webservice/rest/server.php?wstoken=58c7a863ef504cf072a551d1ccb0910e&wsfunction=mod_timetable_get_all_relevant_timetables_data_expanded&moodlewsrestformat=json');

$json_data = json_decode($json,true);

foreach($json_data as $key => $value) {
    $timetableid        = $value['id'];

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
    $dates              = $value['dates'];
    $institutions       = $value['institutions'];
    $learning_outcomes  = $value['learning_outcomes'];

    $post_content = $content;

    $query  = "SELECT post_content, post_title FROM wp_posts";
    $query .= " WHERE post_content = '" . $post_content . "'";
    $query .= " AND post_title = '" . $course_name . "'";
    $result = @mysqli_query($MySQL, $query);
    $rowcount = mysqli_num_rows($result);

    $event_write = false;

    if ($rowcount == 0) {
        $event_write = true;
    }

    if ($event_write == true) {

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

        $_start_end_date = $start_year .'-' . $start_month . '-' . $start_day . ' ' . $end_hour . ':' . $end_min . $end_sec;

        $_query  = "INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, ping_status, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type)";
        $_query .= " VALUES (2, '" . $current_date . "', '" . $current_date . "', '" . $post_content . "', '" . $course_name . "', '" . limit_text(strip_tags($goals_and_purpose), 30) . "', 'publish', 'closed', '" . eburza_make_seo($course_name_url) . "', '', '', '" . $_start_date . "', '" . $_start_date . "', '', '0', '', '0', 'tribe_events')";
        $_result = @mysqli_query($MySQL, $_query);
        $ID = mysqli_insert_id($MySQL);

        $guid = 'https://dsju.hr/?post_type=tribe_events&#038;p=' . $ID;
        $hash = bin2hex(random_bytes(8));

        $s_query  = "UPDATE wp_posts SET guid='" . $guid . "'";
        $s_query .= " WHERE ID = $ID";
        $s_result = @mysqli_query($MySQL, $s_query);

        $_query2  = "INSERT INTO post_event (post_id, api_post)";
        $_query2 .= " VALUES (" . $ID . ", " . $timetableid . ")";
        $_result2 = @mysqli_query($MySQL, $_query2);

        $__query  = "INSERT INTO  wp_tec_events (post_id, start_date, end_date, timezone, start_date_utc, end_date_utc, duration, hash, rset)";
        $__query .= " VALUES ('" . $ID . "', '" . $_start_date . "', '" . $_end_date . "', 'Europe/Zagreb', '" . $_start_date . "', '" . $_end_date . "', '" . $duration . "', '" . $hash . "', '')";
        $__result = @mysqli_query($MySQL, $__query);
        $_ID = mysqli_insert_id($MySQL);

        /*$___query  = "INSERT INTO  wp_tec_occurrences (event_id, post_id, start_date, start_date_utc, end_date, end_date_utc, duration, hash)";
        $___query .= " VALUES ('" . $_ID . "', '" . $ID . "', '" . $_start_date . "', '" . $_start_date . "', '" . $_start_end_date . "', '" . $_start_end_date . "', '" . $duration . "', '" . $hash . "')";
        $___result = @mysqli_query($MySQL, $___query); */

        $____query  = "INSERT INTO  wp_yoast_indexable (permalink, permalink_hash, object_id, object_type, object_sub_type, author_id, post_parent, title, description, breadcrumb_title, post_status, is_public, is_protected, has_public_posts, number_of_pages, canonical, primary_focus_keyword, primary_focus_keyword_score, readability_score, object_last_modified, object_published_at)";
        $____query .= " VALUES ('" . $guid . "', '" . $hash . "', '" . $ID . "', 'post', 'tribe_venue', '2', '0', '" . $course_name . "', '" . $course_name . "', '" . $course_name . "', 'publish', '0', '0', '0', '', '', '', '', '', '" . $current_date . "', '" . $current_date . "')";
        $____result = @mysqli_query($MySQL, $____query);

        foreach($dates as $date_key => $date_value) {

            $start_date    = $date_value['start_date']; $datetime1 = strtotime($start_date);
            $end_date      = $date_value['end_date'];   $datetime2 = strtotime($end_date);

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

            $_start_end_date = $start_year .'-' . $start_month . '-' . $start_day . ' ' . $end_hour . ':' . $end_min . $end_sec;

            $___query  = "INSERT INTO  wp_tec_occurrences (event_id, post_id, start_date, start_date_utc, end_date, end_date_utc, duration, hash)";
            $___query .= " VALUES ('" . $_ID . "', '" . $ID . "', '" . $_start_date . "', '" . $_start_date . "', '" . $_start_end_date . "', '" . $_start_end_date . "', '" . $duration . "', '" . $hash . "')";
            $___result = @mysqli_query($MySQL, $___query);

            $_EventStartDate = '_EventStartDate';
            $EventStartDate = $_start_date;

            $query1  = "INSERT INTO  wp_postmeta (post_id, meta_key, meta_value)";
            $query1 .= " VALUES ('" . $ID . "', '" . $_EventStartDate . "', '" . $EventStartDate . "')";
            $result1 = @mysqli_query($MySQL, $query1);

            $_EventEndDate  = '_EventEndDate';
            $EventEndDate   = $_end_date;

            $query1  = "INSERT INTO  wp_postmeta (post_id, meta_key, meta_value)";
            $query1 .= " VALUES ('" . $ID . "', '" . $_EventEndDate . "', '" . $EventEndDate . "')";
            $result1 = @mysqli_query($MySQL, $query1);

            $_EventStartDateUTC = '_EventStartDateUTC';
            $EventStartDateUTC = $start_date;

            $query1  = "INSERT INTO  wp_postmeta (post_id, meta_key, meta_value)";
            $query1 .= " VALUES ('" . $ID . "', '" . $_EventStartDateUTC . "', '" . $EventStartDateUTC . "')";
            $result1 = @mysqli_query($MySQL, $query1);


            $_EventEndDateUTC   = '_EventEndDateUTC';
            $EventEndDateUTC   = $end_date;

            $query1  = "INSERT INTO  wp_postmeta (post_id, meta_key, meta_value)";
            $query1 .= " VALUES ('" . $ID . "', '" . $_EventEndDateUTC . "', '" . $EventEndDateUTC . "')";
            $result1 = @mysqli_query($MySQL, $query1);


            $has_hall = $date_value['has_hall'];

            if ($has_hall === true) {
                $hall_institution = $date_value['hall_institution'];
                $hall_name        = $date_value['hall_name'];
                $hall_address     = $date_value['hall_address'];
                $hall_city        = $date_value['hall_city'];
                $hall_county      = $date_value['hall_county'];

                $_VenueName   = '_EventVenueName';
                $VenueName   = $hall_name;

                $query1  = "INSERT INTO  wp_postmeta (post_id, meta_key, meta_value)";
                $query1 .= " VALUES ('" . $ID . "', '" . $_VenueName . "', '" . $VenueName . "')";
                $result1 = @mysqli_query($MySQL, $query1);

                $_VenueAddress   = '_EventVenueAddress';
                $VenueAddress   = $hall_address;

                $query1  = "INSERT INTO  wp_postmeta (post_id, meta_key, meta_value)";
                $query1 .= " VALUES ('" . $ID . "', '" . $_VenueAddress . "', '" . $VenueAddress . "')";
                $result1 = @mysqli_query($MySQL, $query1);

                $_VenueCity   = '_EventVenueCity';
                $VenueCity   = $hall_city;

                $query1  = "INSERT INTO  wp_postmeta (post_id, meta_key, meta_value)";
                $query1 .= " VALUES ('" . $ID . "', '" . $_VenueCity . "', '" . $VenueCity . "')";
                $result1 = @mysqli_query($MySQL, $query1);

                $_EventVenueCountry   = '_EventVenueCountry';
                $EventVenueCountry    = $hall_county;

                $query1  = "INSERT INTO  wp_postmeta (post_id, meta_key, meta_value)";
                $query1 .= " VALUES ('" . $ID . "', '" . $_EventVenueCountry . "', '" . $EventVenueCountry . "')";
                $result1 = @mysqli_query($MySQL, $query1);

            } else {
                $hall_name     = 'online';
                $_VenueName   = '_EventVenueName';
                $VenueName   = $hall_name;

                $query1  = "INSERT INTO  wp_postmeta (post_id, meta_key, meta_value)";
                $query1 .= " VALUES ('" . $ID . "', '" . $_VenueName . "', '" . $VenueName . "')";
                $result1 = @mysqli_query($MySQL, $query1);
            }
        }

        $_EventRealisationType   = '_EventRealisationType';
        $EventRealisationType    = $realisation_type;

        $query1  = "INSERT INTO  wp_postmeta (post_id, meta_key, meta_value)";
        $query1 .= " VALUES ('" . $ID . "', '" . $_EventRealisationType . "', '" . $EventRealisationType . "')";
        $result1 = @mysqli_query($MySQL, $query1);

        $_EventStatus   = '_EventStatus';
        $EventStatus    = $status;

        $query1  = "INSERT INTO  wp_postmeta (post_id, meta_key, meta_value)";
        $query1 .= " VALUES ('" . $ID . "', '" . $_EventStatus . "', '" . $EventStatus . "')";
        $result1 = @mysqli_query($MySQL, $query1);

        $_EventGoalsAndPurpose   = '_EventGoalsAndPurpose';
        $EventGoalsAndPurpose    = $goals_and_purpose;

        $query1  = "INSERT INTO  wp_postmeta (post_id, meta_key, meta_value)";
        $query1 .= " VALUES ('" . $ID . "', '" . $_EventGoalsAndPurpose . "', '" . $EventGoalsAndPurpose . "')";
        $result1 = @mysqli_query($MySQL, $query1);

        foreach($learning_outcomes as $learning_outcomes_key => $learning_outcomes_value) {

            $_EvenLearningOutcome   = '_EventLearningOutcome';
            $EvenLearningOutcome    = $learning_outcomes_value;

            $query1  = "INSERT INTO  wp_postmeta (post_id, meta_key, meta_value)";
            $query1 .= " VALUES ('" . $ID . "', '" . $_EvenLearningOutcome . "', '" . $EvenLearningOutcome . "')";
            $result1 = @mysqli_query($MySQL, $query1);
        }


        # TARGET GROUPS
        foreach($target_groups as $tg_key => $tg_value) {

            $target_group  = $tg_value['target_group'];

            $_EventTargetedGroups   = '_EventTargetedGroups';
            $EventTargetedGroups    = $target_group;

            $query1  = "INSERT INTO  wp_postmeta (post_id, meta_key, meta_value)";
            $query1 .= " VALUES ('" . $ID . "', '" . $_EventTargetedGroups . "', '" . $EventTargetedGroups . "')";
            $result1 = @mysqli_query($MySQL, $query1);

        }

        # TRAINERS
        foreach($trainers as $t_key => $t_value) {

            $full_name     = $t_value['full_name'];

            $_EventTrainers   = '_EventTrainers';
            $EventTrainers    = $full_name;

            $query1  = "INSERT INTO  wp_postmeta (post_id, meta_key, meta_value)";
            $query1 .= " VALUES ('" . $ID . "', '" . $_EventTrainers . "', '" . $EventTrainers . "')";
            $result1 = @mysqli_query($MySQL, $query1);

        }


        foreach($institutions as $institution_key => $institution_value) {

            $InstitutionName         = $institution_value['name'];

            $_EventInstitution   = '_EventInstitution';
            $EventInstitution    = $InstitutionName;

            $query1  = "INSERT INTO  wp_postmeta (post_id, meta_key, meta_value)";
            $query1 .= " VALUES ('" . $ID . "', '" . $_EventInstitution . "', '" . $EventInstitution . "')";
            $result1 = @mysqli_query($MySQL, $query1);

        }

        if ($details_page_link != '') {
            $_EventVenueURL   = '_EventVenueURL';
            $EventVenueURL   = $details_page_link;

            $query1  = "INSERT INTO  wp_postmeta (post_id, meta_key, meta_value)";
            $query1 .= " VALUES ('" . $ID . "', '" . $_EventVenueURL . "', '" . $EventVenueURL . "')";
            $result1 = @mysqli_query($MySQL, $query1);
        }

        if ($admin_full_name != '') {
            $_EventOrganizerFullName    = '_EventOrganizerFullName';
            $EventOrganizerFullName     = $admin_full_name;

            $query1  = "INSERT INTO  wp_postmeta (post_id, meta_key, meta_value)";
            $query1 .= " VALUES ('" . $ID . "', '" . $_EventOrganizerFullName . "', '" . $EventOrganizerFullName . "')";
            $result1 = @mysqli_query($MySQL, $query1);
        }

        if ($admin_phone != '') {
            $_EventVenuePhone    = '_EventVenuePhone';
            $EventVenuePhone     = $admin_phone;

            $query1  = "INSERT INTO  wp_postmeta (post_id, meta_key, meta_value)";
            $query1 .= " VALUES ('" . $ID . "', '" . $_EventVenuePhone . "', '" . $EventVenuePhone . "')";
            $result1 = @mysqli_query($MySQL, $query1);
        }

        if ($admin_email != '') {
            $_OrganizerEmail    = '_EventOrganizerEmail';
            $OrganizerEmail     = $admin_email;

            $query1  = "INSERT INTO  wp_postmeta (post_id, meta_key, meta_value)";
            $query1 .= " VALUES ('" . $ID . "', '" . $_OrganizerEmail . "', '" . $OrganizerEmail . "')";
            $result1 = @mysqli_query($MySQL, $query1);
        }

    }


}

print 'success!';


mysqli_close($MySQL);




?>