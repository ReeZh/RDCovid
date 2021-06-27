<?php
/**
 * RDCovid Announcements.
 *
 * @since   1.5.3
 * @package RDCovid
 */

defined('ABSPATH') or die('Error Bro!');

class RDC_Announcements
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
        add_action('wp_head', array( $this, 'do_announce' ));
    }
  
    public function generate_info()
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

        echo strtoupper(__('Latest Indonesian COVID-19 Statistics Data', 'rdcovid'));
        echo ': ' . __('Positive', 'rdcovid') . ': ' . $totpositif . ', ' .
        __('Treated', 'rdcovid') . ': ' . absint($totdirawat) . ', ' .
        __('Recover', 'rdcovid') . ': ' . absint($totsembuh) . ', ' .
        __('Died', 'rdcovid') . ': ' . $totmeninggal . __(' with addition', 'rdcovid') . ' [';

        echo __('positive', 'rdcovid') . ' : ';
        if ($pluspositif > 0) {
            echo '+' . $pluspositif . ', ';
        } elseif ($pluspositif < 0) {
            echo '(-' . $pluspositif . '), ';
        } else {
            echo $pluspositif . ', ';
        }

        echo __('treated', 'rdcovid') . ' : ';
        if ($plusdirawat > 0) {
            echo '+' . $plusdirawat . ', ';
        } elseif ($plusdirawat < 0) {
            echo '(-' . absint($plusdirawat) . '), ';
        } else {
            echo $plusdirawat . ', ';
        }

        echo __('recover', 'rdcovid') . ' : ';
        if ($plussembuh > 0) {
            echo '+' . $plussembuh . ', ';
        } elseif ($plussembuh < 0) {
            echo '(-' . absint($plussembuh) . '), ';
        } else {
            echo $plussembuh . ', ';
        }

        echo __('died', 'rdcovid') . ' : ';
        if ($plusmeninggal > 0) {
            echo '+' . $plusmeninggal;
        } else {
            echo $plusmeninggal;
        }
        echo ']. ';

        echo 'Source: <a href=\"' . get_permalink() . '#rdcovid' . '\">RDCovid</a>';
    }

    public function date_posted()
    {
        $rdcovid_last_update = get_option('rdcovid_last_update', true);
        if ($rdcovid_last_update) {
            echo $rdcovid_last_update;
        }
    }

    public function date_expired()
    {
        $rdcovid_last_update = get_option('rdcovid_last_update', true);
        if ($rdcovid_last_update) {
            $date = date_create($rdcovid_last_update);
            date_add($date, date_interval_create_from_date_string("30 days"));
            echo date_format($date, "Y-m-d H:i:s");
        }
    }

    public function do_announce()
    {
        ?>
<script type="application/ld+json">
  [{
    "@context": "https://schema.org",
    "@type": "SpecialAnnouncement",
    "name": "<?php echo __('Latest Indonesian COVID-19 Statistics Data', 'rdcovid'); ?>",
    "text": "<?php $this->generate_info(); ?>",
    "datePosted": "<?php $this->date_posted(); ?>",
    "expires": "<?php $this->date_expired(); ?>",
    "diseaseSpreadStatistics": "https://covid19.go.id",
    "category": "https://www.wikidata.org/wiki/Q81068910",
    "spatialCoverage": [
      {
        "type": "AdministrativeArea",
        "name": "Indonesia, ID"
      }
    ]
  }
  ]
</script>
        <?php
    }
}
