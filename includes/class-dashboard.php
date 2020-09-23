<?php
/**
 * RDCovid Dashboard.
 *
 * @since   1.5.0
 * @package RDCovid
 */

class RDC_Dashboard
{
  /**
	 * Parent plugin class.
	 *
	 * @var RDCovid
	 * @since  1.0.0
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since  1.0.0
	 *
	 * @param  RDCovid $plugin Main plugin object.
	 */
	public function __construct( $plugin )
  {
		$this->plugin = $plugin;
		$this->hooks();
  }

  public function hooks()
  {
    add_action( 'wp_dashboard_setup', array( $this, 'rdc_support_dashboard_widget' ) );
  }

  public function rdc_support_dashboard_widget() {
    global $wp_meta_boxes;
    wp_add_dashboard_widget('rdc_support_widget', __('RDCovid Support','rdcovid'), array( $this, 'rdc_support_content_widget' ) );
  }

  public function rdc_support_content_widget() {
    echo '<p>This plugin support for COVID-19 Indonesia Update, more to come.</p>';
		echo '<p>created by <a href="https://reezhdesign.com" target="_blank" rel="noopener">ReeZh Design</a></p>';
  }

}
