<?php
/**
 * Plugin Name: Dashboard Custom Feed
 * Plugin URI: http://enshrined.co.uk
 * Description: Retrieves a custom feed and displays it on the users dashboard
 * Version: 1.0
 * Author: Daryll Doyle
 * Author URI: http://enshrined.co.uk
 * License: GPL2
 */

class DCF {

    function init() {
        global $wp_meta_boxes;

        $result = $this->grabFeed();

        wp_add_dashboard_widget('DCF_widget', $result->title, array('DCF','do_the_news'));
        // Globalize the metaboxes array, this holds all the widgets for wp-admin
     
        global $wp_meta_boxes;
        
        // Get the regular dashboard widgets array 
        // (which has our new widget already but at the end)
        $normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
        
        // Backup and delete our new dashboard widget from the end of the array
        $example_widget_backup = array( 'DCF_widget' => $normal_dashboard['DCF_widget'] );
        unset( $normal_dashboard['DCF_widget'] );
     
        // Merge the two arrays together so our widget is at the beginning
        $sorted_dashboard = array_merge( $example_widget_backup, $normal_dashboard );
     
        // Save the sorted array back into the original metaboxes 
        $wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
    }

    private function grabFeed() {
        $dcf_options = get_option( 'dcf-option' );

        // create curl resource 
        $ch = curl_init(); 
        // set url 
        curl_setopt($ch, CURLOPT_URL, $dcf_options['feed_url'].'/dcf/feed/'); 
        //return the transfer as a string 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        // $output contains the output string 
        $output = curl_exec($ch); 
        // close curl resource to free up system resources 
        curl_close($ch);      
        return json_decode($output);

    }

    // Took out the $wp_rewrite->rules replacement so the rewrite rules filter could handle this.
    function do_the_news($rules) {

        $result = DCF::grabFeed();

        print " <style>
                    .EMP_content {
                        border-bottom: 1px solid #eee;
                        padding-bottom: 10px;
                        margin-bottom: 10px;
                    }
                    .EMP_content:last-child {
                        border-bottom: none;
                        margin-bottom: 0;
                    }
                    .EMP_title {
                        line-height: 1.2em;
                    }
                </style>";

        foreach ($result->items as $r) {
            $r->content = nl2br($r->content);
            $r->title = ucwords($r->title);
            print "<h1 class='EMP_title'>{$r->title}</h1>";
            print "<div class='EMP_content'>{$r->content}</div>";
        }
    }

}

include('Dashboard-Custom-Feed-Options.php');

$DCFCode = new DCF();

// Initialze All
add_action( 'wp_dashboard_setup', array($DCFCode, 'init'));
