<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }
    $api_key_pushplus_text_input = smassistro_get_data_by_key_value(1, 'woocom_pushplus_key_text_input');
    $api_key_push_text_input = smassistro_get_data_by_key_value(1, 'woocom_key_push_text_input');
    $woocom_status_both_toggle_input = smassistro_get_data_by_key_value(1, 'woocom_status_both_toggle_input');
    $woocom_status_both_toggle_input=$woocom_status_both_toggle_input;       
      
?>
<div class="wrap border-bottom-grey">
    <h3>WooCommerce</h3>
</div>


<?php
$success_plus = false;
if (isset($_POST['submit_push_plus'])) {
    // Verify nonce
    if (isset($_POST['submit_push_plus_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['submit_push_plus_nonce'])), 'submit_push_plus_action')) {
        // echo $_POST['assistro-status-both'];
        // die;
    
        $woocom_pushplus_key_text_input = sanitize_text_field($_POST['assistro-key-push-plus']);
        smassistro_insert_or_update_data_into_custom_table(1, 'woocom_pushplus_key_text_input', $woocom_pushplus_key_text_input);
        
        if (isset($_POST['assistro-status-both']) && $_POST['assistro-status-both']==true) {
            smassistro_insert_or_update_data_into_custom_table(1, 'woocom_status_both_toggle_input', 'wapushplus');
        }
        
        $success_plus = true; // Set the success variable to true

    } else {
        // Nonce verification failed
        // Handle the error or display a message
        echo '<div class="error-message">Nonce verification failed for submit_push_plus action!</div>';
    }
}

if ($success_plus) {
    echo '<div class="success-message">Thank you for your submission!</div>';
    echo '<script>setTimeout(function() { window.location.href = window.location.href; }, 2000);</script>';
}

?>



<?php
$success_push = false;
if (isset($_POST['submit_push'])) {
    // Verify nonce
    if (isset($_POST['submit_push_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['submit_push_nonce'])), 'submit_push_action')) {
        $woocom_key_push_text_input = sanitize_text_field($_POST['assistro-key-push']);
        smassistro_insert_or_update_data_into_custom_table(1, 'woocom_key_push_text_input', $woocom_key_push_text_input);
        
        if (isset($_POST['assistro-status-both']) && $_POST['assistro-status-both']==true) {
            smassistro_insert_or_update_data_into_custom_table(1, 'woocom_status_both_toggle_input', 'wapush');
        }
    
        $success_push = true; // Set the success_push variable to true
    } else {
        // Nonce verification failed
        // Handle the error or display a message
        echo '<div class="error-message">Nonce verification failed for submit_push action!</div>';
    }

}

if ($success_push) {
    echo '<div class="success-message">Thank you for your submission!</div>';
    echo '<script>setTimeout(function() { window.location.href = window.location.href; }, 2000);</script>';
}

?>


<div class="wrap">
    <div id="custom-tabs">
        <ul class="tabs">
            <li><a href="#tab-1">SyncMate X WAPushPlus</a></li>
            <li><a href="#tab-2">SyncMate X WAPush</a></li>
        </ul>
        <div id="tab-1" style="display: block;">
            <!-- <h2>SyncMate X WAPushPlus</h2> -->
            <div class="tabs" style="justify-content: space-between;align-items:center;">
                <h2>SyncMate X WAPushPlus</h2>
                <?php 
                    if($api_key_pushplus_text_input!=null){
                ?>
                <form action="" method="POST">
                    <input type="submit" class="button-primary" name="testConnectionButton" id="testConnectionButton" value="Test Connection">
                </form>
                <?php
                } ?>
            </div>
            <p class="desc">Delight your customers via Whatsapp notifications on every step of their buying journey. Be it placing an order or a final stage delivery. The days are gone when you used to rely on SMS.</p>
            
            <form method="post" action="">
                <?php wp_nonce_field('submit_push_plus_action', 'submit_push_plus_nonce'); ?>
                <div class="row mb-1">
                    <label for="assistro-key-push-plus">Api key <a href="https://assistro.co/user-guide/connect-your-woocommerce-app-with-wapushplus?utm_source=wordpress-plugin-syncmate&utm_medium=help&utm_campaign=user-guide" target="_blank">(Help)</a></label>
                    <textarea cols="100" rows="5" class="large-text mt-1" id="assistro-key-push-plus" name="assistro-key-push-plus"><?php echo esc_textarea($api_key_pushplus_text_input); ?></textarea>
                </div>


                <div class="row mb-1">
                    <label for="assistro-status-both ">Enable </label>
                    <br>
                    <label class="switch mt-1">
                        <input type="checkbox" name="assistro-status-both" <?php if($woocom_status_both_toggle_input == 'wapushplus') { ?> checked <?php } ?> >
                        <span class="slider round"></span>
                    </label>
                </div>
            
                <input type="submit" class="button-primary" name="submit_push_plus" value="Submit">
            </form>

           
        </div>
        <div id="tab-2" style="display: none;">
            <h2>SyncMate X WAPush</h2>
            <p>Delight your customers via Whatsapp notifications on every step of their buying journey. Be it placing an order or a final stage delivery. The days are gone when you used to rely on SMS.</p>
            
            <form method="post" action="">
                <!-- Add nonce field for submit_push -->
                <?php wp_nonce_field('submit_push_action', 'submit_push_nonce'); ?>
                <div class="row mb-1">
                    <label for="assistro-key-push">Api key <a href="https://assistro.co/user-guide/connect-your-woocommerce-app-with-wapush?utm_source=wordpress-plugin-syncmate&utm_medium=help&utm_campaign=user-guide" target="_blank">(Help)</a></label>
                    <textarea cols="100" rows="5" class="large-text mt-1" id="assistro-key-push" name="assistro-key-push"><?php echo esc_textarea($api_key_push_text_input); ?></textarea>
                </div>

                <div class="row mb-1">
                    <label for="assistro-status-both">Enable</label>
                    <?php $woocom_status_both_toggle_input ?>
                    <br>
                    <label class="switch mt-1">
                    <input type="checkbox" name="assistro-status-both" <?php if($woocom_status_both_toggle_input == 'wapush') { ?>checked <?php } ?> >
                        <span class="slider round"></span>
                    </label>
                </div>
            
                <input type="submit" class="button-primary" name="submit_push" value="Submit">
            </form>

            
        </div>
    </div>
</div>



