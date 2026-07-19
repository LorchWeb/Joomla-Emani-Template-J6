<?php
defined('_JEXEC') or die('Restricted index access');

$customcss_source = $this->params->get('customcss_source', 'default');
$customcss_url    = $this->params->get('customcss_url', '');
$customcss_name   = $this->params->get('customcss_name', 'custom');

// ✅ Inline Custom CSS aus Template-Parametern
$custom_css_inline = $this->params->get('custom_css');
if (!empty($custom_css_inline)) {
    $wa->addInlineStyle($custom_css_inline);
}

// ✅ Custom CSS Datei laden
if ($this->params->get('customcss_sw') == "1") {

    if ($customcss_source == "default") {
        // ✅ Bereits in joomla.asset.json registriert
        $wa->registerAndUseStyle(
            'j51.template.custom',
            'templates/' . $this->template . '/css/custom.css'
        );
    }

    if ($customcss_source == "template") {
        // ✅ registerAndUseStyle statt addStyleSheet
        $wa->registerAndUseStyle(
            'j51.template.custom.tpl',
            'templates/' . $this->template . '/css/custom.css'
        );
    }

    if ($customcss_source == "media") {
        // ✅ registerAndUseStyle statt addStyleSheet
        $wa->registerAndUseStyle(
            'j51.template.custom.media',
            'media/templates/site/' . $this->template . '/css/custom.css'
        );
    }

    if ($customcss_source == "url" && !empty($customcss_url)) {
        // ✅ Externe URL
        $wa->registerAndUseStyle(
            'j51.template.custom.url',
            $customcss_url
        );
    }
}

// ✅ Responsive Custom CSS - NUR laden wenn Inhalt vorhanden
$tabport_css = $this->params->get('tabport_css', '');
if (!empty($tabport_css) && $tabport_css != "1") {
    $wa->addInlineStyle(
        '@media only screen and (min-width: 768px) and (max-width: 959px) {' 
        . $tabport_css . '}'
    );
}

$mobland_css = $this->params->get('mobland_css', '');
if (!empty($mobland_css) && $mobland_css != "1") {
    $wa->addInlineStyle(
        '@media only screen and (max-width: 767px) {' 
        . $mobland_css . '}'
    );
}

$mobport_css = $this->params->get('mobport_css', '');
if (!empty($mobport_css) && $mobport_css != "1") {
    $wa->addInlineStyle(
        '@media only screen and (max-width: 440px) {' 
        . $mobport_css . '}'
    );
}
