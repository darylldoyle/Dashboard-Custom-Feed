<?php
class DCF_SETTINGS
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'DCF Settings', 
            'manage_options', 
            'dfc-setting-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'dcf-option', array('feed_url' => 'http://enshrined.co.uk') );
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>Dashboard Custom Feed</h2>
            <div class="">
                <p>This plugin was created by <a href="http://enshrined.co.uk" target="_NEW">Daryll Doyle</a> Senior Developer at <a href="http://www.digitalwebmedia.co.uk" target="_NEW">Digital Web Media Limited</a></p>
            </div>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'dcf-settings' );   
                do_settings_sections( 'dfc-setting-admin' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'dcf-settings', // Option group
            'dcf-option', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Dashboard Custom Feed Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'dfc-setting-admin' // Page
        );  

        add_settings_field(
            'feed_url', 
            'Feed Base URL', 
            array( $this, 'feed_url_callback' ), 
            'dfc-setting-admin', 
            'setting_section_id'
        );      
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['total_items'] ) )
            $new_input['total_items'] = absint( $input['total_items'] );

        if( isset( $input['feed_url'] ) )
            $new_input['feed_url'] = sanitize_text_field( $input['feed_url'] );

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function feed_url_callback()
    {
        printf(
            '<input type="text" id="feed_url" name="dcf-option[feed_url]" value="%s" />',
            isset( $this->options['feed_url'] ) ? esc_attr( $this->options['feed_url']) : ''
        );
    }
}

if( is_admin() )
    $my_settings_page = new DCF_SETTINGS();