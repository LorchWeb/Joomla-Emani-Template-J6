/**
 * Template Backend Script
 * elements/js/script.js
 *
 * Handles parameter grouping, legend switching,
 * preset loading/export/import in Joomla template manager.
 */

'use strict';

document.addEventListener('DOMContentLoaded', function () {

  // ✅ jQuery-Wrapper entfernt - natives JS + Joomla API
  // Internes jQuery für DOM-Manipulation (Joomla stellt es bereit)
  const $ = window.jQuery;

  if (typeof $ === 'undefined') {
    console.error('j51 script.js: jQuery not available');
    return;
  }

  const j51Template = 'j51_emani';

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
      const $subLegends  = $params.filter('.sub-legend-group');
      const $topGroup    = $('<div />').addClass('top-group').appendTo($pane);

      const $subGroupDirect = $('<div />')
        .addClass('sub-group sub-group-direct')
        .appendTo($topGroup)
        .append(
          $('<div />').addClass('sub-group-inner').append($params)
        );

      $subLegends.each(function () {
        const $subLegendGroup = $(this);
        const $subLegend      = $subLegendGroup.find('h3');
        const $subParams      = $subLegend.data('params');

        $('<div />').addClass('sub-group').appendTo($topGroup).append(
          $('<div />').addClass('sub-group-inner')
            .append($subLegendGroup)
            .append(
              $('<div />').addClass('sub-group-params').append($subParams)
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
      $(this).data('top-group', $topGroup);
    });
  });

  // ── Show/hide top group ───────────────────────────────────────────────────
  const showTopGroup = function ($legendGroup) {
    const $topGroup          = $legendGroup.data('top-group');
    const $tabPane           = $legendGroup.closest('joomla-tab-element');
    const $otherLegendGroups = $tabPane.find('.top-legend-group').not($legendGroup);
    const $otherTopGroups    = $tabPane.find('.top-group').not($topGroup);

    $otherTopGroups.removeClass('active').hide();
    $topGroup.addClass('active').fadeIn();
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
      console.warn('j51: localStorage not available', e);
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
      console.warn('j51: Could not restore last active group', e);
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
        console.warn('j51: preset select element not found');
        return;
      }

      const preset = presetEl.value;

      Joomla.request({
        method: 'POST',
        url: 'index.php?option=com_ajax'
          + '&template=' + j51Template
          + '&format=json'
          + '&method=loadPreset'
          + '&preset=' + encodeURIComponent(preset)
          + '&id=' + Joomla.getOptions('j51.template.style')
          + '&' + Joomla.getOptions('csrf.token') + '=1',
        onSuccess: function (data) {
          // ✅ try/catch für JSON.parse
          let json;
          try {
            json = JSON.parse(data);
          } catch (err) {
            Joomla.renderMessages({ warning: ['Invalid server response'] });
            return;
          }

          if (!json.success) {
            Joomla.renderMessages({
              warning: ['Preset could not be loaded: ' + json.message]
            });
            return;
          }

          alert('Preset got applied successfully! Page will reload.');
          window.location.reload();
        },
        onError: function () {
          Joomla.renderMessages({ warning: ['Request failed. Please try again.'] });
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

      // ✅ location.href statt location =
      window.location.href = 'index.php?option=com_ajax'
        + '&template=' + j51Template
        + '&format=raw'
        + '&method=export'
        + '&id=' + Joomla.getOptions('j51.template.style')
        + '&' + Joomla.getOptions('csrf.token') + '=1';
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

      const reader = new FileReader();

      reader.onload = function (e) {
        Joomla.request({
          method: 'POST',
          url: 'index.php?option=com_ajax'
            + '&template=' + j51Template
            + '&format=json'
            + '&method=import'
            + '&id=' + Joomla.getOptions('j51.template.style')
            + '&' + Joomla.getOptions('csrf.token') + '=1',
          data: e.target.result,
          onSuccess: function (data) {
            // ✅ try/catch für JSON.parse
            let json;
            try {
              json = JSON.parse(data);
            } catch (err) {
              Joomla.renderMessages({ warning: ['Invalid server response'] });
              return;
            }

            if (!json.success) {
              Joomla.renderMessages({
                warning: ['Settings could not be imported: ' + json.message]
              });
              return;
            }

            alert('Settings imported successfully! Page will reload.');
            window.location.reload();
          },
          onError: function () {
            Joomla.renderMessages({ warning: ['Import request failed. Please try again.'] });
          }
        });
      };

      // ✅ onError für FileReader
      reader.onerror = function () {
        console.error('j51: FileReader error');
        Joomla.renderMessages({ warning: ['Could not read file'] });
      };

      reader.readAsText(file);
    });
  }

}); // END DOMContentLoaded
