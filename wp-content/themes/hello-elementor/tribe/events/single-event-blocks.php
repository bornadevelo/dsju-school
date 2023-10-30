<?php
/**
 * Single Event Template
 *
 * A single event complete template, divided in smaller template parts.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/single-event-blocks.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 4.7
 *
 */

$eventId = $this->get( 'post_id' );

$is_recurring = '';

if ( ! empty( $eventId ) && function_exists( 'tribe_is_recurring_event' ) ) {
    $is_recurring = tribe_is_recurring_event( $eventId );
}

include_once(get_template_directory() . '/includes/dsju-single-event.php');

?>
