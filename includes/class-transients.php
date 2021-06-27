<?php
/**
 * RDCovid Transients.
 *
 * @since   1.5.0
 * @package RDCovid
 */

defined('ABSPATH') or die('Error Bro!');

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
    public function __construct($plugin)
    {
        $this->plugin = $plugin;
        $this->hooks();
    }

    public function hooks()
    {
        add_action('init', array( $this, 'sync' ), 0);
        add_action('rdcovid_scheduler', array( $this, 'sync' ), 10);
    }

    public function sync()
    {
      // API URL
        $apiUrl = 'https://data.covid19.go.id/public/api/update.json';

      // Get New Covid Data Transient
        if (false === ( $data_value = get_transient('rdcovid_data') )) {
          // Use the data like you would have normally...
            $heads = array( 'headers' => array("Accept" => "application/json"));
            $response = wp_safe_remote_get($apiUrl, $heads);
            $responseBody = wp_remote_retrieve_body($response);

            $data_result = json_decode($responseBody);
            $data_last_update = get_option('rdcovid_last_update', true);
      
            if (is_object($data_result) && ! is_wp_error($data_result)) {
                $data_created = $data_result->update->penambahan->created;
                if ($data_created !== $data_last_update) {
                  // Sets new transients
                    set_transient('rdcovid_data', $data_result, HOUR_IN_SECONDS);
                    set_transient('rdcovid_last_update', $data_created, HOUR_IN_SECONDS);

                  // Add or Update Options
                    if (get_option('rdcovid_data') !== false) {
                        update_option('rdcovid_data', $data_result);
                    } else {
                        add_option('rdcovid_data', $data_result);
                    }

                    if (get_option('rdcovid_last_update') !== false) {
                        update_option('rdcovid_last_update', $data_created);
                    } else {
                        add_option('rdcovid_last_update', $data_created);
                    }
                }
            }
        }

      // API URL
        $provUrl = 'https://data.covid19.go.id/public/api/prov.json';

      // Get New Province Covid Data Transient
        if (false === ( $prov_value = get_transient('rdcovid_prov') )) {
            $heads = array( 'headers' => array("Accept" => "application/json"));
            $response = wp_safe_remote_get($provUrl, $heads);
            $responseBody = wp_remote_retrieve_body($response);
      
            $prov_result = json_decode($responseBody);
            $prov_lastdate = get_option('rdcovid_lastdate', true);

            if (is_object($prov_result) && ! is_wp_error($prov_result)) {
                $prov_created = $prov_result->last_date;
                if ($prov_created !== $prov_lastdate) {
                    // Sets new transients
                    set_transient('rdcovid_prov', $prov_result, HOUR_IN_SECONDS);
                    set_transient('rdcovid_lastdate', $prov_created, HOUR_IN_SECONDS);
          
                    // Add or Update Province Data Options
                    if (get_option('rdcovid_prov') !== false) {
                        update_option('rdcovid_prov', $prov_result);
                    } else {
                        add_option('rdcovid_prov', $prov_result);
                    }
          
                  // Add or Update Province Last Date Options
                    if (get_option('rdcovid_lastdate') !== false) {
                          update_option('rdcovid_lastdate', $prov_created);
                    } else {
                        add_option('rdcovid_lastdate', $prov_created);
                    }
                }
            }
        }
      // Clear cache transient not in database
      // wp_cache_flush();
    }

    public function cleanup()
    {
      // Cleanup Transient
        delete_transient('rdcovid_data');
        delete_transient('rdcovid_last_update');
        delete_transient('rdcovid_prov');
        delete_transient('rdcovid_lastdate');
    
      // Cleanup Options
        delete_option('rdcovid_data');
        delete_option('rdcovid_last_update');
        delete_option('rdcovid_prov');
        delete_option('rdcovid_lastdate');
        wp_cache_flush();
    }
}
