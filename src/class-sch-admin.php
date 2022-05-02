<?php

namespace sch;

/**
 * Class to handle admin settings
 */
class Admin {
	/**
	 * Contains instance or null
	 *
	 * @var object|null
	 */
	private static $instance = null;

	/**
	 * Returns instance of Admin.
	 *
	 * @return object
	 */
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor for Admin.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'register_menu_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_scripts' ) );
	}

	/**
	 * Enqueue admin scripts
	 *
	 * @return void
	 */
	public function add_admin_scripts() {
		wp_enqueue_script( 'sch-admin', SCH_URL . '/assets/sch-admin.js', array( 'jquery' ), '1.0', true );
		wp_enqueue_style( 'sch-admin-style', SCH_URL . '/assets/sch-admin.css', array(), '1.1.1', 'all' );

		$args = array(
			'ajax_url'      => admin_url( 'admin-ajax.php' ),
			'cache_nonce'   => wp_create_nonce( 'sch-cache-nonce' ),
			'cache_cleared' => __( 'Cache cleared successfully.', 'simply-cdn-helper' ),
		);

		wp_localize_script( 'sch-admin', 'sch_ajax', $args );

	}

	/**
	 * Register settings in WordPress.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting( 'sch_options_group', 'sch_app_site_id', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => NULL ) );
		register_setting( 'sch_cdn_group', 'sch_static_url', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => NULL ) );
		register_setting( 'sch_cdn_group', 'sch_404_path', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => NULL ) );
	}

	/**
	 * Register menu page for settings.
	 *
	 * @return void
	 */
	public function register_menu_page() {
		add_submenu_page( 'simply-static', __( 'Simply CDN', 'simply-cdn-helper' ), __( 'Simply CDN', 'simply-cdn-helper' ), 'manage_options', 'simply-static_cdn', array( $this, 'render_options' ), 10 );
	}

	/**
	 * Render options form.
	 *
	 * @return void
	 */
	public function render_options() {
		$data = Api::get_site_data();

		?>
		<div class="sch-container">
			<h1><?php echo esc_html_e( 'Simply CDN', 'simply-cdn-helper' ); ?></h1>
			<div class="wrap">
				<div>
					<p>
						<h2><?php echo esc_html_e( 'Connect your website', 'simply-cdn-helper' ); ?></h2>
					</p>
					<p>
						<?php echo esc_html_e( 'Add the site id that was sent to you by e-mail after you made your purchase on simplystatic.io.', 'simply-cdn-helper' ); ?>
					</p>
					<p style="margin-bottom: 50px;">
						<?php echo esc_html_e( 'You need an active connection to get your access credentials to your server and to automatically deploy your site to the CDN.', 'simply-cdn-helper' ); ?>
					</p>
					<form method="post" action="options.php">
					<?php settings_fields( 'sch_options_group' ); ?>
					<p>
						<label for="sch_app_site_id"><?php echo esc_html_e( 'Site-ID', 'simply-cdn-helper' ); ?></label></br>
						<input type="text" id="sch_app_site_id" name="sch_app_site_id" value="<?php echo esc_html( get_option( 'sch_app_site_id' ) ); ?>" />
					</p>
					<?php submit_button(); ?>
					<?php if ( ! empty( $data ) ) : ?>
					<p class="success"><?php echo esc_html_e( 'Your site is successfully connected to the platform.', 'simply-cdn-helper' ); ?></p>
					<?php endif; ?>
					</form>
				</div>
				<div>
				</div>
			</div>
			<div class="wrap">
				<div>
					<p>
						<h2><?php echo esc_html_e( 'Configure your static website', 'simply-cdn-helper' ); ?></h2>
					</p>
					<p>
						<?php echo esc_html_e( 'Once your website is connected you can configure all settings related to the CDN here. This includes settings up redirects, proxy URLs and setting up a custom 404 error page.', 'simply-cdn-helper' ); ?>
					</p>
					<form method="post" action="options.php">
					<?php settings_fields( 'sch_cdn_group' ); ?>
					<p>
						<label for="sch_static_url"><?php echo esc_html_e( 'Static URL', 'simply-cdn-helper' ); ?></label></br>
						<input type="url" id="sch_static_url" name="sch_static_url" value="<?php echo esc_html( get_option( 'sch_static_url' ) ); ?>" />
						<small><?php echo esc_html_e( 'Once you change this setting, your static website will be available under the new domain. Make sure you set your CNAME record before you change this setting.', 'simply-cdn-helper' ); ?></small>
					</p>
					<p>
						<label for="sch_404_path"><?php echo esc_html_e( 'Relative path to your 404 page', 'simply-cdn-helper' ); ?></label></br>
						<input type="text" id="sch_404_path" name="sch_404_path" value="<?php echo esc_html( get_option( 'sch_404_path' ) ); ?>" />
					</p>
					<?php submit_button(); ?>
					</form>
					<p>
					<h2><?php echo esc_html_e( 'Caching', 'simply-cdn-helper' ); ?></h2>
						<?php echo esc_html_e( 'The CDN cache is cleared automatically after each static export. Sometimes you want to clear the cache manually to make sure you get the latest results in your browser.', 'simply-cdn-helper' ); ?>
					</p>
					<p>
					<span class="button-secondary button sch-secondary-button" id="sch-clear-cache"><?php echo esc_html_e( 'Clear Cache', 'simply-cdn-helper' ); ?></span>
					</p>
				</div>
				<div>
				</div>
			</div>
		</div>
		<?php
	}
}
