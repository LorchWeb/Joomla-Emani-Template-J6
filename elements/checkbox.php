<?php

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField;

class JFormFieldCheckbox extends FormField
{
    public $type = 'Checkbox';

    protected function getInput(): string
    {
        // Field attributes
        $class    = $this->element['class']
            ? ' class="' . htmlspecialchars((string) $this->element['class'], ENT_COMPAT, 'UTF-8') . '"'
            : '';
        $disabled = ((string) $this->element['disabled'] === 'true') ? ' disabled="disabled"' : '';
        $checked  = ((string) $this->element['value'] === $this->value) ? ' checked="checked"' : '';
        $onclick  = $this->element['onclick']
            ? ' onclick="' . htmlspecialchars((string) $this->element['onclick'], ENT_COMPAT, 'UTF-8') . '"'
            : '';

        return '<input type="checkbox" name="' . $this->name . '" id="' . $this->id . '"'
            . ' value="' . htmlspecialchars((string) $this->element['value'], ENT_COMPAT, 'UTF-8') . '"'
            . $class . $checked . $disabled . $onclick . '/>';
    }
}
