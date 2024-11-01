<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

// Hook into the WooCommerce order completed event
// add_action('woocommerce_payment_complete', 'smassistro_my_custom_product_purchase_action');
// Include Dompdf library
// require_once(plugin_dir_path(__FILE__) . 'dompdf/autoload.inc.php');
// require_once(plugin_dir_path(__FILE__) . 'dompdf/autoload.inc.php');


function smassistro_my_custom_product_purchase_action($order_id)
{
    // return "testdone";
    $woocommerce_id = 1;
    $woocom_status_order_processing = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_status_order_processing');
    if ($woocom_status_order_processing == 1) {
        // print_r($woocom_status_order_processing);
        // die;
        $order = wc_get_order($order_id);
        $order_id_product = $order->get_id();
        $billing_info = $order->get_data()['billing'];

        // Get the customer's first name and last name
        $first_name = $billing_info['first_name'];
        $last_name = $billing_info['last_name'];

        // Combine first name and last name to get the full name
        $full_name = $first_name . ' ' . $last_name;
        $customer_email = $billing_info['email'];
        $customer_phone = $billing_info['phone'];
        $order_date = $order->get_date_created()->format('F j, Y');
        $order_items = $order->get_items();


        $subtotal = $order->get_subtotal();
        $shipping_method = $order->get_shipping_method();
        $payment_method = $order->get_payment_method();
        $total = $order->get_total();

        $billing_company = $order->get_billing_company();
        $billing_address_1 = $order->get_billing_address_1();
        $billing_address_2 = $order->get_billing_address_2();
        $billing_city = $order->get_billing_city();
        $billing_state = $order->get_billing_state();
        $billing_postcode = $order->get_billing_postcode();
        $billing_country = $order->get_billing_country();

        // $invoice_pdf_path = smassistro_generate_invoice_pdf($order_id);




        $api_key_push_plus = smassistro_get_data_by_key_value(1, 'woocom_pushplus_key_text_input');
        $api_key_push = smassistro_get_data_by_key_value(1, 'woocom_key_push_text_input');


        $assistro_status_both_toggle = smassistro_get_data_by_key_value(1, 'woocom_status_both_toggle_input');
        // print_r($api_key_push);
        // die;
        $api_option_select = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_option_select');
        $api_counrty_code = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_country_code_text_select');
        $api_number = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_number_text_input');

        // Use the substr function to get the first character of the string
        $phoneNumber = substr($api_number, 0, 1);
        $customer_phone = smassistro_my_custom_format_phone_number_woocomm($customer_phone);

        if ($api_option_select === 'option1') {
            $api_number = $api_counrty_code . $customer_phone;
        } elseif ($api_option_select === 'option2') {
            $api_number = $customer_phone;
        } else {
            $api_number = $customer_phone;
        }


        $api_message = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_order_processing_message_text_input');
        // $api_message = preg_split('/[\s,]+/', $api_message);
        // foreach($api_message as $item){

        // Use a regular expression to find and extract text within curly braces
        if (preg_match_all('/\{([^}]+)\}/', $api_message, $matches)) {
            $dataInsideBraces = $matches[1];

            // Define variables for replacement
            $order_full_name = $full_name;
            $order_id_product = $order_id_product;
            $order_date = $order_date;
            $order_subtotal = $subtotal;
            $order_payment_method = $payment_method;
            $order_shipping_method = $shipping_method;
            $order_total = $total;

            // Iterate through extracted data and replace in the string
            foreach ($dataInsideBraces as $data) {
                $replacement = isset($$data) ? $$data : ''; // Use $$ to access variable by name
                $api_message = str_replace("{{$data}}", $replacement, $api_message);
            }
        } else {
            $api_message = $api_message;
        }


        if (!empty($api_number) && !empty($api_message)) {
            // Define the API endpoint
            // if(!empty($api_key_push_plus)){
            if ($assistro_status_both_toggle == 'wapushplus') {

                $api_phoneNumber_push = smassistro_get_data_by_key_value(1, 'woocom_pushplus_phoneNumber_text_input');
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
                $api_phoneNumber_push = smassistro_get_data_by_key_value(1, 'woocom_phoneNumber_push_text_input');
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
            } else {
                error_log('API Response Body: some error key not match');
            }
            // dd($api_url);


            // Set up the request arguments, including the API key in the headers
            $args = array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $api_key,
                    'Content-Type' => 'application/json', // Verify the content type
                    'User-Agent' => sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST'])), // Sanitize and escape
                    // 'User-Agent' => 'woocommerce',
                    'Integration' => 'WooCommerce'
                ),
                'body' => json_encode($request_body), // Convert the data to JSON format if needed
            );

            // Make the API POST request
            // $response = wp_safe_remote_post($api_url, $args);

            // Make the API POST request
            $response = wp_remote_post($api_url, $args);
            // print_r($response);
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

                if ($response_code === 200) {


                    // Your code to work with the API response data here
                    // For example, display a success message to the user
                    return 'API Call Successful. Response: ' . json_encode($api_data);
                    // die;
                } else {
                    // Handle API error response (non-200 status code)
                    // You can return an error message to the user or perform error handling here
                    return 'API Call Failed. HTTP Status Code: ' . $response_code . ', Response: ' . $response_body;
                }
            } else {
                // Handle API call error
                $error_message = $response->get_error_message();
                error_log('API Call Error: ' . $error_message);

                return 'API Call Error: ' . $error_message;
            }
        }
    }
    // print_r("no");
    //     die;
}




add_action('woocommerce_order_status_changed', 'smassistro_my_custom_order_status_changed_action', 10, 4);

function smassistro_my_custom_order_status_changed_action($order_id, $old_status, $new_status, $order)
{

    $woocommerce_id = 1;
    if ($new_status === 'pending') {
        // Your custom code for handling pending orders
        $woocom_status_order = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_status_order_pending');
        $api_message = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_order_pending_message_text_input');
    }
    if ($new_status === 'processing') {
        // Your custom code for handling processing orders
        $woocom_status_order_invoice = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_status_order_processing_invoice');
        $woocom_status_order = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_status_order_processing');
        $api_message = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_order_processing_message_text_input');
        if ($woocom_status_order_invoice == 1) {
            $invoice_pdf_path = smassistro_generate_invoice_pdf($order_id);
        }
    }
    if ($new_status === 'completed') {
        // Your custom code for handling completed orders
        $woocom_status_order = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_status_order_complate');
        $api_message = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_order_complate_message_text_input');
    }
    if ($new_status === 'on-hold') {
        // Your custom code for handling completed orders
        $woocom_status_order = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_status_order_onhold');
        $api_message = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_order_onhold_message_text_input');
    }
    if ($new_status === 'refunded') {
        // Your custom code for handling completed orders
        $woocom_status_order = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_status_order_refunded');
        $api_message = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_order_refunded_message_text_input');
    }
    if ($new_status === 'cancelled') {
        // Your custom code for handling completed orders
        $woocom_status_order = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_status_order_cancelled');
        $api_message = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_order_cancelled_message_text_input');
    }
    if ($new_status === 'failed') {
        // Your custom code for handling completed orders
        $woocom_status_order = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_status_order_failed');
        $api_message = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_order_failed_message_text_input');
    }

    if ($woocom_status_order == 1) {
        // print_r($woocom_status_order_complate);
        // die;
        $order = wc_get_order($order_id);
        $order_id_product = $order->get_id();
        $billing_info = $order->get_data()['billing'];

        // Get the customer's first name and last name
        $first_name = $billing_info['first_name'];
        $last_name = $billing_info['last_name'];

        // Combine first name and last name to get the full name
        $full_name = $first_name . ' ' . $last_name;
        $customer_email = $billing_info['email'];
        $customer_phone = $billing_info['phone'];
        $order_date = $order->get_date_created()->format('F j, Y');
        $order_items = $order->get_items();


        $subtotal = $order->get_subtotal();
        $shipping_method = $order->get_shipping_method();
        $payment_method = $order->get_payment_method();
        $total = $order->get_total();

        $billing_company = $order->get_billing_company();
        $billing_address_1 = $order->get_billing_address_1();
        $billing_address_2 = $order->get_billing_address_2();
        $billing_city = $order->get_billing_city();
        $billing_state = $order->get_billing_state();
        $billing_postcode = $order->get_billing_postcode();
        $billing_country = $order->get_billing_country();


        // print_r($invoice_pdf_path);
        // die;




        $api_key_push_plus = smassistro_get_data_by_key_value(1, 'woocom_pushplus_key_text_input');
        $api_key_push = smassistro_get_data_by_key_value(1, 'woocom_key_push_text_input');

        $assistro_status_both_toggle = smassistro_get_data_by_key_value(1, 'woocom_status_both_toggle_input');
        // print_r($api_key_push);
        // die;
        $api_option_select = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_option_select');
        $api_counrty_code = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_country_code_text_select');
        $api_number = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_number_text_input');

        // Use the substr function to get the first character of the string
        $phoneNumber = substr($api_number, 0, 1);

        // Use the proper format number space and (,),- removed
        $customer_phone = smassistro_my_custom_format_phone_number_woocomm($customer_phone);


        if ($api_option_select === 'option1') {
            $api_number = $api_counrty_code . $customer_phone;
        } elseif ($api_option_select === 'option2') {
            $api_number = $customer_phone;
        } else {
            $api_number = $customer_phone;
        }



        // $api_message = preg_split('/[\s,]+/', $api_message);
        // foreach($api_message as $item){
        $order_object_json = json_encode($order->get_data(), JSON_PRETTY_PRINT);
        // Use a regular expression to find and extract text within curly braces
        if (preg_match_all('/\{([^}]+)\}/', $api_message, $matches)) {
            $dataInsideBraces = $matches[1];

            // Define variables for replacement
            $order = $order_object_json;
            $order_full_name = $full_name;
            $order_id_product = $order_id_product;
            $order_date = $order_date;
            $order_subtotal = $subtotal;
            $order_payment_method = $payment_method;
            $order_shipping_method = $shipping_method;
            $order_total = $total;

            // Iterate through extracted data and replace in the string
            foreach ($dataInsideBraces as $data) {
                $replacement = isset($$data) ? $$data : ''; // Use $$ to access variable by name
                $api_message = str_replace("{{$data}}", $replacement, $api_message);
            }
        } else {

            $api_message = $api_message;
        }

        if (!empty($api_number) && !empty($api_message)) {
            // Define the API endpoint
            // if(!empty($api_key_push_plus)){
            if ($assistro_status_both_toggle == 'wapushplus') {

                $api_key = $api_key_push_plus;
                $api_phoneNumber_push = smassistro_get_data_by_key_value(1, 'woocom_pushplus_phoneNumber_text_input');
                $api_url = 'https://app.assistro.co/api/v1/wapushplus/single/message';

                if ($woocom_status_order_invoice == 1 && $new_status === 'processing') {
                    $request_body = array(
                        "wts_phone_number" => $api_phoneNumber_push,
                        "msgs" => array(
                            array(
                                "number" => $api_number,
                                "message" => $api_message,
                                "media" => array(
                                    array(
                                        "media_base64" => $invoice_pdf_path,
                                        "file_name" => 'Invoice for Order #' . $order_id_product
                                    )
                                )
                            )
                        )
                    );
                } else {
                    $request_body = array(
                        "wts_phone_number" => $api_phoneNumber_push,
                        "msgs" => array(
                            array(
                                "number" => $api_number,
                                "message" => $api_message
                            )
                        )
                    );
                }
            } elseif ($assistro_status_both_toggle == 'wapush') {
                $api_key = $api_key_push;
                $api_phoneNumber_push = smassistro_get_data_by_key_value(1, 'woocom_phoneNumber_push_text_input');
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
            } else {
                error_log('API Response Body: some error key not match');
            }
            //   print_r($api_url);
            //   print_r($request_body);
            // die;

            // Set up the request arguments, including the API key in the headers
            $args = array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $api_key,
                    'Content-Type' => 'application/json', // Verify the content type
                    'User-Agent' => sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST'])), // Sanitize and escape
                    // 'User-Agent' => 'woocommerce',
                    'Integration' => 'WooCommerce'
                ),
                'body' => json_encode($request_body), // Convert the data to JSON format if needed
            );
            //   print_r($args);
            // die;

            // Make the API POST request
            // $response = wp_safe_remote_post($api_url, $args);

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


                    // Your code to work with the API response data here
                    // For example, display a success message to the user
                    return 'API Call Successful. Response: ' . json_encode($api_data);
                    // die;
                } else {
                    // Handle API error response (non-200 status code)
                    // You can return an error message to the user or perform error handling here
                    return 'API Call Failed. HTTP Status Code: ' . $response_code . ', Response: ' . $response_body;
                }
            } else {
                // Handle API call error
                $error_message = $response->get_error_message();
                error_log('API Call Error: ' . $error_message);

                return 'API Call Error: ' . $error_message;
            }
        }
    }
}



function smassistro_my_custom_format_phone_number_woocomm($phone_number)
{
    // Remove unwanted characters like +, spaces, parentheses, and hyphens
    $cleaned_number = preg_replace('/[^\d]/', '', $phone_number);
    return $cleaned_number;
}



// Example function for generating the invoice PDF (you need to implement this)
function smassistro_generate_invoice_pdf($order_id)
{
    $dompdf = new Dompdf\Dompdf();

    // Get the order details
    $order = wc_get_order($order_id); // Replace with the appropriate method to retrieve order details

    if ($order) {
        // Begin the HTML content for the invoice
        $html = '<html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                        }
                        th, td {
                            border: 1px solid #000;
                            padding: 8px;
                            text-align: left;
                        }
                    </style>
                </head>
                <body>
                    <h1>Invoice for Order #' . $order_id . '</h1>
                    <p>Order Date: ' . $order->get_date_created()->format('Y-m-d H:i:s') . '</p>
                    <p>Payment Method: ' . $order->get_payment_method() . '</p>';


        // Create a table to display billing and shipping addresses in separate columns
        $html .= '<table>
                        <tr>
                            <th>Billing Address</th>
                            <th>Shipping Address</th>
                        </tr>
                        <tr>
                            <td>' . $order->get_formatted_billing_address() . '</td>
                            <td>' . $order->get_formatted_shipping_address() . '</td>
                        </tr>
                    </table>';

        // $html .= '<p>Total Amount: $' . $order->get_total() . '</p>';

        // Display product details and calculate subtotal
        $html .= '<p>Products:</p>';
        $html .= '<table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>';

        $product_subtotal = 0;

        // Get order items
        foreach ($order->get_items() as $item_id => $item) {
            $product = $item->get_product();
            $product_name = $product ? $product->get_name() : 'Product not found';
            $quantity = $item->get_quantity();
            $price = $item->get_total() / $quantity; // Calculate the price per unit
            $total = $item->get_total();

            // Add a row for each order item
            $html .= '<tr>
                        <td>' . $product_name . '</td>
                        <td>' . $quantity . '</td>
                        <td>$' . number_format($price, 2) . '</td>
                        <td>$' . number_format($total, 2) . '</td>
                    </tr>';

            $product_subtotal += $total;
        }

        $html .= '</tbody>
                </table>';

        // Display the product subtotal
        $html .= '<p>Product Subtotal: $' . number_format($product_subtotal, 2) . '</p>';

        // Display the total amount
        $html .= '<p>Total Amount: $' . number_format($order->get_total(), 2) . '</p>';

        // Close the HTML content
        $html .= '</body>
        </html>';
        $dompdf->loadHtml($html);

        // (Optional) Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the PDF (convert HTML to PDF)
        $dompdf->render();



        /// Output the PDF as a string
        $pdf_content = $dompdf->output();

        // Encode the PDF content as base64
        $base64_pdf = base64_encode($pdf_content);

        return $base64_pdf;
    } else {
        return 'Order not found';
    }
}


// Hook into WooCommerce settings tabs
function smassistro_add_syncmate_tab_to_woocommerce_settings($settings_tabs)
{
    $settings_tabs['syncmate_tab'] = __('SyncMate', 'syncmate-by-assistro');
    return $settings_tabs;
}
add_filter('woocommerce_settings_tabs_array', 'smassistro_add_syncmate_tab_to_woocommerce_settings', 50);


// Define the content for the custom tab
function smassistro_tab_content()
{
    global $wpdb;


    $woocommerce_id = 1;
    // $api_key_text_input = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_key_text_input');
    $woocom_option_select = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_option_select');
    $api_counrty_code_text_select = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_country_code_text_select');
    $api_counrty_code_text_input = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_country_code_text_input');

    $api_option1_number_text_input = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_option1_number_text_input');
    $api_option2_number_text_input = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_option2_number_text_input');
    $api_option3_number_text_input = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_option3_number_text_input');

    $woocom_order_processing_message_text_input = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_order_processing_message_text_input');
    $woocom_status_order_processing = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_status_order_processing');
    $woocom_status_order_processing_invoice = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_status_order_processing_invoice');

    $woocom_order_complate_message_text_input = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_order_complate_message_text_input');
    $woocom_status_order_complate = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_status_order_complate');

    $woocom_order_cancelled_message_text_input = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_order_cancelled_message_text_input');
    $woocom_status_order_cancelled = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_status_order_cancelled');

    $woocom_order_refunded_message_text_input = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_order_refunded_message_text_input');
    $woocom_status_order_refunded = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_status_order_refunded');

    $woocom_order_onhold_message_text_input = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_order_onhold_message_text_input');
    $woocom_status_order_onhold = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_status_order_onhold');

    $woocom_order_pending_message_text_input = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_order_pending_message_text_input');
    $woocom_status_order_pending = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_status_order_pending');

    $woocom_order_failed_message_text_input = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_order_failed_message_text_input');
    $woocom_status_order_failed = smassistro_get_data_by_key_value($woocommerce_id, 'woocom_status_order_failed');


    // Display the custom text input data
    if (!empty($woocom_option_select)) {
        $woocom_option_select = $woocom_option_select;
    } else {
        $woocom_option_select = '';
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
    if (!empty($woocom_order_complate_message_text_input)) {
        $woocom_order_complate_message_text_input = $woocom_order_complate_message_text_input;
    } else {
        $woocom_order_complate_message_text_input = '';
    }


?>


    <div id="custom-tab-1-content" class="mb-2">

        <h2>SyncMate</h2>
        <p>A light-weight plugin to send instant whatsApp messages without using BSP's.</p>
        <!-- <div class="row">
                <label for="woocom-key-textarea">Please enter Jwt api key:</label>
                <textarea cols="100" rows="5" class="large-text" id="woocom-key-textarea" name="woocom-key-textarea"><?php echo esc_textarea($api_key_text_input); ?></textarea>
            </div> -->
        <!-- <div class="row">
                <label for="woocom-key-textarea">Please enter phoneNumber:</label>
                <input type="text" id="woocom-phoneNumber-input" class="large-text" name="woocom-phoneNumber-input" value="<?php echo esc_attr($api_phoneNumber_text_input); ?>" />
            </div> -->


    </div>
    <input type="hidden" name="nonce_field" value="<?php echo esc_attr(wp_create_nonce('smassistro_save_woocom_data')); ?>" />


    <div class="custom-plugin">
        <div class="mb-2">
            <label for="woocom-option-select">Select an option ( If you know your customers coming from one fixed country, use the default settings, otherwise provide the field that relates to country code )</label>
            <br>
            <br>
            <select id="woocom-option-select" name="woocom-option-select">
                <option value="option1" <?php if ($woocom_option_select == "option1") { ?> selected <?php } ?>>Specific Country</option>
                <option value="option2" <?php if ($woocom_option_select == "option2") { ?> selected <?php } ?>>I don't have country code</option>
            </select>
        </div>

        <div id="content-container" class="mt-2 mb-2">
            <!-- Content for Option 1 -->
            <div id="option1-content" style="display:block;">
                <div class="row">
                    <label for="woocom-option1-number-input">Country Code ( Select the country that relates to contact submissions, we will prefix the code while sending messages )</label>
                    <br>
                    <br>
                    <div class="" style="display: flex;">
                        <select name="woocom-country-code-select" style="width:100%;" id="woocom-country-code-select">
                            <option value="">All Country</option>
                            <!-- <option value="91" <?php if ($api_counrty_code_text_select == "91") { ?> selected <?php } ?>>+91 (India)</option>
                                <option value="65" <?php if ($api_counrty_code_text_select == "65") { ?> selected <?php } ?>>+65 (Singapure)</option> -->
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

                        <!-- <input type="text" id="woocom-option1-number-input" class="large-text" style="width:80%;" name="woocom-option1-number-input" value="<?php echo esc_attr($api_option1_number_text_input); ?>" /> -->
                    </div>

                    <!-- <div class="" style="display: flex;">
                            <select name="wpcf7-country-code-select" style="width:20%;" id="wpcf7-country-code-select">
                                <option value="">All Country</option>
                                <?php
                                global $wpdb;
                                $table_countries = $wpdb->prefix . 'syncmate_countries';
                                $countries = $wpdb->get_results("SELECT * FROM $table_countries");
                                foreach ($countries as $country) {
                                    $selected = ($api_counrty_code_text_select == $country->country_code) ? 'selected' : '';
                                    echo '<option value="' . esc_attr($country->country_code) . '" ' . $selected . '>+' . esc_attr($country->country_code) . ' ( ' . esc_html($country->country_name) . ' )</option>';
                                }
                                ?>
                            </select>
                            
                            
                        </div> -->
                </div>
            </div>

            <!-- Content for Option 3 -->
            <div id="option2-content" <?php if ($woocom_option_select == "option2") { ?> style="display:block;" <?php } else { ?>style="display:none;" <?php } ?>>
                <!-- <div class="row">
                    <label for="woocom-option3-number-input">Number:</label>
                        <input type="text" id="woocom-option3-number-input" class="large-text" name="woocom-option3-number-input" value="<?php echo esc_attr($api_option3_number_text_input); ?>" />
                </div> -->
            </div>
        </div>

        <div class="row d-flex mb-2" style="display:flex;">
            <div style="width: 20%;">
                <label for="woocom-payment-processing-message-input">Processing Payment Message:</label>
                <!-- <input type="checkbox" name="woocom-status-payment-processing" > -->
                <div class="row mb-2">
                    <!-- <label for="woocom-status-payment-processing">Status:</label> -->
                    <br>
                    <label class="switch">
                        <input type="checkbox" name="woocom-status-payment-processing" <?php if ($woocom_status_order_processing == 1) { ?>checked <?php } ?> value="1" id="">
                        <span class="slider round"></span>
                    </label>
                </div>
                <label for="woocom-status-payment-processing-invoice">Processing Invoice:</label>
                <div class="row mb-2">
                    <br>
                    <label class="switch">
                        <input type="checkbox" name="woocom-status-payment-processing-invoice" <?php if ($woocom_status_order_processing_invoice == 1) { ?>checked <?php } ?> value="1" id="">
                        <span class="slider round"></span>
                    </label>
                </div>

            </div>
            <div style="width: 50%;">
                <textarea type="text" cols="100" rows="12" id="woocom-payment-processing-message-input" class="large-text" name="woocom-payment-processing-message-input"><?php echo esc_textarea($woocom_order_processing_message_text_input); ?></textarea>
                <button class="insert-variable" data-target="woocom-payment-processing-message-input" data-variable="{order_full_name}">Full Name</button>
                <button class="insert-variable" data-target="woocom-payment-processing-message-input" data-variable="{order_id_product}">Product ID</button>
                <button class="insert-variable" data-target="woocom-payment-processing-message-input" data-variable="{order_date}">Order Date</button>
                <button class="insert-variable" data-target="woocom-payment-processing-message-input" data-variable="{order_subtotal}">Order Subtotal</button>
                <button class="insert-variable" data-target="woocom-payment-processing-message-input" data-variable="{order_payment_method}">Order Payment Method</button>
                <button class="insert-variable" data-target="woocom-payment-processing-message-input" data-variable="{order_shipping_method}">Order Shipping Method</button>
                <button class="insert-variable" data-target="woocom-payment-processing-message-input" data-variable="{order_total}">Order Total</button>
            </div>
            <div style="width: 30%;">
                <h3 class="mt-0">Usable Variables</h3>
                <p>All variables shown below will be replaced by their respective values before sending message.</p>
                <p>{order_full_name} : This will be replaced by Customers Full name.</p>
                <p>{order_id_product} : This will be replaced by Customers Product ID.</p>
                <p>{order_date} : This will be replaced by Customers order date.</p>
                <p>{order_subtotal} : This will be replaced by Customers order subtotal.</p>
                <p>{order_payment_method} : This will be replaced by Customers order payment method.</p>
                <p>{order_shipping_method} : This will be replaced by Customers order shipping method.</p>
                <p>{order_total} : This will be replaced by Customers order total.</p>
            </div>
        </div>

        <div class="row d-flex mb-2" style="display:flex;">
            <div style="width: 20%;">
                <label for="woocom-payment-complate-message-input">Complated Payment Message:</label>
                <!-- <input type="checkbox" name="woocom-status-payment-complate" > -->
                <div class="row mb-2">
                    <!-- <label for="woocom-status-payment-complate">Status:</label> -->
                    <br>
                    <label class="switch">
                        <input type="checkbox" name="woocom-status-payment-complate" <?php if ($woocom_status_order_complate == 1) { ?>checked <?php } ?> value="1" id="">
                        <span class="slider round"></span>
                    </label>
                </div>

            </div>
            <div style="width: 50%;">
                <textarea type="text" cols="100" rows="12" id="woocom-payment-complate-message-input" class="large-text" name="woocom-payment-complate-message-input" value="<?php echo esc_attr($woocom_order_complate_message_text_input); ?>"><?php echo esc_attr($woocom_order_complate_message_text_input); ?></textarea>
                <button class="insert-variable" data-target="woocom-payment-complate-message-input" data-variable="{order_full_name}">Full Name</button>
                <button class="insert-variable" data-target="woocom-payment-complate-message-input" data-variable="{order_id_product}">Product ID</button>
                <button class="insert-variable" data-target="woocom-payment-complate-message-input" data-variable="{order_date}">Order Date</button>
                <button class="insert-variable" data-target="woocom-payment-complate-message-input" data-variable="{order_subtotal}">Order Subtotal</button>
                <button class="insert-variable" data-target="woocom-payment-complate-message-input" data-variable="{order_payment_method}">Order Payment Method</button>
                <button class="insert-variable" data-target="woocom-payment-complate-message-input" data-variable="{order_shipping_method}">Order Shipping Method</button>
                <button class="insert-variable" data-target="woocom-payment-complate-message-input" data-variable="{order_total}">Order Total</button>
            </div>
            <div style="width: 30%;">
                <h3 class="mt-0">Usable Variables</h3>
                <p>All variables shown below will be replaced by their respective values before sending message.</p>
                <p>{order_full_name} : This will be replaced by Customers Full name.</p>
                <p>{order_id_product} : This will be replaced by Customers Product ID.</p>
                <p>{order_date} : This will be replaced by Customers order date.</p>
                <p>{order_subtotal} : This will be replaced by Customers order subtotal.</p>
                <p>{order_payment_method} : This will be replaced by Customers order payment method.</p>
                <p>{order_shipping_method} : This will be replaced by Customers order shipping method.</p>
                <p>{order_total} : This will be replaced by Customers order total.</p>
            </div>
        </div>

        <div class="row d-flex mb-2" style="display:flex;">
            <div style="width: 20%;">
                <label for="woocom-order-cancelled-message-input">Cancelled Order Message:</label>
                <!-- <input type="checkbox" name="woocom-status-order-cancelled" > -->
                <div class="row mb-2">
                    <!-- <label for="woocom-status-order-cancelled">Status:</label> -->
                    <br>
                    <label class="switch">
                        <input type="checkbox" name="woocom-status-order-cancelled" <?php if ($woocom_status_order_cancelled == 1) { ?>checked <?php } ?> value="1" id="">
                        <span class="slider round"></span>
                    </label>
                </div>

            </div>
            <div style="width: 50%;">
                <textarea type="text" cols="100" rows="12" id="woocom-order-cancelled-message-input" class="large-text" name="woocom-order-cancelled-message-input" value="<?php echo esc_attr($woocom_order_cancelled_message_text_input); ?>"><?php echo esc_attr($woocom_order_cancelled_message_text_input); ?></textarea>
                <button class="insert-variable" data-target="woocom-order-cancelled-message-input" data-variable="{order_full_name}">Full Name</button>
                <button class="insert-variable" data-target="woocom-order-cancelled-message-input" data-variable="{order_id_product}">Product ID</button>
                <button class="insert-variable" data-target="woocom-order-cancelled-message-input" data-variable="{order_date}">Order Date</button>
                <button class="insert-variable" data-target="woocom-order-cancelled-message-input" data-variable="{order_subtotal}">Order Subtotal</button>
                <button class="insert-variable" data-target="woocom-order-cancelled-message-input" data-variable="{order_payment_method}">Order Payment Method</button>
                <button class="insert-variable" data-target="woocom-order-cancelled-message-input" data-variable="{order_shipping_method}">Order Shipping Method</button>
                <button class="insert-variable" data-target="woocom-order-cancelled-message-input" data-variable="{order_total}">Order Total</button>
            </div>
            <div style="width: 30%;">
                <h3 class="mt-0">Usable Variables</h3>
                <p>All variables shown below will be replaced by their respective values before sending message.</p>
                <p>{order_full_name} : This will be replaced by Customers Full name.</p>
                <p>{order_id_product} : This will be replaced by Customers Product ID.</p>
                <p>{order_date} : This will be replaced by Customers order date.</p>
                <p>{order_subtotal} : This will be replaced by Customers order subtotal.</p>
                <p>{order_payment_method} : This will be replaced by Customers order payment method.</p>
                <p>{order_shipping_method} : This will be replaced by Customers order shipping method.</p>
                <p>{order_total} : This will be replaced by Customers order total.</p>
            </div>
        </div>

        <div class="row d-flex mb-2" style="display:flex;">
            <div style="width: 20%;">
                <label for="woocom-order-refunded-message-input">Refunded Order Message:</label>
                <!-- <input type="checkbox" name="woocom-status-order-refunded" > -->
                <div class="row mb-2">
                    <!-- <label for="woocom-status-order-refunded">Status:</label> -->
                    <br>
                    <label class="switch">
                        <input type="checkbox" name="woocom-status-order-refunded" <?php if ($woocom_status_order_refunded == 1) { ?>checked <?php } ?> value="1" id="">
                        <span class="slider round"></span>
                    </label>
                </div>

            </div>
            <div style="width: 50%;">
                <textarea type="text" cols="100" rows="12" id="woocom-order-refunded-message-input" class="large-text" name="woocom-order-refunded-message-input" value="<?php echo esc_attr($woocom_order_refunded_message_text_input); ?>"><?php echo esc_attr($woocom_order_refunded_message_text_input); ?></textarea>
                <button class="insert-variable" data-target="woocom-order-refunded-message-input" data-variable="{order_full_name}">Full Name</button>
                <button class="insert-variable" data-target="woocom-order-refunded-message-input" data-variable="{order_id_product}">Product ID</button>
                <button class="insert-variable" data-target="woocom-order-refunded-message-input" data-variable="{order_date}">Order Date</button>
                <button class="insert-variable" data-target="woocom-order-refunded-message-input" data-variable="{order_subtotal}">Order Subtotal</button>
                <button class="insert-variable" data-target="woocom-order-refunded-message-input" data-variable="{order_payment_method}">Order Payment Method</button>
                <button class="insert-variable" data-target="woocom-order-refunded-message-input" data-variable="{order_shipping_method}">Order Shipping Method</button>
                <button class="insert-variable" data-target="woocom-order-refunded-message-input" data-variable="{order_total}">Order Total</button>
            </div>
            <div style="width: 30%;">
                <h3 class="mt-0">Usable Variables</h3>
                <p>All variables shown below will be replaced by their respective values before sending message.</p>
                <p>{order_full_name} : This will be replaced by Customers Full name.</p>
                <p>{order_id_product} : This will be replaced by Customers Product ID.</p>
                <p>{order_date} : This will be replaced by Customers order date.</p>
                <p>{order_subtotal} : This will be replaced by Customers order subtotal.</p>
                <p>{order_payment_method} : This will be replaced by Customers order payment method.</p>
                <p>{order_shipping_method} : This will be replaced by Customers order shipping method.</p>
                <p>{order_total} : This will be replaced by Customers order total.</p>
            </div>
        </div>

        <div class="row d-flex mb-2" style="display:flex;">
            <div style="width: 20%;">
                <label for="woocom-order-onhold-message-input">onhold Order Message:</label>
                <!-- <input type="checkbox" name="woocom-status-order-onhold" > -->
                <div class="row mb-2">
                    <!-- <label for="woocom-status-order-onhold">Status:</label> -->
                    <br>
                    <label class="switch">
                        <input type="checkbox" name="woocom-status-order-onhold" <?php if ($woocom_status_order_onhold == 1) { ?>checked <?php } ?> value="1" id="">
                        <span class="slider round"></span>
                    </label>
                </div>

            </div>
            <div style="width: 50%;">
                <textarea type="text" cols="100" rows="12" id="woocom-order-onhold-message-input" class="large-text" name="woocom-order-onhold-message-input" value="<?php echo esc_attr($woocom_order_onhold_message_text_input); ?>"><?php echo esc_attr($woocom_order_onhold_message_text_input); ?></textarea>
                <button class="insert-variable" data-target="woocom-order-onhold-message-input" data-variable="{order_full_name}">Full Name</button>
                <button class="insert-variable" data-target="woocom-order-onhold-message-input" data-variable="{order_id_product}">Product ID</button>
                <button class="insert-variable" data-target="woocom-order-onhold-message-input" data-variable="{order_date}">Order Date</button>
                <button class="insert-variable" data-target="woocom-order-onhold-message-input" data-variable="{order_subtotal}">Order Subtotal</button>
                <button class="insert-variable" data-target="woocom-order-onhold-message-input" data-variable="{order_payment_method}">Order Payment Method</button>
                <button class="insert-variable" data-target="woocom-order-onhold-message-input" data-variable="{order_shipping_method}">Order Shipping Method</button>
                <button class="insert-variable" data-target="woocom-order-onhold-message-input" data-variable="{order_total}">Order Total</button>
            </div>
            <div style="width: 30%;">
                <h3 class="mt-0">Usable Variables</h3>
                <p>All variables shown below will be replaced by their respective values before sending message.</p>
                <p>{order_full_name} : This will be replaced by Customers Full name.</p>
                <p>{order_id_product} : This will be replaced by Customers Product ID.</p>
                <p>{order_date} : This will be replaced by Customers order date.</p>
                <p>{order_subtotal} : This will be replaced by Customers order subtotal.</p>
                <p>{order_payment_method} : This will be replaced by Customers order payment method.</p>
                <p>{order_shipping_method} : This will be replaced by Customers order shipping method.</p>
                <p>{order_total} : This will be replaced by Customers order total.</p>
            </div>
        </div>

        <div class="row d-flex mb-2" style="display:flex;">
            <div style="width: 20%;">
                <label for="woocom-order-pending-message-input">pending Order Message:</label>
                <!-- <input type="checkbox" name="woocom-status-order-pending" > -->
                <div class="row mb-2">
                    <!-- <label for="woocom-status-order-pending">Status:</label> -->
                    <br>
                    <label class="switch">
                        <input type="checkbox" name="woocom-status-order-pending" <?php if ($woocom_status_order_pending == 1) { ?>checked <?php } ?> value="1" id="">
                        <span class="slider round"></span>
                    </label>
                </div>

            </div>
            <div style="width: 50%;">
                <textarea type="text" cols="100" rows="12" id="woocom-order-pending-message-input" class="large-text" name="woocom-order-pending-message-input" value="<?php echo esc_attr($woocom_order_pending_message_text_input); ?>"><?php echo esc_attr($woocom_order_pending_message_text_input); ?></textarea>
                <button class="insert-variable" data-target="woocom-order-pending-message-input" data-variable="{order_full_name}">Full Name</button>
                <button class="insert-variable" data-target="woocom-order-pending-message-input" data-variable="{order_id_product}">Product ID</button>
                <button class="insert-variable" data-target="woocom-order-pending-message-input" data-variable="{order_date}">Order Date</button>
                <button class="insert-variable" data-target="woocom-order-pending-message-input" data-variable="{order_subtotal}">Order Subtotal</button>
                <button class="insert-variable" data-target="woocom-order-pending-message-input" data-variable="{order_payment_method}">Order Payment Method</button>
                <button class="insert-variable" data-target="woocom-order-pending-message-input" data-variable="{order_shipping_method}">Order Shipping Method</button>
                <button class="insert-variable" data-target="woocom-order-pending-message-input" data-variable="{order_total}">Order Total</button>
            </div>
            <div style="width: 30%;">
                <h3 class="mt-0">Usable Variables</h3>
                <p>All variables shown below will be replaced by their respective values before sending message.</p>
                <p>{order_full_name} : This will be replaced by Customers Full name.</p>
                <p>{order_id_product} : This will be replaced by Customers Product ID.</p>
                <p>{order_date} : This will be replaced by Customers order date.</p>
                <p>{order_subtotal} : This will be replaced by Customers order subtotal.</p>
                <p>{order_payment_method} : This will be replaced by Customers order payment method.</p>
                <p>{order_shipping_method} : This will be replaced by Customers order shipping method.</p>
                <p>{order_total} : This will be replaced by Customers order total.</p>
            </div>
        </div>

        <div class="row d-flex mb-2" style="display:flex;">
            <div style="width: 20%;">
                <label for="woocom-order-failed-message-input">failed Order Message:</label>
                <!-- <input type="checkbox" name="woocom-status-order-failed" > -->
                <div class="row mb-2">
                    <!-- <label for="woocom-status-order-failed">Status:</label> -->
                    <br>
                    <label class="switch">
                        <input type="checkbox" name="woocom-status-order-failed" <?php if ($woocom_status_order_failed == 1) { ?>checked <?php } ?> value="1" id="">
                        <span class="slider round"></span>
                    </label>
                </div>

            </div>
            <div style="width: 50%;">
                <textarea type="text" cols="100" rows="12" id="woocom-order-failed-message-input" class="large-text" name="woocom-order-failed-message-input" value="<?php echo esc_attr($woocom_order_failed_message_text_input); ?>"><?php echo esc_attr($woocom_order_failed_message_text_input); ?></textarea>
                <button class="insert-variable" data-target="woocom-order-failed-message-input" data-variable="{order_full_name}">Full Name</button>
                <button class="insert-variable" data-target="woocom-order-failed-message-input" data-variable="{order_id_product}">Product ID</button>
                <button class="insert-variable" data-target="woocom-order-failed-message-input" data-variable="{order_date}">Order Date</button>
                <button class="insert-variable" data-target="woocom-order-failed-message-input" data-variable="{order_subtotal}">Order Subtotal</button>
                <button class="insert-variable" data-target="woocom-order-failed-message-input" data-variable="{order_payment_method}">Order Payment Method</button>
                <button class="insert-variable" data-target="woocom-order-failed-message-input" data-variable="{order_shipping_method}">Order Shipping Method</button>
                <button class="insert-variable" data-target="woocom-order-failed-message-input" data-variable="{order_total}">Order Total</button>
            </div>
            <div style="width: 30%;">
                <h3 class="mt-0">Usable Variables</h3>
                <p>All variables shown below will be replaced by their respective values before sending message.</p>
                <p>{order_full_name} : This will be replaced by Customers Full name.</p>
                <p>{order_id_product} : This will be replaced by Customers Product ID.</p>
                <p>{order_date} : This will be replaced by Customers order date.</p>
                <p>{order_subtotal} : This will be replaced by Customers order subtotal.</p>
                <p>{order_payment_method} : This will be replaced by Customers order payment method.</p>
                <p>{order_shipping_method} : This will be replaced by Customers order shipping method.</p>
                <p>{order_total} : This will be replaced by Customers order total.</p>
            </div>
        </div>

    </div>

    <script>
        // JavaScript to show content based on the selected option
        document.addEventListener('DOMContentLoaded', function() {
            const optionSelect = document.getElementById('woocom-option-select');
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const insertButtons = document.querySelectorAll('.insert-variable');

            insertButtons.forEach(function(button) {
                button.addEventListener('click', function(event) {
                    event.preventDefault(); // Prevent default form submission or link following

                    const targetId = this.getAttribute('data-target');
                    const targetTextarea = document.getElementById(targetId);

                    if (!targetTextarea) {
                        console.error('Textarea element not found.');
                        return;
                    }

                    const variable = this.getAttribute('data-variable');
                    const selectionStart = targetTextarea.selectionStart || 0; // Use 0 if selectionStart is undefined or null
                    const selectionEnd = targetTextarea.selectionEnd || 0; // Use 0 if selectionEnd is undefined or null
                    const currentValue = targetTextarea.value;

                    const newValue =
                        currentValue.substring(0, selectionStart) +
                        variable +
                        currentValue.substring(selectionEnd);

                    targetTextarea.value = newValue;
                    targetTextarea.setSelectionRange(selectionStart + variable.length, selectionStart + variable.length);

                    // Optionally, you can add a confirmation message
                    // alert('Variable inserted: ' + variable);
                });
            });
        });
    </script>
<?php
}
add_action('woocommerce_sections_syncmate_tab', 'smassistro_tab_content');



// Save custom text input data to the database
function smassistro_save_woocom_key_text_input_data_to_db($post_id)
{
    if (isset($_POST['nonce_field']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce_field'])), 'smassistro_save_woocom_data')) {
        $post_id = 1;
        if (isset($_POST['woocom-key-textarea'])) {
            $woocom_key_text_input = sanitize_text_field($_POST['woocom-key-textarea']);
            smassistro_insert_or_update_data_into_custom_table($post_id, 'woocom_key_text_input', $woocom_key_text_input);
        }
        if (isset($_POST['woocom-option-select'])) {
            $woocom_option_select = sanitize_text_field($_POST['woocom-option-select']);
            smassistro_insert_or_update_data_into_custom_table($post_id, 'woocom_option_select', $woocom_option_select);
            // print_r($change);
            // die;
        }
        if (isset($_POST['woocom-country-code-select'])) {
            $woocom_country_code_text_select = sanitize_text_field($_POST['woocom-country-code-select']);
            smassistro_insert_or_update_data_into_custom_table($post_id, 'woocom_country_code_text_select', $woocom_country_code_text_select);
            // print_r(smassistro_insert_or_update_data_into_custom_table($post_id, 'woocom_country_code_text_select', $woocom_country_code_text_select));
            // die;
        }
        if (isset($_POST['woocom-country-code-input'])) {
            $woocom_country_code_text_input = sanitize_text_field($_POST['woocom-country-code-input']);
            smassistro_insert_or_update_data_into_custom_table($post_id, 'woocom_country_code_text_input', $woocom_country_code_text_input);
        }
        if (isset($_POST['woocom-option1-number-input'])) {
            $woocom_option1_number_text_input = sanitize_text_field($_POST['woocom-option1-number-input']);
            smassistro_insert_or_update_data_into_custom_table($post_id, 'woocom_option1_number_text_input', $woocom_option1_number_text_input);
        }
        if (isset($_POST['woocom-option2-number-input'])) {
            $woocom_option2_number_text_input = sanitize_text_field($_POST['woocom-option2-number-input']);
            smassistro_insert_or_update_data_into_custom_table($post_id, 'woocom_option2_number_text_input', $woocom_option2_number_text_input);
        }
        if (isset($_POST['woocom-option3-number-input'])) {
            $woocom_option3_number_text_input = sanitize_text_field($_POST['woocom-option3-number-input']);
            smassistro_insert_or_update_data_into_custom_table($post_id, 'woocom_option3_number_text_input', $woocom_option3_number_text_input);
        }

        if (isset($_POST['woocom-status-payment-processing']) && $_POST['woocom-status-payment-processing'] === '1') {
            smassistro_insert_or_update_data_into_custom_table($post_id, 'woocom_status_order_processing', 1);
        } else {
            smassistro_insert_or_update_data_into_custom_table($post_id, 'woocom_status_order_processing', 0);
        }
        if (isset($_POST['woocom-payment-processing-message-input'])) {


            $woocom_order_processing_message_text_input = htmlspecialchars($_POST['woocom-payment-processing-message-input']);


            if (smassistro_are_curly_braces_balanced($woocom_order_processing_message_text_input)) {
                smassistro_insert_or_update_data_into_custom_table($post_id, 'woocom_order_processing_message_text_input', $woocom_order_processing_message_text_input);
            } else {
                echo '<div class="notice notice-error"><p>Curly braces are not balanced.</p></div>';
            }
        }

        if (isset($_POST['woocom-status-payment-processing-invoice']) && $_POST['woocom-status-payment-processing-invoice'] === '1') {
            smassistro_insert_or_update_data_into_custom_table($post_id, 'woocom_status_order_processing_invoice', 1);
        } else {
            smassistro_insert_or_update_data_into_custom_table($post_id, 'woocom_status_order_processing_invoice', 0);
        }

        if (isset($_POST['woocom-status-payment-complate']) && $_POST['woocom-status-payment-complate'] === '1') {
            smassistro_insert_or_update_data_into_custom_table($post_id, 'woocom_status_order_complate', 1);
        } else {
            smassistro_insert_or_update_data_into_custom_table($post_id, 'woocom_status_order_complate', 0);
        }
        if (isset($_POST['woocom-payment-complate-message-input'])) {

            $woocom_order_complate_message_text_input = htmlspecialchars($_POST['woocom-payment-complate-message-input']);

            if (smassistro_are_curly_braces_balanced($woocom_order_complate_message_text_input)) {
                smassistro_insert_or_update_data_into_custom_table($post_id, 'woocom_order_complate_message_text_input', $woocom_order_complate_message_text_input);
            } else {
                echo '<div class="notice notice-error"><p>Curly braces are not balanced.</p></div>';
            }
        }

        if (isset($_POST['woocom-status-order-cancelled']) && $_POST['woocom-status-order-cancelled'] === '1') {
            smassistro_insert_or_update_data_into_custom_table($post_id, 'woocom_status_order_cancelled', 1);
        } else {
            smassistro_insert_or_update_data_into_custom_table($post_id, 'woocom_status_order_cancelled', 0);
        }
        if (isset($_POST['woocom-order-cancelled-message-input'])) {

            $woocom_order_cancelled_message_text_input = htmlspecialchars($_POST['woocom-order-cancelled-message-input']);

            if (smassistro_are_curly_braces_balanced($woocom_order_cancelled_message_text_input)) {
                smassistro_insert_or_update_data_into_custom_table($post_id, 'woocom_order_cancelled_message_text_input', $woocom_order_cancelled_message_text_input);
            } else {
                echo '<div class="notice notice-error"><p>Curly braces are not balanced.</p></div>';
            }
        }

        if (isset($_POST['woocom-status-order-refunded']) && $_POST['woocom-status-order-refunded'] === '1') {
            smassistro_insert_or_update_data_into_custom_table($post_id, 'woocom_status_order_refunded', 1);
        } else {
            smassistro_insert_or_update_data_into_custom_table($post_id, 'woocom_status_order_refunded', 0);
        }
        if (isset($_POST['woocom-order-refunded-message-input'])) {

            $woocom_order_refunded_message_text_input = htmlspecialchars($_POST['woocom-order-refunded-message-input']);

            if (smassistro_are_curly_braces_balanced($woocom_order_refunded_message_text_input)) {
                smassistro_insert_or_update_data_into_custom_table($post_id, 'woocom_order_refunded_message_text_input', $woocom_order_refunded_message_text_input);
            } else {
                echo '<div class="notice notice-error"><p>Curly braces are not balanced.</p></div>';
            }
        }

        if (isset($_POST['woocom-status-order-onhold']) && $_POST['woocom-status-order-onhold'] === '1') {
            smassistro_insert_or_update_data_into_custom_table($post_id, 'woocom_status_order_onhold', 1);
        } else {
            smassistro_insert_or_update_data_into_custom_table($post_id, 'woocom_status_order_onhold', 0);
        }
        if (isset($_POST['woocom-order-onhold-message-input'])) {

            $woocom_order_onhold_message_text_input = htmlspecialchars($_POST['woocom-order-onhold-message-input']);

            if (smassistro_are_curly_braces_balanced($woocom_order_onhold_message_text_input)) {
                smassistro_insert_or_update_data_into_custom_table($post_id, 'woocom_order_onhold_message_text_input', $woocom_order_onhold_message_text_input);
            } else {
                echo '<div class="notice notice-error"><p>Curly braces are not balanced.</p></div>';
            }
        }

        if (isset($_POST['woocom-status-order-pending']) && $_POST['woocom-status-order-pending'] === '1') {
            smassistro_insert_or_update_data_into_custom_table($post_id, 'woocom_status_order_pending', 1);
        } else {
            smassistro_insert_or_update_data_into_custom_table($post_id, 'woocom_status_order_pending', 0);
        }
        if (isset($_POST['woocom-order-pending-message-input'])) {

            $woocom_order_pending_message_text_input = htmlspecialchars($_POST['woocom-order-pending-message-input']);

            if (smassistro_are_curly_braces_balanced($woocom_order_pending_message_text_input)) {
                smassistro_insert_or_update_data_into_custom_table($post_id, 'woocom_order_pending_message_text_input', $woocom_order_pending_message_text_input);
            } else {
                echo '<div class="notice notice-error"><p>Curly braces are not balanced.</p></div>';
            }
        }

        if (isset($_POST['woocom-status-order-failed']) && $_POST['woocom-status-order-failed'] === '1') {
            smassistro_insert_or_update_data_into_custom_table($post_id, 'woocom_status_order_failed', 1);
        } else {
            smassistro_insert_or_update_data_into_custom_table($post_id, 'woocom_status_order_failed', 0);
        }
        if (isset($_POST['woocom-order-failed-message-input'])) {

            $woocom_order_failed_message_text_input = htmlspecialchars($_POST['woocom-order-failed-message-input']);

            if (smassistro_are_curly_braces_balanced($woocom_order_failed_message_text_input)) {
                smassistro_insert_or_update_data_into_custom_table($post_id, 'woocom_order_failed_message_text_input', $woocom_order_failed_message_text_input);
            } else {
                echo '<div class="notice notice-error"><p>Curly braces are not balanced.</p></div>';
            }
        }
        echo '<div class="notice notice-success"><p>Data saved successfully!</p></div>';
    } else {
        // Nonce verification failed, do not process the form data
        echo '<div class="notice notice-error"><p>Nonce verification failed. Form data not processed.</p></div>';
    }
}
add_action('woocommerce_settings_saved', 'smassistro_save_woocom_key_text_input_data_to_db');

function smassistro_are_curly_braces_balanced($string)
{
    // Remove HTML tags from the string
    $stringWithoutTags = strip_tags($string);

    $stack = [];

    // Iterate through each character in the string without HTML tags
    for ($i = 0; $i < strlen($stringWithoutTags); $i++) {
        $char = $stringWithoutTags[$i];

        if ($char == '{') {
            // Push an opening brace onto the stack
            array_push($stack, $char);
        } elseif ($char == '}') {
            // If a closing brace is encountered and the stack is empty, braces are not balanced
            // Otherwise, pop the top element from the stack
            if (empty($stack)) {
                return false;
            }
            array_pop($stack);
        }
    }

    // If the stack is empty at the end, braces are balanced
    return empty($stack);
}

?>