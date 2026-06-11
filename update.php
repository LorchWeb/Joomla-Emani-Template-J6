<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Installer\InstallerScript;
use Joomla\CMS\Language\Text;

class J51_EmaniInstallerScript extends InstallerScript
{
    protected function fixTemplateMode(): void
    {
        // ✅ Factory::getContainer()->get('DatabaseDriver') ist korrekt für J4/J5
        $db = Factory::getContainer()->get('DatabaseDriver');

        $query = $db->getQuery(true)
            ->update($db->quoteName('#__template_styles'))
            ->set($db->quoteName('inheritable') . ' = 1')
            ->where($db->quoteName('template') . ' = ' . $db->quote('j51_emani'))
            ->where($db->quoteName('client_id') . ' = 0');

        try {
            $db->setQuery($query)->execute();
        } catch (\Exception $e) {
            // ✅ \Exception mit Backslash für globalen Namespace
            echo Text::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()) . '<br>';
            return;
        }
    }

    public function postflight($type, $installer)
    {
        $foldersToDelete = [
            '/templates/j51_emani/html/com_contact',
            '/templates/j51_emani/html/com_content/article',
            '/templates/j51_emani/html/com_finder',
            '/templates/j51_emani/html/com_media',
            '/templates/j51_emani/html/com_search',
            '/templates/j51_emani/html/com_users',
            '/templates/j51_emani/html/layout/joomla',
            '/templates/j51_emani/html/mod_breadcrumbs',
            '/templates/j51_emani/html/mod_custom',
            '/templates/j51_emani/html/mod_finder',
            '/templates/j51_emani/html/mod_login',
            '/templates/j51_emani/html/mod_search',
            '/templates/j51_emani/html/mod_tags_popular',
            '/templates/j51_emani/html/plg_content_pagenavigation',
            '/templates/j51_emani/html/plg_content_vote',
        ];

        $filesToDelete = [
            '/templates/j51_emani/html/modules.php',
        ];

        foreach ($foldersToDelete as $folder) {
            if (Folder::exists(JPATH_ROOT . $folder)) {
                Folder::delete(JPATH_ROOT . $folder);
            }
        }

        foreach ($filesToDelete as $file) {
            if (File::exists(JPATH_ROOT . $file)) {
                File::delete(JPATH_ROOT . $file);
            }
        }

        $this->fixTemplateMode();
    }
}
