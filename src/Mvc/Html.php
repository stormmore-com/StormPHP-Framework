<?php

namespace Stormmore\Framework\Mvc;

use Stormmore\Framework\SourceCode\Parser\Models\PhpUse;

class Html
{
    function checkbox(string $id, string $name, mixed $value, mixed $selected, string $className = ""): void
    {
        $html = "<input ";
        $html .= $this->html_attr('type', 'checkbox');
        $html .= $this->html_attr('name', $name);
        $html .= $this->html_attr('value', $value);
        $html .= $this->html_attr('class', $className);
        if (
            (is_array($selected) and in_array($value, $selected)) or
            (!is_array($selected) and $value === $selected)
        )
        {
            $html .= $this->html_attr('checked');
        }
        $html .= $this->html_attr('id', $id);
        $html .= "/>";
        echo $html;
    }

    function radio(string $id, string $name, mixed $value, mixed $selected, string $className = null): void
    {
        $html = "<input ";

        $html .= $this->html_attr('type', 'radio');
        $html .= $this->html_attr('name', $name);
        $html .= $this->html_attr('value', $value);
        $html .= $this->html_attr('class', $className);
        if ($value === $selected) {
            $html .= $this->html_attr('checked');
        }
        $html .= $this->html_attr('id', $id);
        $html .= "/>";
        echo $html;
    }

    function options($options, $selected = null): void
    {
        $html = "";
        foreach ($options as $value => $name) {
            $html .= "<option ";
            $html .= "value=\"$value\" ";
            if ($selected != null && $value == $selected)
                $html .= "selected ";
            $html .= ">";
            $html .= $name;
            $html .= "</option>";
        }
        echo $html;
    }

    function html_text(string $name, string|null $value = "", $class = null, $required = null, $disabled = null): string
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

    function html_password(string $name, string|null $value = "", string|null $class = null): string
    {
        $html = "<input type=\"password\" id=\"$name\" name=\"$name\" value=\"$value\" ";
        $html .= html_attr('class', $class);
        $html .= "/>";
        return $html;
    }

    function html_checkbox($name, bool|null $checked = null): string
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

    function html_error($valid, $message, string $class = "form-error"): string
    {
        $html = "";
        if (!$valid) {
            $html = "<div class=\"$class\">$message</div>";
        }
        return $html;
    }

    private function html_attr($attr, mixed $value = null): string
    {
        if ($attr == 'checked') {
            return "checked ";
        }
        $valString = $value;
        if ($value === true) {
            $valString = "true";
        }
        if ($value === false) {
            $valString = "false";
        }
        if ($value === null) {
            return '';
        }
        return "$attr=\"$valString\" ";
    }
}