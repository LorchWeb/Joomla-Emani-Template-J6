<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormField;

/**
 * Form Field class for the Joomla CMS.
 * Renders a select list of Web Safe Fonts and Google Fonts.
 *
 * @since  3.0
 */
class JFormFieldGoogleFonts extends FormField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  3.0
     */
    protected $type = 'GoogleFonts';

    /**
     * Method to get the field input markup for a generic list.
     *
     * @return  string  The field input markup.
     * @since   3.0
     */
    protected function getInput()
    {
        $html = [];
        $attr = '';

        // ✅ Basis-Klasse
        $attr .= 'class="form-select"';

        // ✅ === statt == für String-Vergleich
        if ((string) $this->element['readonly'] === 'true'
            || (string) $this->element['disabled'] === 'true')
        {
            $attr .= ' disabled="disabled"';
        }

        $attr .= $this->element['size']
            ? ' size="' . (int) $this->element['size'] . '"'
            : '';

        $attr .= $this->multiple ? ' multiple="multiple"' : '';

        // Get the field options
        $options = $this->getOptions();

        // Read-only: render list without name + hidden input for value
        if ((string) $this->element['readonly'] === 'true')
        {
            $html[] = HTMLHelper::_(
                'select.genericlist',
                $options,
                '',
                trim($attr),
                'value',
                'text',
                $this->value,
                $this->id
            );
            $html[] = '<input type="hidden" name="'
                . $this->name
                . '" value="'
                . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8')
                . '"/>';
        }
        else
        {
            $html[] = HTMLHelper::_(
                'select.genericlist',
                $options,
                $this->name,
                trim($attr),
                'value',
                'text',
                $this->value,
                $this->id
            );
        }

        return implode('', $html);
    }

    /**
     * Method to get the field options.
     * Returns an array of Web Safe Fonts and Google Fonts as option objects.
     *
     * @return  array  The field option objects.
     * @since   3.0
     */
    protected function getOptions()
    {
        // ✅ Font-Daten als statisches Array - KEIN foreach nötig
        $fontData = [

            // ── Web Safe Fonts ──────────────────────────────────────────────
            'Arial, sans-serif'                                => '-------- Web Safe Fonts --------',
            'Arial, Helvetica, sans-serif'                     => 'Arial',
            'Courier, monospace'                               => 'Courier',
            'Garamond, serif'                                  => 'Garamond',
            'Georgia, serif'                                   => 'Georgia',
            'Impact, Charcoal, sans-serif'                     => 'Impact',
            'Lucida Console, Monaco, monospace'                => 'Lucida Console',
            'Lucida Sans Unicode, Lucida Grande, sans-serif'   => 'Lucida Sans Unicode',
            'MS Sans Serif, Geneva, sans-serif'                => 'MS Sans Serif',
            'MS Serif, New York, sans-serif'                   => 'MS Serif',
            'Palatino Linotype, Book Antiqua, Palatino, serif' => 'Palatino Linotype',
            'Tahoma, Geneva, sans-serif'                       => 'Tahoma',
            'Times New Roman, Times, serif'                    => 'Times New Roman',
            'Trebuchet MS, Helvetica, sans-serif'              => 'Trebuchet MS',
            'Verdana, Geneva, sans-serif'                      => 'Verdana',

            // ── Google Fonts ─────────────────────────────────────────────────
            'Arial'                          => '---------- Google Fonts ----------',
            'ABeeZee'                         => 'ABeeZee',
            'Actor'                           => 'Actor',
            'Abril+Fatface'                   => 'Abril Fatface',
            'Allan'                           => 'Allan',
            'Allerta'                         => 'Allerta',
            'Allerta+Stencil'                 => 'Allerta Stencil',
            'Anonymous+Pro'                   => 'Anonymous Pro',
            'Amiri'                           => 'Amiri',
            'Anton'                           => 'Anton',
            'Arimo'                           => 'Arimo',
            'Archivo+Black'                   => 'Archivo Black',
            'Arvo'                            => 'Arvo',
            'Assistant'                       => 'Assistant',
            'Astloch'                         => 'Astloch',
            'Barlow+Semi+Condensed'           => 'Barlow Semi Condensed',
            'Bentham'                         => 'Bentham',
            'Bevan'                           => 'Bevan',
            'Buda'                            => 'Buda',
            'Cabin'                           => 'Cabin',
            'Calligraffitti'                  => 'Calligraffitti',
            'Cantarell'                       => 'Cantarell',
            'Cardo'                           => 'Cardo',
            'Carme'                           => 'Carme',
            'Catamaran'                       => 'Catamaran',
            'Cherry+Cream+Soda'               => 'Cherry Cream Soda',
            'Chewy'                           => 'Chewy',
            'Coda'                            => 'Coda',
            'Coming+Soon'                     => 'Coming Soon',
            'Comfortaa'                       => 'Comfortaa',
            'Copse'                           => 'Copse',
            'Corben'                          => 'Corben',
            'Cormorant'                       => 'Cormorant',
            'Cookie'                          => 'Cookie',
            'Cousine'                         => 'Cousine',
            'Covered+By+Your+Grace'           => 'Covered By Your Grace',
            'Crafty+Girls'                    => 'Crafty Girls',
            'Crimson+Text'                    => 'Crimson Text',
            'Crushed'                         => 'Crushed',
            'Cuprum'                          => 'Cuprum',
            'Cutive'                          => 'Cutive',
            'Dosis'                           => 'Dosis',
            'Dancing+Script'                  => 'Dancing Script',
            'Droid+Sans'                      => 'Droid Sans',
            'Droid+Sans+Mono'                 => 'Droid Sans Mono',
            'Droid+Serif'                     => 'Droid Serif',
            'Economica'                       => 'Economica',
            'Erica+One'                       => 'Erica One',
            'Expletus+Sans'                   => 'Expletus Sans',
            'Fontdiner+Swanky'                => 'Fontdiner Swanky',
            'Fira+Sans'                       => 'Fira Sans',
            'Geo'                             => 'Geo',
            'Goudy+Bookletter+1911'           => 'Goudy Bookletter 1911',
            'Grand+Hotel'                     => 'Grand Hotel',
            'Great+Vibes'                     => 'Great Vibes',
            'Gruppo'                          => 'Gruppo',
            'Hammersmith+One'                 => 'Hammersmith One',
            'Hind'                            => 'Hind',
            'Homemade+Apple'                  => 'Homemade Apple',
            'Helvetica'                       => 'Helvetica',
            'IM+Fell'                         => 'IM Fell',
            'Inconsolata'                     => 'Inconsolata',
            'Irish+Grover'                    => 'Irish Grover',
            'Jomhuria'                        => 'Jomhuria',
            'Josefin+Slab'                    => 'Josefin Slab',
            'Josefin+Sans'                    => 'Josefin Sans',
            'Josefin+Sans+Std+Light'          => 'Josefin Sans Std Light',
            'Junge'                           => 'Junge',
            'Just+Another+Hand'               => 'Just Another Hand',
            'Just+Me+Again+Down+Here'         => 'Just Me Again Down Here',
            'Kenia'                           => 'Kenia',
            'Kranky'                          => 'Kranky',
            'Kreon'                           => 'Kreon',
            'Kristi'                          => 'Kristi',
            'Lato'                            => 'Lato',
            'Lekton'                          => 'Lekton',
            'Lobster'                         => 'Lobster',
            'Lora'                            => 'Lora',
            'Luckiest+Guy'                    => 'Luckiest Guy',
            'Mako'                            => 'Mako',
            'Meddon'                          => 'Meddon',
            'Merriweather'                    => 'Merriweather',
            'Metrophobic'                     => 'Metrophobic',
            'Michroma'                        => 'Michroma',
            'Molengo'                         => 'Molengo',
            'Montserrat'                      => 'Montserrat',
            'Montserrat+Alternates'           => 'Montserrat Alternates',
            'Mountains+of+Christmas'          => 'Mountains of Christmas',
            'Muli'                            => 'Muli',
            'Noto+Sans'                       => 'Noto Sans',
            'Neucha'                          => 'Neucha',
            'Neuton'                          => 'Neuton',
            'Nobile'                          => 'Nobile',
            'Nunito'                          => 'Nunito',
            'OFL+Sorts+Mill+Goudy+TT'         => 'OFL Sorts Mill Goudy TT',
            'Old+Standard+TT'                 => 'Old Standard TT',
            'Open+Sans'                       => 'Open Sans',
            'Oranienbaum'                     => 'Oranienbaum',
            'Orbitron'                        => 'Orbitron',
            'Oswald'                          => 'Oswald',
            'Pacifico'                        => 'Pacifico',
            'Passion+One'                     => 'Passion One',
            'Pathway+Gothic+One'              => 'Pathway Gothic One',
            'Permanent+Marker'                => 'Permanent Marker',
            'Playfair+Display'                => 'Playfair Display',
            'Philosopher'                     => 'Philosopher',
            'Poppins'                         => 'Poppins',
            'PT+Sans'                         => 'PT Sans',
            'PT+Serif'                        => 'PT Serif',
            'Poiret+One'                      => 'Poiret One',
            'Puritan'                         => 'Puritan',
            'Questrial'                       => 'Questrial',
            'Radley'                          => 'Radley',
            'Raleway'                         => 'Raleway',
            'Reenie+Beanie'                   => 'Reenie Beanie',
            'Roboto'                          => 'Roboto',
            'Roboto+Condensed'                => 'Roboto Condensed',
            'Rock+Salt'                       => 'Rock Salt',
            'Rubik'                           => 'Rubik',
            'Sans-Serif'                      => 'Sans-Serif',
            'Schoolbell'                      => 'Schoolbell',
            'Share'                           => 'Share',
            'Slackey'                         => 'Slackey',
            'Sniglet'                         => 'Sniglet',
            'Source+Sans+3'                   => 'Source Sans 3',
            'Source+Sans+Pro'                 => 'Source Sans Pro',
            'Sunshiney'                       => 'Sunshiney',
            'Syncopate'                       => 'Syncopate',
            'Tangerine'                       => 'Tangerine',
            'Tinos'                           => 'Tinos',
            'Titillium+Web'                   => 'Titillium Web',
            'Trirong'                         => 'Trirong',
            'Ubuntu'                          => 'Ubuntu',
            'UnifrakturCook'                  => 'UnifrakturCook',
            'UnifrakturMaguntia'              => 'UnifrakturMaguntia',
            'Unkempt'                         => 'Unkempt',
            'Vibur'                           => 'Vibur',
            'Vollkorn'                        => 'Vollkorn',
            'VT323'                           => 'VT323',
            'Walter+Turncoat'                 => 'Walter Turncoat',
            'Yanone+Kaffeesatz'               => 'Yanone Kaffeesatz',
        ];

        // ✅ Font-Daten in HTMLHelper-kompatible Option-Objekte umwandeln
        $options = [];

        foreach ($fontData as $value => $text)
        {
            $options[] = HTMLHelper::_('select.option', $value, $text);
        }

        return $options;
    }
}
