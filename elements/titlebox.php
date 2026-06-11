<?php

defined('JPATH_BASE') or die();

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;

class JFormFieldTitleBox extends FormField
{
    public $type = 'TitleBox';

    public function getInput(): string
    {
        return '<div class="titleBox">'
            . Text::_(htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8'))
            . '</div>';
    }

    public function getLabel(): string|bool
    {
        return false;
    }
}
