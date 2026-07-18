<?php
defined('_JEXEC') or die('Restricted index access');

$wa  = $this->getWebAssetManager();

$fontawesome_sw = $this->params->get('fontawesome_sw', 1);
$fontawesome_solid = $this->params->get('fontawesome_solid', 1);
$fontawesome_reg = $this->params->get('fontawesome_reg', 1);
$fontawesome_brands = $this->params->get('fontawesome_brands', 1);
$animatecss_sw = $this->params->get('animatecss_sw', 1);

$wa->registerStyle('j51.template.base', 'templates/' . $this->template . '/css/base/template.css');
$wa->registerStyle('j51.template.bonsai.util.min', 'templates/' . $this->template . '/css/bonsaicss/bonsai-utilities.min.css');
$wa->registerStyle('j51.template.bonsai.util', 'templates/' . $this->template . '/css/bonsaicss/bonsai-utilities.css');
$wa->registerStyle('j51.template.nexus.min', 'templates/' . $this->template . '/css/nexus.min.css');
$wa->registerStyle('j51.template.nexus', 'templates/' . $this->template . '/css/nexus.css');
$wa->registerStyle('j51.template.responsive.min', 'templates/' . $this->template . '/css/responsive.min.css');
$wa->registerStyle('j51.template.responsive', 'templates/' . $this->template . '/css/responsive.css');
$wa->registerStyle('j51.template.animate.min', 'templates/' . $this->template . '/css/animate.min.css');
$wa->registerStyle('j51.template.animate', 'templates/' . $this->template . '/css/animate.css');
$wa->registerStyle('j51.fontawesome', 'templates/' . $this->template . '/css/fontawesome/fontawesome.min.css');
$wa->registerStyle('j51.fontawesome.shims', 'templates/' . $this->template . '/css/fontawesome/v4-shims.min.css');
$wa->registerStyle('j51.fontawesome.regular', 'templates/' . $this->template . '/css/fontawesome/regular.min.css');
$wa->registerStyle('j51.fontawesome.brands', 'templates/' . $this->template . '/css/fontawesome/brands.min.css');
$wa->registerStyle('j51.fontawesome.solid', 'templates/' . $this->template . '/css/fontawesome/solid.min.css');

if ($min_css) {
	$wa->useStyle('j51.template.base');
	$wa->useStyle('j51.template.bonsai.util.min');
	$wa->useStyle('j51.template.nexus.min');
	$wa->useStyle('j51.template.responsive.min');
} else {
	$wa->useStyle('j51.template.base');
	$wa->useStyle('j51.template.bonsai.util');
	$wa->useStyle('j51.template.nexus');
	$wa->useStyle('j51.template.responsive');
}

if ($animatecss_sw) {
	if ($min_css) {
		$wa->useStyle('j51.template.animate.min');
	} else {
		$wa->useStyle('j51.template.animate');
	}
}

if ($fontawesome_sw) {
	$wa->useStyle('j51.fontawesome');
	$wa->useStyle('j51.fontawesome.shims');
	if ($fontawesome_reg) {
		$wa->useStyle('j51.fontawesome.regular');
	}
	if ($fontawesome_brands) {
		$wa->useStyle('j51.fontawesome.brands');
	}
	if ($fontawesome_solid) {
		$wa->useStyle('j51.fontawesome.solid');
	}
}

require_once('fonts.php');
require_once('style.php');
require_once('overrides.php');
require_once('custom_css.php');
