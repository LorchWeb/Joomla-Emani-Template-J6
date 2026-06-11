<?php

defined('JPATH_PLATFORM') or die();

use Joomla\CMS\Form\FormField;

class JFormFieldlefttextpx extends FormField
{
    protected $type = 'lefttextpx';

    public function getInput()
    {
        return '<div class="input-group">' .
            '<span class="input-group-text">Left</span>' .
            '<input class="form-control" type="text" name="' . $this->name . '" id="' . $this->id . '"' .
            ' value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"/>' .
            '<span class="input-group-text">px</span>' .
            '</div>';
    }
}
