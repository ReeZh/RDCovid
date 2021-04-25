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
  public function __construct( $plugin )
  {
    $this->plugin = $plugin;
    $this->hooks();
  }

  public function hooks()
  {
    add_action( 'wp_dashboard_setup', array( $this, 'rdc_support_dashboard_widget' ) );
  }

  public function rdc_support_dashboard_widget()
  {
    global $wp_meta_boxes;
    wp_add_dashboard_widget('rdc_support_widget', __('RDCovid Bantuan','rdcovid'), array( $this, 'rdc_support_content_widget' ) );
  }

  public function rdc_support_current_info()
  {
    $rdcovid_data = get_option( 'rdcovid_data', true );
    $rdcovid_last_update = get_option( 'rdcovid_last_update', true );

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

    echo '<p><strong>' . strtoupper( __('Data Statistik COVID-19 Indonesia Terbaru','rdcovid') ) . '</strong></p>';
    echo '<p><strong>' . __('Total','rdcovid') . '</strong>:<br>';
    echo __('Positif','rdcovid') . ': ' . $totpositif . ', ' .
    __('Dirawat','rdcovid') . ': ' . absint($totdirawat) . ', ' .
    __('Sembuh','rdcovid') . ': ' . absint($totsembuh) . ', ' .
    __('Meninggal','rdcovid') . ': ' . $totmeninggal . '<br>';

    echo '<strong>' . __('Penambahan','rdcovid') . '</strong>:<br>';

    echo __('Positif','rdcovid') . ': ';
    if ( $pluspositif > 0 ) {
      echo '+' . $pluspositif . ', ';
    } elseif ( $pluspositif < 0 ) {
      echo '(-' . $pluspositif . '), ';
    } else {
      echo $pluspositif . ', ';
    }

    echo __('Dirawat','rdcovid') . ': ';
    if ( $plusdirawat > 0 ) {
      echo '+' . $plusdirawat . ', ';
    } elseif ( $plusdirawat < 0 ) {
      echo '(-' . absint($plusdirawat) . '), ';
    } else {
      echo $plusdirawat . ', ';
    }

    echo __('Sembuh','rdcovid') . ': ';
    if ( $plussembuh > 0 ) {
      echo '+' . $plussembuh . ', ';
    } elseif ( $plussembuh < 0 ) {
      echo '(-' . absint($plussembuh) . '), ';
    } else {
      echo $plussembuh . ', ';
    }

    echo __('Meninggal','rdcovid') . ': ';
    if ( $plusmeninggal > 0 ) {
      echo '+' . $plusmeninggal;
    } else {
      echo $plusmeninggal;
    }
    echo '</p>';

    echo '<p><strong>' . __('Tanggal Update','rdcovid') . '</strong> - ' .
    $rdcovid_last_update . '</p>';

    // Province
    if ( false !== get_option( 'rdcovid_prov_current') )
    {
      echo '<p><strong>' . __('Current Province: ', 'rdcovid' ) . '</strong> ' . strtoupper( get_option( 'rdcovid_prov_current', true ) ) . '</p>';
    }

    // Cache Status
    if ( true === wp_using_ext_object_cache() ) {
      $msg = __('This site using External Cache','rdcovid');
    } else {
      $msg = __('This site using Internal Cache','rdcovid');
    }
    echo '<p><strong>' . __('Cache Status','rdcovid') . '</strong> - ' . $msg . '</p>';

  }

  public function rdc_support_content_widget()
  {
    $this->rdc_support_current_info();
    echo '<p>' . __('RDCovid menampilkan data statistik COVID-19 Indonesia terbaru, masih banyak yang akan dikembangkan.','rdcovid') . '</p>';
    echo '<p>dibuat oleh <a href="https://reezhdesign.com" target="_blank" rel="noopener">ReeZh Design</a></p>';
  }

}