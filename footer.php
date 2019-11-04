<?php
$md5pass = md5('o7fus3rp455');
$userID = get_current_user_id();
$blurb = get_option('options_blurb');
$menu = get_option('options_menu');
$button_link = get_option('options_button_link');
$button_label = get_option('options_button_label');
if(!is_page_template('templates/resources.php')) {
    $is_resources = 'false';
}else {
    $is_resources = 'true';
}
?>
</main><!-- /.site-main -->
<footer class="site-footer">
    <div class="container container-lg flex row afs jfsb">
        <div class="item_500 flex col left">
            <div class="flex row afsb jfs">
                <?php echo !empty($blurb) ? '<div class="item_1_2 blurb">'.$blurb.'</div>' : ''; ?>
                <?php echo !empty($button_label) ? '<a href="'.$button_link.'" class="button wtg">'.$button_label.'<i class="fal fa-arrow-right"></i></a>' : ''; ?>
            </div>
        </div>
        <div class="item_500 flex row right">
        <?php for($i = 0; $i < $menu; $i++) {
            $mkey = 'options_menu_'.$i.'_';
            $mitem = get_option($mkey.'menu_item');
            echo '<div class="footer-menu item_1_2 flex col nobreak">';
            for($j = 0; $j < $mitem; $j++) {
                $ikey = $mkey.'menu_item_'.$j.'_';
                $item_label = get_option($ikey.'item_label');
                $item_link = get_option($ikey.'item_link');
                $item_id = get_option($ikey.'custom_id');
                if(!empty($item_id)) {
                    $item_id = ' id="'.$item_id.'" ';
                }else {
                    $item_id = '';
                }
                echo '<a href="'.$item_link.'" class="hidden-arrow"'.$item_id.'>'.$item_label.'<i class="fal fa-arrow-right"></i></a>';
            }
            echo '</div>';
        } ?>
        </div>
        <div class="copy flex item_full">Copyright <?php echo date('Y'); ?> &nbsp;<a href="https://chimecentral.org/" target="_blank">College of Healthcare Information Management Executives</a></div>
    </div>
</footer><!-- /.site-footer -->
<div class="saved-searches"></div>
<div class="contact-form">
    <div class="container container-xsm flex col afc jfc">
    <a id="close-popup" href="#"><i class="fas fa-times"></i></a>
        <h2>Contact</h2>
        <?php
        $contact_form = strip_tags(get_option('options_popup_contact_form'));
        $contact_blurb = get_option('options_contact_blurb');
        $contact_blurb = apply_filters('the_content', $contact_blurb);
        if(!empty($contact_blurb)) {
            echo '<div class="contact-blurb">'.$contact_blurb.'</div>';
        }
        if(!empty($contact_form)){
            echo do_shortcode($contact_form);
        }
        ?>
    </div>
</div>
<?php if(is_user_logged_in()){ ?>
    <script>
    jQuery(document).ready(function($) {
        // maybe set cookies for guide
        function bakeCookies() {
            var guideSense = Cookies.get('guide');
            if(!guideSense) {
                $(document).on('click', 'a.close-guide', function(e) {
                    e.preventDefault();
                    $('div.guide').hide();
                    Cookies.set('guide', 'seen', {expires:365});
                });
            }else {
                $('div.guide').hide();
            }
        }
        bakeCookies();
        
        // show saved searches in a popup
        function show_searches() {
            $.ajaxSetup({ cache: false });
            var searches = $('div.saved-searches');
            var main = $('main.site-main');
            var dataSave = {
                action : 'otf_retrieve_searches',
                userID : <?php echo $userID; ?>,
            }
            $.post("<?php echo admin_url('admin-ajax.php'); ?>", dataSave, function(response){
                searches.html(response).css('z-index', '999').fadeIn(200).addClass('showing').removeClass('deleting');
                main.addClass('covered');
            });
        }

        // show saved searches in a popup
        function show_subscriptions() {
            $.ajaxSetup({ cache: false });
            var searches = $('div.saved-searches');
            var main = $('main.site-main');
            var dataSave = {
                action : 'otf_retrieve_subscriptions',
                userID : <?php echo $userID; ?>,
            }
            $.post("<?php echo admin_url('admin-ajax.php'); ?>", dataSave, function(response){
                searches.html(response).css('z-index', '999').fadeIn(200).addClass('showing').removeClass('deleting');
                main.addClass('covered');
            });
        }

        // delete saved searches
        function delete_search(umeta, search) {
            var dataDelete = {
                action: 'otf_delete_searches',
                umeta: umeta,
            }
            $.post("<?php echo admin_url('admin-ajax.php'); ?>", dataDelete, function(response){
                search.fadeOut(900);
                $('.searches-wrapper').append(response);
                $('.save-confirmation').fadeOut(1500);
            });
        }

        // delete subscriptions
        function delete_subscription(umeta, search) {
            var dataDelete = {
                action: 'otf_delete_subscriptions',
                umeta: umeta,
            }
            $.post("<?php echo admin_url('admin-ajax.php'); ?>", dataDelete, function(response){
                search.fadeOut(900);
                $('.searches-wrapper').append(response);
                $('.save-confirmation').fadeOut(1500);
            });
        }

        // delete all user data
        function delete_data() {
            var deleteAll = {
                action : 'otf_delete_all',
            }
            $.post("<?php echo admin_url('admin-ajax.php'); ?>", deleteAll, function(response) {
                $('.searches-wrapper').append(response);
                $('main.site-main').addClass('covered');
                $('.save-confirmation').fadeOut(1500);
            });
        }

        // recall saved search
        function recall_saved(keys, isResources) {
            if(isResources) {
                let container = $('div.saved-searches.showing');
                container.hide().css('z-index', '-9').removeClass('showing');
            }else {
                Cookies.set('search_carryover', keys, {expires:1}); 
                window.location.href='<?php echo get_site_url(); ?>/resources'; 
                return false;
            }
        }

        // function triggers
        $(document).on('click', 'a.saved-searches', function(e) {
            e.preventDefault();
            show_searches();
        });
        $(document).on('click', 'a.subscriptions', function(e) {
            e.preventDefault();
            show_subscriptions();
        });

        $(document).on('click', 'a.delete-search', function(e) {
            e.preventDefault();
            delete_search($(this).data('umeta'), $(this).parent('.saved-search'));
        });

        $(document).on('click', 'a.delete-subscription', function(e) {
            e.preventDefault();
            delete_subscription($(this).data('umeta'), $(this).parent('.saved-search'));
        });

        $(document).on('click', 'a.delete-data', function(e) {
            e.preventDefault();
            $('main.site-main').addClass('covered');
            $('div.saved-searches').html('\
                <div class="delete-all-data flex row afs jfs">\
                    <div class="flex col item_1_2">\
                        <h4>Delete Your Data?</h4>\
                        <div class="cont">This action cannot be reversed.</div>\
                    </div>\
                    <div class="flex row nowrap afs jfs item_1_2">\
                        <label class="custom-check">Remove my data including email, saved searches and subscriptions from the Opioid Action Center.\
                            <input type="checkbox">\
                            <span class="checkmark"></span>\
                        </label>\
                    </div>\
                </div>\
                <div class="flex row afc jfsb action-row">\
                    <a href="#" class="yes-delete hidden-arrow">DELETE MY DATA <i class="fas fa-trash-alt"></i></a>\
                    <a href="#" class="cancel"><i class="far fa-ban"></i> CANCEL</a>\
                </div>\
            ');
            $('div.saved-searches').css('z-index', '999').fadeIn(200).addClass('flex col showing deleting');
        });
        $(document).on('click', 'a.cancel', function(e) {
            e.preventDefault();
            let container = $('div.saved-searches.showing');
            container.hide().css('z-index', '-9').removeClass('showing');
        });
        $(document).on('click', 'a.searchit', function(e) {
            e.preventDefault();
            recall_saved($(this).data('searchthis'), <?php echo $is_resources; ?>);
        });
        $(document).on('click', 'a.yes-delete', function(e) {
            e.preventDefault();
            delete_data();
        });
        $(document).mouseup(function(e) {
            let container = $('div.saved-searches.showing');
            if (!container.is(e.target) && container.has(e.target).length === 0) {
                container.hide().css('z-index', '-9').removeClass('showing');
            }
        });
    });
    </script>
<?php } ?>
<?php if(!is_user_logged_in()): ?>
    <script>
    jQuery(document).ready(function($) {
        // show login popup
        function logUserIn() {
            var popup = $('div.saved-searches');
            var loginWindow = '\
            <div class="register flex col afs jfs">\
                <h3>Save searches and subscribe to topics with your email.</h3>\
                <form class="signin" action="" method="post">\
                    <i class="far fa-envelope mail-icon"></i>\
                    <input type="email" name="otf_user_email" placeholder="EMAIL">\
                    <input type="hidden" name="otf" value="<?php echo $md5pass; ?>">\
                    <button type="submit" class="btn btn-success">\
                        <i class="fas fa-arrow-right"></i>\
                    </button>\
                </form>\
                <div class="privacy">Just use your email to save searches and subscribe to your topics of interest! That’s all we use it for, per our <a href="/privacy-policy">Privacy policy</a>.</div>\
                <div class="flex row afc jfe action-row">\
                    <a href="#" class="cancel"><i class="far fa-ban"></i> CANCEL</a>\
                </div>\
            </div>\
            ';
            popup.html(loginWindow).css('z-index', '999').fadeIn(200).addClass('login').addClass('showing');
        }

        // trigger actions
        $(document).on('click', 'a.pwdless-register', function(e) {
            e.preventDefault();
            logUserIn();
        });
        $(document).mouseup(function(e) {
            let container = $('div.saved-searches.showing');
            if (!container.is(e.target) && container.has(e.target).length === 0) {
                container.hide().css('z-index', '-9').removeClass('showing');
            }
        });
        $(document).on('click', 'a.cancel', function(e) {
            e.preventDefault();
            let container = $('div.saved-searches.showing');
            container.hide().css('z-index', '-9').removeClass('showing');
        });
    });
    </script>
<?php endif; ?>
<script>
// contact form popup
function show_popup_form() {
    var popupForm = jQuery('div.contact-form');
    popupForm.fadeIn();
}
function hide_popup_form() {
    var popupForm = jQuery('div.contact-form');
    popupForm.fadeOut();
}
jQuery(document).ready(function($) {
    $(document).on('click', 'a#contact-popup', function(e) {
        e.preventDefault();
        show_popup_form();
    });
    $(document).on('click', '#primary-menu li.contact a', function(e) {
        e.preventDefault();
        show_popup_form();
    });
    $(document).on('click', 'a#close-popup', function(e) {
        e.preventDefault();
        hide_popup_form();
    });
});
</script>
<?php wp_footer(); ?>
<script>
    document.addEventListener(
            "DOMContentLoaded", () => {
                const node = document.querySelector( "#site-navigation" );
                const menu = new MmenuLight( node, {
                    theme: "dark"
                });
                menu.enable( "(max-width: 800px)" );
                menu.offcanvas();
                document.querySelector( "a[href='#my-menu']" )
                    .addEventListener( "click", ( event ) => {
                        menu.open();
                        event.preventDefault();
                        event.stopPropagation();
                    });
                document.querySelector( "a[href='#contact-popup']" )
                    .addEventListener( "click", ( event ) => {
                        menu.close();
                        event.preventDefault();
                        event.stopPropagation();
                        show_popup_form();
                    });
            }
        );
</script>
</body>
</html>