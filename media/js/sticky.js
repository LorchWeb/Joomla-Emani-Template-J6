/*!
 * Sticky Header - Vanilla JS
 * Ersetzt jquery.sticky.js für Joomla 5/6
 */
(function() {
  'use strict';

  var StickyElement = function(element, options) {
    this.element = element;
    this.options = Object.assign({
      topSpacing: 0,
      bottomSpacing: 0,
      className: 'is-sticky'
    }, options || {});
    
    this.originalTop = 0;
    this.isSticky = false;
    this.wrapper = null;
    
    this._init();
  };

  StickyElement.prototype._init = function() {
    // Wrapper erstellen
    var wrapper = document.createElement('div');
    wrapper.id = this.element.id + '-sticky-wrapper';
    wrapper.className = 'sticky-wrapper';
    wrapper.style.height = this.element.offsetHeight + 'px';
    this.element.parentNode.insertBefore(wrapper, this.element);
    wrapper.appendChild(this.element);
    this.wrapper = wrapper;

    // Original-Position merken
    this.originalTop = this._getOffsetTop(this.wrapper);
    
    window.addEventListener('scroll', this._onScroll.bind(this), false);
    window.addEventListener('resize', this._onResize.bind(this), false);
  };

  StickyElement.prototype._getOffsetTop = function(el) {
    var top = 0;
    while (el) {
      top += el.offsetTop || 0;
      el = el.offsetParent;
    }
    return top;
  };

  StickyElement.prototype._onScroll = function() {
    var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    var wrapperTop = this._getOffsetTop(this.wrapper);
    var etse = wrapperTop - this.options.topSpacing;

    if (scrollTop >= etse) {
      if (!this.isSticky) {
        this.element.style.position = 'fixed';
        this.element.style.top = this.options.topSpacing + 'px';
        this.element.style.width = this.wrapper.offsetWidth + 'px';
        this.wrapper.classList.add(this.options.className);
        this.isSticky = true;
      }
    } else {
      if (this.isSticky) {
        this.element.style.position = '';
        this.element.style.top = '';
        this.element.style.width = '';
        this.wrapper.classList.remove(this.options.className);
        this.isSticky = false;
      }
    }
  };

  StickyElement.prototype._onResize = function() {
    if (this.isSticky) {
      this.element.style.width = this.wrapper.offsetWidth + 'px';
    }
  };

  // Globale API
  window.Sticky = function(elementOrSelector, options) {
    var element = typeof elementOrSelector === 'string'
      ? document.querySelector(elementOrSelector)
      : elementOrSelector;
    
    if (element) {
      return new StickyElement(element, options);
    }
  };

})();
