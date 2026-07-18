/*!
 * scripts.js - Vanilla JS
 * Joomla 5/6 kompatibel
 */
(function() {
  'use strict';

  document.addEventListener('DOMContentLoaded', function() {
    var templateOptions = Joomla.getOptions('j51_template');

    // Bootstrap btn-group (ohne jQuery)
    document.querySelectorAll('.btn-group label:not(.active)').forEach(function(label) {
      label.addEventListener('click', function() {
        var input = document.getElementById(label.getAttribute('for'));
        if (!input) return;

        if (!input.checked) {
          label.closest('.btn-group').querySelectorAll('label').forEach(function(l) {
            l.classList.remove('active', 'btn-success', 'btn-danger', 'btn-primary');
          });

          if (input.value === '') {
            label.classList.add('active', 'btn-primary');
          } else if (input.value === '0') {
            label.classList.add('active', 'btn-danger');
          } else {
            label.classList.add('active', 'btn-success');
          }
          input.checked = true;
        }
      });
    });

    document.querySelectorAll('.btn-group input[checked="checked"]').forEach(function(input) {
      var lbl = document.querySelector('label[for="' + input.id + '"]');
      if (!lbl) return;

      if (input.value === '') {
        lbl.classList.add('active', 'btn-primary');
      } else if (input.value === '0') {
        lbl.classList.add('active', 'btn-danger');
      } else {
        lbl.classList.add('active', 'btn-success');
      }
    });

    // Header
    var header = document.getElementById('container_header');

    // Hornav - parent-hover auf ALLE li
    document.querySelectorAll('.hornav li').forEach(function(li) {
      li.addEventListener('mouseenter', function() {
        li.classList.add('parent-hover');
      });
      li.addEventListener('mouseleave', function() {
        li.classList.remove('parent-hover');
      });
    });

    // Mobile Menu
    var mobileMenuEl = document.querySelector('#mobile-menu');
    if (mobileMenuEl && typeof MmenuLight !== 'undefined') {
      var menu = new MmenuLight(mobileMenuEl);

      menu.navigation({
        theme: 'dark',
        title: templateOptions.mobileMenuTitle
      });

      var drawer = menu.offcanvas({
        position: templateOptions.mobileMenuPosition
      });

      var menuToggle = document.querySelector('a[href="#mobile-menu"]');
      if (menuToggle) {
        menuToggle.addEventListener('click', function(evnt) {
          evnt.preventDefault();
          drawer.open();
        });
      }
    }

    // Animate on scroll - Waypoints
    var executeWaypoints = function() {
      document.querySelectorAll('.animate').forEach(function(el) {
        new Waypoint({
          element: el,
          handler: function() {
            this.element.classList.add('animated');
          },
          offset: '85%'
        });
      });
    };

    var checkWaypoint = function() {
      if (typeof Waypoint === 'undefined') {
        setTimeout(checkWaypoint, 50);
      } else {
        executeWaypoints();
      }
    };
    checkWaypoint();

    // Scroll-Klasse auf body
    if (header) {
      var header_height = header.offsetHeight;

      window.addEventListener('scroll', function() {
        if (window.scrollY >= header_height) {
          document.body.classList.add('scrolled');
        } else {
          document.body.classList.remove('scrolled');
        }
      });
    }

  });

})();

// Aktive Navigation
function dodajAktywne(elem) {
  document.querySelectorAll('a').forEach(function(a) {
    a.parentNode.classList.remove('active');
  });
  elem.parentNode.classList.add('active');
}
