<?php
/**
 * RDCovid Transients.
 *
 * @since   1.5.0
 * @package RDCovid
 */

 /**
  * RDCovid Transients class.
  *
  * @since 1.0.0
  */

defined( 'ABSPATH' ) or die( 'Error Bro!' );

class RDC_Transients
{
  /**
	 * Parent plugin class.
	 *
	 * @var RDCovid
	 * @since  1.0.0
	 */
  protected $plugin = null;
	protected $cur_data = null;
  protected $last_update = null;

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
    add_action( 'init', array( $this, 'sync' ) );
  }

  public function sync()
  {
    $value = get_transient( 'rdcovid_data' );
    $last_update = get_transient( 'rdcovid_last_update' );

    if (!empty($value)) {
      return $value;

    } else {
      $apiUrl = 'https://data.covid19.go.id/public/api/update.json';
      $provUrl = 'https://data.covid19.go.id/public/api/prov.json';

      // Get new Transient
      if ( false === ( $value = get_transient( 'rdcovid_data' ) ) )
      {
        // Use the data like you would have normally...
        $heads = array( 'headers' => array("Accept" => "application/json"));
        $response = wp_safe_remote_get($apiUrl, $heads);
        $responseBody = wp_remote_retrieve_body( $response );
        $result = json_decode( $responseBody );

        if ( is_object( $result ) && ! is_wp_error( $result ) )
        {
          $created = $result->update->penambahan->created;

          if ( $created !== $last_update )
          {
            // Sets new transients
            set_transient( 'rdcovid_data', $result, 60*60 );
            set_transient( 'rdcovid_last_update', $created, 60*60 );

            // Clear cache transient not in database
            wp_cache_flush();

          }
        }

      }

    }

  }

  // Purge all the transients associated with our plugin.
  public function purge() {

    global $wpdb;

    $prefix = esc_sql( $this -> get_transient_prefix() );

    $options = $wpdb -> options;

    $t  = esc_sql( "_transient_timeout_$prefix%" );

    $sql = $wpdb -> prepare (
      "
        SELECT option_name
        FROM $options
        WHERE option_name LIKE '%s'
      ",
      $t
    );

    $transients = $wpdb -> get_col( $sql );

    // For each transient...
    foreach( $transients as $transient ) {

      // Strip away the WordPress prefix in order to arrive at the transient key.
      $key = str_replace( '_transient_timeout_', '', $transient );

      // Now that we have the key, use WordPress core to the delete the transient.
      delete_transient( $key );

    }

    // But guess what?  Sometimes transients are not in the DB, so we have to do this too:
    wp_cache_flush();

  }

  public function delete()
  {
    // Delete Transient
    delete_transient( 'rdcovid_data' );
  }

}
