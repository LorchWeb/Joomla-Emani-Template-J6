<?php

\defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;

class JFormFieldInfoBox extends FormField
{
    public $type = 'InfoBox';

    public function getInput(): string
    {
        return '<div class="infoBox">'
            . Text::_(htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8'))
            . '</div>';
    }

    public function getLabel(): string|bool
    {
        return false;
    }
}
