<?php

defined('JPATH_PLATFORM') or die();

use Joomla\CMS\Form\Field\NumberField;
use Joomla\CMS\HTML\HTMLHelper;

class JFormFieldOpacitySlider extends NumberField
{
    protected $type = 'OpacitySlider';

    protected function getInput(): string
    {
        // Field attributes
        $max       = !empty($this->max)       ? ' max="'   . htmlspecialchars($this->max,  ENT_COMPAT, 'UTF-8') . '"' : '';
        $min       = !empty($this->min)       ? ' min="'   . htmlspecialchars($this->min,  ENT_COMPAT, 'UTF-8') . '"' : '';
        $step      = !empty($this->step)      ? ' step="'  . htmlspecialchars($this->step, ENT_COMPAT, 'UTF-8') . '"' : '';
        $class     = !empty($this->class)     ? ' class="' . htmlspecialchars($this->class, ENT_COMPAT, 'UTF-8') . '"' : '';
        $readonly  = $this->readonly          ? ' readonly'  : '';
        $disabled  = $this->disabled          ? ' disabled'  : '';
        $autofocus = $this->autofocus         ? ' autofocus' : '';
        $onchange  = !empty($this->onchange)
            ? ' onchange="' . htmlspecialchars($this->onchange, ENT_COMPAT, 'UTF-8') . '"'
            : '';

        $value = (float) $this->value;
        $value = empty($value) ? $this->min : $value;

        // jQuery nur noch wenn wirklich nötig
        HTMLHelper::_('jquery.framework');

        return '<span class="opacityvalue">0.0</span>'
            . '<input type="range" name="' . $this->name . '" id="' . $this->id . '"'
            . ' value="' . htmlspecialchars((string) $value, ENT_COMPAT, 'UTF-8') . '"'
            . $class . $disabled . $readonly . $onchange . $max . $step . $min . $autofocus . ' />'
            . '<span class="opacityvalue">1.0</span>';
    }
}
