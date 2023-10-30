<?php
/**
 * Plugin Name: DSJU Events
 * Description: DSJU Moodle events
 * Version: 1.0
 * Author: eBurza
 * License: GPL2
 */

/*  Copyright 2023 eBurza

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// DEFAULT SCRIPTS
function dsju_events_default_scripts()
{
    wp_enqueue_style('font', 'https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700&display=swap&subset=cyrillic-ext,latin-ext,latin', false);

    wp_register_style('style', plugins_url('style.css', __FILE__), array(), '1.0');
    wp_enqueue_style('style');
}
add_action('wp_enqueue_scripts', 'dsju_events_default_scripts');

//ACTIVATE PLUGIN
register_activation_hook(__FILE__, 'dsju_events_activation');
function dsju_events_activation()
{
    global $wp_version;

    if (!current_user_can('activate_plugins')) return;

    if (version_compare($wp_version, '4.9.10', '<')) {
        wp_die('This plugin requires WordPress version 5.0.3 or higher.');
    }
}

//DEACTIVATE PLUGIN
register_deactivation_hook(__FILE__, 'dsju_events_deactivation');
function dsju_events_deactivation()
{
    if (!current_user_can('deactivate_plugins')) return;

    flush_rewrite_rules();
}

//UNINSTALL PLUGIN
register_uninstall_hook(__FILE__, 'dsju_events_uninstall');
function dsju_events_uninstalll()
{

}

function events_schedule_shortcode() {
    $sortQuery = $_GET['sort'] ? $_GET['sort'] : 'DESC';
    $statusQuery = $_GET['status'] ? $_GET['status'] : 'all';

    $metaQuery = [];

    if ($statusQuery !== 'all'){
        $metaQuery[] = [
            'key' => '_EventStatus',
            'value' => $statusQuery,
            'compare' => 'LIKE'
        ];
    }

    $args = array(
        'post_type' => 'tribe_events',
        'post_status' => 'publish',
        'orderby' => 'ID',
        'order' => $sortQuery,
        'meta_query' => $metaQuery,
    );

    $loop = new WP_Query($args);

//    IF YOU NEED FILTERS INSERT THIS CODE BELOW <h1 class="ds-schedule__title">Raspored</h1>
//    <form method="get" class="ds-schedule__filters">
//        <select name="sort" onchange="this.form.submit()">
//            <option value="DESC" '. ($sortQuery === 'DESC' ? 'selected' : '') .'>Noviji prvo</option>
//            <option value="ASC" '. ($sortQuery === 'ASC' ? 'selected' : '') .'>Stariji prvo</option>
//        </select>
//        <select name="status" onchange="this.form.submit()">
//            <option value="all" '. ($statusQuery === 'all' ? 'selected' : '') .'>Svi događaji</option>
//            <option value="PRIJAVE U TIJEKU" '. ($statusQuery === 'PRIJAVE U TIJEKU' ? 'selected' : '') .'>Prijave u tijeku</option>
//            <option value="ZATVORENO ZA PRIJAVE" '. ($statusQuery === 'ZATVORENO ZA PRIJAVE' ? 'selected' : '') .'>Zatvoreno za prijave</option>
//        </select>
//    </form>

    echo '<div class="ds-schedule">
            <div class="ds-schedule__header">
                <h1 class="ds-schedule__title">Raspored</h1>
            </div>  
            <div class="ds-schedule__items">';

        if($loop->have_posts()){
            while ($loop->have_posts()) : $loop->the_post();
                $startDate = get_post_meta(get_the_ID(), "_EventStartDateUTC", true);
                $endDateString = get_post_meta(get_the_ID(), "_EventEndDateUTC", true);
                $endDate = strtotime($endDateString);
                $isEventVisible = $endDate > time();

                $eventVenueName = get_post_meta(get_the_ID(), "_EventVenueName", true);
                $location = $eventVenueName === 'online'
                    ? $eventVenueName
                    : get_post_meta(get_the_ID(), "_EventVenueCity", true);

                $eventRegistrationLink = get_post_meta(get_the_ID(), "_VenueURL", true);
                $realisationType = get_post_meta(get_the_ID(), "_EventRealisationType", true);
                $status = get_post_meta(get_the_ID(), "_EventStatus", true);
                $isEventDisabled = strtolower($status) !== 'prijave u tijeku';

                $eventLinkClass = $isEventDisabled ? 'ds-event__link ds-event__link_disabled' : 'ds-event__link';

                if ($isEventVisible){
                    echo ' <article class="ds-event">
                        <h2 class="ds-event__title">
                            <a href="' .get_the_permalink(). '" class="ds-event__title-link">' .get_the_title(). '</a>
                        </h2>
                        <div class="ds-event__details">
                            <div class="ds-event__detail">
                                <h3 class="ds-event__detail-title">Termin</h3>
                                <p class="ds-event__detail-text">'. $startDate. 'h</p>
                            </div>
                            <div class="ds-event__detail">
                                <h3 class="ds-event__detail-title">Mjesto održavanja</h3>
                                <p class="ds-event__detail-text">'. $location. '</p>
                            </div>
                            <div class="ds-event__detail">
                                <h3 class="ds-event__detail-title">Način izvođenja</h3>
                                <p class="ds-event__detail-text">'. $realisationType. '</p>
                            </div>
                            <div class="ds-event__detail">
                                <h3 class="ds-event__detail-title">Status</h3>
                                <p class="ds-event__detail-text">'. $status. '</p>
                            </div>
                        </div>';
                         if (!$isEventDisabled){
                             echo '<a
                                href="'. $eventRegistrationLink. '"
                                class="'.$eventLinkClass.'"
                                 >
                                 Prijava
                           </a>';
                         }
                    echo '</article>';
                }

            endwhile;
            wp_reset_postdata();
        } else {
            echo '<p class="ds-schedule__message">Nema nadolazećih događaja.</p>';
        }

    echo '</div>
        </div>';
}
add_shortcode( 'events_schedule_shortcode', 'events_schedule_shortcode' );