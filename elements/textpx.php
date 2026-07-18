<?php

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField;

class JFormFieldtextpx extends FormField
{
    protected $type = 'textpx';

    public function getInput()
    {
        return '<div class="input-group">' .
            '<input class="form-control" type="text" name="' . $this->name . '" id="' . $this->id . '"' .
            ' value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"/>' .
            '<span class="input-group-text">px</span>' .
            '</div>';
    }
}
