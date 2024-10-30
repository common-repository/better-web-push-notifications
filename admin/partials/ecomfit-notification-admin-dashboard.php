<div class="ecomfit-wrapper ecomfit-wrapper-manager">
    <div class="ecomfit-container">
        <div class="ecomfit-header">
            <a class="ecomfit-img-wrapper" target="_blank" href="https://ecomfit.com">
                <img src="<?php echo plugins_url('images/ecomfit.jpg', plugin_dir_path(__FILE__)); ?>"/>
            </a>
        </div>

        <div class="ecomfit-content ecomfit-text-center">
            <div class="ecomfit-title">
                Hi
                <?php
                global $current_user;
                get_currentuserinfo();
                echo $current_user->user_login;
                ?>, Welcome To Better Web Push Notification
            </div>
            <p>
                A simple and effective way to recover abandoned carts and bring back customer to your sites.
            </p>
            <p>
                <a class="ecomfit-btn ecomfit-btn-primary" target="_blank"
                   href="<?php echo ECOMFIT_NOTIFICATION_APP_URL . '/app/' . ECOMFIT_NOTIFICATION_APP_TYPE; ?>">
                    Start your campaign to boost conversion rate now
                </a>
            </p>
            <div class="ecomfit-support">
                <div>Need our support? Let us know, we'll be back shortly!</div>
                <a class="" target="_blank" style="text-decoration: underline;"
                   href="https://ecomfit.com/contact-us.html">
                    Contact Us
                </a>
            </div>
        </div>

        <!-- Footer -->
        <footer class="ecomfit-footer">
            <div class="ecomfit-float-left">
                <a class="ecomfit-img-wrapper" target="_blank" href="https://ecomfit.com">
                    <img src="<?php echo plugins_url('images/ecomfit.jpg', plugin_dir_path(__FILE__)); ?>"/>
                </a>
                Copyright Ecomfit © 2018 - The Simplest eCommerce Analytics Platform
            </div>
            <div class="ecomfit-float-right">
                <div class="social text-center text-lg-right">
                    <a class="social-facebook" href="https://www.facebook.com/ecomfit/" target="_blank"><i
                                class="fa fa-facebook-square" width="60px"></i>Facebook</a>
                    &emsp;
                    <a class="social-twitter" target="_blank" href="https://twitter.com/ecomfit"><i
                                class="fa fa-twitter-square"></i>Twitter</a>
                </div>
            </div>
        </footer>
        <!-- END Footer -->
    </div>
</div>
