<?php
if(!is_user_logged_in()) {
	if (isset($_POST['otf']) && isset($_POST['otf_user_email'])) {
		handle_authentication();
	}
}else{
	if(isset($_POST['otf_logout'])) {
		wp_logout();
		$redirect_to = $_SERVER['REQUEST_URI'];
		wp_safe_redirect($redirect_to);
		exit;
	}
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<script src="https://cdn.jsdelivr.net/npm/js-cookie@2/src/js.cookie.min.js"></script>
	<?php 
	wp_head(); 
	?>
</head>
<body <?php body_class(); ?>>
    <header class="site-header">
			<div class="flex row container container-xlg less-padding jfsb afc">
				<div class="member–register flex row afc jfe full">
					<?php
					if(!is_user_logged_in()) {
						?>
						<a href="#" class="pwdless-register">SIGN IN</a>/
						<a href="#" class="pwdless-register">REGISTER</a>
						<?php
					}else {
						?>
						<form action="" method="post">
							<input type="hidden" name="otf_logout">
							<button id="pwdless-logout" type="submit">SIGN OUT</button>
						</form>
						<?php
					}
					?>
				</div>
				<div class="logo item_1_3 flex row afc nowrap">
					<?php if($logo = get_option('options_logo')): echo '<a href="'.get_site_url().'"><img class="item_full" src="'.wp_get_attachment_url($logo).'"></a>'; endif; ?>
				</div>
				<div class="item_2_3">
					<div class="main-navigation flex afc jfe">
						<form class="otf-search flex item_2_9" action="" method="get" 
						<?php if(!is_page_template('templates/resources.php')): ?>
							onsubmit="Cookies.set('search_carryover', document.getElementById('header-keywords').value, {expires:1}); window.location.href='<?php echo get_site_url(); ?>/resources'; return false;" 
						<?php endif; ?>
						>
							<button class="post-search" type="submit"><i class="fas fa-search"></i></button>
							<input class="keywords" id="header-keywords" name="otf_resource_search" type="text" placeholder="SEARCH RESOURCES" />
						</form>
						<a id="click-menu" href="#my-menu"><i class="far fa-bars"></i></a>
						<nav id="site-navigation" class="main-navigation flex afc jfe item_7_9" role="navigation">
							<?php
							wp_nav_menu( array(
								'theme_location' => 'menu-1',
								'menu_id'        => 'primary-menu',
								'menu_class'        => 'flex full row jfse',
								'container_class'	 => 'flex row full flexend',
								'link_after'	 => '<i class="fad fa-caret-right"></i>',
							) );
							$menu = wp_get_nav_menu_object('primary-menu');
							?>
						</nav><!-- #site-navigation -->
					</div>
				</div>
				<?php if(is_user_logged_in()){
					?>
					<div class="user-actions flex row">
						<a href="#" class="saved-searches"><i class="fal fa-plus"></i> SAVED SEARCHES</a>
						<a href="#" class="subscriptions"><i class="fal fa-envelope"></i> SUBSCRIPTIONS</a>
						<a href="#" class="delete-data"><i class="fal fa-times"></i> DELETE MY DATA</a>
						<?php if(!is_front_page()): ?>
						<div class="guide flex col afc jfc">
							<a href="#" class="close-guide"><i class="far fa-times-circle"></i></a>
							<i class="far fa-arrow-up"></i>
							<p>You can manage your searches, subscriptions or delete your data at any time from right here.</p>
						</div>
						<?php endif; ?>
					</div>
					<?php
				} ?>
			</div>
	</header><!-- /.site-header -->
    <main class="site-main">