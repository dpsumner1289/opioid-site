<?php
// theme setup
include (trailingslashit(get_template_directory()).'inc/common/theme-setup.php');
include (trailingslashit(get_template_directory()).'inc/hook-changes.php');

// passwordless user creation and login functions
include (trailingslashit(get_template_directory()).'inc/user-registration.php');

// include modular function files
include (trailingslashit(get_template_directory()).'inc/common/acf-functions.php');
include (trailingslashit(get_template_directory()).'inc/common/generic-functions.php');

// ajax loaders
include (trailingslashit(get_template_directory()).'inc/ajax-loaders.php');

// include modular components
include (trailingslashit(get_template_directory()).'inc/components.php');