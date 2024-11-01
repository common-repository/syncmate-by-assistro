<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }
    $api_key_pushplus_text_input = smassistro_get_data_by_key_value(1, 'cf7_pushplus_key_text_input');
    $api_key_push_text_input = smassistro_get_data_by_key_value(1, 'cf7_key_push_text_input');
    $cf7_status_both_toggle_input = smassistro_get_data_by_key_value(1, 'cf7_status_both_toggle_input');
    $cf7_status_both_toggle_input=$cf7_status_both_toggle_input;
      
?>
<div class="wrap border-bottom-grey">
    <h3>Contact Form 7</h3>
</div>


<?php
$success_plus = false;
if ( isset( $_POST['submit_push_plus'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['push_plus_nonce'] ) ), 'push_plus_nonce' ) ) {
    $cf7_pushplus_key_text_input = sanitize_text_field($_POST['assistro-key-push-plus']);
    smassistro_insert_or_update_data_into_custom_table(1, 'cf7_pushplus_key_text_input', $cf7_pushplus_key_text_input);
   
    if (isset($_POST['assistro-status-both']) && $_POST['assistro-status-both']==true) {
        smassistro_insert_or_update_data_into_custom_table(1, 'cf7_status_both_toggle_input', 'wapushplus');
    }
    
    
   
    $success_plus = true; // Set the success variable to true

    // Capture the current page URL
    // $current_page_url = $_SERVER['REQUEST_URI'];
}

if ($success_plus) {
    echo '<div class="success-message">Thank you for your submission!</div>';
    echo '<script>setTimeout(function() { window.location.href = window.location.href; }, 2000);</script>';
    // Redirect back to the same page
    // wp_redirect(home_url($current_page_url));
    // exit;
}

?>



<?php
$success_push = false;
if ( isset( $_POST['submit_push'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['push_nonce'] ) ), 'push_nonce' ) ) {
    $cf7_key_push_text_input = sanitize_text_field($_POST['assistro-key-push']);
    smassistro_insert_or_update_data_into_custom_table(1, 'cf7_key_push_text_input', $cf7_key_push_text_input);
   
    if (isset($_POST['assistro-status-both']) && $_POST['assistro-status-both']==true) {
        smassistro_insert_or_update_data_into_custom_table(1, 'cf7_status_both_toggle_input', 'wapush');
    }    
   
    $success_push = true; // Set the success_push variable to true

}

if ($success_push) {
    echo '<div class="success-message">Thank you for your submission!</div>';
    echo '<script>setTimeout(function() { window.location.href = window.location.href; }, 2000);</script>';
}



?>

<?php
    $api_key_pushplus_text_input = smassistro_get_data_by_key_value(1, 'cf7_pushplus_key_text_input');
?>




<div class="wrap">
    <div id="custom-tabs">
        <ul class="tabs">
            <li><a href="#tab-1">SyncMate X WAPushPlus</a></li>
            <li><a href="#tab-2">SyncMate X WAPush</a></li>
        </ul>
        <div id="tab-1" style="display: block;">
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
            <!-- <button class="button-primary" id="testConnection">Test Connection</button> -->
            <p class="desc">Supercharge your website with our add-on and delight your customer with a feedback via Whatsapp message whenever they fill-up your form with Zero code.</p>
            
            <form method="post" action="">
                <?php wp_nonce_field('push_plus_nonce', 'push_plus_nonce'); ?>
                <div class="row mb-1">
                    <label for="assistro-key-push-plus">Api key <a href="https://assistro.co/user-guide/connect-your-contact-form-7-app-with-wapushplus?utm_source=wordpress-plugin-syncmate&utm_medium=help&utm_campaign=user-guide" target="_blank">(Help)</a></label>
                    <textarea cols="100" rows="5" class="large-text mt-1" id="assistro-key-push-plus" name="assistro-key-push-plus"><?php echo esc_textarea($api_key_pushplus_text_input); ?></textarea>
                </div>

                <div class="row mb-1">
                    <label for="assistro-status-both ">Enable</label>
                    <br>
                    <label class="switch mt-1">
                        <input type="checkbox" name="assistro-status-both" <?php if($cf7_status_both_toggle_input == 'wapushplus') { ?> checked <?php } ?> >
                        <span class="slider round"></span>
                    </label>
                </div>

            
                <input type="submit" class="button-primary" name="submit_push_plus" value="Submit">
            </form>

           
        </div>
        <div id="tab-2" style="display: none;">
            <h2>SyncMate X WAPush</h2>
            <p>Supercharge your website with our add-on and delight your customer with a feedback via Whatsapp message whenever they fill-up your form with Zero code.</p>
            
            <form method="post" action="">
                <?php wp_nonce_field('push_nonce', 'push_nonce'); ?>
                <div class="row mb-1">
                    <label for="assistro-key-push">Api key <a href="https://assistro.co/user-guide/connect-your-contact-form-7-app-with-wapush?utm_source=wordpress-plugin-syncmate&utm_medium=help&utm_campaign=user-guide" target="_blank">(Help)</a></label>
                    <textarea cols="100" rows="5" class="large-text mt-1" id="assistro-key-push" name="assistro-key-push"><?php echo esc_textarea($api_key_push_text_input); ?></textarea>
                </div>

                <div class="row mb-1">
                    <label for="assistro-status-both">Enable</label>
                    <?php $cf7_status_both_toggle_input ?>
                    <br>
                    <label class="switch mt-1">
                    <input type="checkbox" name="assistro-status-both" <?php if($cf7_status_both_toggle_input == 'wapush') { ?>checked <?php } ?> >
                        <span class="slider round"></span>
                    </label>
                </div>
            
                <input type="submit" class="button-primary" name="submit_push" value="Submit">
            </form>

           
            
        </div>
    </div>
</div>



