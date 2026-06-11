<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Form\FormField;

class JFormFieldTplhelper extends FormField
{
    protected $type = 'Tplhelper';

    protected function getInput()
    {
        $task = isset($this->element['task']) 
            ? (string)$this->element['task'] 
            : 'default';

        switch ($task) {
            case 'asset':
                $this->assetTask();
                break;
            case 'html':
                $this->htmlTask();
                break;
            default:
                $this->defaultTask();
                break;
        }

        return $this->html ?? '';
    }

    protected function defaultTask()
    {
        $this->html = '<input type="hidden" id="tplhelper" name="' 
            . $this->name 
            . '" value="' 
            . htmlspecialchars($this->value, ENT_QUOTES, 'UTF-8') 
            . '" />';
    }

    protected function assetTask()
    {
        // ✅ WebAssetManager statt $doc->addScript/addStyleSheet
        $wa   = Factory::getApplication()->getDocument()->getWebAssetManager();
        $path = str_replace(JPATH_ROOT, '', dirname(__DIR__));
        $path = str_replace('\\', '/', substr($path, 1)) . '/assets/';
        $base = Uri::root() . $path;

        // JS laden
        $js = isset($this->element['js']) 
            ? explode(',', (string)$this->element['js']) 
            : [];

        foreach ($js as $name) {
            $name = trim($name);
            if ($name) {
                // ✅ registerAndUseScript statt $doc->addScript
                $wa->registerAndUseScript(
                    'tplhelper.' . $name,
                    $base . 'js/' . $name . '.js',
                    [],
                    ['defer' => true]
                );
            }
        }

        // CSS laden
        $css = isset($this->element['css']) 
            ? explode(',', (string)$this->element['css']) 
            : [];

        foreach ($css as $name) {
            $name = trim($name);
            if ($name) {
                // ✅ registerAndUseStyle statt $doc->addStyleSheet
                $wa->registerAndUseStyle(
                    'tplhelper.' . $name,
                    $base . 'css/' . $name . '.css'
                );
            }
        }
    }

    protected function htmlTask()
    {
        $file = isset($this->element['file']) 
            ? (string)$this->element['file'] 
            : null;

        if ($file && is_file(JPATH_ROOT . '/' . $file)) {
            $this->html = file_get_contents(JPATH_ROOT . '/' . $file);
        }
    }
}
