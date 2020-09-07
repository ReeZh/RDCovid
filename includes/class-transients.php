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
    $apiUrl = 'https://data.covid19.go.id/public/api/update.json';

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

        if ( $created !== $this->last_update )
        {
          $this->last_update = $created;
          $this->cur_data = $result;
          set_transient( 'rdcovid_data', $result, 60*60 );
        }
      }
    }
  }

  public function delete()
  {
    // Delete Transient
    delete_transient( 'rdcovid_data' );
  }

}
