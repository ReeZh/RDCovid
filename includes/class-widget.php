<?php
/**
 * RDCovid Widget
 *
 * @since   1.5.2
 * @package RDCovid
 */

class RDC_Widget extends WP_Widget {

	/**
	 * Parent plugin class.
	 *
	 * @var RDCovid
	 * @since  1.0.0
	 */
	protected $created = null;
	protected $tanggalbaru = null;
	protected $pluspositif = null;
	protected $plusdirawat = null;
	protected $plussembuh = null;
	protected $plusmeninggal = null;
	protected $totpositif = null;
	protected $totdirawat = null;
	protected $totsembuh = null;
	protected $totmeninggal = null;
	protected $cur_data = null;
  protected $last_update = null;

  public function __construct() {
		parent::__construct(
			'rdc-widget',  // Base ID
			'RDCovid Widget'   // Name
		);
		add_action( 'widgets_init', function() {
			register_widget( 'RDC_Widget' );
		});

  }

	public $args = array(
      'before_title'  => '<h4 class="widgettitle">',
      'after_title'   => '</h4>',
      'before_widget' => '<div class="widget">',
      'after_widget'  => '</div>'
  );

	public function setdata( $result )
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
	}

	public function getdata()
	{
		if ( false === ( $result = get_transient( 'rdcovid_data' ) ) )
		{
			RDC_Transients::sync();
		}
		$result = get_transient('rdcovid_data');
		$this->setdata( $result );
		return $result;
	}

	public function drawdata()
	{
		$this->getdata();

		echo '<div class="rdcovid_container">';
		echo '
		<div class="rdcovid_pembaruan">' . __('Pembaruan Terakhir','rdcovid') .'<span class="date">' . $this->tanggalbaru . '</span></div>';
		echo '<div class="rdcovid_positif">'. __('POSITIF','rdcovid');
		echo '<span class="number">' . $this->totpositif . '</span>';
		echo '<span class="small">';
		if ( $this->pluspositif > 0) {
			echo '+';
		}
		echo $this->pluspositif . '</span>';
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
		echo '
		<div class="d-block w-100">
			<label class="switch"><input type="checkbox" id="rdcSwitch"><span class="toggler round"></span></label>
		</div>';
		echo '
		<div class="d-block w-100">
			<span class="small">RDCovid WordPress Widget Plugin</span>
			<span class="small">sumber data: https://covid19.go.id</span>
		</div>';
		echo '</div>';
	}

  public function widget( $args, $instance )
	{
		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) :
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		endif;

		echo '<div id="rdcovid" class="textwidget">';

		$this->drawdata();

    echo esc_html__( $instance['text'], 'rdcovid' );
		echo '</div>';
    echo $args['after_widget'];
  }

  public function form( $instance )
	{
    $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'RDCovid', 'rdcovid' );
    $text = ! empty( $instance['text'] ) ? $instance['text'] : esc_html__( '', 'rdcovid' );
		$location = ! empty( $instance['location'] ) ? $instance['location'] : esc_html__( 'Indonesia', 'rdcovid' );
    ?>
    <p>
	    <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php echo esc_html__( 'Title:', 'rdcovid' ); ?></label>
      <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
    </p>
    <p>
      <label for="<?php echo esc_attr( $this->get_field_id( 'Text' ) ); ?>"><?php echo esc_html__( 'Text:', 'rdcovid' ); ?></label>
      <textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>" type="text" cols="30" rows="10"><?php echo esc_attr( $text ); ?>
			</textarea>
    </p>
		<p>
	    <label for="<?php echo esc_attr( $this->get_field_id( 'location' ) ); ?>"><?php echo esc_html__( 'Location:', 'rdcovid' ); ?></label>
      <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'location' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'location' ) ); ?>" type="text" value="<?php echo esc_attr( $location ); ?>">
    </p>
  <?php }

  public function update( $new_instance, $old_instance )
	{
	  $instance = array();
	  $instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
	  $instance['text'] = ( !empty( $new_instance['text'] ) ) ? $new_instance['text'] : '';
		$instance['location'] = ( !empty( $new_instance['location'] ) ) ? $new_instance['location'] : '';
	  return $instance;
  }

}

$rdcwidget = new RDC_Widget();
