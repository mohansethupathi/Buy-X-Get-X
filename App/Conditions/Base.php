<?php

namespace App\Conditions;

class Base
{
    public $available_conditions = array();

    public function checkConditions()
    {
        $condition_files = glob(WP_PLUGIN_DIR . '/buy-x-get-x/App/Conditions/*.php');

        if (count($condition_files) <= 0) return false;

        foreach ($condition_files as $file) {
            // Extract the class name from the file name
            $class_name = basename($file, '.php');

            // Combine the namespace and class name
            $full_class_name = '\App\Conditions\\' . $class_name;

            // Check if the class exists
            if (class_exists($full_class_name) && $class_name !== 'Base') {

                $this->available_conditions[] = $full_class_name;

            }
        }
        return $this->available_conditions;
    }

}
