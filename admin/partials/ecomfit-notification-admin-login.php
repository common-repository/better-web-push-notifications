<div class="ecomfit-wrapper ecomfit-wrapper-login">
    <div class="ecomfit-container">
        <div class="ecomfit-header ecomfit-text-center">
            <div class="ecomfit-img-wrapper">
                <img src="<?php echo plugins_url('images/ecomfit.jpg', plugin_dir_path(__FILE__)); ?>"/>
            </div>
        </div>
        <div class="ecomfit-content ecomfit-text-center">
            <div class="ecomfit-title ecomfit-text-white">
                Hi <?php global $current_user;
                get_currentuserinfo();
                echo $current_user->user_login; ?>, Welcome to Ecomfit!
            </div>
            <p class="ecomfit-text-white ecomfit-text-bold" style="font-size: 1.2em;">
                Let's connect your online stores and join with over 50,000+ merchants are
                using Ecomfit to skyrocket their sales right now!
            </p>
            <br><br>
            <button data-url="<?php echo ECOMFIT_NOTIFICATION_APP_URL . '/platform/wordpress/login?domain=' . $_SERVER['SERVER_NAME'] . '&url=' . get_home_url() . '&webId=' . get_option('_ecomfit_web_id') . '&ecfApp=' . ECOMFIT_NOTIFICATION_APP_TYPE; ?>"
                    class="ecomfit-btn ecomfit-btn-lg ecomfit-btn-primary ecomfit-notification-btn-login">
                <?php
                echo get_option('_ecomfit_notification_login') ? "Let Connect Again" : "Let Connect Now";
                ?>
            </button>
            <p class="ecomfit-text-white">
                No credit cards required. Integration takes seconds.
            </p>
        </div>
    </div>
</div>
