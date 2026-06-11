<?php
defined('_JEXEC') or die('Restricted index access');

// BC
function j51BlockExists($tpl, $module)
{
    return (new \J51\Helper\BlockHelper())->blockExists($tpl, $module);
}

// BC
function j51Block($tpl, $module)
{
    return (new \J51\Helper\BlockHelper())->renderBlock($tpl, $module);
}

// ✅ Modulzählung - keine Änderungen nötig, countModules() ist in J6 noch verfügbar
$contenttop_counted = 0;
if ($this->countModules('contenttop-a')) { $contenttop_counted++; }
if ($this->countModules('contenttop-b')) { $contenttop_counted++; }
if ($this->countModules('contenttop-c')) { $contenttop_counted++; }

$contenttop_width = match($contenttop_counted) {
    3 => '33.3%',
    2 => '49.9%',
    1 => '100%',
    default => '100%'
};


//COUNT MODULES IN CONTENTBOTTOM - DECIDE WIDTH - COLLAPSE IF NECESSARY
$contentbottom_counted = 0;
if ($this->countModules('contentbottom-a')) { $contentbottom_counted++; }
if ($this->countModules('contentbottom-b')) { $contentbottom_counted++; }
if ($this->countModules('contentbottom-c')) { $contentbottom_counted++; }

$contentbottom_width = match($contentbottom_counted) {
    3 => '33.3%',
    2 => '49.9%',
    1 => '100%',
    default => '100%'
};
