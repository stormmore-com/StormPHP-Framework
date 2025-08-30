<?php

namespace Stormmore\Framework\Mvc\View;

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
            (!is_array($selected) and $value === $selected) or
            (is_bool($selected) and $selected === true)
        )
        {
            $html .= $this->html_attr('checked');
        }
        $html .= $this->html_attr('id', $id);
        $html .= "/>";
        echo $html;
    }

    function radio(string $id, string $name, mixed $value, mixed $selected, ?string $className = null): void
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
        $isList = array_is_list($options);
        $html = "";
        foreach ($options as $value => $name) {
            if ($isList) {
                $value = $name;
            }
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