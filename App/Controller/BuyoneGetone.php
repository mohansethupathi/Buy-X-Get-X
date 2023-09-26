<?php

namespace App\Controller;

if (!defined('ABSPATH')) exit();

class BuyoneGetone
{
    private static $conditions;
    private static $check_conditions;

    public function addBuyxGetxFields()
    {
        if (!is_admin()) return false;

        global $post;
        // Add a custom meta box
        add_meta_box(
            'bogo-meta-box',
            'BOGO Deals',
            array($this, 'renderBuxGetxFields'),
            'product',
            'normal',
            'default'
        );
    }

    public function renderBuxGetxFields()
    {
        if (!is_admin()) return false;

        global $post;
        $bogo_enabled = get_post_meta($post->ID, 'bogo_enabled', true);

        include(WP_PLUGIN_DIR . "/buy-x-get-x/App/View/bogorule.php");
    }

    public function saveBuyxgetxFields($post_id)
    {
        if (!is_admin()) return false;

        if (!$post_id) return false;
        $bogo_rule_status = isset($_POST['bogo_enabled']) ? 'yes' : 'no';
        update_post_meta($post_id, 'bogo_enabled', $bogo_rule_status);

    }

    public function makeQuantityFieldReadonlyForSpecificProducts($product_quantity, $cart_item_key, $cart_item)
    {
        if (isset($cart_item['free_price']) && isset($cart_item_key)) {
            $product_quantity = '';
            $product_quantity = '<label >' . esc_html($cart_item['quantity']) . '</label>';
        }

        return $product_quantity;
    }

    public function removeRemoveIcon($link, $cart_item_key)
    {

        if (isset($link) && isset($cart_item_key)) {
            // Get the cart object
            $cart = WC()->cart;

            $cart_item = $cart->get_cart_item($cart_item_key);

            if (isset($cart_item['free_price'])) {
                return '';
            }

            return $link;
        }
    }

    public function setCartItemPrice($cart)
    {
        if (!$cart) return false;

        $new_price = 0.00;
        foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
            if (isset($cart_item['free_price'])) {
                $cart_item['data']->set_price($new_price);
            }
        }
    }

    public function checkBuyxGetxOnProductRemoved($cart_item_key, $cart)
    {
        if (!$cart && !$cart_item_key) return false;

        $product_id_to_remove = $cart->cart_contents[$cart_item_key]['product_id'];

        if (!$product_id_to_remove) {
            return false;
        }

        foreach ($cart->cart_contents as $key => $cart_item) {
            if ($cart_item['product_id'] == $product_id_to_remove) {
                // Remove the item from the cart
                remove_action('woocommerce_remove_cart_item', array($this, 'checkBuyxGetxOnProductRemoved'), 10, 2);
                WC()->cart->remove_cart_item($key);
                // Exit the loop after removing the item

            }
        }
    }

    public function customUpdateProductQuantityInCart($cart_item_key, $quantity, $old_quantity, $cart)
    {
        if (!$cart && !$cart_item_key) return false;
        // Get the cart item data
        $cart_item = $cart->cart_contents[$cart_item_key];
        $product_id = $cart_item['product_id'];
        // Check if the product ID matches the target product
        if ($product_id) {

            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                if (isset($cart_item['free_price']) && $product_id == $cart_item['product_id']) {
                    remove_action('woocommerce_after_cart_item_quantity_update', array($this, 'customUpdateProductQuantityInCart'), 10, 2);
                    WC()->cart->set_quantity($cart_item_key, $quantity);
                    break;
                }
            }

            // Calculate and update cart totals
            $cart->calculate_totals();
        }
    }

    public function redirectToProductAfterAddToCart($url, $product)
    {
        // Check if the user just added a product to the cart
        if (isset($_REQUEST['add-to-cart']) && isset($product)) {
            // Redirect back to the product detail page
            $url = get_permalink($product->get_id());
        }

        return $url;
    }

    public function checkConditionsAndAddProducts($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data)
    {

        if (!$product_id && !$cart_item_data && !$cart_item_key) return false;

        self::$conditions = empty(self::$conditions) ? new \App\Conditions\Base() : self::$conditions;

        $condition_results = self::$conditions->checkConditions();

        if (count($condition_results) <= 0) return false;

        foreach ($condition_results as $conditions_list) {

            $condition_instance = new $conditions_list();
            $conditions_result = $condition_instance->check($product_id);

            if (!$conditions_result) return false;
        }

        $is_product_in_cart = false;

        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            if ($cart_item['product_id'] == $product_id && isset($cart_item['free_price'])) {
                $is_product_in_cart = true;
                $quantity += $cart_item['quantity'];
                WC()->cart->set_quantity($cart_item_key, $quantity);
                break;
            }
        }

        // If the free product is not in the cart, add it
        if (!$is_product_in_cart) {

            $cart_item_data = array(
                'price' => 0,
                'free_price' => 0
            );

            remove_action('woocommerce_add_to_cart', array($this, 'checkConditionsAndAddProducts'), 10);
            WC()->cart->add_to_cart($product_id, $quantity, 0, array(), $cart_item_data);

        }
    }


}
