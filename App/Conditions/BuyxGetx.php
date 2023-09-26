<?php

namespace App\Conditions;

class BuyxGetx
{
    public function check($product_id)
    {
        $bogo_enabled = get_post_meta($product_id, 'bogo_enabled', true);
        if ($bogo_enabled === 'yes') {
            return true;
        }
        return false;
    }

}