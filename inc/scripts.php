<?php
defined('_JEXEC') or die('Restricted index access');
$wa->registerScript('j51.template.mmenu', 'templates/' . $this->template . '/js/mmenu-light.js', [], ['defer' => true]);
$wa->registerScript('j51.template.waypoints', 'templates/' . $this->template . '/js/noframework.waypoints.min.js', [], ['defer' => true]);
$wa->registerScript('j51.template.jarallax', 'templates/' . $this->template . '/js/jarallax.min.js', [], ['defer' => true]);
$wa->registerScript('j51.template.sticky', 'templates/' . $this->template . '/js/sticky.js', [], ['defer' => true]);
$wa->registerScript('j51.template.script', 'templates/' . $this->template . '/js/scripts.js', [], ['defer' => true]);

// ✅ jQuery wird von Joomla6 Core bereitgestellt - NICHT selbst laden!
// $wa->useScript('template.jquery'); // ENTFERNT

// MMenu
$wa->useScript('j51.template.mmenu');

// Animate on Scroll
$wa->useScript('j51.template.waypoints');

if ($sidecol_responsive_pos == 'after') {
    // ✅ KORRIGIERT: addStyleDeclaration -> WebAssetManager
    $wa->addInlineStyle('
        @media only screen and (max-width: 767px) {
            .sidecol_a, .sidecol_b {
                order: 1 !important;
            }
        }
    ');
}

if ($this->params->get('top1_parallax') == '1'
    || $this->params->get('top2_parallax') == '1'
    || $this->params->get('top3_parallax') == '1'
    || $this->params->get('bottom1_parallax') == '1'
    || $this->params->get('bottom2_parallax') == '1'
    || $this->params->get('bottom3_parallax') == '1'
    || $this->params->get('base1_parallax') == '1'
    || $this->params->get('base2_parallax') == '1') {
    
    // ✅ Parallax Background
    $wa->useScript('j51.template.jarallax');
    
    // ✅ KORRIGIERT: addScriptDeclaration -> addInlineScript
    $wa->addInlineScript("
        window.addEventListener('load', function () {
            if (typeof jarallax === 'function') {
                jarallax(document.querySelectorAll('.jarallax'));
            }
        });
    ");
}

if ($this->params->get('sticky_sw')) {
    $wa->useScript('j51.template.sticky');
    
    $wa->addInlineScript('
        window.addEventListener("load", function(){
            var header = document.getElementById("container_header");
            if (header && typeof Sticky !== "undefined") {
                Sticky("#container_header", { topSpacing: 0 });
            }
        });
    ');
}


// ✅ Script Options - OK, keine Änderung nötig
$this->addScriptOptions('j51_template', [
    'scrolltoOffset'    => $this->params->get('scrollto_offset', -55),
    'mobileMenuPosition' => $this->params->get('mobile_menu_position', 'left'),
    'mobileMenuTitle'   => $this->params->get('mobile_menu_title', 'Menu'),
]);

$wa->useScript('j51.template.script');

