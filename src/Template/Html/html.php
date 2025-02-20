<?php

class html
{
    static function label(string $for, string $text, string $className = ""): string
    {
        return "<label for=\"$for\" class=\"$className\">$text</label>";
    }

    static function text(string $name, string|null $value = "", $class = null, $required = null,
                                $disabled = null, $autofocus = null, $onChange = null, $onClick = null): string
    {
        $html = "<input type=\"text\" id=\"$name\" name=\"$name\" value=\"$value\" ";
        $html .= self::attr('class', $class);
        $html .= self::attr('disabled', $disabled);
        $html .= "/>";
        return $html;
    }

    static function link(string $name, string $href, array $attributes = []): string
    {
        $attributes['href'] = $href;
        $html = "<a ";
        foreach ($attributes as $key => $value) {
            $html .= "$key=\"$value\" ";
        }
        $html .= ">$name</a>";
        return $html;
    }

    static function password(string $name, string|null $value = "", string $class = null): string
    {
        $html = "<input type=\"password\" id=\"$name\" name=\"$name\" value=\"$value\" ";
        $html .= self::attr('class', $class);
        $html .= "/>";
        return $html;
    }

    static function checkbox($name, bool $checked = null): string
    {
        $html = "<input type=\"checkbox\" name=\"$name\" value=\"false\" checked style=\"display: none\" /> \n";
        $html .= "<input type=\"checkbox\" name=\"$name\" id=\"$name\" value=\"true\" ";
        $html .= self::attr('checked', $checked);
        $html .= "/> \n";

        return $html;
    }

    static function select($name, $values, $selected = null, $class = null, $required = null, $disabled = null,
                           $autofocus = null, $onChange = null, $onClick = null): string
    {
        $html = "<select id=\"$name\" name=\"$name\" ";
        $html .= self::attr('class', $class);
        $html .= self::attr('required', $required);
        $html .= self::attr('disabled', $disabled);
        $html .= self::attr('autofocus', $autofocus);
        $html .= self::attr('onChange', $onChange);
        $html .= self::attr('onClick', $onClick);
        $html .= ">";
        $html .= html::options($values, $selected);
        $html .= "</select>";
        return $html;
    }

    static function options($options, $selected = null): string
    {
        $html = "";
        foreach ($options as $value => $name) {
            $attr = '';
            if ($selected != null && $value == $selected)
                $attr = "selected";
            $html .= "<option ";
            $html .= "value=\"$value\" ";
            $html .= "$attr>$name</option>";
        }
        return $html;
    }

    static function error($valid, $message, string $class = "form-error"): string
    {
        $html = "";
        if (!$valid) {
            $html = "<div class=\"$class\">$message</div>";
        }
        return $html;
    }

    private static function attr($attr, $value = null): string
    {
        if (empty($value)) return '';

        if ($value === true) {
            return $attr . " ";
        }

        return "$attr=\"$value\" ";
    }
}