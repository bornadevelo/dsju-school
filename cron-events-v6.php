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
 


#$json = file_get_contents('https://demo-dsju.ebitsdev.com/webservice/rest/server.php?wstoken=046a91a0eb7e0e3df4a0a4427fffb5b5&wsfunction=mod_timetable_get_all_relevant_timetables&moodlewsrestformat=json');
#$json = file_get_contents('https://lmstest.dsju.hr/webservice/rest/server.php?wstoken=58c7a863ef504cf072a551d1ccb0910e&wsfunction=mod_timetable_get_all_relevant_timetables&moodlewsrestformat=json');
#$json = file_get_contents('https://172.16.19.136/webservice/rest/server.php?wstoken=58c7a863ef504cf072a551d1ccb0910e&wsfunction=mod_timetable_get_all_relevant_timetables&moodlewsrestformat=json');
#$json = file_get_contents('https://demo-dsju.ebitsdev.com/webservice/rest/server.php?wstoken=046a91a0eb7e0e3df4a0a4427fffb5b5&wsfunction=mod_timetable_get_all_relevant_timetables_data_expanded&moodlewsrestformat=json');
$json = file_get_contents('http://lmstest.dsju.hr/webservice/rest/server.php?wstoken=58c7a863ef504cf072a551d1ccb0910e&wsfunction=mod_timetable_get_all_relevant_timetables_data_expanded&moodlewsrestformat=json');
$json_data = json_decode($json,true);


foreach($json_data as $key => $value) {
    $id                 = $value['id'];

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
    $dates              = $value['dates'];
    $institutions       = $value['institutions'];
        

    $post_content  = '';
    
    
    if ($category_name != '') {
        $post_content .= '<!-- wp:paragraph -->';
        $post_content .= '<h3 style="color: #6e478b;margin:20px 0">' . $category_name . '</h3>';
        $post_content .= '<!-- /wp:paragraph -->
        <hr style="margin:25px 0;border: 1px dotted #6e478b;">';
    }

    $post_content .= '<!-- wp:paragraph -->
        <p style="margin-bottom:50px;"><span style="color: #6e478b; background-color: transparent; background-image: none; border-color: #007bff; display: inline-block; font-weight: 400; text-align: center; white-space: nowrap; vertical-align: middle; border: 1px solid #6e478b; padding: 0.375rem 0.75rem; font-size: 1rem; line-height: 1.5; border-radius: 0.25rem;"><a href="' . $details_page_link .'" target="_blank" rel="noopener">PRIJAVI SE</a></span></p>
        <!-- /wp:paragraph -->';

    $post_content .= '<!-- wp:paragraph -->';
    $post_content .=  $content . '<!-- /wp:paragraph -->';

    $post_content .= '<p style="margin:50px 0"></p>
    
    <!-- wp:paragraph -->
    <table width="100%">
        <tbody>';
        if ($admin_full_name != '') {
            $post_content .= '<tr>
                    <td width="190"><b>KONTAKT</b></td>
                    <td>' . $admin_full_name . '</td>
                </tr>';
        }

        if ($admin_email != '') {
            $post_content .= '<tr>
            <td width="190"><b>E-MAIL</b></td>
            <td><a href="mailto:' . $admin_email . '">' . $admin_email . '</a></td>
            </tr>';
        }

        if ($admin_phone != '') {
            $post_content .= '<tr>
            <td width="190"><b>TELEFON</b></td>
            <td>' . $admin_phone . '</td>
            </tr>';
        }

        if ($realisation_type != '') {
            $post_content .= '<tr>
            <td width="190"><b>LOKACIJA</b></td>
            <td>' . $realisation_type . '</td>
            </tr>';
        }

        foreach($dates as $date_key => $date_value) {


            $start_date    = $date_value['start_date']; $datetime1 = strtotime($start_date);
            $end_date      = $date_value['end_date'];   $datetime2 = strtotime($end_date);
    
            $hall_name     = $date_value['hall_name'];
            $hall_address  = $date_value['hall_address'];
            
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
    
            # vrijeme održavanja
            if ($start_date != '') {
                $post_content .= '<tr>
                <td width="190"><b>POČETAK EDUKACIJE</b></td>
                <td>' . $start_date . '</td>
                </tr>';
            }
            if ($end_date != '') {

                $post_content .= '<tr>
                <td width="190"><b>KRAJ EDUKACIJE</b></td>
                <td>' . $end_date . '</td>
                </tr>';

            }
            # Dvorana i adresa
            if ($hall_name != '') {
                $post_content .= '<tr>
                <td width="190"><b>DVORANA</b></td>
                <td>' . $hall_name . '</td>
                </tr>';
            }
            if ($hall_address != '') {
                $post_content .= '<tr>
                <td width="150"><b>ADRESA</b></td>
                <td>' . $hall_address . '</td>
                </tr>';
            }
            
        }

        # TARGET GROUPS
        foreach($target_groups as $tg_key => $tg_value) {
            $target_group  = $tg_value['target_group'];
            
            if (isset($target_group)) {
                if ($tg_key == 0) {
                    $post_content .= '<tr>
                    <td width="150"><b>POLAZNICI</b></td>
                    <td>';
                }

                $post_content .= $target_group . '<br>'; 
                
                if ($tg_key == 0) {
                    $post_content .= '</td>
                    </tr>';
                }
            }
        }

        # TRAINERS
        foreach($trainers as $t_key => $t_value) {
            $full_name     = $t_value['full_name']; 
            if (isset($full_name)) {
                
                if ($t_key == 0) {
                    $post_content .= '<tr>
                    <td width="190"><b>TRENER</b></td>
                    <td>';
                }

                $post_content .= $full_name . '<br>'; 
                
                if ($t_key == 0) {
                    $post_content .= '</td>
                    </tr>';
                }

            }
        }

        if ($in_house != 0) {

            $post_content .= '<tr>
                <td width="190"></td>
                <td>Državna škola za javnu upravu</td>
                </tr>';
        }

        foreach($institutions as $institution_key => $institution_value) {
            $name         = $institution_value['name']; 
            
            if (isset($name)) {
                
                if ($institution_key == 0) {
                    $post_content .= '<tr>
                    <td width="220" style="color:#9a2424">EDUKACIJA JE NAMJENJENA ISKLJUČIVO SLUŽBENICIMA</td>
                    <td>';
                }

                $post_content .= $name . '<br>'; 
                
                if ($institution_key == 0) {
                    $post_content .= '</td>
                    </tr>';
                }

            }
        }

        $post_content .= '
        </tbody>
    </table>
    <!-- /wp:paragraph -->';

    $post_content .= '<hr style="margin:35px 0;border: 1px dotted #6e478b;">
    <!-- wp:paragraph -->
    <p><span style="color: #6e478b; background-color: transparent; background-image: none; border-color: #007bff; display: inline-block; font-weight: 400; text-align: center; white-space: nowrap; vertical-align: middle; border: 1px solid #6e478b; padding: 0.375rem 0.75rem; font-size: 1rem; line-height: 1.5; border-radius: 0.25rem;"><a href="' . $details_page_link .'" target="_blank" rel="noopener">PRIJAVI SE</a></span></p>
    <!-- /wp:paragraph -->';


    #print $post_content;
    
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
        $EventStartDateUTC = $_start_date;

        $query1  = "INSERT INTO  wp_postmeta (post_id, meta_key, meta_value)";
        $query1 .= " VALUES ('" . $ID . "', '" . $_EventStartDateUTC . "', '" . $EventStartDateUTC . "')";
        $result1 = @mysqli_query($MySQL, $query1);


        $_EventEndDateUTC   = '_EventEndDateUTC';
        $EventEndDateUTC   = $_end_date;

        $query1  = "INSERT INTO  wp_postmeta (post_id, meta_key, meta_value)";
        $query1 .= " VALUES ('" . $ID . "', '" . $_EventEndDateUTC . "', '" . $EventEndDateUTC . "')";
        $result1 = @mysqli_query($MySQL, $query1); 

        

        if ($admin_email != '') {
            $_OrganizerEmail    = '_OrganizerEmail';
            $OrganizerEmail     = $admin_email;

            $query1  = "INSERT INTO  wp_postmeta (post_id, meta_key, meta_value)";
            $query1 .= " VALUES ('" . $ID . "', '" . $_OrganizerEmail . "', '" . $OrganizerEmail . "')";
            $result1 = @mysqli_query($MySQL, $query1); 
        }


        if ($details_page_link != '') {
            $_VenueURL   = '_VenueURL';
            $VenueURL   = $details_page_link;

            $query1  = "INSERT INTO  wp_postmeta (post_id, meta_key, meta_value)";
            $query1 .= " VALUES ('" . $ID . "', '" . $_VenueURL . "', '" . $VenueURL . "')";
            $result1 = @mysqli_query($MySQL, $query1); 
        }

        if ($hall_address != '') {
            $_VenueAddress   = '_VenueAddress';
            $VenueAddress   = $hall_address;

            $query1  = "INSERT INTO  wp_postmeta (post_id, meta_key, meta_value)";
            $query1 .= " VALUES ('" . $ID . "', '" . $_VenueAddress . "', '" . $VenueAddress . "')";
            $result1 = @mysqli_query($MySQL, $query1); 

            $query2  = "INSERT INTO  wp_postmeta (post_id, meta_key, meta_value)";
            $query2 .= " VALUES ('" . $ID . "', '_VenueCountry', 'Croatia (Local Name: Hrvatska)')";
            $result2 = @mysqli_query($MySQL, $query2); 
        }

        if ($admin_phone != '') {
            $_VenuePhone    = '_VenuePhone';
            $VenuePhone     = $admin_phone;

            $query1  = "INSERT INTO  wp_postmeta (post_id, meta_key, meta_value)";
            $query1 .= " VALUES ('" . $ID . "', '" . $_VenuePhone . "', '" . $VenuePhone . "')";
            $result1 = @mysqli_query($MySQL, $query1); 
        }
        

   }

}

print 'success!';


mysqli_close($MySQL);




?>