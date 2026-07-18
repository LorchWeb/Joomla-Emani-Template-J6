<?php

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;

class JFormFieldOpacityList extends FormField
{
    protected $type = 'OpacityList';

    protected function getInput(): string
    {
        $html = [];
        $attr = '';

        // Field attributes
        $attr .= !empty($this->class)     ? ' class="'    . htmlspecialchars($this->class, ENT_COMPAT, 'UTF-8') . '"' : '';
        $attr .= !empty($this->size)      ? ' size="'     . (int) $this->size . '"' : '';
        $attr .= $this->multiple          ? ' multiple'   : '';
        $attr .= $this->required          ? ' required aria-required="true"' : '';
        $attr .= $this->autofocus         ? ' autofocus'  : '';

        // Readonly/disabled
        if (
            (string) $this->readonly === '1' || (string) $this->readonly === 'true' ||
            (string) $this->disabled === '1' || (string) $this->disabled === 'true'
        ) {
            $attr .= ' disabled="disabled"';
        }

        // JavaScript attributes
        $attr .= $this->onchange
            ? ' onchange="' . htmlspecialchars($this->onchange, ENT_COMPAT, 'UTF-8') . '"'
            : '';

        // Options
        $options = [
            HTMLHelper::_('select.option', '0.0', 'Transparent'),
            HTMLHelper::_('select.option', '0.1', '0.1'),
            HTMLHelper::_('select.option', '0.2', '0.2'),
            HTMLHelper::_('select.option', '0.3', '0.3'),
            HTMLHelper::_('select.option', '0.4', '0.4'),
            HTMLHelper::_('select.option', '0.5', '0.5'),
            HTMLHelper::_('select.option', '0.6', '0.6'),
            HTMLHelper::_('select.option', '0.7', '0.7'),
            HTMLHelper::_('select.option', '0.8', '0.8'),
            HTMLHelper::_('select.option', '0.9', '0.9'),
            HTMLHelper::_('select.option', '1.0', '1.0'),
        ];

        // Readonly: list ohne name + hidden input
        if ((string) $this->readonly === '1' || (string) $this->readonly === 'true') {
            $html[] = HTMLHelper::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
            $html[] = '<input type="hidden" name="' . $this->name . '" value="'
                . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"/>';
        } else {
            $html[] = HTMLHelper::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
        }

        return implode('', $html);
    }
}
