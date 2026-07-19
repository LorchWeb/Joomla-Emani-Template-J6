<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Form\FormField;

class JFormFieldTplhelper extends FormField
{
    protected $type = 'Tplhelper';

    protected function getInput()
    {
        $this->registerScriptLanguage();

        $task = isset($this->element['task'])
            ? (string)$this->element['task']
            : 'default';

        switch ($task) {
            case 'asset':
                return $this->assetTask();
            case 'html':
                return $this->htmlTask();
            default:
                return $this->defaultTask();
        }
    }

    protected function defaultTask()
    {
        return '<input type="hidden" id="tplhelper" name="'
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
                    'j51.tplhelper.' . $name,
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
                    'j51.tplhelper.' . $name,
                    $base . 'css/' . $name . '.css'
                );
            }
        }

        return '';
    }

    private function registerScriptLanguage(): void
    {
        $scriptLanguageKeys = [
            'TPL_J51_EMANI_JS_JQUERY_MISSING',
            'TPL_J51_EMANI_JS_ACTION_PREPARE_FAILED',
            'TPL_J51_EMANI_JS_LOCAL_STORAGE_UNAVAILABLE',
            'TPL_J51_EMANI_JS_LAST_GROUP_RESTORE_FAILED',
            'TPL_J51_EMANI_JS_PRESET_SELECT_MISSING',
            'TPL_J51_EMANI_JS_INVALID_RESPONSE',
            'TPL_J51_EMANI_JS_PRESET_LOAD_FAILED',
            'TPL_J51_EMANI_JS_PRESET_LOAD_SUCCESS',
            'TPL_J51_EMANI_JS_REQUEST_FAILED',
            'TPL_J51_EMANI_JS_IMPORT_FAILED',
            'TPL_J51_EMANI_JS_IMPORT_SUCCESS',
            'TPL_J51_EMANI_JS_FILE_READ_FAILED_CONSOLE',
            'TPL_J51_EMANI_JS_FILE_READ_FAILED',
        ];

        foreach ($scriptLanguageKeys as $key) {
            Text::script($key);
        }
    }

    protected function htmlTask()
    {
        $file = isset($this->element['file'])
            ? (string)$this->element['file']
            : null;

        if ($file && is_file(JPATH_ROOT . '/' . $file)) {
            return file_get_contents(JPATH_ROOT . '/' . $file);
        }

        return '';
    }
}
