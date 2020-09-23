<?php
/**
 * RDCovid Transients.
 *
 * @since   1.5.0
 * @package RDCovid
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
    $value = get_transient('rdcovid_data');
    $provalue = get_transient('rdcovid_prov');
    $lastdate = get_transient('rdcovid_lastdate');
    $last_update = get_transient('rdcovid_last_update');

    if (!empty($value)) {
      return $value;

    } else {

      // API URL
      $apiUrl = 'https://data.covid19.go.id/public/api/update.json';
      $provUrl = 'https://data.covid19.go.id/public/api/prov.json';

      // Get New Covid Data Transient
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
          }
        }

      }

      // Get New Province Covid Data Transient
      if ( false === ( $provalue = get_transient( 'rdcovid_prov' ) ) )
      {
        // Use the data like you would have normally...
        $heads = array( 'headers' => array("Accept" => "application/json"));
        $response = wp_safe_remote_get($provUrl, $heads);
        $responseBody = wp_remote_retrieve_body( $response );
        $provresult = json_decode( $responseBody );

        if ( is_object( $provresult ) && ! is_wp_error( $provresult ) )
        {
          $prov_lastdate = $provresult->last_date;

          if ( $prov_lastdate !== $lastdate )
          {
            // Sets new transients
            set_transient( 'rdcovid_prov', $provresult, 60*60 );
            set_transient( 'rdcovid_lastdate', $prov_lastdate, 60*60 );
          }
        }
      }

      // Clear cache transient not in database
      wp_cache_flush();

    }

  }

}
