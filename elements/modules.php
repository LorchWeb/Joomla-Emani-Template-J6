<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// ✅ _JEXEC statt JPATH_PLATFORM
defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField;

/**
 * Form Field class for the Joomla CMS.
 * Renders a text input field for module positions.
 *
 * @since  3.0
 */
class JFormFieldModules extends FormField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  3.0
     */
    protected $type = 'Modules';

    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     *
     * @since   3.0
     */
    protected function getInput()
    {
        // Field attributes
        $size = $this->element['size']
            ? ' size="' . (int) $this->element['size'] . '"'
            : '';

        $maxLength = $this->element['maxlength']
            ? ' maxlength="' . (int) $this->element['maxlength'] . '"'
            : '';

        // ✅ htmlspecialchars für class-Attribut
        $class = $this->element['class']
            ? ' class="' . htmlspecialchars((string) $this->element['class'], ENT_QUOTES, 'UTF-8') . '"'
            : '';

        // ✅ Strikte Vergleiche mit ===
        $readonly = ((string) $this->element['readonly'] === 'true')
            ? ' readonly="readonly"'
            : '';

        $disabled = ((string) $this->element['disabled'] === 'true')
            ? ' disabled="disabled"'
            : '';

        // ✅ htmlspecialchars für onchange
        $onchange = $this->element['onchange']
            ? ' onchange="' . htmlspecialchars((string) $this->element['onchange'], ENT_QUOTES, 'UTF-8') . '"'
            : '';

        return '<input'
            . ' type="text"'
            . ' class="module_width"'
            . ' name="' . $this->name . '"'
            . ' id="' . $this->id . '"'
            . ' value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"'
            . $class
            . $size
            . $disabled
            . $readonly
            . $onchange
            . $maxLength
            . '/>';
    }

    /**
     * Method to get the field label markup.
     * Returns empty string to suppress label rendering.
     *
     * @return  string
     * @since   3.0
     */
    public function getLabel()
    {
        // ✅ Leerer String statt false - konsistenter Rückgabetyp
        return '';
    }
}
