<?php

namespace App;

if (!defined('ABSPATH')) exit();

class Routes
{
    private static $buy_one_get_one;

    public function init()
    {
        self::$buy_one_get_one = empty(self::$buy_one_get_one) ? new \App\Controller\BuyoneGetone() : self::$buy_one_get_one;
        add_action('add_meta_boxes', array(self::$buy_one_get_one, 'addBuyxGetxFields'));
        add_action('save_post_product', array(self::$buy_one_get_one, 'saveBuyxgetxFields'));
        add_filter('woocommerce_add_to_cart_redirect', array(self::$buy_one_get_one,  'redirectToProductAfterAddToCart'), 10, 2);
        add_filter('woocommerce_before_calculate_totals', array(self::$buy_one_get_one, 'setCartItemPrice'));
        add_filter('woocommerce_cart_item_quantity', array(self::$buy_one_get_one, 'makeQuantityFieldReadonlyForSpecificProducts'), 10, 3);
        add_filter('woocommerce_cart_item_remove_link', array(self::$buy_one_get_one, 'removeRemoveIcon'), 10, 2);
        add_action('woocommerce_remove_cart_item', array(self::$buy_one_get_one, 'checkBuyxGetxOnProductRemoved'), 10, 2);
        add_action('woocommerce_after_cart_item_quantity_update', array(self::$buy_one_get_one, 'customUpdateProductQuantityInCart'), 10, 4);
        add_action('woocommerce_add_to_cart',  array(self::$buy_one_get_one, 'checkConditionsAndAddProducts'), 10, 6);
    }

}