<?php
defined('_JEXEC') or die('Restricted index access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\Database\DatabaseInterface;

class TplJ51EmaniHelper
{
    private const TEMPLATE_NAME = 'j51_emani';

    public static function importAjax(): void
    {
        self::checkAccess();

        $app      = Factory::getApplication();
        $input    = $app->getInput();
        $contents = $input->json->getRaw();
        $styleId  = $input->getInt('id');
        $params   = self::normalisePresetJson($contents);
        $db       = Factory::getContainer()->get(DatabaseInterface::class);

        self::getStyle($db, $styleId);
        self::saveStyleParams($db, $styleId, $params);

        $app->enqueueMessage(Text::_('TPL_J51_EMANI_PRESET_IMPORT_SUCCESS'));
    }

    public static function exportAjax(): string
    {
        self::checkAccess();

        $app   = Factory::getApplication();
        $input = $app->getInput();
        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $style = self::getStyle($db, $input->getInt('id'));

        // Download als JSON
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . self::TEMPLATE_NAME . '-preset.json"');

        return $style->params;
    }

    public static function loadPresetAjax(): void
    {
        self::checkAccess();

        $app       = Factory::getApplication();
        $input     = $app->getInput();
        $preset    = $input->getCmd('preset');
        $styleId   = $input->getInt('id');
        $presetDir = realpath(JPATH_ROOT . '/templates/' . self::TEMPLATE_NAME . '/presets');
        $path      = $presetDir ? realpath($presetDir . '/' . $preset . '.json') : false;

        if (!$path || !str_starts_with($path, $presetDir . DIRECTORY_SEPARATOR) || !is_file($path)) {
            throw new \RuntimeException(
                Text::_('TPL_J51_EMANI_PRESET_NOT_FOUND')
            );
        }

        $presetData = file_get_contents($path);

        if ($presetData === false) {
            throw new \RuntimeException(Text::_('TPL_J51_EMANI_PRESET_READ_FAILED'));
        }

        $params = self::normalisePresetJson($presetData);
        $db     = Factory::getContainer()->get(DatabaseInterface::class);

        self::getStyle($db, $styleId);
        self::saveStyleParams($db, $styleId, $params);

        $app->enqueueMessage(Text::sprintf('TPL_J51_EMANI_PRESET_LOAD_SUCCESS', $preset));
    }

    private static function normalisePresetJson(string $contents): string
    {
        try {
            $data = json_decode($contents, false, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new \RuntimeException(Text::_('TPL_J51_EMANI_PRESET_INVALID_JSON'), 400, $exception);
        }

        if (!is_object($data)) {
            throw new \RuntimeException(Text::_('TPL_J51_EMANI_PRESET_OBJECT_REQUIRED'), 400);
        }

        try {
            return json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } catch (\JsonException $exception) {
            throw new \RuntimeException(Text::_('TPL_J51_EMANI_PRESET_PROCESS_FAILED'), 400, $exception);
        }
    }

    private static function getStyle(DatabaseInterface $db, int $styleId): object
    {
        if ($styleId < 1) {
            throw new \RuntimeException(Text::_('TPL_J51_EMANI_STYLE_ID_INVALID'), 400);
        }

        $query = $db->createQuery()
            ->select([
                $db->quoteName('id'),
                $db->quoteName('template'),
                $db->quoteName('params'),
            ])
            ->from($db->quoteName('#__template_styles'))
            ->where($db->quoteName('id') . ' = ' . $styleId)
            ->where($db->quoteName('template') . ' = ' . $db->quote(self::TEMPLATE_NAME))
            ->where($db->quoteName('client_id') . ' = 0');

        $style = $db->setQuery($query)->loadObject();

        if (!$style) {
            throw new \RuntimeException(Text::_('TPL_J51_EMANI_STYLE_NOT_FOUND'), 404);
        }

        return $style;
    }

    private static function saveStyleParams(DatabaseInterface $db, int $styleId, string $params): void
    {
        $query = $db->createQuery()
            ->update($db->quoteName('#__template_styles'))
            ->set($db->quoteName('params') . ' = ' . $db->quote($params))
            ->where($db->quoteName('id') . ' = ' . $styleId)
            ->where($db->quoteName('template') . ' = ' . $db->quote(self::TEMPLATE_NAME))
            ->where($db->quoteName('client_id') . ' = 0');

        $db->setQuery($query)->execute();
    }

    private static function checkAccess(): void
    {
        if (!Session::checkToken('get')) {
            throw new \RuntimeException(Text::_('JINVALID_TOKEN'), 403);
        }

        if (!Factory::getApplication()->getIdentity()->authorise('core.edit', 'com_templates')) {
            throw new \RuntimeException(Text::_('TPL_J51_EMANI_ACTION_NOT_ALLOWED'), 403);
        }
    }
}
