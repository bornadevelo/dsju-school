<?php
/*Template Name: API javno upravljanje*/

/**
 * The template for displaying archive pages.
 *
 * @package HelloElementor
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
?>
<main id="content" class="site-main">

    <?php if (apply_filters('hello_elementor_page_title', true)) : ?>
        <header class="page-header">
            <?php
            the_archive_title('<h1 class="entry-title">', '</h1>');
            the_archive_description('<p class="archive-description">', '</p>');
            ?>
        </header>
    <?php endif; ?>
    <div class="page-content">
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
                    echo '<h2><a href="' . $course['details_page_link'] . 'target="blank"">' . $course['course_name'] . '</a></h2>';
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

    <?php wp_link_pages(); ?>

    <?php
    global $wp_query;
    if ($wp_query->max_num_pages > 1) :
    ?>
        <nav class="pagination">
            <?php /* Translators: HTML arrow */ ?>
            <div class="nav-previous"><?php next_posts_link(sprintf(__('%s older', 'hello-elementor'), '<span class="meta-nav">&larr;</span>')); ?></div>
            <?php /* Translators: HTML arrow */ ?>
            <div class="nav-next"><?php previous_posts_link(sprintf(__('newer %s', 'hello-elementor'), '<span class="meta-nav">&rarr;</span>')); ?></div>
        </nav>
    <?php endif; ?>
</main>