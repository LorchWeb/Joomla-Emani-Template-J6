<?php
defined('_JEXEC') or die('Restricted index access');

// ✅ jQuery wird von Joomla6 Core bereitgestellt - NICHT selbst laden!
// $wa->useScript('template.jquery'); // ENTFERNT

// MMenu
$wa->useScript('template.mmenu');

// Animate on Scroll
$wa->useScript('template.waypoints');

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

if (!$this->params->get('top1_bg') 
    || !$this->params->get('top2_bg') 
    || !$this->params->get('top3_bg') 
    || !$this->params->get('bottom1_bg') 
    || !$this->params->get('bottom2_bg') 
    || !$this->params->get('bottom3_bg')) {
    
    // ✅ Parallax Background
    $wa->useScript('template.jarallax');
    
    // ✅ KORRIGIERT: addScriptDeclaration -> addInlineScript
    $wa->addInlineScript("
        jarallax(document.querySelectorAll('.jarallax'));
    ");
}

if ($this->params->get('sticky_sw')) {
    $wa->useScript('template.sticky');
    
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
$wa->addScriptOptions('j51_template', [
    'scrolltoOffset'    => $this->params->get('scrollto_offset', -55),
    'mobileMenuPosition' => $this->params->get('mobile_menu_position', 'left'),
    'mobileMenuTitle'   => $this->params->get('mobile_menu_title', 'Menu'),
]);

$wa->useScript('template.script');

