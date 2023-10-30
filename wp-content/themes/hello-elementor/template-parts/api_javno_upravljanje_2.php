<?php
/*Template Name: API javno upravljanje 2*/

/**
 * The template for displaying singular post-types: posts, pages and user-defined custom post types.
 *
 * @package HelloElementor
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

$site_name = get_bloginfo('name');
$tagline   = get_bloginfo('description', 'display');
$header_nav_menu = wp_nav_menu([
    'theme_location' => 'menu-1',
    'fallback_cb' => false,
    'echo' => false,
]);

?>

<main id="content" class='site-main'>
    <div class="page-content">
        <?php the_content();
        ?>
        <div class="api">
            <?php
            // Ovaj URL se mijenja ovisno o template-u
            $json_url = 'http://lmstest.dsju.hr/webservice/rest/server.php?wstoken=58c7a863ef504cf072a551d1ccb0910e&wsfunction=mod_timetable_get_all_relevant_timetables_by_category_id&moodlewsrestformat=json&categoryid=3';
            $json_data = file_get_contents($json_url);

            if ($json_data === false) {
                echo 'Nije moguće dohvatiti podatke.';
            } else {
                $data = json_decode($json_data, true);

                // Prikaz podataka
                foreach ($data as $course) {
                    echo '<h2><a href="' . $course['details_page_link'] . 'target="blank">' . $course['course_name'] . '</a></h2>';
                    echo '<p>Status: ' . $course['status'] . '</p>';
                    echo '<p>Sadržaj: ' . $course['content'] . '</p>';
                    echo '<p>Ciljevi i svrha: ' . $course['goals_and_purpose'] . '</p>';
                    echo '<p>Početak: ' . $course['dates'][0]['start_date'] . '</p>';
                    echo '<p>Kraj: ' . $course['dates'][0]['end_date'] . '</p>';
                    // Ostali podaci ovdje na isti način kao prethodni.
                }
            }
            ?>
        </div>
    </div>

</main>


<?php
$footer_nav_menu = wp_nav_menu([
    'theme_location' => 'menu-2',
    'fallback_cb' => false,
    'echo' => false,
]);
?>
<footer id="site-footer" class="site-footer" role="contentinfo">
    <?php if ($footer_nav_menu) : ?>
        <nav class="site-navigation">
            <?php
            // PHPCS - escaped by WordPress with "wp_nav_menu"
            echo $footer_nav_menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            ?>
        </nav>
    <?php endif; ?>
</footer>