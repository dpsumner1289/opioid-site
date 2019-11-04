<?php
// passwordless user creation/authentication
if(!function_exists('handle_authentication')) {
    function handle_authentication() {
        // get submitted email and default global pass
        $email = filter_var($_POST['otf_user_email'], FILTER_SANITIZE_EMAIL);
        $md5pass = filter_var($_POST['otf'], FILTER_SANITIZE_STRING);
        $email = trim($email);
        $md5pass = trim($md5pass);
        // changed for security purposes for Github
        $global_pass = 'XXXXXXXX';
        // check for password variation
        if(!empty($email) && md5($global_pass) === $md5pass) {
            $username = $email;
            $pass = $global_pass;
            // if email doesn't exists, create user
            if(!email_exists($email)) {
                $user_id = wp_create_user($username, $pass, $email);
                $user = new WP_User($user_id);
                $user->set_role('subscriber');
                // autologin for new user
                $creds = array(
                    'user_login'    => $username,
                    'user_password' => $global_pass,
                    // 'remember'      => $remember,
                );
                $autologin_user = get_user_by('email', $email);
                do_action('wp_login', $autologin_user->user_login, $autologin_user);
                wp_set_current_user( $autologin_user->ID );
                wp_set_auth_cookie( $autologin_user->ID );
                $redirect_to = $_SERVER['REQUEST_URI'];
                wp_safe_redirect($redirect_to);
                exit;
            } 
            // if email DOES exist, log user in
            else {
                $user = get_user_by('email', $email);
                do_action('wp_login', $user->user_login, $user);
                wp_set_current_user( $user->ID );
                wp_set_auth_cookie( $user->ID );
                $redirect_to = $_SERVER['REQUEST_URI'];
                wp_safe_redirect($redirect_to);
                exit;
            }
        }
    }
}