<?php
/*
Plugin Name:  Custom Login Styler
Plugin URI:   https://temparcweb.com
Description:  Simple customization or custom css of the wp-login form through the admin area
Version:      1.0.0
Author:       Temparc Web
Author URI:   https://temparcweb.com
License:      GPL2
Text Domain:  temparc
*/

if ( ! class_exists( 'Tmprc_Custom_Login_Styler' ) ) {

    class Tmprc_Custom_Login_Styler {

        // slug for options page & settings link
        public $plugin_slug = 'custom-login-styler';

        /**
         * Init plugin
         */
        public function __construct() {

            add_action( 'init',  array( $this, 'init' ) );

        }

        /**
         * Initialization functions
         */
        public function init() {

            $plugin = plugin_basename( __FILE__ );

            // hide menu page for non-admins
            if ( is_admin() && current_user_can( 'activate_plugins' ) ) {

                add_action( 'admin_init', array( $this, 'register_option_settings' ) );

                add_action( 'admin_menu', array( $this, 'login_menu_page' ) );

                add_filter( "plugin_action_links_$plugin", array( $this, 'tmprc_login_add_settings_link' ) );

            }

            // login image link url
            add_filter( 'login_headerurl', array( $this, 'login_logo_url' ) );

            //load styles if user is on login page
            if ( $this->wp_is_login() ) {

                add_action( 'login_enqueue_scripts', array( $this, 'login_page_css_output' ) );

            }

            // js
            add_action( 'admin_enqueue_scripts', array( $this, 'custom_login_enqueue_scripts' ) );

        }


        /**
         * Register Option Settings
         */
        public function register_option_settings() {

            // option names - correspond with html name values
            $options = array(
                'tmprc_login_image_id',
                'tmprc_logo_link',
                'tmprc_color_primary',
                'tmprc_color_secondary',
                'tmprc_color_background',
                'tmprc_login_background_image_id',
                'tmprc_login_css'
            );

            // register options and settings via settings api
            foreach ( $options as $option ) {

                add_option( $option );

                register_setting( $this->plugin_slug, $option );

            }

        }


        /**
         * Set up for options page
         */
        public function login_menu_page() {

            add_options_page(
                'Custom Login Styler',
                'Custom Login',
                'upload_files',
                $this->plugin_slug,
                array( $this, 'add_login_menu_page_output' )
            );

        }


        /**
         * Options page html
         */
        public function add_login_menu_page_output() { ?>

            <div class="wrap temparc-custom-login">
                <h1>Custom Login Page Settings</h1>
                <span class="description">A simple and easy way to customize the default admin login page | by <a href="https://temparcweb.com/">Temparc Web</a> | <a href="">Plugin Details</a></span>
                <form method="post" action="options.php">
                    <?php
                    settings_fields( $this->plugin_slug );
                    do_settings_sections( $this->plugin_slug );
                    wp_enqueue_media();
                    ?>
            <table class="form-table">
                <input type="hidden" id="post_id" value="<?php echo get_option( 'media_selector_attachment_id' ); ?>">
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="image_attachment_id">Login Logo Image</label>
                                </th>
                                <td>
                                    <div class="img-wrap">
                                        <div class='image-preview-wrapper' id="login-preview">
                                            <?php if ( !empty( get_option( 'tmprc_login_image_id' ) ) ) { ?>
                                            <img class='image-preview' src='<?php echo wp_get_attachment_image_src( get_option( 'tmprc_login_image_id' ), 'thumbnail' )[0]; ?>' width='100' height='100' style='max-height: 100px; width: 100px;'>
                                            <?php } ?>
                                        </div>
                                        <input id="tmprc_login_button" type="button" class="button-primary tmprc-media-upload" value="<?php _e( 'Upload image' ); ?>" />
                                        <input type='hidden' class="image-id" name='tmprc_login_image_id' id='tmprc_login_image_id' value="<?php echo esc_attr( get_option( 'tmprc_login_image_id' ) ); ?>">
                                    </div>
                                    <br>
                                    <span class="description">The image that appears above the login form</span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="tmprc_logo_link">Login Link URL</label>
                                </th>
                                <td>
                                    <input type="text" id="tmprc_logo_link" name="tmprc_logo_link" value="<?php echo esc_attr( get_option( 'tmprc_logo_link' ) ); ?>">
                                    <br>
                                    <span class="description">The image's link destination. Defaults to your home page URL</span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="tmprc_color_primary">Primary Color</label>
                                </th>
                                <td>
                                    <input type="text" id="tmprc_color_primary" name="tmprc_color_primary" value="<?php echo esc_attr( get_option( 'tmprc_color_primary' ) ); ?>">
                                    <br>
                                    <span class="description">Color of the form borders and text</span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="tmprc_color_primary">Secondary Color</label>
                                </th>
                                <td>
                                    <input type="text" id="tmprc_color_secondary" name="tmprc_color_secondary" value="<?php echo esc_attr( get_option( 'tmprc_color_secondary' ) ); ?>">
                                    <br>
                                    <span class="description">Color of the form buttons</span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="tmprc_color_background">Background Color</label>
                                </th>
                                <td>
                                    <input type="text" id="tmprc_color_background" name="tmprc_color_background" value="<?php echo esc_attr( get_option( 'tmprc_color_background' ) ); ?>">
                                    <br>
                                    <span class="description">Color of the whole page background</span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="tmprc_login_background_image_id">Background Image</label>
                                </th>
                                <td>
                                    <div class="img-wrap">
                                        <div class='image-preview-wrapper' id="background-preview">

                                        <?php if ( !empty( get_option( 'tmprc_login_background_image_id' ) ) ) { ?>
                                            <img class='image-preview' src='<?php echo wp_get_attachment_image_src( get_option( 'tmprc_login_background_image_id' ), 'thumbnail' )[0]; ?>' width='100' height='100' style='max-height: 100px; width: 100px;'>
                                        <?php } ?>
                                        </div>

                                        <input type="button" class="button-primary tmprc-media-upload" id="tmprc_background_button" value="<?php _e( 'Upload image' ); ?>" />

                                        <input type='hidden' name='tmprc_login_background_image_id' id='tmprc_login_background_image_id' class="image-id" value="<?php echo esc_attr( get_option( 'tmprc_login_background_image_id' ) ); ?>">

                                    </div>
                                    <br>
                                    <span class="description">Background image for the login page. This will override any background color settings</span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="tmprc_login_css">Custom CSS</label>
                                </th>
                                <td>
                                    <textarea style="height:200px;width: 100%;overflow:auto;" name="tmprc_login_css" id="tmprc_login_css"><?php echo esc_attr( get_option( 'tmprc_login_css' ) ); ?></textarea>
                                    <br>
                                    <span class="description">Add any custom css here to override whatever you'd like</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="submit"><?php submit_button(); ?></p>
                </form>
            </div>

        <?php

        }


        /**
         * Add settings link to plugin.php
         * @param $links
         * @return mixed
         */
        public function tmprc_login_add_settings_link( $links ) {

            $settings_link = '<a href="options-general.php?page=' . $this->plugin_slug . '">' . __( 'Settings' ) . '</a>';

            array_push( $links, $settings_link );

            return $links;

        }


        /**
         * Set the logo link url
         * @return mixed|string|void
         */
        public function login_logo_url( $url ) {

            $new_url = get_option( 'tmprc_logo_link' );

            if ( $new_url ) {
                return wp_strip_all_tags( $new_url );
            }

            return home_url();

        }


        /**
         * Login CSS
         */
        public function login_page_css_output(){

            //register styles
            wp_enqueue_style(
                'custom-login-styler',
                plugins_url() . '/custom-login-styler/css/login.css'
            );

            $logo_img = wp_get_attachment_image_src( get_option( 'tmprc_login_image_id' ) )[0];

           // generate css output
           $css =  '.login #login_error, .login .message{
                border-color: '. get_option( 'tmprc_color_primary' ) .';
            }
            body.login{
                background-image: url(\''. wp_get_attachment_image_src( get_option( 'tmprc_login_background_image_id' ), 'full' )[0].'\');
                background-repeat: no-repeat;
                background-size: cover;
                background-position: center top;
                background-color: '. get_option( 'tmprc_color_background' ) .';
            }
            body.login div#login form#loginform {
                border: 1px solid '. get_option( 'tmprc_color_primary' ) . ';
            }
            body.login div#login form#loginform p, body.login div#login form#loginform p label {
                color: ' . get_option( 'tmprc_color_primary' ) . ';
            }
            body.login div#login form#loginform p.forgetmenot {
                margin-bottom: 15px;
            }
            body.login div#login form#loginform p.submit input#wp-submit {
                background-color: ' . get_option( 'tmprc_color_secondary' ) .';
                color: #ffffff;
                border: none;
                text-shadow: none;
                float: none;
                display: block;
                margin: 0 auto;
                width: 100%;
                margin-top: 20px;
            }';

            if ( ! empty( $logo_img ) ) {

                $css .= '#login h1 a, .login h1 a {
                        background-image: url( \''.$logo_img.'\' );
                        padding-bottom: 30px;
                    }';

            }

            $css .= wp_strip_all_tags( get_option( 'tmprc_login_css' ) );

            // add css to existing stylesheet
            wp_add_inline_style( 'custom-login-styler', wp_strip_all_tags( $css ) );

        }

        /**
         * Check if is login page
         * @return bool
         */
        public function wp_is_login() {

            return in_array( $GLOBALS[ 'pagenow' ], array( 'wp-login.php' , 'wp-register.php' ) );

        }


        /**
         * Scripts for media uploader
         */
        public function custom_login_enqueue_scripts() {

            // load scripts
            if ( 'settings_page_' . $this->plugin_slug == get_current_screen()->id ) {

            wp_enqueue_script( 'tmprc-custom-login-script', plugins_url() .'/custom-login-styler/js/custom-login-styler.js', array( 'jquery' ) );

            }

        }
    }

    new Tmprc_Custom_Login_Styler();

}