<?php
/**
 * RDCovid Widget
 *
 * @since   1.5.2
 * @package RDCovid
 */

class RDC_Widget extends WP_Widget
{

  /**
   * Parent plugin class.
   *
   * @var RDCovid
   * @since  1.0.0
   */
  protected $created = null;
  
  /* Global protected value */
  protected $current_data = null;
  protected $last_update = null;
  protected $tanggalbaru = null;
  
  protected $pluspositif = null;
  protected $plusdirawat = null;
  protected $plussembuh = null;
  protected $plusmeninggal = null;
  protected $totpositif = null;
  protected $totdirawat = null;
  protected $totsembuh = null;
  protected $totmeninggal = null;
  
  /* Province protected value */ 
  protected $province_data = null;
  protected $last_date = null;
  protected $current_province = null;
  
  protected $province_penambahan_positif = null;
  protected $province_penambahan_sembuh = null;
  protected $province_penambahan_meninggal = null;
  protected $province_jumlah_kasus = null;
  protected $province_jumlah_sembuh = null;
  protected $province_jumlah_meninggal = null;
  protected $province_jumlah_dirawat = null;
  

  public function __construct()
  {
    parent::__construct(
      'rdc-widget',  // Base ID
      'RDCovid Widget'   // Name
    );
    add_action( 'widgets_init', function() {
      register_widget( 'RDC_Widget' );
    });
  }

  public $args = array(
    'before_title'  => '',
    'after_title'   => '',
    'before_widget' => '',
    'after_widget'  => ''
  );

  public function setdata( $result )
  {
    if ( is_object( $result ) )
    {
      $this->tanggalbaru = date_i18n('l, j F Y G:i:s', strtotime(str_replace('/', '-', $result->update->penambahan->created )));

      // Penambahan Kasus
      $this->pluspositif = $result->update->penambahan->jumlah_positif;
      $this->plusdirawat = $result->update->penambahan->jumlah_dirawat;
      $this->plussembuh = $result->update->penambahan->jumlah_sembuh;
      $this->plusmeninggal = $result->update->penambahan->jumlah_meninggal;

      // Total Kasus
      $this->totpositif = $result->update->total->jumlah_positif;
      $this->totdirawat = $result->update->total->jumlah_dirawat;
      $this->totsembuh = $result->update->total->jumlah_sembuh;
      $this->totmeninggal = $result->update->total->jumlah_meninggal;      
    } else
    {
      $this->tanggalbaru = date_i18n('l, j F Y G:i:s', strtotime(str_replace('/', '-', $result['update']['penambahan']['created'] )));

      // Penambahan Kasus
      $this->pluspositif = $result['update']['penambahan']['jumlah_positif'];
      $this->plusdirawat = $result['update']['penambahan']['jumlah_dirawat'];
      $this->plussembuh = $result['update']['penambahan']['jumlah_sembuh'];
      $this->plusmeninggal = $result['update']['penambahan']['jumlah_meninggal'];

      // Total Kasus
      $this->totpositif = $result['update']['total']['jumlah_positif'];
      $this->totdirawat = $result['update']['total']['jumlah_dirawat'];
      $this->totsembuh = $result['update']['total']['jumlah_sembuh'];
      $this->totmeninggal = $result['update']['total']['jumlah_meninggal'];      
    }
    return $result;
  }

  public function getdata()
  {
    if ( false === ( $value = get_option( 'rdcovid_data' ) ) ) :
      call_user_func( array( 'RDC_Transients', 'sync') );
    endif;
    $result = get_option( 'rdcovid_data', true );
    $this->current_data = $result;
    $this->setdata( $result );
  }
  
  public function set_province_data ( $result )
  {
    if ( is_object( $result ) )
    {
      $this->last_date = date_i18n( 'l, j F Y G:i:s', strtotime( str_replace( '/', '-', $result->last_date ) ) );

      // Penambahan Kasus
      $this->province_data = $result->list_data;
    } else
    {
      $this->last_date = date_i18n( 'l, j F Y G:i:s', strtotime( str_replace( '/', '-', $result['last_date'] ) ) );

      // Penambahan Kasus
      $this->province_data = $result['list_data'];
    }
  }
  
  public function get_province_data()
  {
    if ( false === ( $value = get_option( 'rdcovid_prov' ) ) ) :
      call_user_func( array( 'RDC_Transients', 'sync') );
    endif;
    $result = get_option( 'rdcovid_prov', true );
    $this->province_data = $result;
    return $result;
  }

  public function is_province( $instance )
  {
    if ( $instance['location'] !== '' )
    {
      $match = false;
      $provs = $this->province_data;
      foreach ($provs as $key -> $value) {
        if ( strtolower( $instance['location'] ) == strtolower( $prov->key ) )
        {
          $match = true;
        }
      }
      
      if ($match) {
        return true;
      } else {
        return false;
      }
      
    } else {
      return false;
    }
    
  }

  public function drawdata()
  {
    $this->getdata();
    echo '<div class="rdcovid_pembaruan">' . __('Pembaruan Terakhir','rdcovid') . '<span class="date">' . $this->tanggalbaru . '</span></div>';
    echo '<div class="rdcovid_positif">'. __('POSITIF','rdcovid');
    echo '<span class="number">';
    if ( $this->pluspositif > 0) {
        echo '+';
    }
    echo $this->pluspositif . '</span>';
    echo '<span class="small">' . $this->totpositif . '</span>';
    echo '</div>';
    echo '<div class="rdcovid_dirawat">' . __('DIRAWAT','rdcovid');
    echo '<span class="number">' . $this->totdirawat . '</span>';
    echo '<span class="small">';
    if ( $this->plusdirawat > 0) {
        echo '+';
    }
    echo $this->plusdirawat . '</span>';
    echo '</div>';
    echo '<div class="rdcovid_sembuh">' . __('SEMBUH','rdcovid');
    echo '<span class="number">' . $this->totsembuh . '</span>';
    echo '<span class="small">';
    if ( $this->plussembuh > 0) {
        echo '+';
    }
    echo $this->plussembuh . '</span>';
    echo '</div>';
    echo '<div class="rdcovid_meninggal">' . __('MENINGGAL','rdcovid');
    echo '<span class="number">' . $this->totmeninggal . '</span>';
    echo '<span class="small">';
    if ( $this->plusmeninggal > 0) {
        echo '+';
    }
    echo $this->plusmeninggal . '</span>';
    echo '</div>';
  }

  public function draw_province_data( $instance )
  {
    if ( ! empty( $instance['location'] ) ) {
      $this->current_province = $instance['location'];
    }
    
    $prov_data = get_option( 'rdcovid_prov', true );
    
    $provs = $prov_data->list_data;
    
    foreach ( $provs as $prov )
    {
      if ( strtolower( $instance['location'] ) == strtolower( $prov->key ) )
      {
        $this->last_date = date_i18n('l, j F Y', strtotime(str_replace('/', '-', $this->last_date )));
        echo '<div class="rdcovid_pembaruan"><span>' . __('Pembaruan Terakhir','rdcovid') . '</span><span class="date">' . $this->last_date . '</span></div>';
        echo '<div class="data_wrapper">';
        echo '<div class="rdcovid_positif">'. __('POSITIF','rdcovid');
        echo '<span class="number">';
        if ( $prov->penambahan->positif > 0) {
          echo '+';
        }
        echo esc_html( $prov->penambahan->positif ) . '</span>';
        echo '<span class="small">' . $prov->jumlah_kasus . '</span>';
        echo '</div>';
        echo '<div class="rdcovid_dirawat">' . __('DIRAWAT','rdcovid');
        echo '<span class="number">' . $prov->jumlah_dirawat . '</span>';
        echo '</div>';
        echo '<div class="rdcovid_sembuh">' . __('SEMBUH','rdcovid');
        echo '<span class="number">';
        if ( $prov->penambahan->sembuh > 0) {
          echo '+';
        }
        echo $prov->penambahan->sembuh . '</span>';
        echo '<span class="small">' . $prov->jumlah_sembuh . '</span>';
        echo '</div>';
        echo '<div class="rdcovid_meninggal">' . __('MENINGGAL','rdcovid');
        echo '<span class="number">';
        if ( $prov->penambahan->meninggal > 0) {
          echo '+';
        }
        echo $prov->penambahan->meninggal . '</span>';
        echo '<span class="small">' . $prov->jumlah_meninggal . '</span>';
        echo '</div>';
        echo '</div>';

        if ( 'on' == $instance['details'] )
        {
          echo '<div class="data_wrapper">';
          /* Jenis Kelamin */
          echo '<div class="rdcovid_jeniskelamin w-50">';
          _e('Jenis Kelamin','rdcovid');
          $arr_jeniskelamin = $prov->jenis_kelamin;
          foreach( $arr_jeniskelamin as $key => $value)
          {
            echo '<span class="small">'
            . $value->key . ' : ' . $value->doc_count . '</span>';
          }
          echo '</div>';

          /* Kelompok Umur */
          echo '<div class="rdcovid_kelompok_umur w-50">';
          _e('Kelompok Umur','rdcovid');
          $arr_kelompok_umur = $prov->kelompok_umur;
          foreach( $arr_kelompok_umur as $key => $value)
          {
            echo '<span class="small">' . $value->key . ' tahun: ' .
            $value->doc_count . '</span>';
          }
          echo '</div>';
          echo '</div>';
        }
      }
    }
  }

  public function draw_head()
  {
    echo '<div class="rdcovid_container">';
  }

  public function draw_footer()
  {
    echo '<div class="switch">';
    echo '<label><span class="rdlabeltext" aria-label="Pilih Mode">' . __('mode gelap / terang','rdcovid') . '</span><input type="checkbox" id="rdcSwitch"></label>';
    echo '<span class="toggler round" onclick="rdc_checkDarkmode()"></span>';
    echo '</div>';
    echo '<div>';
    echo '<span class="small">RDCovid WordPress Widget Plugin</span><span class="small">sumber data: https://covid19.go.id</span>';
    echo '</div>';
    echo '</div>';
  }

  public function tabbed_data( $instance )
  {
    echo '<ul class="tabs">';
    echo '<li class="tab-link current" data-tab="rdcovid-global" onclick="rdc_openTab(event,\'rdcovid-global\');">';
    echo esc_attr( strtoupper( __('Indonesia','rdcovid') ) );
    echo '</li>';
    echo '<li class="tab-link" data-tab="rdcovid-province" onclick="rdc_openTab(event,\'rdcovid-province\');">';
    echo esc_attr( strtoupper( ($instance['location']) ) );
    echo '</li>';
    echo '</ul>';
  }

  public function tabbed_global_data()
  {
    $this->getdata();
    echo '<div class="rdcovid_pembaruan">' . __('Pembaruan Terakhir','rdcovid') .'<span class="date">' . $this->tanggalbaru . '</span></div>';
    echo '<div class="data_wrapper">';
    echo '<div class="rdcovid_positif">'. __('POSITIF','rdcovid');
    echo '<span class="number">';
    if ( $this->pluspositif > 0) {
        echo '+';
    }
    echo $this->pluspositif . '</span>';
    echo '<span class="small">' . $this->totpositif . '</span>';
    echo '</div>';
    echo '<div class="rdcovid_dirawat">' . __('DIRAWAT','rdcovid');
    echo '<span class="number">';
    if ( $this->plusdirawat > 0) {
        echo '+';
    }
    echo $this->plusdirawat . '</span>';
    echo '<span class="small">' . $this->totdirawat . '</span>';
    echo '</div>';
    echo '<div class="rdcovid_sembuh">' . __('SEMBUH','rdcovid');
    echo '<span class="number">';
    if ( $this->plussembuh > 0) {
        echo '+';
    }
    echo $this->plussembuh . '</span>';
    echo '<span class="small">' . $this->totsembuh . '</span>';
    echo '</div>';
    echo '<div class="rdcovid_meninggal">' . __('MENINGGAL','rdcovid');
    echo '<span class="number">';
    if ( $this->plusmeninggal > 0) {
        echo '+';
    }
    echo $this->plusmeninggal . '</span>';
    echo '<span class="small">' . $this->totmeninggal . '</span>';
    echo '</div>';
    echo '</div>';
  }

  /* Draw Province Data */
  public function tabbed_province_data( $instance )
  {
    $this->draw_province_data( $instance );
  }

  public function tabbed_content( $instance )
  {
    echo '<div id="rdcovid-global" class="tab-content current">';
    $this->tabbed_global_data();
    echo '</div>';
    echo '<div id="rdcovid-province" class="tab-content">';
    $this->tabbed_province_data( $instance );
    echo '</div>';
  }

  public function widget( $args, $instance )
  {
    $details = false;
    extract($args);
    $details = $instance[ 'details' ] ? 'true' : 'false';

    echo $args['before_widget'];

    if ( ! empty( $instance['title'] ) ) :
      echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
    endif;

    echo '<div id="rdcovid" class="textwidget">';

    $this->draw_head();

    /* check for location */
    if ( strtolower($instance['location']) == 'indonesia' )
    {
      $this->drawdata();
    } else {
      $this->tabbed_data( $instance );
      $this->tabbed_content( $instance );
    }

    $this->draw_footer();

    echo esc_html__( $instance['text'], 'rdcovid' );
    echo '</div>';
    
    echo $args['after_widget'];
  }

  public function form( $instance )
  {
    $title = !empty( $instance['title'] ) ? $instance['title'] : __( 'RDCovid', 'rdcovid' );
    $text = !empty( $instance['text'] ) ? $instance['text'] : '';
    $location = !empty( $instance['location'] ) ? $instance['location'] : __( 'Indonesia', 'rdcovid' );
    $details = !empty( $instance['details'] ) ? $instance['details'] : false;
?>
<p>
  <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php echo esc_html__( 'Title:', 'rdcovid' ); ?></label>
  <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
</p>
<p>
  <label for="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>"><?php echo esc_html__( 'Text:', 'rdcovid' ); ?></label>
  <textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>" type="text" cols="30" rows="10"><?php echo esc_attr( $text ); ?>
  </textarea>
</p>
<p>
  <label for="<?php echo $this->get_field_id('location'); ?>"><?php _e('Location:','rdcovid'); ?>
    <select class="widefat" id="<?php echo $this->get_field_id('location'); ?>" name="<?php echo $this->get_field_name('location'); ?>" type="text">
      <option value='indonesia' <?php echo ( strtolower($instance['location']) == strtolower( 'Indonesia' ) ) ? ' selected' : ''; ?>><?php echo strtoupper( __('Indonesia','rdcovid') ); ?></option>
      <?php
      $provs = get_option('rdcovid_prov', true);
      $region = $provs->list_data;
      foreach( $region as $key => $value )
      {
        $strprov = $value->key;
      ?>
      <option value='<?php echo strtolower( $strprov ); ?>' <?php echo ( strtolower($instance['location']) == strtolower($strprov) ) ? 'selected' : ''; ?>><?php echo esc_attr( ucfirst( $strprov ) ); ?>
      </option>
      <?php	} ?>
    </select>
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id( 'details' ); ?>"><?php _e('Show Details?','rdcovid'); ?></label>
  <input class="checkbox" type="checkbox" <?php checked( $instance[ 'details' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'details' ); ?>" name="<?php echo $this->get_field_name( 'details' ); ?>" />
</p>
<?php }

  public function update( $new_instance, $old_instance )
  {
    $instance = $old_instance;
    $instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    $instance['text'] = ( !empty( $new_instance['text'] ) ) ? $new_instance['text'] : '';
    $instance['location'] = ( !empty( $new_instance['location'] ) ) ? $new_instance['location'] : '';
    $instance[ 'details' ] = ( !empty( $new_instance['details'] ) ) ? $new_instance[ 'details' ] : 'true';
    
    if ( get_option( 'rdcovid_prov_current' ) !== false )
    {
      update_option( 'rdcovid_prov_current', $instance['location'] );
    } else 
    {
      add_option( 'rdcovid_prov_current', $instance['location'] );
    }
    
    return $instance;
  }

}

$rdcwidget = new RDC_Widget();