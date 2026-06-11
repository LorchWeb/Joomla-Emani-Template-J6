<?php

defined('JPATH_PLATFORM') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Uri\Uri;

class JFormFieldCheckbox extends FormField
{
    public $type = 'Checkbox';

    protected function getInput(): string
    {
        // Determine template filepath
        $basePath = str_replace(
            DIRECTORY_SEPARATOR,
            '/',
            str_replace(JPATH_SITE, Uri::base(), dirname(__DIR__))
        );
        $basePath = str_replace('/administrator/', '', $basePath);

        // Add script via WebAssetManager
        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        $wa->registerAndUseScript(
            'j51.ibutton',
            $basePath . '/elements/js/ibutton.js',
            [],
            ['defer' => true]
        );

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
