<?php

function html_label(string $for, string $text, string $className = ""): string
{
    return "<label for=\"$for\" class=\"$className\">$text</label>";
}

function html_text(string $name, string|null $value = "", $class = null, $required = null,
                            $disabled = null, $autofocus = null, $onChange = null, $onClick = null): string
{
    $html = "<input type=\"text\" id=\"$name\" name=\"$name\" value=\"$value\" ";
    $html .= html_attr('class', $class);
    $html .= html_attr('disabled', $disabled);
    $html .= "/>";
    return $html;
}
function html_link(string $name, string $href, array $attributes = []): string
{
    $attributes['href'] = $href;
    $html = "<a ";
    foreach ($attributes as $key => $value) {
        $html .= "$key=\"$value\" ";
    }
    $html .= ">$name</a>";
    return $html;
}

function html_password(string $name, string|null $value = "", string $class = null): string
{
    $html = "<input type=\"password\" id=\"$name\" name=\"$name\" value=\"$value\" ";
    $html .= html_attr('class', $class);
    $html .= "/>";
    return $html;
}

function html_checkbox($name, bool $checked = null): string
{
    $html = "<input type=\"checkbox\" name=\"$name\" value=\"false\" checked style=\"display: none\" /> \n";
    $html .= "<input type=\"checkbox\" name=\"$name\" id=\"$name\" value=\"true\" ";
    $html .= html_attr('checked', $checked);
    $html .= "/> \n";

    return $html;
}

function html_select($name, $values, $selected = null, $class = null, $required = null, $disabled = null,
                       $autofocus = null, $onChange = null, $onClick = null): string
{
    $html = "<select id=\"$name\" name=\"$name\" ";
    $html .= html_attr('class', $class);
    $html .= html_attr('required', $required);
    $html .= html_attr('disabled', $disabled);
    $html .= html_attr('autofocus', $autofocus);
    $html .= html_attr('onChange', $onChange);
    $html .= html_attr('onClick', $onClick);
    $html .= ">";
    $html .= html_options($values, $selected);
    $html .= "</select>";
    return $html;
}

function html_options($options, $selected = null): void
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
    echo $html;
}

function html_error($valid, $message, string $class = "form-error"): string
{
    $html = "";
    if (!$valid) {
        $html = "<div class=\"$class\">$message</div>";
    }
    return $html;
}

function html_attr($attr, $value = null): string
{
    if (empty($value)) return '';

    if ($value === true) {
        return $attr . " ";
    }

    return "$attr=\"$value\" ";
}