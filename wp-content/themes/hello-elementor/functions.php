<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_VERSION', '2.7.1' );

if ( ! isset( $content_width ) ) {
	$content_width = 800; // Pixels.
}

if ( ! function_exists( 'hello_elementor_setup' ) ) {
	/**
	 * Set up theme support.
	 *
	 * @return void
	 */
	function hello_elementor_setup() {
		if ( is_admin() ) {
			hello_maybe_update_theme_version_in_db();
		}

		if ( apply_filters( 'hello_elementor_register_menus', true ) ) {
			register_nav_menus( [ 'menu-1' => esc_html__( 'Header', 'hello-elementor' ) ] );
			register_nav_menus( [ 'menu-2' => esc_html__( 'Footer', 'hello-elementor' ) ] );
		}

		if ( apply_filters( 'hello_elementor_post_type_support', true ) ) {
			add_post_type_support( 'page', 'excerpt' );
		}

		if ( apply_filters( 'hello_elementor_add_theme_support', true ) ) {
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'title-tag' );
			add_theme_support(
				'html5',
				[
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
					'script',
					'style',
				]
			);
			add_theme_support(
				'custom-logo',
				[
					'height'      => 100,
					'width'       => 350,
					'flex-height' => true,
					'flex-width'  => true,
				]
			);

			/*
			 * Editor Style.
			 */
			add_editor_style( 'classic-editor.css' );

			/*
			 * Gutenberg wide images.
			 */
			add_theme_support( 'align-wide' );

			/*
			 * WooCommerce.
			 */
			if ( apply_filters( 'hello_elementor_add_woocommerce_support', true ) ) {
				// WooCommerce in general.
				add_theme_support( 'woocommerce' );
				// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
				// zoom.
				add_theme_support( 'wc-product-gallery-zoom' );
				// lightbox.
				add_theme_support( 'wc-product-gallery-lightbox' );
				// swipe.
				add_theme_support( 'wc-product-gallery-slider' );
			}
		}
	}
}
add_action( 'after_setup_theme', 'hello_elementor_setup' );

function hello_maybe_update_theme_version_in_db() {
	$theme_version_option_name = 'hello_theme_version';
	// The theme version saved in the database.
	$hello_theme_db_version = get_option( $theme_version_option_name );

	// If the 'hello_theme_version' option does not exist in the DB, or the version needs to be updated, do the update.
	if ( ! $hello_theme_db_version || version_compare( $hello_theme_db_version, HELLO_ELEMENTOR_VERSION, '<' ) ) {
		update_option( $theme_version_option_name, HELLO_ELEMENTOR_VERSION );
	}
}

if ( ! function_exists( 'hello_elementor_scripts_styles' ) ) {
	/**
	 * Theme Scripts & Styles.
	 *
	 * @return void
	 */
	function hello_elementor_scripts_styles() {
		$min_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( apply_filters( 'hello_elementor_enqueue_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor',
				get_template_directory_uri() . '/style' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if ( apply_filters( 'hello_elementor_enqueue_theme_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor-theme-style',
				get_template_directory_uri() . '/theme' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_scripts_styles' );

if ( ! function_exists( 'hello_elementor_register_elementor_locations' ) ) {
	/**
	 * Register Elementor Locations.
	 *
	 * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
	 *
	 * @return void
	 */
	function hello_elementor_register_elementor_locations( $elementor_theme_manager ) {
		if ( apply_filters( 'hello_elementor_register_elementor_locations', true ) ) {
			$elementor_theme_manager->register_all_core_location();
		}
	}
}
add_action( 'elementor/theme/register_locations', 'hello_elementor_register_elementor_locations' );

if ( ! function_exists( 'hello_elementor_content_width' ) ) {
	/**
	 * Set default content width.
	 *
	 * @return void
	 */
	function hello_elementor_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'hello_elementor_content_width', 800 );
	}
}
add_action( 'after_setup_theme', 'hello_elementor_content_width', 0 );

if ( is_admin() ) {
	require get_template_directory() . '/includes/admin-functions.php';
}

/**
 * If Elementor is installed and active, we can load the Elementor-specific Settings & Features
*/

// Allow active/inactive via the Experiments
require get_template_directory() . '/includes/elementor-functions.php';

/**
 * Include customizer registration functions
*/
function hello_register_customizer_functions() {
	if ( is_customize_preview() ) {
		require get_template_directory() . '/includes/customizer-functions.php';
	}
}
add_action( 'init', 'hello_register_customizer_functions' );

if ( ! function_exists( 'hello_elementor_check_hide_title' ) ) {
	/**
	 * Check hide title.
	 *
	 * @param bool $val default value.
	 *
	 * @return bool
	 */
	function hello_elementor_check_hide_title( $val ) {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$current_doc = Elementor\Plugin::instance()->documents->get( get_the_ID() );
			if ( $current_doc && 'yes' === $current_doc->get_settings( 'hide_title' ) ) {
				$val = false;
			}
		}
		return $val;
	}
}
add_filter( 'hello_elementor_page_title', 'hello_elementor_check_hide_title' );

/**
 * BC:
 * In v2.7.0 the theme removed the `hello_elementor_body_open()` from `header.php` replacing it with `wp_body_open()`.
 * The following code prevents fatal errors in child themes that still use this function.
 */
if ( ! function_exists( 'hello_elementor_body_open' ) ) {
	function hello_elementor_body_open() {
		wp_body_open();
	}
}


function custom_api_shortcode($atts)
{
	// zadani URL ako korisnik nije pružio URL atribut
	$default_url = 'http://lmstest.dsju.hr/webservice/rest/server.php?wstoken=58c7a863ef504cf072a551d1ccb0910e&wsfunction=mod_timetable_get_all_relevant_timetables_by_category_id&moodlewsrestformat=json&categoryid=3';

	// URL iz atributa shortcode-a ili koristite zadani URL ako nije pružen
	$json_url = isset($atts['url']) ? esc_url($atts['url']) : $default_url;

	// Dohvaćanje JSON podataka
	$json_data = file_get_contents($json_url);

	// Provjera je li dohvaćanje uspjelo
	if ($json_data === false) {
		return 'Nije moguće dohvatiti podatke.';
	} else {
		// Pretvorba JSON podatke u PHP niz
		$data = json_decode($json_data, true);

		// Prikaz podataka
		$output = '<div class="api">';
		foreach ($data as $course) {
			$output .= '<!-- wp:paragraph -->
			<h3 style="color: #6e478b;margin:20px 0"><a href="' . esc_url($course['details_page_link']) . '" target="_blank">' . esc_html($course['course_name']) . '</a></h3>
			<!-- /wp:paragraph -->
			
			<!-- wp:paragraph -->
			<table width="100%">
				<tbody>
					<tr>
						<td width="190"><b>STATUS</b></td>
						<td>' . esc_html($course['status']) . '</td>
					</tr>
					<tr>
						<td width="190"><b>SADRŽAJ</b></td>
						<td>' . wp_strip_all_tags($course['content']) . '</td>
					</tr>
					<tr>
						<td width="190"><b>CILJEVI</b></td>
						<td>' . wp_strip_all_tags($course['goals_and_purpose']) . '</td>
					</tr>
					<tr>
						<td width="190"><b>DATUMI</b></td>
						<td>' . esc_html($course['dates'][0]['start_date']) . ' - ' . esc_html($course['dates'][0]['end_date']) . '</td>
					</tr>
					<tr>
						<td width="190"><b>DVORANA</b></td>
						<td>' . esc_html($course['dates'][0]['hall_name']) . ', ' . esc_html($course['dates'][0]['hall_address']) . ', ' . esc_html($course['dates'][0]['hall_city']) . ', ' . esc_html($course['dates'][0]['hall_county']) . '</td>
					</tr>
				</tbody>
			</table>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph -->
        		<p style="margin-bottom:50px;"><span style="color: #6e478b; background-color: transparent; background-image: none; border-color: #007bff; display: inline-block; font-weight: 400; text-align: center; white-space: nowrap; vertical-align: middle; border: 1px solid #6e478b; padding: 0.375rem 0.75rem; font-size: 1rem; line-height: 1.5; border-radius: 0.25rem;"><a href="' . esc_url($course['details_page_link']) .'" target="_blank" rel="noopener">PRIJAVI SE</a></span></p>
        	<!-- /wp:paragraph -->';
			// Dodajte ostale podatke koje želite prikazati
		}
		$output .= '</div>';

		return $output;
	}
}
add_shortcode('custom-api', 'custom_api_shortcode');

function custom_api_shortcode_javno_upravljanje($atts)
{
// URL do JSON-a
$json_url = 'https://lmstest.dsju.hr/webservice/rest/server.php?wstoken=58c7a863ef504cf072a551d1ccb0910e&wsfunction=mod_timetable_get_courses_with_upcoming_terms&moodlewsrestformat=json&categoryid=9';

// Dohvaćanje JSON podataka
$json_data = file_get_contents($json_url);

// Provjera je li dohvaćanje uspjelo
if ($json_data === false) {
    echo 'Nije moguće dohvatiti podatke.';
} else {
		// Pretvorite JSON podatke u PHP niz
		$data = json_decode($json_data, true);

		// Prikaz podataka
		?>
		<div class="tecaj-container">
		<?php	
		foreach ($data as $course) {

			?>
			<div class="tecaj">
				<div class="datum-naslov javno-upravljanje">
					<?php	
					#echo '<!-- p>' . $course['dates'][0]['start_date'] . '</p -->';
					#echo '<div class="hero-image" style="background: url(https://webtest.dsju.hr/wp-content/uploads/2023/10/hero-javno-upravljanje.png) no-repeat;"></div>';
					echo '<h2>' . $course['course_name'] . '</h2>';
					echo '<div style="text-align: left;"><img style="vertical-align: middle;" src="https://webtest.dsju.hr/wp-content/uploads/2023/10/icon-jp.png" width="18"><span style="margin-left:5px;font-size:13px;color:#808080">Javno upravljanje</span></div>';
					?>
				</div>
				<div class="detalji">
				<?php
				echo '<p><span style="font-weight:600;font-size:16px;display:block">Status:</span> ' . $course['status'] . '</p>';
				echo '<p><span style="font-weight:600;font-size:16px;display:block">Sadržaj:</span> ' . $course['content'] . '</p>';
				echo '<p><span style="font-weight:600;font-size:16px;display:block">Ciljevi i svrha:</span> ' . $course['goals_and_purpose'] . '</p>';
				echo '<p><span style="font-weight:600;font-size:16px;display:block">Kraj:</span> ' . $course['dates'][0]['end_date'] . '</p>';
				// Dodajte ostale podatke koje želite prikazati
				?>
				</div>
			</div>
			<?php
		}
		
		?>
		</div>
		<?php
	}
}

add_shortcode('custom-api-javno-upravljanje', 'custom_api_shortcode_javno_upravljanje');


function custom_api_shortcode_javno_upravljanje_2($atts)
{
// URL do JSON-a
$json_url = 'http://lmstest.dsju.hr/webservice/rest/server.php?wstoken=58c7a863ef504cf072a551d1ccb0910e&wsfunction=mod_timetable_get_all_relevant_timetables_by_category_id&moodlewsrestformat=json&categoryid=3';

// Dohvaćanje JSON podataka
$json_data = file_get_contents($json_url);

// Provjera je li dohvaćanje uspjelo
if ($json_data === false) {
    echo 'Nije moguće dohvatiti podatke.';
} else {
		// Pretvorite JSON podatke u PHP niz
		$data = json_decode($json_data, true);

		// Prikaz podataka
		?>
		<div class="tecaj-container">
		<?php	
		foreach ($data as $course) {

			?>
			<div class="tecaj">
				<div class="datum-naslov javno-upravljanje">
					<?php	
					#echo '<!-- p>' . $course['dates'][0]['start_date'] . '</p -->';
					echo '<div class="hero-image" style="background: url(https://webtest.dsju.hr/wp-content/uploads/2023/10/hero-javno-upravljanje.png) no-repeat;"></div>';
					echo '<h2>' . $course['course_name'] . '</h2>';
					echo '<div style="text-align: left;"><img style="vertical-align: middle;" src="https://webtest.dsju.hr/wp-content/uploads/2023/10/icon-jp.png" width="18"><span style="margin-left:5px;font-size:13px;color:#808080">Javno upravljanje</span></div>';
					?>
				</div>
				<div class="detalji">
				<?php
				echo '
<div class="elementor-container elementor-column-gap-default">
	<div class="elementor-column elementor-col-50 elementor-top-column elementor-element elementor-element-b78b0e4" data-id="b78b0e4" data-element_type="column">
<div class="elementor-widget-wrap elementor-element-populated">
				<div class="elementor-element elementor-element-5fcb198 elementor-widget elementor-widget-heading" data-id="5fcb198" data-element_type="widget" data-widget_type="heading.default">
<div class="elementor-widget-container">
<h2 class="elementor-heading-title elementor-size-default">Osnove upravljanja vremenom</h2>		</div>
</div>
<div class="elementor-element elementor-element-1876cbe elementor-widget elementor-widget-text-editor" data-id="1876cbe" data-element_type="widget" data-widget_type="text-editor.default">
<div class="elementor-widget-container">
			<p><span style="font-size: 14px;">Kroz ovaj sadržaj, polaznici će dobiti praktične i teorijske alate koji će im pomoći u svakodnevnom životu i radu, kako bi efikasnije koristili svoje vrijeme i ostvarivali svoje ciljeve.</span></p><ol style="margin: 6px 0px 2px; font-size: 16px; background-color: #ffffff;"><li style="margin-top: 9px; font-size: 14px;"><p style="margin-top: 9px; margin-bottom: 0px;">Uvod u upravljanje vremenom</p><ul style="margin: 6px 0px 2px; font-size: 14px;"><li style="margin-top: 9px; font-size: 14px;">Definiranje upravljanja vremenom i njegovog značaja</li><li style="margin-top: 9px; font-size: 14px;">Uobičajene prepreke u upravljanju vremenom</li></ul></li><li style="margin-top: 9px; font-size: 14px;"><p style="margin-top: 9px; margin-bottom: 0px;">Samoprocjena i prepoznavanje “kradljivaca” vremena</p><ul style="margin: 6px 0px 2px; font-size: 14px;"><li style="margin-top: 9px; font-size: 14px;">Analiza svakodnevnih rutina i identifikacija neučinkovitosti</li><li style="margin-top: 9px; font-size: 14px;">Razlikovanje i postavljanje prioriteta</li></ul></li><li style="margin-top: 9px; font-size: 14px;"><p style="margin-top: 9px; margin-bottom: 0px;">Tehnike i alati za upravljanje vremenom</p><ul style="margin: 6px 0px 2px; font-size: 14px;"><li style="margin-top: 9px; font-size: 14px;">Planiranje i postavljanje ciljeva</li><li style="margin-top: 9px; font-size: 14px;">Lista zadataka, korištenje kalendara i aplikacija za upravljanje vremenom</li><li style="margin-top: 9px; font-size: 14px;">Metoda Pomodoro, 2-minutno pravilo i ostale tehnike</li></ul></li></ol>						</div>
</div>
	</div>
</div>
<div class="elementor-column elementor-col-50 elementor-top-column elementor-element elementor-element-8bf692d" data-id="8bf692d" data-element_type="column" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
<div class="elementor-widget-wrap elementor-element-populated">
				<div class="elementor-element elementor-element-ca886e3 elementor-widget elementor-widget-text-editor" data-id="ca886e3" data-element_type="widget" data-widget_type="text-editor.default">
<div class="elementor-widget-container">
			<p><b>CILJEVI I SVRHA:</b></p><p>U suvremenom ubrzanom društvu, sposobnost efikasnog upravljanja vremenom postala je ključna vještina za ostvarivanje osobnih i profesionalnih ciljeva. Program “Osnove upravljanja vremenom” osmišljen je kako bi polaznicima pružio sveobuhvatno razumijevanje principa i tehnika upravljanja vremenom. Svrha ovog programa je omogućiti polaznicima da maksimiziraju iskoristivost svojih dnevnih resursa, povećaju produktivnost i postignu bolju ravnotežu između poslovnih i privatnih obaveza.</p>						</div>
</div>
<section class="elementor-section elementor-inner-section elementor-element elementor-element-352a9f1 elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="352a9f1" data-element_type="section">
		<div class="elementor-container elementor-column-gap-default">
	<div class="elementor-column elementor-col-25 elementor-inner-column elementor-element elementor-element-f25da54" data-id="f25da54" data-element_type="column">
<div class="elementor-widget-wrap elementor-element-populated">
				<div class="elementor-element elementor-element-4b3f831 elementor-widget__width-initial elementor-view-default elementor-widget elementor-widget-icon" data-id="4b3f831" data-element_type="widget" data-widget_type="icon.default">
<div class="elementor-widget-container">
	<div class="elementor-icon-wrapper">
<div class="elementor-icon">
<i aria-hidden="true" class="far fa-edit"></i>			</div>
</div>
</div>
</div>
	</div>
</div>
<div class="elementor-column elementor-col-25 elementor-inner-column elementor-element elementor-element-06890a5" data-id="06890a5" data-element_type="column">
<div class="elementor-widget-wrap elementor-element-populated">
				<div class="elementor-element elementor-element-649d763 elementor-widget__width-initial elementor-widget elementor-widget-text-editor" data-id="649d763" data-element_type="widget" data-widget_type="text-editor.default">
<div class="elementor-widget-container">
			<p><b>ISHODI:</b><br>Neki tekst</p>						</div>
</div>
	</div>
</div>
<div class="elementor-column elementor-col-25 elementor-inner-column elementor-element elementor-element-4a19a1c" data-id="4a19a1c" data-element_type="column">
<div class="elementor-widget-wrap elementor-element-populated">
				<div class="elementor-element elementor-element-3ab4c67 elementor-widget__width-initial elementor-view-default elementor-widget elementor-widget-icon" data-id="3ab4c67" data-element_type="widget" data-widget_type="icon.default">
<div class="elementor-widget-container">
	<div class="elementor-icon-wrapper">
<div class="elementor-icon">
<i aria-hidden="true" class="far fa-calendar-alt"></i>			</div>
</div>
</div>
</div>
	</div>
</div>
<div class="elementor-column elementor-col-25 elementor-inner-column elementor-element elementor-element-d410c7e" data-id="d410c7e" data-element_type="column">
<div class="elementor-widget-wrap elementor-element-populated">
				<div class="elementor-element elementor-element-498b487 elementor-widget__width-initial elementor-widget elementor-widget-text-editor" data-id="498b487" data-element_type="widget" data-widget_type="text-editor.default">
<div class="elementor-widget-container">
			<p><strong>NADOLAZEĆI TERMINI:<br></strong><span aria-labelledby="value"><span class="objectBox objectBox-string">16.10.2023.<br>20.10.2023.<br></span></span></p>						</div>
</div>
	</div>
</div>
			</div>
</section>
	</div>
</div>
</div>';
				// Dodajte ostale podatke koje želite prikazati
				?>
				</div>
			</div>
			<?php
		}
		
		?>
		</div>
		<?php
	}
}

add_shortcode('custom-api-javno-upravljanje-2', 'custom_api_shortcode_javno_upravljanje_2');


# Centar izvrsnosti za javne finacije
function api_centar_izvrsnosti($atts)
{
// URL do JSON-a
# API Centar izvrsnosti za javne finacije
#$json_url = 'http://lmstest.dsju.hr/webservice/rest/server.php?wstoken=58c7a863ef504cf072a551d1ccb0910e&wsfunction=mod_timetable_get_courses_with_upcoming_terms&moodlewsrestformat=json&categoryid=13';

# Test
$json_url = 'http://lmstest.dsju.hr/webservice/rest/server.php?wstoken=58c7a863ef504cf072a551d1ccb0910e&wsfunction=mod_timetable_get_courses_with_upcoming_terms&moodlewsrestformat=json&categoryid=3';
// Dohvaćanje JSON podataka
$json_data = file_get_contents($json_url);

// Provjera je li dohvaćanje uspjelo
if ($json_data === false) {
    echo 'Nije moguće dohvatiti podatke.';
} else {
		// Pretvorite JSON podatke u PHP niz
		$data = json_decode($json_data, true);

		// Prikaz podataka
		?>
		<div class="tecaj-container">
		<?php	
		foreach ($data as $course) {

			?>
			<div class="tecaj">
				<div class="datum-naslov">
					<?php	
					echo '
					<div class="elementor-container elementor-column-gap-default">
						<div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-79d313d" data-id="79d313d" data-element_type="column">
							<div class="elementor-widget-wrap elementor-element-populated">
								<section class="elementor-section elementor-inner-section elementor-element elementor-element-8d14afd elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="8d14afd" data-element_type="section">
									<div class="elementor-background-overlay"></div>
									<div class="elementor-container elementor-column-gap-default">
										<div class="elementor-column elementor-col-25 elementor-inner-column elementor-element elementor-element-3161738" data-id="3161738" data-element_type="column">
											<div class="elementor-widget-wrap elementor-element-populated">
												<div class="elementor-element elementor-element-7c5c878 elementor-widget elementor-widget-heading" data-id="7c5c878" data-element_type="widget" data-widget_type="heading.default">
													<div class="elementor-widget-container">
														<h2 class="elementor-heading-title elementor-size-default programi">' . $course['course_name'] . '</h2>
													</div>
												</div>
												<div class="elementor-element elementor-element-18ff892 elementor-widget elementor-widget-text-editor" data-id="18ff892" data-element_type="widget" data-widget_type="text-editor.default">
													<div class="elementor-widget-container">
														<style>/*! elementor - v3.12.2 - 23-04-2023 */
															.elementor-widget-text-editor.elementor-drop-cap-view-stacked .elementor-drop-cap{background-color:#69727d;color:#fff}.elementor-widget-text-editor.elementor-drop-cap-view-framed .elementor-drop-cap{color:#69727d;border:3px solid;background-color:transparent}.elementor-widget-text-editor:not(.elementor-drop-cap-view-default) .elementor-drop-cap{margin-top:8px}.elementor-widget-text-editor:not(.elementor-drop-cap-view-default) .elementor-drop-cap-letter{width:1em;height:1em}.elementor-widget-text-editor .elementor-drop-cap{float:left;text-align:center;line-height:1;font-size:50px}.elementor-widget-text-editor .elementor-drop-cap-letter{display:inline-block}</style>				
															<p>' . $course['goals_and_purpose'] . '</p>
													</div>
												</div>
											</div>
										</div>
										<div class="elementor-column elementor-col-25 elementor-inner-column elementor-element elementor-element-a42143a" data-id="a42143a" data-element_type="column" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
											<div class="elementor-widget-wrap elementor-element-populated">
												<div class="elementor-element elementor-element-eb621af elementor-widget__width-auto elementor-widget elementor-widget-heading" data-id="eb621af" data-element_type="widget" data-widget_type="heading.default">
													<div class="elementor-widget-container">
														<h4 class="elementor-heading-title elementor-size-default">Ciljna skupina</h4>		
													</div>
												</div>
												<div class="elementor-element elementor-element-7c94d74 elementor-widget elementor-widget-text-editor" data-id="7c94d74" data-element_type="widget" data-widget_type="text-editor.default">
												<div class="elementor-widget-container">
													<p>Svi službenici</p>
												</div>
											</div>
										</div>
									</div>
									<div class="elementor-column elementor-col-25 elementor-inner-column elementor-element elementor-element-2c3008e" data-id="2c3008e" data-element_type="column" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
										<div class="elementor-widget-wrap elementor-element-populated">
											<div class="elementor-element elementor-element-af7ff1b elementor-widget__width-auto elementor-widget elementor-widget-heading" data-id="af7ff1b" data-element_type="widget" data-widget_type="heading.default">
												<div class="elementor-widget-container">
													<h4 class="elementor-heading-title elementor-size-default">Način izvođenja</h4>		
												</div>
											</div>
											<div class="elementor-element elementor-element-9962543 elementor-widget elementor-widget-text-editor" data-id="9962543" data-element_type="widget" data-widget_type="text-editor.default">
												<div class="elementor-widget-container">
													<p>samostalno e-učenje</p>
												</div>
											</div>
										</div>
									</div>
									<div class="elementor-column elementor-col-25 elementor-inner-column elementor-element elementor-element-66092ea" data-id="66092ea" data-element_type="column" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
										<div class="elementor-widget-wrap elementor-element-populated">
											<div class="elementor-element elementor-element-8f36cbd elementor-widget__width-auto elementor-widget elementor-widget-heading" data-id="8f36cbd" data-element_type="widget" data-widget_type="heading.default">
												<div class="elementor-widget-container">
													<h4 class="elementor-heading-title elementor-size-default">Nadolazeći termini</h4>		
												</div>
											</div>
												<div class="elementor-element elementor-element-044501d elementor-widget elementor-widget-text-editor" data-id="044501d" data-element_type="widget" data-widget_type="text-editor.default">
													<div class="elementor-widget-container">
														<p>' . $course['upcoming_terms'][0]['start_date'] . '</p>						
													</div>
												</div>
											</div>
										</div>
									</div>
								</section>
							</div>
						</div>
					</div>';
					?>
				</div>
				<div class="detalji">
				<?php
				echo '
				<div class="elementor-container elementor-column-gap-default">
					<div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-5102d6d" data-id="5102d6d" data-element_type="column">
						<div class="elementor-widget-wrap elementor-element-populated">
							<div class="elementor-element elementor-element-64851dd elementor-widget elementor-widget-heading" data-id="64851dd" data-element_type="widget" data-widget_type="heading.default">
								<div class="elementor-widget-container">
									<h3 class="elementor-heading-title elementor-size-default">' . $course['course_name'] . '</h3>
								</div>
							</div>
							<section class="elementor-section elementor-inner-section elementor-element elementor-element-03ecc80 elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="03ecc80" data-element_type="section">
								<div class="elementor-container elementor-column-gap-default">
									<div class="elementor-column elementor-col-50 elementor-inner-column elementor-element elementor-element-b9a926a" data-id="b9a926a" data-element_type="column">
										<div class="elementor-widget-wrap elementor-element-populated">
											<div class="elementor-element elementor-element-33216ea elementor-widget elementor-widget-heading" data-id="33216ea" data-element_type="widget" data-widget_type="heading.default">
												<div class="elementor-widget-container">
													<h4 class="elementor-heading-title elementor-size-default">Način izvođenja​</h4>		
												</div>
											</div>
											<div class="elementor-element elementor-element-a73c25f elementor-widget elementor-widget-text-editor" data-id="a73c25f" data-element_type="widget" data-widget_type="text-editor.default">
												<div class="elementor-widget-container">
													<style>/*! elementor - v3.12.2 - 23-04-2023 */
														.elementor-widget-text-editor.elementor-drop-cap-view-stacked .elementor-drop-cap{background-color:#69727d;color:#fff}.elementor-widget-text-editor.elementor-drop-cap-view-framed .elementor-drop-cap{color:#69727d;border:3px solid;background-color:transparent}.elementor-widget-text-editor:not(.elementor-drop-cap-view-default) .elementor-drop-cap{margin-top:8px}.elementor-widget-text-editor:not(.elementor-drop-cap-view-default) .elementor-drop-cap-letter{width:1em;height:1em}.elementor-widget-text-editor .elementor-drop-cap{float:left;text-align:center;line-height:1;font-size:50px}.elementor-widget-text-editor .elementor-drop-cap-letter{display:inline-block}</style>				<p>Samostalno e-učenje</p>						</div>
												</div>
											</div>
										</div>
										<div class="elementor-column elementor-col-50 elementor-inner-column elementor-element elementor-element-2b0f729" data-id="2b0f729" data-element_type="column">
											<div class="elementor-widget-wrap elementor-element-populated">
												<div class="elementor-element elementor-element-04b9b1e elementor-widget elementor-widget-heading" data-id="04b9b1e" data-element_type="widget" data-widget_type="heading.default">
													<div class="elementor-widget-container">
														<h4 class="elementor-heading-title elementor-size-default">Nadolazeći termini</h4>		
													</div>
												</div>
												<div class="elementor-element elementor-element-982cd4b elementor-widget elementor-widget-text-editor" data-id="982cd4b" data-element_type="widget" data-widget_type="text-editor.default">
													<div class="elementor-widget-container">
														<p>18.10.2023.</p>		
													</div>
												</div>
											</div>
										</div>
									</div>
							</section>
						</div>
					</div>
				</div>';
				// Dodajte ostale podatke koje želite prikazati
				?>
				</div>
			</div>
			<?php
		}
		
		?>
		</div>
		<?php
	}
}

add_shortcode('centar-izvrsnosti', 'api_centar_izvrsnosti');

