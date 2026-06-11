<?php
defined('_JEXEC') or die('Restricted index access');

$googlefonts = array(
    $body_fontstyle,
    $h1head_fontstyle,
    $articlehead_fontstyle,
    $modulehead_fontstyle,
    $hornav_fontstyle,
    $h4head_fontstyle
);

$websafefonts = array(
	"Arial, sans-serif",
	"Arial, Helvetica, sans-serif",
	"Courier, monospace",
	"Garamond, serif",
	"Georgia, serif",
	"Impact, Charcoal, sans-serif",
	"Lucida Console, Monaco, monospace",
	"Lucida Sans Unicode, Lucida Grande, sans-serif",
	"MS Sans Serif, Geneva, sans-serif",
	"MS Serif, New York, sans-serif",
	"Palatino Linotype, Book Antiqua, Palatino, serif",
	"Tahoma, Geneva, sans-serif",
	"Times New Roman, Times, serif",
	"Trebuchet MS, Helvetica, sans-serif",
	"Verdana, Geneva, sans-serif",
	"Arial"
);

if ($this->params->get('logoImage') == '0') {
    array_push($googlefonts, $logo_fontstyle);
}

$googlefonts  = array_diff($googlefonts, $websafefonts);
$googlefonts  = array_keys(array_flip($googlefonts));
$font_subset  = str_replace(' ', '', $font_subset);
$font_weights = str_replace(' ', '', $font_weights);

$wa->registerAndUseStyle(
    'template.local-fonts',
    'templates/' . $this->template . '/css/local-fonts.css'
);