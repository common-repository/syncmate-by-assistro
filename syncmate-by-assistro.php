<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://assistro.co/
 * @since      1.0.0
 *
 * @package    Syncmate_By_Assistro
 */

/*
Plugin Name: SyncMate by Assistro
Plugin URI: https://assistro.co/syncmate-x-wa-push-plus?utm_source=wordpress-plugin-repo&utm_medium=syncmate-by-assistro
Text Domain: syncmate-by-assistro
Description: Supercharge your website with our add-on and delight your customer with a feedback via Whatsapp message whenever they fill-up your form with Zero code.Delight your customers via Whatsapp notifications on every step of their buying journey.
Author: Assistro
Author URI: https://assistro.co?utm_source=wordpress-plugin-repo&utm_medium=syncmate-by-assistro
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Version: 1.2
Requires at least: 6.4
Requires PHP: 7.4
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


register_activation_hook(__FILE__, 'smassistro_activate_plugin');
register_deactivation_hook(__FILE__, 'smassistro_deactivate_plugin');
require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';




function smassistro_activate_plugin()
{
    // echo "active";
    // die;
    // global $wpdb;
    // $table_name = $wpdb->prefix . 'syncmate_form';

    // $sql = "CREATE TABLE $table_name (
    //     id INT NOT NULL AUTO_INCREMENT,
    //     form_id INT NOT NULL,
    //     api_key INT NOT NULL,
    //     PRIMARY KEY (id)
    // )";

    // $wpdb->query($sql);

    global $wpdb;
    $table_name = $wpdb->prefix . 'syncmate_form';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        post_id mediumint(9) NOT NULL,
        meta_key varchar(255) NOT NULL,
        meta_value longtext NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);


    // Create wp_syncmate_countries table if it doesn't exist
    // $table_countries = $wpdb->prefix . 'syncmate_countries';
    // if ($wpdb->get_var("SHOW TABLES LIKE '$table_countries'") != $table_countries) {
    //     // SQL statement for countries table creation
    //     $sql_countries = "CREATE TABLE $table_countries (
    //         id INT AUTO_INCREMENT PRIMARY KEY,
    //         country_code VARCHAR(10) NOT NULL,
    //         country_name VARCHAR(255) NOT NULL
    //     ) $charset_collate;";
    //     dbDelta($sql_countries);

    //     // Insert default data into wp_syncmate_countries table
    //     $default_countries = array(
    //         array('country_code' => '91', 'country_name' => 'India'),
    //         array('country_code' => '65', 'country_name' => 'Singapore'),
    //         // Add more countries as needed
    //     );

    //     foreach ($default_countries as $country) {
    //         $wpdb->insert(
    //             $table_countries,
    //             $country,
    //             array('%s', '%s')
    //         );
    //     }
    // }



}


add_action('admin_notices', 'smassistro_update_notice');

function smassistro_update_notice()
{
    $current_version = '1.2';
    $plugin_version = get_option('smassistro_plugin_version');

    if ($plugin_version !== $current_version) {
        echo '<div class="updated"><p>';
        printf(__('SyncMate by Assistro has been updated to version %s.', 'syncmate-assistro'), $current_version);
        echo '</p></div>';

        update_option('smassistro_plugin_version', $current_version);
    }
}




function smassistro_deactivate_plugin()
{

    // echo "deactive";
    // die;
    // global $wpdb;
    // $table_name = $wpdb->prefix . 'syncmate_form';

    // $sql = "DROP TABLE $table_name";

    // $wpdb->query($sql);
}

add_action('admin_menu', 'smassistro_plugin_menu');

function smassistro_plugin_menu()
{
    // add_menu_page('Assistro','Assistro',8,__FILE__,'wpsmassistro_cf7_plugin_list','dashicons-format-chat');
    // Create the top-level menu
    // Create the top-level menu
    add_menu_page('SyncMate', 'SyncMate', 'manage_options', 'assistro-top-level', 'smassistro_welcome_plugin_list', 'dashicons-format-chat', 6);

    add_submenu_page('assistro-top-level', 'Welcome', 'Welcome', 'manage_options', 'assistro-top-level', 'smassistro_welcome_plugin_list');
    $woocom_check_active = class_exists('WooCommerce');
    if ($woocom_check_active == true) {
        add_submenu_page('assistro-top-level', 'WooCommerce', 'WooCommerce', 'manage_options', 'assistro-woocom', 'smassistro_woocom_plugin_list');
    }

    $cf7_check_active = class_exists('WPCF7');
    if ($cf7_check_active == true) {
        add_submenu_page('assistro-top-level', 'Contact Form 7', 'Contact Form 7', 'manage_options', 'assistro-cf7', 'smassistro_cf7_plugin_list');
    }
    // Create a submenu under the top-level menu
    add_submenu_page('assistro-top-level', 'Help', 'Help', 'manage_options', 'assistro-help', 'smassistro_product_callback_function');
}

function smassistro_welcome_plugin_list()
{

    include('syncmate_welcome.php');
}
function smassistro_woocom_plugin_list()
{

    include('syncmate_woocom.php');
}
function smassistro_cf7_plugin_list()
{

    include('syncmate_cf7.php');
}

include('includes/wpcf7.php');
include('includes/woocommerce.php');

function smassistro_product_callback_function()
{
    // Code for the "Product" sub-menu page
    echo '<div class="wrap">';
    echo '<h2>Help</h2>';
    // Your content for the "Product" page goes here
    echo '</div>';
}






// Define a function to add a custom field to the notifications tab
function smassistro_wpforms_notifications_field($form_data)
{
    // Add your custom field HTML here
    $json_data = json_encode($form_data);
    // Decode the JSON data
    $data = json_decode($json_data, true);

?>
    <tr>
        <th><?php esc_html_e('Custom Field Label:', 'syncmate-by-assistro'); ?></th>
        <td>
            <input type="text" name="custom_field" value="<?php echo esc_attr(isset($data['form']['ID']) ? $data['form']['ID'] : ''); ?>" />
        </td>
    </tr>
    <form method="POST">
        <div class="row">
            <label for="wpcf7-option1-number-input">Please enter Number:</label>
            <input type="text" id="wpcf7-option1-number-input" class="large-text" name="wpcf7-option1-number-input" value="<?php echo esc_attr($api_number_text_input); ?>" />
        </div>
        <div class="row">
            <label for="wpcf7-message-input">Please enter Message:</label>
            <input type="text" id="wpcf7-message-input" class="large-text" name="wpcf7-message-input" value="<?php echo esc_attr($api_message_text_input); ?>" />
        </div>
    </form>
<?php
}

// Hook into the wpforms_form_settings_notifications filter to add the custom field
add_action('wpforms_form_settings_notifications', 'smassistro_wpforms_notifications_field', 10, 2);


// Hook into the 'save_post' action to capture form settings updates
// function smassistro_save_wpform_settings($post_id,$form_data) {
//     // Check if this is a WPForms form post type

//     // die;
//     if (get_post_type($post_id) === 'wpforms') {
//         // print_r("test");
//         // die;
//         // Check if your custom field data is available in the POST request
//         // if (isset($_POST['custom_field_data'])) {
//             // Sanitize and save your custom field data to postmeta
//             // $custom_field_data = sanitize_text_field("test data");
//             // smassistro_insert_or_update_data_into_custom_table($post_id, '_custom_field_data', $custom_field_data);
//         // }
//         if (isset($_POST['wpcf7-option1-number-input'])) {
//             $form_id = $post_id;
//             $wpcf7_number_text_input = sanitize_text_field($_POST['wpcf7-option1-number-input']);
//             smassistro_insert_or_update_data_into_custom_table($form_id, 'wpcf7_number_text_input', $wpcf7_number_text_input);
//         }
//         if (isset($_POST['wpcf7-message-input'])) {
//             $form_id = $post_id;
//             $wpcf7_message_text_input = sanitize_text_field($_POST['wpcf7-message-input']);
//             smassistro_insert_or_update_data_into_custom_table($form_id, 'wpcf7_message_text_input', $wpcf7_message_text_input);
//         }
//     }
// }

// add_action('save_post', 'smassistro_save_wpform_settings');
// Save custom text input data to the database
function smassistro_save_wpcf7_wpforms_text_input_data_to_db($contact_form)
{
    global $wpdb;

    // Check if nonce is set and verify it
    if (isset($_POST['nonce_field']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce_field'])), 'smassistro_save_wpcf7_data')) {
        // Process form data securely
        if (isset($_POST['wpcf7-key-textarea'])) {
            $form_id = $contact_form->id();
            $wpcf7_key_text_input = sanitize_text_field($_POST['wpcf7-key-textarea']);
            smassistro_insert_or_update_data_into_custom_table($form_id, 'wpcf7_key_text_input', $wpcf7_key_text_input);
        }
        if (isset($_POST['wpcf7-phoneNumber-input'])) {
            $form_id = $contact_form->id();
            $wpcf7_phoneNumber_text_input = sanitize_text_field($_POST['wpcf7-phoneNumber-input']);
            smassistro_insert_or_update_data_into_custom_table($form_id, 'wpcf7_phoneNumber_text_input', $wpcf7_phoneNumber_text_input);
        }
        if (isset($_POST['wpcf7-option1-number-input'])) {
            $form_id = $contact_form->id();
            $wpcf7_number_text_input = sanitize_text_field($_POST['wpcf7-option1-number-input']);
            smassistro_insert_or_update_data_into_custom_table($form_id, 'wpcf7_number_text_input', $wpcf7_number_text_input);
        }
        if (isset($_POST['wpcf7-message-input'])) {
            $form_id = $contact_form->id();
            $wpcf7_message_text_input = sanitize_text_field($_POST['wpcf7-message-input']);
            smassistro_insert_or_update_data_into_custom_table($form_id, 'wpcf7_message_text_input', $wpcf7_message_text_input);
        }
    }
}

add_action('wpform_save_contact_form', 'smassistro_save_wpcf7_wpforms_text_input_data_to_db');

// require_once('wp-content/plugins/waforms/waforms-functions.php');
function smassistro_wpforms_process_complete($form_data, $entry_id, $form_id)
{
    // Retrieve the submitted form data
    // print_r($form_id['id']);
    $form_id = $form_id['id'];
    // $api_key = smassistro_get_data_by_key_value($form_id, 'wpcf7_key_text_input');
    // $api_phoneNumber = smassistro_get_data_by_key_value($form_id, 'wpcf7_phoneNumber_text_input');
    // $api_number = smassistro_get_data_by_key_value($form_id, 'wpcf7_number_text_input');
    // $api_message = smassistro_get_data_by_key_value($form_id, 'wpcf7_message_text_input');
    $api_key = smassistro_get_data_by_key_value(1, 'assistro_pushplus_key_text_input');
    // $api_phoneNumber_push = smassistro_get_data_by_key_value(1, 'assistro_phoneNumber_push_text_input');
    // $api_key = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL2Rldi5hcHAuYXNzaXN0cm8uY28vd2EtcHVzaC1wbHVzL3NldHRpbmdzL2FwaS1nZW5lcmF0ZS8xIiwiaWF0IjoxNjkzNzI2ODg5LCJleHAiOjE2OTYzMTg4ODksIm5iZiI6MTY5MzcyNjg4OSwianRpIjoiNlNlczdLSTVyMktZMUt6UyIsInN1YiI6IjEiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3IiwidXNlcl9jb21wYW55X2lkIjoxOCwidXNlcl93ZWJob29rX2lkIjoxfQ.4LnaNEpzOKgkSPAftj4B4QP9Y0vxbYGmOYyoGUGuwMs';
    // $api_phoneNumber = '1';
    $api_number = smassistro_get_data_by_key_value(1, 'wpcf7_pushplus_phoneNumber_text_input');
    $api_message = smassistro_get_data_by_key_value(1, 'wpcf7_pushplus_message_text_input');
    // $api_number = '919928044051';
    // $api_message = 'hello how';

    if (!empty($api_key) && !empty($api_number) && !empty($api_message)) {
        // Define the API endpoint
        // $api_url = 'https://app.assistro.co/api/v1/wapushplus/single/message';
        $api_url = 'https://app.assistro.co/api/v1/wapushplus/single/message';

        // Define the data you want to send in the request body as an array
        $request_body = array(
            // "phoneNumber" => $api_phoneNumber,
            "number" => $api_number,
            "message" => $api_message,
        );

        // Set up the request arguments, including the API key in the headers
        $args = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json', // Verify the content type
            ),
            'body' => json_encode($request_body), // Convert the data to JSON format if needed
        );

        // Make the API POST request
        $response = wp_safe_remote_post($api_url, $args);
        // print_r($response);
        // die;
        if (!is_wp_error($response)) {
            // Process the API response data
            $response_code = wp_remote_retrieve_response_code($response); // Get the HTTP response code
            $response_body = wp_remote_retrieve_body($response); // Get the response body
            $api_data = json_decode($response_body, true); // Parse JSON response data

            // You can now work with the API response data
            // Example: Log the response data
            error_log('API Response Code: ' . $response_code);
            error_log('API Response Body: ' . print_r($api_data, true));
        } else {
            // Handle API call error
            $error_message = $response->get_error_message();
            error_log('API Call Error: ' . $error_message);
        }
    }
}
add_action('wpforms_process_complete', 'smassistro_wpforms_process_complete', 10, 3);





// Function to display the "Hello, World!" message
function smassistro_send_whatsapp_shortcode($atts)
{
    $atts = shortcode_atts(
        array(
            'number' => 'default_value1',
            'message' => 'default_value2',
        ),
        $atts
    );
    // Retrieve the submitted form data
    // print_r($form_id['id']);

    // $api_key = smassistro_get_data_by_key_value($form_id, 'wpcf7_key_text_input');
    // $api_phoneNumber = smassistro_get_data_by_key_value($form_id, 'wpcf7_phoneNumber_text_input');
    // $api_number = smassistro_get_data_by_key_value($form_id, 'wpcf7_number_text_input');
    // $api_message = smassistro_get_data_by_key_value($form_id, 'wpcf7_message_text_input');
    $api_key = smassistro_get_data_by_key_value(1, 'assistro_pushplus_key_text_input');
    // $api_phoneNumber = '1';

    $api_number = $atts['number'];
    $api_message = $atts['message'];

    // return $api_message;

    if (!empty($api_key)  && !empty($api_number) && !empty($api_message)) {
        // Define the API endpoint
        // return "Test";
        $api_url = 'https://app.assistro.co/api/v1/wapushplus/single/message';

        // Define the data you want to send in the request body as an array
        $request_body = array(
            // "phoneNumber" => $api_phoneNumber,
            "number" => $api_number,
            "message" => $api_message,
        );

        // Set up the request arguments, including the API key in the headers
        $args = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json', // Verify the content type
            ),
            'body' => json_encode($request_body), // Convert the data to JSON format if needed
        );

        // Make the API POST request
        $response = wp_safe_remote_post($api_url, $args);
        // return $response;
        return json_encode($response);

        if (!is_wp_error($response)) {
            // Process the API response data
            $response_code = wp_remote_retrieve_response_code($response); // Get the HTTP response code
            $response_body = wp_remote_retrieve_body($response); // Get the response body
            $api_data = json_decode($response_body, true); // Parse JSON response data

            // You can now work with the API response data
            // Example: Log the response data
            error_log('API Response Code: ' . $response_code);
            error_log('API Response Body: ' . print_r($api_data, true));
        } else {
            // Handle API call error
            $error_message = $response->get_error_message();
            error_log('API Call Error: ' . $error_message);
        }
    }
}

// Register the shortcode
add_shortcode('wpcf7-send-message', 'smassistro_send_whatsapp_shortcode');


// Register the custom email action.
// function register_custom_email_action($actions) {
//     $actions['custom_email_action'] = array(
//         'name' => 'Custom Email Action',
//         'settings' => array(
//             'to' => array(
//                 'name' => 'To',
//                 'type' => 'textbox',
//             ),
//             // Add more settings fields as needed.
//         ),
//         'use_builder' => true,
//         'save_email' => true,
//     );
//     return $actions;
// }
// add_filter('ninja_forms_register_actions', 'register_custom_email_action');





// function my_custom_style(){
$path_css = plugins_url('css/main.css', __FILE__);
$dep_css = array();
$ver_css = "1.1";
wp_enqueue_style('my-syncmate-css', $path_css, $dep_css, $ver_css);
// }

// add_action('wp_enqueue_styles','my_custom_style');

// function my_custom_scripts(){
$path_js = plugins_url('js/main.js', __FILE__);
$dep_js = array();
$ver_js = "1.0";
wp_enqueue_script('my-syncmate-js', $path_js, $dep_js, $ver_js);
// }

// add_action('wp_enqueue_scripts','my_custom_scripts');



// Insert or update data in the custom table based on post_id and meta_key
function smassistro_insert_or_update_data_into_custom_table($post_id, $meta_key, $meta_value)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'syncmate_form';

    // Check if data with the same post_id and meta_key already exists
    $existing_row = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE post_id = %d AND meta_key = %s",
            $post_id,
            $meta_key
        )
    );

    if ($existing_row) {
        // Data already exists, update it
        $updated = $wpdb->update(
            $table_name,
            array('meta_value' => $meta_value),
            array('post_id' => $post_id, 'meta_key' => $meta_key),
            array('%s'), // Data format for meta_value (string)
            array('%d', '%s') // Data format for post_id (integer) and meta_key (string)
        );

        return $updated !== false; // Return true if data was updated, false otherwise
    } else {
        // Data doesn't exist, insert it
        $inserted = $wpdb->insert(
            $table_name,
            array(
                'post_id' => $post_id,
                'meta_key' => $meta_key,
                'meta_value' => $meta_value,
            ),
            array(
                '%d', // post_id is an integer
                '%s', // meta_key is a string
                '%s'  // meta_value is a string
            )
        );

        return $inserted;
    }
}


// Retrieve data from the custom table based on post_id and meta_key
function smassistro_get_data_by_key_value($post_id, $meta_key)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'syncmate_form';

    $result = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE post_id = %d AND meta_key = %s",
            $post_id,
            $meta_key
        )
    );

    if (isset($result->meta_value) && !empty($result->meta_value)) {
        return $result->meta_value;
    } else {
        return false;
    }
}




// Enqueue WhatsApp icon script
function smassistro_enqueue_whatsapp_icon_script()
{
    wp_enqueue_script('whatsapp-icon-script', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/fontawesome.min.js', array(), '1.0', true);
}
add_action('wp_enqueue_scripts', 'smassistro_enqueue_whatsapp_icon_script');


// Display WhatsApp icon HTML
function smassistro_display_whatsapp_icon()
{
    $whatsapp_number = '919928044051'; // Replace with your WhatsApp number
    $whatsapp_icon_left = '<div style="position: fixed; bottom: 20px; left: 20px;"><a href="https://wa.me/' . $whatsapp_number . '" target="_blank" rel="nofollow"><i class="fa fa-whatsapp"></i></a></div>';
    $whatsapp_icon_right = '<div style="position: fixed; bottom: 20px; right: 20px;"><a href="https://wa.me/' . $whatsapp_number . '" target="_blank" rel="nofollow"><i class="fa fa-whatsapp"></i></a></div>';
    echo $whatsapp_icon_left . $whatsapp_icon_right;
}
add_action('wp_footer', 'smassistro_display_whatsapp_icon');
