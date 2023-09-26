<?php

/**
 * Plugin Name: Buy One Get One Deals
 * Description: Implement Buy One Get One (BOGO) Free deals for WooCommerce products.
 * Version: 1.0.0
 * Author: Woo Commerce
 */

if( ! defined('ABSPATH') ) exit();

if(!file_exists( WP_PLUGIN_DIR . '/buy-x-get-x/vendor/autoload.php' )) return;

require_once WP_PLUGIN_DIR . '/buy-x-get-x/vendor/autoload.php';

if(!class_exists('App\Routes')) return ;

$routes =  new App\Routes();
$routes->init();
