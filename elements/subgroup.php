<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// ✅ _JEXEC statt JPATH_PLATFORM
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\WebAsset\WebAssetManager;

/**
 * Form Field class for the Joomla CMS.
 * Renders a subgroup fieldset with optional enabler toggle.
 *
 * @since  3.0
 */
class JFormFieldSubGroup extends FormField
{
    /**
     * The field type.
     *
     * @var string
     */
    protected $type = 'SubGroup';

    /**
     * Method to get the field input markup.
     * Renders an optional Yes/No radio toggle (enabler).
     *
     * @return  string  The field input markup.
     */
    protected function getInput()
    {
        $html = '';

        if ($this->element['enabler'])
        {
            // ✅ === statt == für String-Vergleich
            $enablerClass = ((string) $this->element['enabler'] === 'top')
                ? 'top-group-enabler'
                : 'group-enabler';

            $html .= '<fieldset class="btn-group btn-group-yesno '
                . $enablerClass
                . ' radio" id="'
                . $this->id
                . '">';

            // ✅ (int) Cast für sicheren Wert-Vergleich
            $html .= '<input type="radio" '
                . ((int) $this->value === 1 ? 'checked="checked" ' : '')
                . 'value="1" name="' . $this->name . '" id="' . $this->id . '0">';
            $html .= '<label for="' . $this->id . '0" class="btn">'
                . Text::_('JON')
                . '</label>';

            $html .= '<input type="radio" '
                . ((int) $this->value === 0 ? 'checked="checked" ' : '')
                . 'value="0" name="' . $this->name . '" id="' . $this->id . '1">';
            $html .= '<label for="' . $this->id . '1" class="btn">'
                . Text::_('JOFF')
                . '</label>';

            $html .= '</fieldset>';
        }

        return $html;
    }

    /**
     * Method to get the field label markup.
     * Renders an h3 legend with optional icon, tooltip and expend attribute.
     *
     * @return  string  The field label markup.
     */
    protected function getLabel()
    {
        // Get the label text from the XML element, defaulting to the element name
        $text = $this->element['label']
            ? (string) $this->element['label']
            : (string) $this->element['name'];
        $text = $this->translateLabel ? Text::_($text) : $text;

        $desc = $this->element['description']
            ? (string) $this->element['description']
            : '';
        $desc = $this->translateLabel ? Text::_($desc) : $desc;

        // Build CSS class string
        $class  = 'legend';
        $class .= !empty($this->class) ? ' ' . $this->class : '';
        $class .= $this->element['subgroup'] ? ' sub-legend' : '';

        // ✅ htmlspecialchars für icon-Attribut
        $icon = $this->element['icon']
            ? '<span class="fas fa-'
                . htmlspecialchars((string) $this->element['icon'], ENT_QUOTES, 'UTF-8')
                . '"></span>'
            : '';

        // ✅ htmlspecialchars für expend-Attribut
        $expend = $this->element['expend']
            ? ' data-expend="'
                . htmlspecialchars((string) $this->element['expend'], ENT_QUOTES, 'UTF-8')
                . '"'
            : '';

        // ✅ htmlspecialchars statt htmlentities für Tooltip
        $tooltip = $desc
            ? ' class="hasTooltip" title="'
                . htmlspecialchars($desc, ENT_QUOTES, 'UTF-8')
                . '"'
            : '';

        return '<h3 class="' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '"'
            . $expend
            . '><span'
            . $tooltip
            . '>'
            . $icon
            . $text
            . '</span></h3>';
    }

    /**
     * Method to attach a Form\Field to the form.
     * Registers script options and loads the field JS asset.
     *
     * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag.
     * @param   mixed             $value    The form field value to validate.
     * @param   string            $group    The field name group control value.
     *
     * @return  boolean  True on success.
     */
    public function setup(SimpleXMLElement $element, $value, $group = null)
    {
        // ✅ WebAssetManager statt deprecated $doc->addScript()
        $app = Factory::getApplication();
        HTMLHelper::_('jquery.framework');

        // Aktuellen Style-ID für JS bereitstellen
        $app->getDocument()->addScriptOptions(
            'j51.template.style',
            $app->getInput()->getInt('id')
        );

        // Template-Verzeichnis ermitteln
        $templateName = basename(dirname(__DIR__));
        $scriptUrl    = Uri::root()
            . 'templates/'
            . $templateName
            . '/elements/js/script.js';

        // ✅ WebAssetManager für Script-Einbindung
        /** @var WebAssetManager $wa */
        $wa = $app->getDocument()->getWebAssetManager();
        $wa->registerAndUseScript(
            'j51.subgroup.script',  // Asset-Name (eindeutig)
            $scriptUrl,
            [],                      // Options
            ['defer' => true],       // Attributes
            []                       // Dependencies
        );

        return parent::setup($element, $value, $group);
    }
}
