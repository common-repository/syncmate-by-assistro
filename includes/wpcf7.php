<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Add custom tabs to the Contact Form 7 editor
function smassistro_cf7_tabs($panels)
{
    $panels['custom-tab-1'] = array(
        'title' => 'SyncMate',
        'callback' => 'smassistro_tab_1_content'
    );

    return $panels;
}
add_filter('wpcf7_editor_panels', 'smassistro_cf7_tabs');


// Content for Custom Tab 1
function smassistro_tab_1_content($contact_form)
{
    global $wpdb;


    $form_id = $contact_form->id();
    // $api_key_text_input = smassistro_get_data_by_key_value($form_id, 'wpcf7_key_text_input');
    $wpcf7_option_select = smassistro_get_data_by_key_value($form_id, 'wpcf7_option_select');
    $api_counrty_code_text_select = smassistro_get_data_by_key_value($form_id, 'wpcf7_country_code_text_select');
    $api_counrty_code_text_input = smassistro_get_data_by_key_value($form_id, 'wpcf7_country_code_text_input');

    $api_option1_number_text_input = smassistro_get_data_by_key_value($form_id, 'wpcf7_option1_number_text_input');
    $api_option2_number_text_input = smassistro_get_data_by_key_value($form_id, 'wpcf7_option2_number_text_input');
    $api_option3_number_text_input = smassistro_get_data_by_key_value($form_id, 'wpcf7_option3_number_text_input');
    $api_message_text_input = smassistro_get_data_by_key_value($form_id, 'wpcf7_message_text_input');
    $api_enable_disable_toggle = smassistro_get_data_by_key_value($form_id, 'wpcf7_enable_disable_toggle');


    // Display the custom text input data
    if (!empty($wpcf7_option_select)) {
        $wpcf7_option_select = $wpcf7_option_select;
    } else {
        $wpcf7_option_select = '';
    }
    if (!empty($api_counrty_code_text_select)) {
        $api_counrty_code_text_select = $api_counrty_code_text_select;
    } else {
        $api_counrty_code_text_select = '';
    }
    if (!empty($api_counrty_code_text_input)) {
        $api_counrty_code_text_input = $api_counrty_code_text_input;
    } else {
        $api_counrty_code_text_input = '';
    }
    // Display the custom text input data
    if (!empty($api_option1_number_text_input)) {
        $api_option1_number_text_input = $api_option1_number_text_input;
    } else {
        $api_option1_number_text_input = '';
    }
    // Display the custom text input data
    if (!empty($api_option2_number_text_input)) {
        $api_option2_number_text_input = $api_option2_number_text_input;
    } else {
        $api_option2_number_text_input = '';
    }
    // Display the custom text input data
    if (!empty($api_option3_number_text_input)) {
        $api_option3_number_text_input = $api_option3_number_text_input;
    } else {
        $api_option3_number_text_input = '';
    }
    // Display the custom text input data
    if (!empty($api_message_text_input)) {
        $api_message_text_input = $api_message_text_input;
    } else {
        $api_message_text_input = '';
    }
    // Display the custom text input data
    if (!empty($api_enable_disable_toggle)) {
        $api_enable_disable_toggle = $api_enable_disable_toggle;
    } else {
        $api_enable_disable_toggle = 'off';
    }

?>

    <div id="custom-tab-1-content" class="mb-2">
        <h2>SyncMate</h2>
        <p>A light-weight plugin to send instant whatsApp messages without using BSP's.</p>
        <!-- <div class="row">
                <label for="wpcf7-key-textarea">Please enter Jwt api key:</label>
                <textarea cols="100" rows="5" class="large-text" id="wpcf7-key-textarea" name="wpcf7-key-textarea"><?php echo esc_textarea($api_key_text_input); ?></textarea>
            </div> -->
        <!-- <div class="row">
                <label for="wpcf7-key-textarea">Please enter phoneNumber:</label>
                <input type="text" id="wpcf7-phoneNumber-input" class="large-text" name="wpcf7-phoneNumber-input" value="<?php echo esc_attr($api_phoneNumber_text_input); ?>" />
            </div> -->


    </div>
    <input type="hidden" name="nonce_field" value="<?php echo esc_attr(wp_create_nonce('smassistro_save_wpcf7_data')); ?>" />


    <div class="custom-plugin">
        <div class="mb-2">
            <label for="wpcf7-option-select">Select an option ( If you know your customers coming from one fixed country, use the default settings, otherwise provide the field that relates to country code )</label>
            <br>
            <br>
            <select id="wpcf7-option-select" name="wpcf7-option-select">
                <option value="option1" <?php if ($wpcf7_option_select == "option1") { ?> selected <?php } ?>>specific Country</option>
                <option value="option2" <?php if ($wpcf7_option_select == "option2") { ?> selected <?php } ?>>I have a feild for country code</option>
                <option value="option3" <?php if ($wpcf7_option_select == "option3") { ?> selected <?php } ?>>I don't have country code</option>
            </select>
        </div>

        <div id="content-container" class="mb-1">
            <!-- Content for Option 1 -->
            <div id="option1-content" style="display:block;">
                <div class="row">
                    <label for="wpcf7-option1-number-input">Number : ( put the name attribute value of your contact form here enclosed between `[ ]` )</label>
                    <br>
                    <div class="" style="display: flex;">
                        <select name="wpcf7-country-code-select" style="width:20%;" id="wpcf7-country-code-select">
                            <option value="">All Country</option>
                            <?php
                            $countries = [
                                "91" => "+91 (India)",
                                "65" => "+65 (Singapore)",
                                "1" => "+1 (United States)",
                                "86" => "+86 (China)",
                                "44" => "+44 (United Kingdom)",
                                "81" => "+81 (Japan)",
                                "49" => "+49 (Germany)",
                                "33" => "+33 (France)",
                                "7" => "+7 (Russia)",
                                "54" => "+54 (Argentina)",
                                "55" => "+55 (Brazil)",
                                "61" => "+61 (Australia)",
                                "62" => "+62 (Indonesia)",
                                "234" => "+234 (Nigeria)",
                                "92" => "+92 (Pakistan)",
                                "880" => "+880 (Bangladesh)",
                                "20" => "+20 (Egypt)",
                                "27" => "+27 (South Africa)",
                                // Add more countries as needed
                            ];

                            foreach ($countries as $code => $name) {
                                $selected = ($api_counrty_code_text_select == $code) ? 'selected' : '';
                                echo "<option value=\"$code\" $selected>$name</option>";
                            }
                            ?>
                        </select>

                        <input type="text" id="wpcf7-option1-number-input" class="large-text" style="width:80%;" name="wpcf7-option1-number-input" value="<?php echo esc_attr($api_option1_number_text_input); ?>" />
                    </div>
                </div>
            </div>

            <!-- Content for Option 2 -->
            <div id="option2-content" <?php if ($wpcf7_option_select == "option2") { ?> style="display:block;" <?php } else { ?>style="display:none;" <?php } ?>>
                <div class="row">
                    <label for="wpcf7-option2-number-input">Country : ( put the name attribute value of your contact form here enclosed between `[ ]` )</label>
                    <input type="text" id="wpcf7-country-code-input" class="large-text mb-1" name="wpcf7-country-code-input" value="<?php echo esc_attr($api_counrty_code_text_input); ?>" />
                    <label for="wpcf7-option2-number-input">Number : ( put the name attribute value of your contact form here enclosed between `[ ]` )</label>
                    <input type="text" id="wpcf7-option2-number-input" class="large-text" name="wpcf7-option2-number-input" value="<?php echo esc_attr($api_option2_number_text_input); ?>" />
                </div>
            </div>

            <!-- Content for Option 3 -->
            <div id="option3-content" <?php if ($wpcf7_option_select == "option3") { ?> style="display:block;" <?php } else { ?>style="display:none;" <?php } ?>>
                <div class="row">
                    <label for="wpcf7-option3-number-input">Number : ( put the name attribute value of your contact form here enclosed between `[ ]` )</label>
                    <input type="text" id="wpcf7-option3-number-input" class="large-text" name="wpcf7-option3-number-input" value="<?php echo esc_attr($api_option3_number_text_input); ?>" />
                </div>
            </div>
        </div>
        <div class="row mb-1">
            <label for="wpcf7-message-input">Message : ( Enclose the values of the name attribute within { } to make it dynamic. )</label>
            <textarea id="wpcf7-message-input" name="wpcf7-message-input" cols="100" rows="12" class="large-text"><?php echo isset($api_message_text_input) ? esc_textarea($api_message_text_input) : ''; ?></textarea>
        </div>


        <div class="row mb-1">
            <label for="wpcf7-enable-disable-toggle">Enable</label>
            <?php $api_enable_disable_toggle ?>
            <br>
            <label class="switch mt-1">
                <input type="checkbox" name="wpcf7-enable-disable-toggle" <?php if ($api_enable_disable_toggle == 'on') { ?>checked <?php } ?>>
                <span class="slider round"></span>
            </label>
        </div>

    </div>

    <script>
        // JavaScript to show content based on the selected option
        document.addEventListener('DOMContentLoaded', function() {
            const optionSelect = document.getElementById('wpcf7-option-select');
            const contentContainer = document.getElementById('content-container');

            optionSelect.addEventListener('change', function() {
                // Hide all content divs
                Array.from(contentContainer.children).forEach(function(contentDiv) {
                    contentDiv.style.display = 'none';
                });

                // Show the selected content div
                const selectedOption = optionSelect.value;
                const selectedContent = document.getElementById(selectedOption + '-content');
                if (selectedContent) {
                    selectedContent.style.display = 'block';
                }
            });
        });
    </script>

<?php
    // Retrieve the custom text input data

}


// Save custom text input data to the database
function smassistro_save_wpcf7_key_text_input_data_to_db($contact_form)
{
    global $wpdb;
    if (isset($_POST['nonce_field']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce_field'])), 'smassistro_save_wpcf7_data')) {

        // print_r($_POST['wpcf7-country-code-select']);
        // die;

        if (isset($_POST['wpcf7-key-textarea'])) {
            $form_id = $contact_form->id();
            $wpcf7_key_text_input = sanitize_text_field($_POST['wpcf7-key-textarea']);
            smassistro_insert_or_update_data_into_custom_table($form_id, 'wpcf7_key_text_input', $wpcf7_key_text_input);
        }
        if (isset($_POST['wpcf7-option-select'])) {
            $form_id = $contact_form->id();
            $wpcf7_option_select = sanitize_text_field($_POST['wpcf7-option-select']);
            smassistro_insert_or_update_data_into_custom_table($form_id, 'wpcf7_option_select', $wpcf7_option_select);
        }
        if (isset($_POST['wpcf7-country-code-select'])) {
            $form_id = $contact_form->id();
            $wpcf7_country_code_text_select = sanitize_text_field($_POST['wpcf7-country-code-select']);
            smassistro_insert_or_update_data_into_custom_table($form_id, 'wpcf7_country_code_text_select', $wpcf7_country_code_text_select);
        }
        if (isset($_POST['wpcf7-country-code-input'])) {
            $form_id = $contact_form->id();
            $wpcf7_country_code_text_input = sanitize_text_field($_POST['wpcf7-country-code-input']);
            smassistro_insert_or_update_data_into_custom_table($form_id, 'wpcf7_country_code_text_input', $wpcf7_country_code_text_input);
        }
        if (isset($_POST['wpcf7-option1-number-input'])) {
            $form_id = $contact_form->id();
            $wpcf7_option1_number_text_input = sanitize_text_field($_POST['wpcf7-option1-number-input']);
            smassistro_insert_or_update_data_into_custom_table($form_id, 'wpcf7_option1_number_text_input', $wpcf7_option1_number_text_input);
        }
        if (isset($_POST['wpcf7-option2-number-input'])) {
            $form_id = $contact_form->id();
            $wpcf7_option2_number_text_input = sanitize_text_field($_POST['wpcf7-option2-number-input']);
            smassistro_insert_or_update_data_into_custom_table($form_id, 'wpcf7_option2_number_text_input', $wpcf7_option2_number_text_input);
        }
        if (isset($_POST['wpcf7-option3-number-input'])) {
            $form_id = $contact_form->id();
            $wpcf7_option3_number_text_input = sanitize_text_field($_POST['wpcf7-option3-number-input']);
            smassistro_insert_or_update_data_into_custom_table($form_id, 'wpcf7_option3_number_text_input', $wpcf7_option3_number_text_input);
        }
        if (isset($_POST['wpcf7-message-input'])) {
            $form_id = $contact_form->id();

            $wpcf7_message_text_input = htmlspecialchars($_POST['wpcf7-message-input']);
            // print_r($wpcf7_message_text_input);
            // die;
            smassistro_insert_or_update_data_into_custom_table($form_id, 'wpcf7_message_text_input', $wpcf7_message_text_input);
        }
        if (isset($_POST['wpcf7-enable-disable-toggle'])) {
            $form_id = $contact_form->id();
            $wpcf7_enable_disable_toggle = isset($_POST['wpcf7-enable-disable-toggle']) && $_POST['wpcf7-enable-disable-toggle'] === 'on' ? 'on' : 'off';
            // print_r($_POST['wpcf7-enable-disable-toggle']);
            // die;
            smassistro_insert_or_update_data_into_custom_table($form_id, 'wpcf7_enable_disable_toggle', $wpcf7_enable_disable_toggle);
        } else {
            $form_id = $contact_form->id();
            $wpcf7_enable_disable_toggle = isset($_POST['wpcf7-enable-disable-toggle']) && $_POST['wpcf7-enable-disable-toggle'] === 'on' ? 'on' : 'off';
            smassistro_insert_or_update_data_into_custom_table($form_id, 'wpcf7_enable_disable_toggle', $wpcf7_enable_disable_toggle);
        }
    } else {
        // Nonce verification failed, do not process the form data
        echo '<div class="notice notice-error"><p>Nonce verification failed. Form data not processed.</p></div>';
        // die;
    }
}
add_action('wpcf7_save_contact_form', 'smassistro_save_wpcf7_key_text_input_data_to_db');



function smassistro_make_api_call_on_form_submit($contact_form)
{
    // Check if the nonce field is set
    // Generate the nonce field
    $nonce = wp_create_nonce();

    // echo '<input type="hidden" name="_wpcf7_nonce" value="' . esc_attr($nonce) . '">';
    // Include the nonce field in your form data
    $_POST['_wpcf7_nonce'] = $nonce;
    //  print_r(isset($_POST['_wpcf7_nonce']) && wpcf7_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpcf7_nonce']) ) ));
    //  die;

    if (isset($_POST['_wpcf7_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpcf7_nonce'])))) {



        // Proceed only if nonce verification passes

        $form_id = $contact_form->id();
        $api_key_push_plus = smassistro_get_data_by_key_value(1, 'cf7_pushplus_key_text_input');
        $assistro_status_both_toggle = smassistro_get_data_by_key_value(1, 'cf7_status_both_toggle_input');
        $api_key_push = smassistro_get_data_by_key_value(1, 'cf7_key_push_text_input');
        $api_phoneNumber_push = smassistro_get_data_by_key_value(1, 'cf7_phoneNumber_push_text_input');

        $api_option_select = smassistro_get_data_by_key_value($form_id, 'wpcf7_option_select');
        $api_counrty_code = smassistro_get_data_by_key_value($form_id, 'wpcf7_country_code_text_select');
        $api_number = smassistro_get_data_by_key_value($form_id, 'wpcf7_number_text_input');
        $api_enable_disable_toggle = smassistro_get_data_by_key_value($form_id, 'wpcf7_enable_disable_toggle');

        if ($api_enable_disable_toggle === 'on') {
            // print_r("send mesage");
            // die;
            // Use the substr function to get the first character of the string
            $phoneNumber = substr($api_number, 0, 1);
            // Compare the first character to an underscore

            if ($api_option_select === 'option1') {
                $api_number = smassistro_get_data_by_key_value($form_id, 'wpcf7_option1_number_text_input');
                $phoneNumber = substr($api_number, 0, 1);
                if ($phoneNumber === '[') {
                    $result = substr($api_number, 1, -1);
                    $api_single_number = isset($_POST[$result]) ? sanitize_text_field($_POST[$result]) : '';
                    $api_number = $api_counrty_code . $api_single_number;
                } else {
                    $api_number = '';
                }
            } elseif ($api_option_select === 'option2') {
                $api_number = smassistro_get_data_by_key_value($form_id, 'wpcf7_option2_number_text_input');
                $phoneNumber = substr($api_number, 0, 1);
                $api_country_code = smassistro_get_data_by_key_value($form_id, 'wpcf7_country_code_text_input');
                $countryCode = substr($api_country_code, 0, 1);
                if ($phoneNumber === '[' && $countryCode === '[') {
                    $result = substr($api_number, 1, -1);
                    $result_code = substr($api_country_code, 1, -1);
                    $api_single_number = isset($_POST[$result]) ? sanitize_text_field($_POST[$result]) : '';
                    $api_single_country_code = isset($_POST[$result_code]) ? sanitize_text_field($_POST[$result_code]) : '';
                    $api_number = $api_counrty_code . $api_single_number;
                    $api_number = smassistro_my_custom_format_phone_number_wpcf7($api_number);
                } else {
                    $api_number = '';
                }
            } else {
                $api_number = smassistro_get_data_by_key_value($form_id, 'wpcf7_option1_number_text_input');
                $phoneNumber = substr($api_number, 0, 1);
                if ($phoneNumber === '[') {
                    $result = substr($api_number, 1, -1);
                    $api_single_number = isset($_POST[$result]) ? sanitize_text_field($_POST[$result]) : '';
                    $api_number = smassistro_my_custom_format_phone_number_wpcf7($api_number);
                    $api_number = $api_single_number;
                } else {
                    $api_number = '';
                }
            }

            $api_message = smassistro_get_data_by_key_value($form_id, 'wpcf7_message_text_input');

            // Use a regular expression to find and extract text within curly braces
            if (preg_match_all('/\{([^}]+)\}/', $api_message, $matches)) {
                $dataInsideBraces = $matches[1];

                // Iterate through extracted data and replace in the string
                foreach ($dataInsideBraces as $data) {
                    // $replacement = isset($$data) ? $$data : ''; // Use $$ to access variable by name
                    $replacement = isset($_POST[$data]) ? $_POST[$data] : ''; // Get data from POST
                    $api_message = str_replace("{{$data}}", $replacement, $api_message);
                }
            } else {
                $api_message = $api_message;
            }
            // $api_message=htmlspecialchars_decode($api_message);
            // print_r($api_message);
            // die;





            if (!empty($api_number) && !empty($api_message)) {
                // Define the API endpoint
                if ($assistro_status_both_toggle == 'wapushplus') {

                    $api_key = $api_key_push_plus;
                    $api_url = 'https://app.assistro.co/api/v1/wapushplus/single/message';
                    $request_body = array(
                        "wts_phone_number" => $api_phoneNumber_push,
                        "msgs" => array(
                            array(
                                "number" => $api_number,
                                "message" => $api_message
                            )
                        )
                    );
                } elseif ($assistro_status_both_toggle == 'wapush') {

                    $api_key = $api_key_push;

                    $api_url = 'https://app.assistro.co/api/v1/wapush/single/message';
                    $request_body = array(
                        "wts_phone_number" => $api_phoneNumber_push,
                        "msgs" => array(
                            array(
                                "number" => $api_number,
                                "message" => $api_message
                            )
                        )
                    );
                    // $json_string = json_encode($request_body);
                } else {
                    error_log('API Response Body: some error key not match');
                }
                // Define the data you want to send in the request body as an array


                // Set up the request arguments, including the API key in the headers
                $args = array(
                    'headers' => array(
                        'Authorization' => 'Bearer ' . $api_key,
                        'Content-Type' => 'application/json', // Verify the content type
                        'User-Agent' => sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST'])), // Sanitize and escape
                        // 'User-Agent' => 'wordpressform',
                        'Integration' => 'WordpressForm',
                    ),
                    'body' => json_encode($request_body), // Convert the data to JSON format if needed
                );



                // Make the API POST request


                // Make the API POST request
                $response = wp_remote_post($api_url, $args);



                if (!is_wp_error($response)) {
                    // Process the API response data
                    $response_code = wp_remote_retrieve_response_code($response); // Get the HTTP response code
                    $response_body = wp_remote_retrieve_body($response); // Get the response body
                    $api_data = json_decode($response_body, true); // Parse JSON response data



                    // You can now work with the API response data
                    // Example: Log the response data
                    error_log('API Response Code: ' . $response_code);
                    error_log('API Response Body: ' . print_r($api_data, true));

                    if ($response_code === 200) {
                        error_log('API Response Data: ' . json_encode($api_data));
                        // Return the API response data
                        return json_encode($api_data);
                    } else {
                        // Handle API error response (non-200 status code)
                        // You can return an error message to the user or perform error handling here
                        error_log('API Call Failed. HTTP Status Code: ' . $response_code . ', Response: ' . $response_body);
                        // Set the HTTP status code to 429
                        header("Content-Type: application/json"); // Example: JSON response
                        header("HTTP/1.1 200 Too Many Requests");

                        $json_data = json_encode($api_data);

                        echo esc_html($json_data);


                        // Exit to prevent further processing
                        exit;
                        return 'API Call Failed. HTTP Status Code: ' . $response_code . ', Response: ' . $response_body;
                    }
                } else {
                    // Handle API call error
                    $error_message = $response->get_error_message();
                    error_log('API Call Error: ' . $error_message);

                    return 'API Call Error: ' . $error_message;
                }
            }
        } else {

            return;
        }
    } else {
        // print_r("sfd");
        // die;
        // Nonce field not set, handle the error
        error_log('Nonce field not set. Form data not processed.');
        return;
    }
}

add_action('wpcf7_before_send_mail', 'smassistro_make_api_call_on_form_submit');

function smassistro_my_custom_format_phone_number_wpcf7($phone_number)
{
    // Remove unwanted characters like +, spaces, parentheses, and hyphens
    $cleaned_number = preg_replace('/[^\d]/', '', $phone_number);
    return $cleaned_number;
}



?>