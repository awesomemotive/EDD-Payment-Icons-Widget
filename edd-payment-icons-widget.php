<?php
/**
 * Plugin Name:     Easy Digital Downloads - Payment Icons Widget
 * Plugin URI:      wordpress.org/plugins/easy-digital-downloads-payment-icons-widget
 * Description:     Adds a widget to display the EDD accepted payment icons
 * Version:         1.0.0
 * Author:          Daniel J Griffiths
 * Author URI:      http://section214.com
 * Text Domain:     edd-payment-icons-widget
 *
 * @package         EDD\Widgets\PaymentIcons
 * @author          Daniel J Griffiths <dgriffiths@section214.com>
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


if( ! class_exists( 'EDD_Payment_Icons_Widget' ) ) {


    /**
     * Main EDD_Payment_Icons_Widget class
     *
     * @since       1.0.0
     */
    class EDD_Payment_Icons_Widget_Loader {


        /**
         * @var         EDD_Payment_Icons_Widget $instance The one true EDD_Payment_Icons_Widget
         * @since       1.0.0
         */
        private static $instance;


        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      self::$instance The one true EDD_Payment_Icons_Widget
         */
        public static function instance() {
            if( ! self::$instance ) {
                self::$instance = new EDD_Payment_Icons_Widget_Loader();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->load_textdomain();
                self::$instance->hooks();
            }

            return self::$instance;
        }


        /**
         * Setup plugin constants
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function setup_constants() {
            // Plugin version
            define( 'EDD_PAYMENT_ICONS_WIDGET_VER', '1.0.1' );
            
            // Plugin path
            define( 'EDD_PAYMENT_ICONS_WIDGET_DIR', plugin_dir_path( __FILE__ ) );

            // Plugin URL
            define( 'EDD_PAYMENT_ICONS_WIDGET_URL', plugin_dir_url( __FILE__ ) );
        }


        /**
         * Include necessary files
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function includes() {
            require_once EDD_PAYMENT_ICONS_WIDGET_DIR . 'includes/widgets.php';
        }


        /**
         * Run action and filter hooks
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function hooks() {
        }


        /**
         * Internationalization
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function load_textdomain() {
            // Set filter for language directory
            $lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
            $lang_dir = apply_filters( 'edd_payment_icons_widget_language_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), '' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'edd-payment-icons-widget', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/edd-payment-icons-widget/' . $mofile;

            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/edd-payment-icons-widget/ folder
                load_textdomain( 'edd-payment-icons-widget', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/edd-payment-icons-widget/ folder
                load_textdomain( 'edd-payment-icons-widget', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'edd-payment-icons-widget', false, $lang_dir );
            }
        }
    }
}


/**
 * The main function responsible for returning the one true EDD_Payment_Icons_Widget
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      EDD_Payment_Icons_Widget The one true EDD_Payment_Icons_Widget
 */
function edd_payment_icons_widget() {
    if( ! class_exists( 'Easy_Digital_Downloads' ) ) {
        if( ! class_exists( 'S214_EDD_Activation' ) ) {
            require_once 'includes/class.s214-edd-activation.php';
        }

        $activation = new S214_EDD_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
        $activation = $activation->run();
        
        return EDD_Payment_Icons_Widget_Loader::instance();
    } else {
        return EDD_Payment_Icons_Widget_Loader::instance();
    }
}
add_action( 'plugins_loaded', 'edd_payment_icons_widget' );
