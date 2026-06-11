<?php
defined('_JEXEC') or die('Restricted index access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;

class TplJ51EmaniHelper
{
    public static function importAjax()
    {
        self::checkAccess();

        $input    = Factory::getApplication()->input;
        $contents = $input->json->getRaw();

        // Check if it is valid JSON
        $contents = @json_encode(@json_decode($contents));
        if (!$contents)
        {
            throw new \RuntimeException('No content available or an invalid format is uploaded');
        }

        $db = Factory::getDbo();
        $db->setQuery(
            'UPDATE #__template_styles SET params = ' 
            . $db->quote($contents) 
            . ' WHERE id = ' 
            . $input->getInt('id')
        );
        $db->execute();

        Factory::getApplication()->enqueueMessage('Uploaded successfully settings');
    }

    public static function exportAjax()
    {
        self::checkAccess();

        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__template_styles')
            ->where('id = ' . (int) Factory::getApplication()->input->getInt('id'));

        $db->setQuery($query);
        $style = $db->loadObject();

        // Download als JSON
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $style->template . '-preset.json"');

        return $style->params;
    }

    public static function loadPresetAjax()
    {
        self::checkAccess();

        $input    = Factory::getApplication()->input;
        $template = $input->getCmd('template');
        $preset   = $input->getCmd('preset');
        $path     = JPATH_ROOT . '/templates/' . $template . '/presets/' . $preset . '.json';

        if (!file_exists($path))
        {
            throw new \RuntimeException(
                'File ' . (defined('JDEBUG') && JDEBUG ? $path : '') . ' does not exist!'
            );
        }

        $presetData = file_get_contents($path);

        $db = Factory::getDbo();
        $db->setQuery(
            'UPDATE #__template_styles SET params = ' 
            . $db->quote($presetData) 
            . ' WHERE id = ' 
            . $input->getInt('id')
        );
        $db->execute();

        Factory::getApplication()->enqueueMessage('Applied successfully preset ' . $preset);
    }

    private static function checkAccess()
    {
        // ✅ KORRIGIERT: \JSession::checkToken() -> Session::checkToken()
        if (!Session::checkToken('get'))
        {
            throw new \RuntimeException(Text::_('JINVALID_TOKEN'), 403);
        }

        if (!Factory::getUser()->authorise('core.create', 'com_templates'))
        {
            throw new \RuntimeException('Not allowed!!', 403);
        }
    }
}
