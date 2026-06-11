<?php

defined('JPATH_PLATFORM') or die();

use Joomla\CMS\Form\FormField;

class JFormFieldModulesLabel extends FormField
{
    protected $type = 'ModulesLabel';

    protected function getInput(): string
    {
        $size      = $this->element['size']      ? ' size="'      . (int)    $this->element['size']      . '"' : '';
        $maxLength = $this->element['maxlength'] ? ' maxlength="' . (int)    $this->element['maxlength'] . '"' : '';
        $class     = $this->element['class']     ? ' class="'     . htmlspecialchars((string) $this->element['class'],     ENT_COMPAT, 'UTF-8') . '"' : '';
        $onchange  = $this->element['onchange']  ? ' onchange="'  . htmlspecialchars((string) $this->element['onchange'],  ENT_COMPAT, 'UTF-8') . '"' : '';
        $readonly  = ((string) $this->element['readonly']  === 'true') ? ' readonly="readonly"'   : '';
        $disabled  = ((string) $this->element['disabled']  === 'true') ? ' disabled="disabled"'   : '';

        return '<input type="text" class="module_width" name="' . $this->name . '" id="' . $this->id . '"'
            . ' value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"'
            . $class . $size . $disabled . $readonly . $onchange . $maxLength . '/>';
    }
}
