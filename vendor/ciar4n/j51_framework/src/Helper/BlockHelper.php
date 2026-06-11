<?php
namespace J51\Helper;

defined('_JEXEC') or die('Restricted index access');

use Joomla\CMS\Factory;

class BlockHelper
{
    /**
     * Function to check if module exists in block.
     *
     * @param   object  $tpl     The template
     * @param   string  $module  The module block name
     *
     * @return bool
     */
    public function blockExists($tpl, $module): bool
    {
        return (
            $tpl->countModules($module . 'a') ||
            $tpl->countModules($module . 'b') ||
            $tpl->countModules($module . 'c') ||
            $tpl->countModules($module . 'd') ||
            $tpl->countModules($module . 'e') ||
            $tpl->countModules($module . 'f')
        );
    }

    /**
     * Function to output module block.
     *
     * @param   object  $tpl          The template
     * @param   string  $moduleBlock  The module block name
     */
    public function renderBlock($tpl, $moduleBlock): void
    {
        $moduleColumns     = range('a', 'f');
        $moduleBlockClean  = str_replace('-', '', $moduleBlock);  // eg. top1
        $moduleBlockVar    = str_replace('-', '_', $moduleBlock); // eg. top_1

        // ✅ WebAssetManager statt $document
        $wa = Factory::getApplication()
                     ->getDocument()
                     ->getWebAssetManager();

        // Anzahl belegter Spalten zählen
        $counted = 0;
        foreach ($moduleColumns as $col) {
            if ($tpl->countModules($moduleBlock . $col)) {
                $counted++;
            }
        }

        // Auto-Breite berechnen
        $widthMap = [
            6 => '16.66%',
            5 => '20%',
            4 => '25%',
            3 => '33.3%',
            2 => '50%',
            1 => '100%',
        ];
        $moduleWidthAuto = $widthMap[$counted] ?? '100%';

        echo '<div id="' . $moduleBlockClean . '_modules" class="block_holder">';

        if ($this->blockExists($tpl, $moduleBlock)) {

            echo '<div id="wrapper_' . $moduleBlock . '" class="block_holder_margin">';

            foreach ($moduleColumns as $moduleColumn) {
                $moduleBlockColumn = $moduleBlock . $moduleColumn;        // eg. block-1a
                $moduleBlockColVar = str_replace('-', '_', $moduleBlockColumn); // eg. block_1a

                if ($tpl->countModules($moduleBlockColumn)) {

                    // auto = 0 bedeutet: automatische Breite verwenden
                    if ($tpl->params->get($moduleBlockClean . '_auto') != '1') {
                        echo '<div class="' . $moduleBlock . ' ' . $moduleBlockColumn . '" '
                           . 'style="max-width:' . $moduleWidthAuto . ';">';
                    } else {
                        $manualWidth = (int) $tpl->params->get($moduleBlockColVar . '_manual', 100);
                        echo '<div class="' . $moduleBlock . ' ' . $moduleBlockColumn . '" '
                           . 'style="flex: 0 0 ' . $manualWidth . '%; max-width:' . $manualWidth . '%;">';
                    }

                    echo '<jdoc:include type="modules" name="' . $moduleBlockColumn . '" style="mod_standard"/>';
                    echo '</div>';
                }
            }

            echo '<div class="clear"></div>';
            echo '</div>';

            // ✅ Background Image
            $moduleBlockCleanBg = $tpl->params->get($moduleBlockClean . '_bg');
            if (!empty($moduleBlockCleanBg)) {
                $wa->addInlineStyle(
                    '#container_' . $moduleBlockClean . '_modules {'
                    . 'background-image: url(' . $tpl->baseurl . '/' . $moduleBlockCleanBg . ');'
                    . '}'
                );
            }

            // ✅ Background Color - nur wenn gesetzt
            $moduleBlockCleanColor = $tpl->params->get($moduleBlockClean . '_color');
            if (!empty($moduleBlockCleanColor)) {
                $wa->addInlineStyle(
                    '#container_' . $moduleBlockClean . '_modules {'
                    . 'background-color: ' . $moduleBlockCleanColor . ';'
                    . '}'
                );
            }

            // ✅ 100% Width
            if ($tpl->params->get($moduleBlockClean . '_width100') != '0') {
                $wa->addInlineStyle(
                    '#container_' . $moduleBlockClean . '_modules > .wrapper960 { width: 100%; }'
                    . ' #' . $moduleBlockClean . '_modules.block_holder { padding: 0; }'
                );
            }

            // ✅ Remove Padding
            if ($tpl->params->get($moduleBlockClean . '_padding') != '0') {
                $wa->addInlineStyle(
                    '#' . $moduleBlockClean . '_modules.block_holder,'
                    . ' #' . $moduleBlockClean . '_modules .module_surround,'
                    . ' #' . $moduleBlockClean . '_modules .module_content { padding: 0; }'
                );
            }

            // ✅ Mobile disable
            if ($tpl->params->get('res_' . $moduleBlockClean . '_sw') != '1') {
                $wa->addInlineStyle(
                    '@media only screen and (max-width: 767px) {'
                    . ' #container_' . $moduleBlockClean . '_modules { display: none; }'
                    . '}'
                );
            }
        }

        echo '</div>';
    }
}
