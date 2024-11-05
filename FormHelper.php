<?php

namespace App\Http\Helper;

use function route;

class FormHelper
{
    public static function open(array $options = [])
    {

        $method = strtoupper($options['method'] ?? 'POST');
        $class = $options['class'] ?? '';

        // Generate the CSRF token input
        $csrfField = '<input type="hidden" name="_token" value="' . csrf_token() . '">';

        // Include method spoofing for DELETE and PUT requests
        $methodField = '';
        if ($method === 'DELETE' || $method === 'PUT' || $method == 'patch' || $method == 'PATCH') {

            $methodField = '<input type="hidden" name="_method" value="' . $method . '">';

            $method = 'POST'; // Use POST as the actual method for form submission
        }

        if (in_array($method, ['DELETE', 'PUT', 'PATCH'])) {
            $methodField = '<input type="hidden" name="_method" value="' . $method . '">';
            $method = 'POST'; // Use POST as the actual method for form submission
        }

        $additional = '';
        if (isset($options['files']) && $options['files'] == true) {
            $additional = 'multipart/form-data';
        }
        // Determine the action URL
        $actionUrl = '';
        if (isset($options['url'])) {
            // Use the provided URL directly
            $actionUrl = $options['url'][0]; // Assuming URL is an array with one element
        } elseif (isset($options['route'])) {
            // Handle routing if route is set
            if (is_array($options['route'])) {
                $routeName = $options['route'][0];
                $routeParams = array_slice($options['route'], 1);
                $actionUrl = route($routeName, $routeParams);
            } else {
                $actionUrl = route($options['route']);
            }
        }

        // Return the form opening tag along with the CSRF field and method spoofing
        return "<form action='{$actionUrl}' method='{$method}' class='{$class}'enctype='{$additional}'>" . $csrfField . $methodField;
    }


    protected static $model = null;

    public static function model($model, array $options = [])
    {

        if (!$model instanceof \Illuminate\Database\Eloquent\Model) {
            throw new \InvalidArgumentException('The model must be an instance of Eloquent Model.');
        }

        self::$model = $model;

        return self::open($options);
    }

    protected static function getModelValue($name)
    {
        return self::$model ? self::$model->{$name} : null;
    }

    public static function close()
    {
        return "</form>";
    }

    public static function label($name, $value, $attributes = [])
    {
        $class = $attributes['class'] ?? '';
        return "<label for='{$name}' class='{$class}'>{$value}</label>";
    }

    public static function hidden($name, $value)
    {
        return "<input type='hidden' name='{$name}' value='{$value}'>";
    }

    public static function text($name, $value = '', $attributes = [])
    {
        $class = $attributes['class'] ?? '';
        $value = $value ?: self::getModelValue($name);

        return "<input type='text' name='{$name}' value='" . htmlspecialchars($value, ENT_QUOTES) . "' class='{$class}'>";
    }

    public static function submit($value, $attributes = [])
    {
        $class = $attributes['class'] ?? '';
        return "<button type='submit' class='{$class}'>{$value}</button>";
    }

    public static function select($name, $options = [], $selected = null, $attributes = [])
    {
        $class = $attributes['class'] ?? '';
        $selected = $selected ?? self::getModelValue($name);

        $html = "<select name='{$name}' class='{$class}'>";


        foreach ($options as $value => $label) {
            $isSelected = ($value == $selected) ? 'selected' : '';
            $html .= "<option value='{$value}' {$isSelected}>{$label}</option>";
        }

        $html .= "</select>";
        return $html;
    }

    public static function email($name, $value = null, $attributes = [])
    {
        $class = $attributes['class'] ?? '';
        $placeholder = $attributes['placeholder'] ?? '';
        $value = htmlspecialchars($value ?? self::getModelValue($name), ENT_QUOTES);
        return "<input type='email' name='{$name}' value='" . htmlspecialchars($value, ENT_QUOTES) . "' class='{$class}' placeholder='{$placeholder}' />";
    }

    public static function textarea($name, $value = null, array $options = [])
    {
        // Ensure the value is properly escaped
        $value = htmlspecialchars($value ?? self::getModelValue($name), ENT_QUOTES);

//        $value = htmlspecialchars($value ?? '', ENT_QUOTES);

        // Prepare attributes string
        $attributes = '';
        foreach ($options as $key => $val) {
            $attributes .= "{$key}=\"{$val}\" ";
        }

        // Return the textarea HTML
        return "<textarea name=\"{$name}\" {$attributes}>{$value}</textarea>";
    }


    public static function checkbox($name, $value = null, $attributes = [])
    {
        $value = htmlspecialchars($value ?? self::getModelValue($name), ENT_QUOTES);
        // Check if the checkbox is pre-selected
        $checked = isset($value) && $value == 'block' ? 'checked' : '';

        // Add additional attributes if provided
        $attrString = '';
        if (is_array($attributes) && count($attributes) > 0)
            foreach ($attributes as $key => $val) {
                $attrString .= "{$key}=\"{$val}\" ";
            }

        // Return the final checkbox HTML code
        return "<input type='checkbox'  name='{$name}' value='" . htmlspecialchars($value ?? 1, ENT_QUOTES) . "' {$attrString} {$checked}>";
    }

    public static function password($name, $attributes = [])
    {
        // Prepare attributes string
        $attrString = '';
        foreach ($attributes as $key => $val) {
            $attrString .= "{$key}=\"{$val}\" ";
        }

        // Return the final password input HTML
        return "<input type='password' name='{$name}' {$attrString}>";
    }

    public static function number($name, $value = null, $attributes = [])
    {
        $class = $attributes['class'] ?? '';
        $placeholder = $attributes['placeholder'] ?? '';
        $rows = $attributes['rows'] ?? 4;
        $cols = $attributes['cols'] ?? 40;

        // Retrieve the old input value, then the model value, then the provided default value
        $inputValue = old($name, self::getModelValue($name) ?? $value);

        // Prepare additional attributes if provided
        $attrString = '';
        foreach ($attributes as $key => $val) {
            $attrString .= "{$key}=\"{$val}\" ";
        }

        // Return the number input HTML
        return "<input type='number' name='{$name}' value='" . htmlspecialchars($inputValue ?? '', ENT_QUOTES) . "' class='{$class}' placeholder='{$placeholder}' rows='{$rows}' cols='{$cols}' {$attrString}>";
    }
}