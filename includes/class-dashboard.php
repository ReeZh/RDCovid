<?php
/**
 * RDCovid Dashboard
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
    public function __construct($plugin)
    {
        $this->plugin = $plugin;
        $this->hooks();
    }

    public function hooks()
    {
        add_action('wp_dashboard_setup', array( $this, 'rdc_support_dashboard_widget' ));
    }

    public function rdc_support_dashboard_widget()
    {
        global $wp_meta_boxes;
        wp_add_dashboard_widget('rdc_support_widget', __('RDCovid Help', 'rdcovid'), array( $this, 'rdc_support_content_widget' ));
    }

    public function rdc_support_current_info()
    {
        $rdcovid_data = get_option('rdcovid_data', true);
        $rdcovid_last_update = get_option('rdcovid_last_update', true);

      // Total Kasus
        $totpositif = $rdcovid_data->update->total->jumlah_positif;
        $totdirawat = $rdcovid_data->update->total->jumlah_dirawat;
        $totsembuh = $rdcovid_data->update->total->jumlah_sembuh;
        $totmeninggal = $rdcovid_data->update->total->jumlah_meninggal;

      // Penambahan Kasus
        $pluspositif = $rdcovid_data->update->penambahan->jumlah_positif;
        $plusdirawat = $rdcovid_data->update->penambahan->jumlah_dirawat;
        $plussembuh = $rdcovid_data->update->penambahan->jumlah_sembuh;
        $plusmeninggal = $rdcovid_data->update->penambahan->jumlah_meninggal;

        echo '<p><strong>' . strtoupper(__('Latest Indonesian COVID-19 Statistics Data', 'rdcovid')) . '</strong></p>';
        echo '<p><strong>' . __('Total', 'rdcovid') . '</strong>:<br>';
        echo __('Positive', 'rdcovid') . ': ' . $totpositif . ', ' .
        __('Treated', 'rdcovid') . ': ' . absint($totdirawat) . ', ' .
        __('Recover', 'rdcovid') . ': ' . absint($totsembuh) . ', ' .
        __('Died', 'rdcovid') . ': ' . $totmeninggal . '<br>';

        echo '<strong>' . __('Increase', 'rdcovid') . '</strong>:<br>';

        echo __('Positive', 'rdcovid') . ': ';
        if ($pluspositif > 0) {
            echo '+' . $pluspositif . ', ';
        } elseif ($pluspositif < 0) {
            echo '(-' . $pluspositif . '), ';
        } else {
            echo $pluspositif . ', ';
        }

        echo __('Treated', 'rdcovid') . ': ';
        if ($plusdirawat > 0) {
            echo '+' . $plusdirawat . ', ';
        } elseif ($plusdirawat < 0) {
            echo '(-' . absint($plusdirawat) . '), ';
        } else {
            echo $plusdirawat . ', ';
        }

        echo __('Recover', 'rdcovid') . ': ';
        if ($plussembuh > 0) {
            echo '+' . $plussembuh . ', ';
        } elseif ($plussembuh < 0) {
            echo '(-' . absint($plussembuh) . '), ';
        } else {
            echo $plussembuh . ', ';
        }

        echo __('Died', 'rdcovid') . ': ';
        if ($plusmeninggal > 0) {
            echo '+' . $plusmeninggal;
        } else {
            echo $plusmeninggal;
        }
        echo '</p>';

        echo '<p><strong>' . __('Last Update', 'rdcovid') . '</strong> - ' .
        $rdcovid_last_update . '</p>';

      // Province
        if (false !== get_option('rdcovid_prov_current')) {
            echo '<p><strong>' . __('Current Province: ', 'rdcovid') . '</strong> ' . strtoupper(get_option('rdcovid_prov_current', true)) . '</p>';
        }

      // Cache Status
        if (true === wp_using_ext_object_cache()) {
            $msg = __('This site using External Cache', 'rdcovid');
        } else {
            $msg = __('This site using Internal Cache', 'rdcovid');
        }
        echo '<p><strong>' . __('Cache Status', 'rdcovid') . '</strong> - ' . $msg . '</p>';
    }

    public function rdc_support_content_widget()
    {
        $this->rdc_support_current_info();
        echo '<p>' . __('RDCovid displays the latest Indonesian COVID-19 statistics, there is still much to be developed.', 'rdcovid') . '</p>';
        echo '<p>' . __('created by ', 'rdcovid') . '<a href="https://reezhdesign.com" target="_blank" rel="noopener">ReeZh Design</a></p>';
    }
}
