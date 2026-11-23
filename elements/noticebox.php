<?php

\defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;

class JFormFieldNoticeBox extends FormField
{
    public $type = 'NoticeBox';

    public function getInput(): string
    {
        return '<div class="noticeBox">'
            . Text::_(htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8'))
            . '</div>';
    }

    public function getLabel(): string|bool
    {
        return false;
    }
}
