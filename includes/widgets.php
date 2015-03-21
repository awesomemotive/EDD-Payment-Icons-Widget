<?php
/**
 * Widgets
 *
 * @package     EDD\Widgets\PaymentIcons
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Payment icons widget
 *
 * @since       1.0.0
 */
class edd_payment_icons_widget extends WP_Widget {


    /**
     * Get things started
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function __construct() {
        parent::__construct( 'edd_payment_icons_widget', __( 'Payment Icons', 'edd-payment-icons-widget' ), array( 'description' => __( 'Display the EDD accepted payment icons', 'edd-payment-icons-widget' ) ) );
    }


    /**
     * Create the widget
     *
     * @access      public
     * @since       1.0.0
     * @param       array $args The widget arguements
     * @param       array $instance This widget instance
     * @return      void
     */
    public function widget( $args, $instance ) {
        $title              = apply_filters( 'widget_title', $instance['title'], $instance, $args['id'] );
        $payment_methods    = edd_get_option( 'accepted_cards', array() );
        $icon_width         = ( isset( $instance['icon_width'] ) ? $instance['icon_width'] . 'px' : '32px' );

        if( empty( $payment_methods ) ) {
            return;
        }

        echo $args['before_widget'];

        if( $title ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        do_action( 'edd_before_payment_icons_widget' );

        echo '<div class="edd-payment-icons-widget">';

        if( $instance['above_icons'] ) {
            echo '<div class="edd-payment-icons-widget-text">';
            echo html_entity_decode( esc_html( $instance['above_icons'] ) );
            echo '</div>';
        }

        foreach( $payment_methods as $key => $card ) {
            if( edd_string_is_image_url( $key ) ) {
                echo '<img class="payment-icon" src="' . esc_url( $key ) . '" style="width: ' . $icon_width . '" />';
            } else {
                $card = strtolower( str_replace( ' ', '', $card ) );

                if( has_filter( 'edd_accepted_payment_' . $card . '_image' ) ) {
                    $image = apply_filters( 'edd_accepted_payment_' . $card . '_image', '' );
                } else {
                    $image = edd_locate_template( 'images' . DIRECTORY_SEPARATOR . 'icons' . DIRECTORY_SEPARATOR . $card . '.gif', false );
                    $content_dir = WP_CONTENT_DIR;

                    if( function_exists( 'wp_normalize_path' ) ) {
                        $image = wp_normalize_path( $image );
                        $content_dir = wp_normalize_path( $content_dir );
                    }

                    $image = str_replace( $content_dir, WP_CONTENT_URL, $image );
                }

                if( edd_is_ssl_enforced() || is_ssl() ) {
                    $image = edd_enforced_ssl_asset_filter( $image );
                }
                
                echo '<img class="payment-icon" src="' . esc_url( $image ) . '" style="width: ' . $icon_width . '" />';
            }
        }

        if( $instance['below_icons'] ) {
            echo '<div class="edd-payment-icons-widget-text">';
            echo html_entity_decode( $instance['below_icons'] );
            echo '</div>';
        }

        echo '</div>';

        do_action( 'edd_after_payment_icons_widget' );

        echo $args['after_widget'];
    }


    /**
     * Widget update routine
     *
     * @access      public
     * @since       1.0.0
     * @param       array $new_instance The new widget instance
     * @param       array $old_instance The old widget instance
     * @return      array $instance The updated instance
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance['title']      = strip_tags( $new_instance['title'] );
        $instance['above_icons']= esc_html( $new_instance['above_icons'] );
        $instance['below_icons']= esc_html( $new_instance['below_icons'] );
        $instance['icon_width'] = esc_attr( absint( $new_instance['icon_width'] ) );

        return $instance;
    }


    /**
     * The widget dashboard form
     *
     * @access      public
     * @since       1.0.0
     * @param       array $instance A given widget instance
     * @return      void
     */
    public function form( $instance ) {
        $defaults = array(
            'title'         => '',
            'above_icons'   => '',
            'below_icons'   => '',
            'icon_width'    => 32
        );

        $instance = wp_parse_args( (array) $instance, $defaults );
        ?>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'edd-payment-icons-widget' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo $instance['title']; ?>" />
        </p>
        
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'above_icons' ) ); ?>"><?php _e( 'Above Icons:', 'edd-payment-icons-widget' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'above_icons' ) ); ?>" name="<?php echo esc_html( $this->get_field_name( 'above_icons' ) ); ?>" type="text" value="<?php echo $instance['above_icons']; ?>" />
        </p>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'below_icons' ) ); ?>"><?php _e( 'Below Icons:', 'edd-payment-icons-widget' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'below_icons' ) ); ?>" name="<?php echo esc_html( $this->get_field_name( 'below_icons' ) ); ?>" type="text" value="<?php echo $instance['below_icons']; ?>" />
        </p>
        
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'icon_width' ) ); ?>"><?php _e( 'Icon Width (in pixels):', 'edd-payment-icons-widget' ); ?></label>
            <input class="small-text" id="<?php echo esc_attr( $this->get_field_id( 'icon_width' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'icon_width' ) ); ?>" type="number" min="0" step="1" value="<?php echo $instance['icon_width']; ?>" />
        </p>
        <?php
    }
}


/**
 * Register widget
 *
 * @since       1.0.0
 * @return      void
 */
function edd_register_payment_icons_widget() {
    register_widget( 'edd_payment_icons_widget' );
}
add_action( 'widgets_init', 'edd_register_payment_icons_widget' );
