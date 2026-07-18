<?php

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$document = Factory::getApplication()->getDocument();
$wa       = $document->getWebAssetManager();

if (!$wa->assetExists('script', 'mod_j51layerslideshow.tiny-slider')) {
    $wa->registerScript('mod_j51layerslideshow.tiny-slider', 'media/mod_j51layerslideshow/js/tiny-slider.min.js');
}

if (!$wa->assetExists('script', 'mod_j51layerslideshow.imagesloaded')) {
    $wa->registerScript('mod_j51layerslideshow.imagesloaded', 'media/mod_j51layerslideshow/js/imagesloaded.pkgd.min.js');
}

if (!$wa->assetExists('style', 'mod_j51layerslideshow.base')) {
    $wa->registerStyle('mod_j51layerslideshow.base', 'media/mod_j51layerslideshow/css/style.css');
}

if (!$wa->assetExists('style', 'mod_j51layerslideshow.tiny-slider')) {
    $wa->registerStyle('mod_j51layerslideshow.tiny-slider', 'media/mod_j51layerslideshow/css/tiny-slider.min.css');
}

$wa->useScript('mod_j51layerslideshow.tiny-slider');
$wa->useScript('mod_j51layerslideshow.imagesloaded');
$wa->useStyle('mod_j51layerslideshow.base');
$wa->useStyle('mod_j51layerslideshow.tiny-slider');

$wa->addInlineScript('
    document.addEventListener("DOMContentLoaded", function() {
        tns({
            container: "#layerslideshow' . (int) $j51_moduleid . '",
            mode: "gallery",
            items: 1,
            slideBy: "page",
            controls: true,
            autoplay: ' . $autoplay . ',
            autoplayTimeout: ' . $autoplaySpeed . ',
            speed: ' . $speed . ',
            controlsContainer: "#layerslideshow' . (int) $j51_moduleid . '-controls",
            autoplayButton: "#layerslideshow' . (int) $j51_moduleid . '-play",
            navContainer: "#layerslideshow' . (int) $j51_moduleid . '-nav"
        });
    });
', ['name' => 'mod_j51layerslideshow.init.' . (int) $j51_moduleid], [], ['mod_j51layerslideshow.tiny-slider']);

if ($j51_header_overlap) {
    $wa->addInlineStyle('
        @media only screen and (min-width: 768px) {
            #container_header,
            .sticky-wrapper {
                position: absolute;
            }
            #container_header,
            .header_row,
            .header_top {
                background-color: transparent;
            }
            .layerslideshow' . (int) $j51_moduleid . ' .info {
                opacity: 0;
                transition: opacity .5s;
            }
        }
    ', ['name' => 'mod_j51layerslideshow.overlap.' . (int) $j51_moduleid]);

    $wa->addInlineScript('
        window.addEventListener("load", setOverlap' . (int) $j51_moduleid . ');
        window.addEventListener("resize", setOverlap' . (int) $j51_moduleid . ');

        function setOverlap' . (int) $j51_moduleid . '() {
            var header = document.getElementById("container_header");
            var nextArrow = document.getElementById("layerslideshow-next-' . (int) $j51_moduleid . '");
            var prevArrow = document.getElementById("layerslideshow-prev-' . (int) $j51_moduleid . '");

            if (!header) {
                return;
            }

            var headerHeight = header.clientHeight;

            if (nextArrow) {
                nextArrow.style.marginTop = ((headerHeight * 0.5) - 20) + "px";
            }

            if (prevArrow) {
                prevArrow.style.marginTop = ((headerHeight * 0.5) - 20) + "px";
            }

            var elements = document.querySelectorAll(".layerslideshow' . (int) $j51_moduleid . ' .info");

            for (var i = 0; i < elements.length; i++) {
                elements[i].style.paddingTop = headerHeight + "px";
                elements[i].style.opacity = "1";
            }
        }
    ', ['name' => 'mod_j51layerslideshow.overlap.' . (int) $j51_moduleid]);
}

$wa->addInlineStyle('
    .layerslideshow' . (int) $j51_moduleid . ' .layerslideshow-title {
        color:' . $j51_title_color . ';
    }
    .layerslideshow' . (int) $j51_moduleid . ' .layerslideshow-caption {
        color:' . $j51_text_color . ';
    }
    .layerslideshow' . (int) $j51_moduleid . ', .layerslideshow .img-fill {
        max-height: ' . $j51_max_height . 'px;
    }
    .layerslideshow' . (int) $j51_moduleid . ' .info-wrapper,
    .layerslideshow .NextArrow,
    .layerslideshow .PrevArrow {
        background-color: ' . $j51_text_bg_color . ';
    }
    .layerslideshow' . (int) $j51_moduleid . ' .active .layerslideshow-title,
    .layerslideshow' . (int) $j51_moduleid . ' .active .layerslideshow-caption {
        -webkit-animation-duration: ' . $j51_anim_speed . 'ms;
        animation-duration: ' . $j51_anim_speed . 'ms;
    }
    .layerslideshow' . (int) $j51_moduleid . ' .active .layerslideshow-title {
        -webkit-animation-delay: ' . $j51_title_delay . 'ms;
        animation-delay: ' . $j51_title_delay . 'ms;
    }
    .layerslideshow' . (int) $j51_moduleid . ' .active .layerslideshow-caption {
        -webkit-animation-delay: ' . $j51_caption_delay . 'ms;
        animation-delay: ' . $j51_caption_delay . 'ms;
    }
    @media only screen and (max-width: ' . $j51_title_breakpoint . 'px) {
        .layerslideshow' . (int) $j51_moduleid . ' .layerslideshow-title {display: none;}
    }
    @media only screen and (max-width: ' . $j51_caption_breakpoint . 'px) {
        .layerslideshow' . (int) $j51_moduleid . ' .layerslideshow-caption {display: none;}
    }
    @media only screen and (max-width: ' . min($j51_title_breakpoint, $j51_caption_breakpoint) . 'px) {
        .layerslideshow' . (int) $j51_moduleid . ' .info-wrapper {display: none;}
    }
', ['name' => 'mod_j51layerslideshow.theme.' . (int) $j51_moduleid]);

if ($j51_overflow_hidden) {
    $wa->addInlineStyle('
        .layerslideshow-title-container,
        .layerslideshow-caption-container {
            overflow: hidden;
        }
    ', ['name' => 'mod_j51layerslideshow.overflow.' . (int) $j51_moduleid]);
}

echo '<div class="layerslideshow layerslideshow' . (int) $j51_moduleid . '">';
echo '<div id="layerslideshow' . (int) $j51_moduleid . '">';

if (!empty($j51slideimages)) {
    foreach ($j51slideimages as $item) {
        $slidetype = (strpos($item->j51_slidelink, 'vimeo.com') !== false || strpos($item->j51_slidelink, 'youtube.com') !== false) ? 'item-video' : 'item';
        $alignment = htmlspecialchars((string) $item->j51_slidetextalign, ENT_QUOTES, 'UTF-8');
        $slideImage = htmlspecialchars((string) $item->j51_slideimage, ENT_QUOTES, 'UTF-8');
        $slideTitle = htmlspecialchars((string) $item->j51_slidetitle, ENT_QUOTES, 'UTF-8');
        $slideLink = htmlspecialchars((string) $item->j51_slidelink, ENT_QUOTES, 'UTF-8');

        echo '<div class="' . $slidetype . ' item-align-' . $alignment . '">';
        echo '<div class="img-fill">';
        echo '<img src="' . $slideImage . '" alt="' . $slideTitle . '">';

        if (!empty($item->j51_slidetitle) || !empty($item->j51_slidecaption)) {
            echo '<div class="layerslideshow' . (int) $j51_moduleid . '__info info wrapper960">';
            echo '<div class="info-wrapper" style="max-width: ' . (int) $item->j51_slidetextwidth . 'px;">';

            if (!empty($item->j51_slidetitle)) {
                echo '<div class="layerslideshow-title-container">';
                echo '<' . $j51_title_tag . ' class="layerslideshow-title ';
                echo $item->j51_override_anim ? $item->j51_title_anim : $j51_title_anim_default;
                echo '" style="animation-delay: ' . (int) $j51_title_delay . 'ms">';
                echo $item->j51_slidetitle;
                echo '</' . $j51_title_tag . '>';
                echo '</div>';
            }

            if (!empty($item->j51_slidecaption)) {
                echo '<div class="layerslideshow-caption-container">';
                echo '<' . $j51_text_tag . ' class="layerslideshow-caption ';
                echo $item->j51_override_anim ? $item->j51_caption_anim : $j51_caption_anim_default;
                echo '" style="animation-delay: ' . (int) $j51_caption_delay . 'ms">';
                echo $item->j51_slidecaption;
                echo '</' . $j51_text_tag . '>';
                echo '</div>';
            }

            echo '</div>';
            echo '</div>';
        }

        if (strpos($item->j51_slidelink, 'vimeo.com') !== false || strpos($item->j51_slidelink, 'youtube.com') !== false) {
            echo '<a class="owl-video" href="' . $slideLink . '"></a>';
        }

        if (!empty($item->j51_slidelink)) {
            echo '<a href="' . $slideLink . '" class="slidelink" aria-label="' . $slideTitle . '"';

            if ($item->j51_linknewtab) {
                echo ' target="_blank" rel="noopener noreferrer"';
            }

            echo '></a>';
        }

        echo '</div>';
        echo '</div>';
    }
}

echo '</div>';
echo '<div id="layerslideshow' . (int) $j51_moduleid . '-controls" class="layerslideshow-controls">';
echo '<a id="layerslideshow-prev-' . (int) $j51_moduleid . '" class="layerslideshow-prev" href="#" role="button" aria-label="' . Text::_('JPREV') . '"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M5.41 11H21a1 1 0 0 1 0 2H5.41l5.3 5.3a1 1 0 0 1-1.42 1.4l-7-7a1 1 0 0 1 0-1.4l7-7a1 1 0 0 1 1.42 1.4L5.4 11z"/></svg></a>';
echo '<a id="layerslideshow-next-' . (int) $j51_moduleid . '" class="layerslideshow-next" href="#" role="button" aria-label="' . Text::_('JNEXT') . '"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M18.59 13H3a1 1 0 0 1 0-2h15.59l-5.3-5.3a1 1 0 1 1 1.42-1.4l7 7a1 1 0 0 1 0 1.4l-7 7a1 1 0 0 1-1.42-1.4l5.3-5.3z"/></svg></a>';
echo '<div id="layerslideshow' . (int) $j51_moduleid . '-play" style="display:none;"></div>';
echo '<div class="j51-nav-dots" id="layerslideshow' . (int) $j51_moduleid . '-nav">';

if (!empty($j51slideimages)) {
    foreach ($j51slideimages as $item) {
        echo '<span role="button" class="j51-nav-dot" tabindex="0"><span></span></span>';
    }
}

echo '</div>';
echo '</div>';
echo '</div>';
