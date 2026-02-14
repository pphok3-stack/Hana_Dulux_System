<?php

if (!function_exists('settings')) {
    function settings() {
        $settings = cache()->remember('settings', 24*60, function () {
            //return \Modules\Setting\Entities\Setting::firstOrFail();
        });

        return $settings;
    }
}

if (!function_exists('format_currency')) {
    function format_currency($value, $format = true) {
        if (!$format) {
            return $value;
        }

        $settings = settings();
        $position = $settings->default_currency_position ?? '2';
        $symbol = $settings->currency->symbol ?? ' $';
        $decimal_separator = $settings->currency->decimal_separator ?? '.';
        $thousand_separator = $settings->currency->thousand_separator ?? ',';

        if ($position == 'prefix') {
            $formatted_value = $symbol . number_format((float) $value, 2, $decimal_separator, $thousand_separator);
        } else {
            $formatted_value = number_format((float) $value, 2, $decimal_separator, $thousand_separator) . $symbol;
        }

        return $formatted_value;
    }
}

if (!function_exists('make_reference_id')) {
    function make_reference_id($prefix, $number) {
        $padded_text = $prefix . '-' . str_pad($number, 5, 0, STR_PAD_LEFT);

        return $padded_text;
    }
}

if (!function_exists('array_merge_numeric_values')) {
    function array_merge_numeric_values() {
        $arrays = func_get_args();
        $merged = array();
        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                if (!is_numeric($value)) {
                    continue;
                }
                if (!isset($merged[$key])) {
                    $merged[$key] = $value;
                } else {
                    $merged[$key] += $value;
                }
            }
        }

        return $merged;
    }
}

if (!function_exists('generate_invoice_number')) {
    function generate_invoice_number($prefix = 'INV-', $length = 10) {
        // Get the latest invoice number from the database
        $lastOrder = \App\Models\Order::orderBy('id', 'desc')->first();

        if (!$lastOrder || empty($lastOrder->invoice_no)) {
            $number = 1;
        } else {
            // Extract number from the last invoice
            $lastInvoice = $lastOrder->invoice_no;
            $lastNumber = (int) str_replace($prefix, '', $lastInvoice);
            $number = $lastNumber + 1;
        }

        // Generate the new invoice number with padding
        $paddingLength = $length - strlen($prefix);
        return $prefix . str_pad($number, $paddingLength, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('generate_code')) {
    function generate_code($table, $field, $prefix = 'PC', $length = 4) {
        // Get the model class name from table name
        $modelClass = '\\App\\Models\\' . ucfirst(rtrim($table, 's'));

        if (!class_exists($modelClass)) {
            // Fallback for different naming conventions
            $modelClass = '\\App\\Models\\' . str_replace('_', '', ucwords($table, '_'));
        }

        if (class_exists($modelClass)) {
            $lastRecord = $modelClass::orderBy('id', 'desc')->first();
        } else {
            $lastRecord = null;
        }

        if (!$lastRecord || empty($lastRecord->$field)) {
            $number = 1;
        } else {
            // Extract number from the last code
            $lastCode = $lastRecord->$field;
            $lastNumber = (int) str_replace($prefix, '', $lastCode);
            $number = $lastNumber + 1;
        }

        // Generate the new code with padding
        $paddingLength = $length - strlen($prefix);
        return $prefix . str_pad($number, $paddingLength, '0', STR_PAD_LEFT);
    }
}
