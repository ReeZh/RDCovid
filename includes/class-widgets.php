<?php
/**
 * RDCovid Widgets.
 *
 * @since   1.0.0
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

	public function getdata()
	{
		if ( false === ( $result = get_transient( 'rdcovid_data' ) ) )
		{
			RDC_Transients::sync();
		}

		$result = get_transient('rdcovid_data');
		return $result;
	}

	public function drawdata()
	{
		$result = get_transient('rdcovid_data');
		$this->tanggalbaru = date_i18n('l, j F Y G:i:s', strtotime(str_replace('/', '-', $result->update->penambahan->created )));
		$this->totpositif = $result->update->total->jumlah_positif;
		$this->totdirawat = $result->update->total->jumlah_dirawat;
		$this->totsembuh = $result->update->total->jumlah_sembuh;
		$this->totmeninggal = $result->update->total->jumlah_meninggal;

		echo '<div class="rdcovid_container">';
		echo '
		<div class="rdcovid_pembaruan">Pembaruan Terakhir <span class="date">' . $this->tanggalbaru . '</span></div>
		<div class="rdcovid_positif">Positif <span class="number">' . $this->totpositif . '</span></div>
		<div class="rdcovid_dirawat">Dirawat <span class="number">' . $this->totdirawat . '</span></div>
		<div class="rdcovid_sembuh">Sembuh <span class="number">' . $this->totsembuh . '</span></div>
		<div class="rdcovid_meninggal">Meninggal <span class="number">' . $this->totmeninggal . '</span></div>
		</div>';
	}

  public function widget( $args, $instance )
	{
		$apiUrl = 'https://data.covid19.go.id/public/api/update.json';
		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) :
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		endif;

		echo '<div id="rdcovid" class="textwidget">';

		if ( ! $this->cur_data ) :
			$result = $this->getdata();
			$rdcovid_data = 'rdcovid_data';
			$data_timeout = get_option('_transient_timeout_' . $rdcovid_data );
		else :

			if ( is_array( $result ) && ! is_wp_error( $result ) ) {
				// Work with the $result data
				echo 'Data Received';

			} elseif ( is_object( $result ) && ! is_wp_error( $result ) )	{

			} else {
				// Work with the error
				echo 'No Data Received.';
			}

		endif;

    echo esc_html__( $instance['text'], 'rdcovid' );
		$this->drawdata();
		echo '</div>';
    echo $args['after_widget'];
  }

  public function form( $instance )
	{
    $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( '', 'rdcovid' );
    $text = ! empty( $instance['text'] ) ? $instance['text'] : esc_html__( '', 'rdcovid' );
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
  <?php }

  public function update( $new_instance, $old_instance )
	{
	  $instance = array();
	  $instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
	  $instance['text'] = ( !empty( $new_instance['text'] ) ) ? $new_instance['text'] : '';
	  return $instance;
  }

}

$rdcwidget = new RDC_Widget();
