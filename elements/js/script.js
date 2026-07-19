/**
 * Template Backend Script
 * elements/js/script.js
 *
 * Handles parameter grouping, legend switching,
 * preset loading/export/import in Joomla template manager.
 */

'use strict';

document.addEventListener('DOMContentLoaded', function () {

  const translate = function (key, fallback) {
    if (Joomla.Text && typeof Joomla.Text._ === 'function') {
      return Joomla.Text._(key, fallback);
    }

    return fallback;
  };

  // ✅ jQuery-Wrapper entfernt - natives JS + Joomla API
  // Internes jQuery für DOM-Manipulation (Joomla stellt es bereit)
  const $ = window.jQuery;

  if (typeof $ === 'undefined') {
    console.error(translate('TPL_J51_EMANI_JS_JQUERY_MISSING', 'j51 script.js: jQuery is not available.'));
    return;
  }

  const templateOptions = Joomla.getOptions('j51.template') || {};
  const templateName    = templateOptions.name || 'j51_emani';
  const styleId         = Number(templateOptions.styleId || 0);
  const csrfToken       = Joomla.getOptions('csrf.token');

  const buildAjaxUrl = function (method, format, extraParams) {
    const params = new URLSearchParams({
      option: 'com_ajax',
      template: templateName,
      format: format,
      method: method,
      id: String(styleId)
    });

    Object.entries(extraParams || {}).forEach(function ([key, value]) {
      params.set(key, value);
    });

    if (csrfToken) {
      params.set(csrfToken, '1');
    }

    return 'index.php?' + params.toString();
  };

  const requestOptionsAreValid = function () {
    if (styleId > 0 && csrfToken) {
      return true;
    }

    Joomla.renderMessages({
      warning: [translate('TPL_J51_EMANI_JS_ACTION_PREPARE_FAILED', 'The template action could not be prepared. Please reload the page.')]
    });

    return false;
  };

  // ── Width100 ──────────────────────────────────────────────────────────────
  $('.control-group:has(.width100)').addClass('container-width100');

  // ── Group index for legends ───────────────────────────────────────────────
  const $legends = $('h3.legend');

  $legends.each(function () {
    const $legend      = $(this);
    const $legendGroup = $legend.closest('.control-group');
    const isSub        = $legend.is('.sub-legend');

    // Add legend class to parent control-group
    $legendGroup.addClass(isSub ? 'sub-legend-group' : 'top-legend-group');

    const $params = $legendGroup.nextUntil(function () {
      const $next           = $(this);
      const $nextIsLegend   = $next.has('h3.legend').length;
      const $nextIsSubLegend = $nextIsLegend
        && $next.find('h3.legend').is('.sub-legend');

      if (!isSub && $nextIsLegend && $nextIsSubLegend) {
        $next.find('h3.legend').data('top-legend', $legend);
      }

      return !$next.is('.control-group')
        || ($nextIsLegend && (isSub || !$nextIsSubLegend));
    });

    // Store references
    $params.data('legend', $legend);
    $legend.data('params', $params);
  });

  // ── Grouping legends and params ───────────────────────────────────────────
  $('.form-grid').each(function () {
    const $pane       = $(this);
    const $topLegends = $pane.find('.top-legend-group');

    $('<div />').addClass('group-legends').appendTo($pane).append($topLegends);

    $topLegends.each(function () {
      const $legend      = $(this).find('h3');
      const $params      = $legend.data('params');
      const $mediaParams = $params.filter(function () {
        return $(this).find('joomla-field-media').length;
      });
      const $directParams = $params.not($mediaParams);
      const $subLegends  = $params.filter('.sub-legend-group');
      const $topGroup    = $('<div />').addClass('top-group').appendTo($pane);

      const $subGroupDirect = $('<div />')
        .addClass('sub-group sub-group-direct')
        .appendTo($topGroup)
        .append(
          $('<div />').addClass('sub-group-inner').append($directParams)
        );

      $subLegends.each(function () {
        const $subLegendGroup = $(this);
        const $subLegend      = $subLegendGroup.find('h3');
        const $subParams      = $subLegend.data('params');
        const $subMediaParams = $subParams.filter(function () {
          return $(this).find('joomla-field-media').length;
        });
        const $subDirectParams = $subParams.not($subMediaParams);

        $('<div />').addClass('sub-group').appendTo($topGroup).append(
          $('<div />').addClass('sub-group-inner')
            .append($subLegendGroup)
            .append(
              $('<div />').addClass('sub-group-params').append($subDirectParams)
            )
        );
      });

      // ✅ Leere Gruppen entfernen
      if (!$subGroupDirect.find('.sub-group-inner').children().length) {
        $subGroupDirect.remove();
      }

      // Sub-group-direct Klasse für top-group-enabler
      $topGroup.find('.top-group-enabler')
        .closest('.sub-group')
        .addClass('sub-group-direct');

      // Referenz speichern
      $(this).data('media-params', $mediaParams);
      $(this).data('top-group', $topGroup);
    });
  });

  // ── Show/hide top group ───────────────────────────────────────────────────
  const showTopGroup = function ($legendGroup) {
    const $topGroup          = $legendGroup.data('top-group');
    const $tabPane           = $legendGroup.closest('joomla-tab-element');
    const $otherLegendGroups = $tabPane.find('.top-legend-group').not($legendGroup);
    const $otherTopGroups    = $tabPane.find('.top-group').not($topGroup);
    const $mediaParams       = $legendGroup.data('media-params') || $();

    $otherTopGroups.removeClass('active').hide();
    $otherLegendGroups.each(function () {
      const $otherMediaParams = $(this).data('media-params');

      if ($otherMediaParams) {
        $otherMediaParams.hide();
      }
    });
    $topGroup.addClass('active').fadeIn();
    $mediaParams.show();
    $legendGroup.addClass('active');
    $otherLegendGroups.removeClass('active');

    $(document).trigger('switchLegendGroup', $legendGroup);
  };

  $('.top-legend-group').on('click', function () {
    showTopGroup($(this));

    // ✅ localStorage mit try/catch (Privacy-Mode kann Fehler werfen)
    try {
      if (localStorage) {
        const selector = '#'
          + $(this).closest('joomla-tab-element').attr('id')
          + ' .top-legend-group:nth-child('
          + ($(this).index() + 1)
          + ')';
        localStorage.setItem('last_active_group', selector);
      }
    } catch (e) {
      console.warn(translate('TPL_J51_EMANI_JS_LOCAL_STORAGE_UNAVAILABLE', 'j51: localStorage is not available.'), e);
    }
  });

  // ── Restore last active group ─────────────────────────────────────────────
  setTimeout(function () {
    $('.tab-pane .top-legend-group:first-child').trigger('click');

    // ✅ null-Check vor trigger()
    try {
      const lastGroup = localStorage
        ? localStorage.getItem('last_active_group')
        : null;

      if (lastGroup) {
        const $lastActiveGroup = $(lastGroup);
        if ($lastActiveGroup.length) {
          $lastActiveGroup.trigger('click');
        }
      }
    } catch (e) {
      console.warn(translate('TPL_J51_EMANI_JS_LAST_GROUP_RESTORE_FAILED', 'j51: The last active group could not be restored.'), e);
    }
  }, 500);

  // ── Preset Loader ─────────────────────────────────────────────────────────
  const presetLoader = document.querySelector('.preset-loader');

  // ✅ null-Check
  if (presetLoader) {
    presetLoader.addEventListener('click', function (e) {
      e.preventDefault();

      const presetEl = document.querySelector('[name="preset"]');
      if (!presetEl) {
        console.warn(translate('TPL_J51_EMANI_JS_PRESET_SELECT_MISSING', 'j51: The preset selector was not found.'));
        return;
      }

      const preset = presetEl.value;

      if (!requestOptionsAreValid()) {
        return;
      }

      Joomla.request({
        method: 'POST',
        url: buildAjaxUrl('loadPreset', 'json', { preset: preset }),
        onSuccess: function (data) {
          // ✅ try/catch für JSON.parse
          let json;
          try {
            json = JSON.parse(data);
          } catch (err) {
            Joomla.renderMessages({ warning: [translate('TPL_J51_EMANI_JS_INVALID_RESPONSE', 'The server returned an invalid response.')] });
            return;
          }

          if (!json.success) {
            Joomla.renderMessages({
              warning: [translate('TPL_J51_EMANI_JS_PRESET_LOAD_FAILED', 'The preset could not be loaded:') + ' ' + json.message]
            });
            return;
          }

          alert(translate('TPL_J51_EMANI_JS_PRESET_LOAD_SUCCESS', 'The preset was applied successfully. The page will now reload.'));
          window.location.reload();
        },
        onError: function () {
          Joomla.renderMessages({ warning: [translate('TPL_J51_EMANI_JS_REQUEST_FAILED', 'The request failed. Please try again.')] });
        }
      });
    });
  }

  // ── Preset Export ─────────────────────────────────────────────────────────
  const presetExport = document.querySelector('.preset-export');

  // ✅ null-Check
  if (presetExport) {
    presetExport.addEventListener('click', function (e) {
      e.preventDefault();

      if (!requestOptionsAreValid()) {
        return;
      }

      window.location.href = buildAjaxUrl('export', 'raw');
    });
  }

  // ── Preset Import ─────────────────────────────────────────────────────────
  const presetInput = document.querySelector('.preset-input');

  // ✅ null-Check
  if (presetInput) {
    presetInput.addEventListener('change', function (e) {
      e.preventDefault();

      const file = e.target.files[0];
      if (!file) {
        return;
      }

      if (!requestOptionsAreValid()) {
        return;
      }

      const reader = new FileReader();

      reader.onload = function (e) {
        Joomla.request({
          method: 'POST',
          url: buildAjaxUrl('import', 'json'),
          data: e.target.result,
          onSuccess: function (data) {
            // ✅ try/catch für JSON.parse
            let json;
            try {
              json = JSON.parse(data);
            } catch (err) {
              Joomla.renderMessages({ warning: [translate('TPL_J51_EMANI_JS_INVALID_RESPONSE', 'The server returned an invalid response.')] });
              return;
            }

            if (!json.success) {
              Joomla.renderMessages({
                warning: [translate('TPL_J51_EMANI_JS_IMPORT_FAILED', 'The settings could not be imported:') + ' ' + json.message]
              });
              return;
            }

            alert(translate('TPL_J51_EMANI_JS_IMPORT_SUCCESS', 'The settings were imported successfully. The page will now reload.'));
            window.location.reload();
          },
          onError: function () {
            Joomla.renderMessages({ warning: [translate('TPL_J51_EMANI_JS_REQUEST_FAILED', 'The request failed. Please try again.')] });
          }
        });
      };

      // ✅ onError für FileReader
      reader.onerror = function () {
        console.error(translate('TPL_J51_EMANI_JS_FILE_READ_FAILED_CONSOLE', 'j51: The preset file could not be read.'));
        Joomla.renderMessages({ warning: [translate('TPL_J51_EMANI_JS_FILE_READ_FAILED', 'The selected file could not be read.')] });
      };

      reader.readAsText(file);
    });
  }

}); // END DOMContentLoaded
