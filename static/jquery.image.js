(function(factory) {
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else {
        factory(jQuery);
    }
}(function($) {
	"use strict";

	function doExpand() {
		var self = this,
			opts = self.options,
			elem = self.element,
			xsrc = elem.data('expand');

		if (self.wrapper.data('loading')) {
			return;
		}

		self.wrapper.data('loading', true);
		self.loading();

		if (xsrc) {
			loadImage.call(self, xsrc);
		}
		else {
			$.isFunction(opts.load) && opts.load($.proxy(loadImage, self));
		}
	}

	function loadImage(src) {
		var self = this,
			opts = self.options,
			elem = self.element,
			pane = self.pane;

		var	img = new Image();
		img.onerror = img.onload = function(ev) {
			pane.html('<img src="'+ src +'" /><span onclick="imageZoom(\''+ src +'\');" style="position:absolute;top:0;left:0;width:50px;height:50px;cursor:url(static/magnifier.cur),auto;"></span>');
			self.hide();
		};
		img.src = src;
		img = null;
	}

    function doListenEvent(ev) {
    	var self = this,
    		pane = self.pane;

    	pane.on('click.shut.data-api', function(ev) {

    		var target = ev.target || ev.srcElement;
    		
    		var tag = target.tagName;
    		
    		if ( tag ) {
    			if( tag.toLowerCase() === 'img' ) {
    				self.show();
    				ev.preventDefault();
    			}
    		}
    	});
    }
    
	function Expand(element, options) {
		var self = this,
			opts = $.extend({}, Expand.defaults, options || {});

		self.options = opts;
		self.element = $(element);

		self.init();
	}

	Expand.prototype = {
		contructor: Expand,
		init: function() {
			var self = this,
				opts = self.options,
				elem = self.element,

				paneCls = opts.paneCls,
				pane;

			self.wrapper = opts.wrapper && opts.wrapper == 'parent' ? elem.parent() : elem.parents(opts.wrapper);
			
			if (opts.node) {
				pane = $(opts.node).find(paneCls);
			}
			else {
				pane = self.wrapper.siblings(paneCls);
			}

			if (!pane[0]) {
				pane = $('<div style="display:none;"></div');

				paneCls && pane.addClass(paneCls.replace(/\.(?=[^\s]+)/g, ''));

				if (opts.node) {
					$(opts.node).append(pane);
				}
				else {
					self.wrapper.after(pane);
				}
			}

			self.pane = pane;

			doListenEvent.call(self);

			return self;
		},
		dispatch: function() {
			doExpand.call(this);
		},
		hide: function() {
			var self = this,
				wrap = self.wrapper,
				pane = self.pane;

			wrap.data('loading', false).hide();
			pane.show();
		},
		show: function() {
			var self = this,
				elem = self.element,
				wrap = self.wrapper,
				pane = self.pane;

			pane.hide();
			wrap.show().find('.loading').hide();

			if (self.flow) elem.parent().css('position', self.flow);
		},
		loading: function() {
			var self = this,
				opts = self.options,
				elem = self.element,
				wrap = self.wrapper,
				flow = elem.parent().css('position'),
				node = wrap.find('.loading');

			if (!flow || flow == 'static') {
				elem.parent().css('position', 'relative');
				self.flow = flow;
			}

			if (!node[0]) {
				node = $(opts.loading);
			}

			elem.parent().append(node);

			node.css({
				width: elem.width(),
				height: elem.height(),
				top: 0,
				left: 0
			}).show();
		}
	};

	Expand.defaults = {
		wrapper: 'parent',
		paneCls: '.zoom-picture',
		loading: '<span class="loading"></span>',
		node: null,
		load: null
	};

	var old = $.fn.expand;

	$.fn.expand = function(option) {
		return this.each(function() {
			var self = $(this),
				data = self.data('phpsay.expand'),
				options = typeof option == 'object' && option;

			if (!data) {
				self.data('phpsay.expand', data = new Expand(this, options));
			}

			data[typeof option == 'string' ? option : 'dispatch']();
		});
	};

	$.fn.expand.Constructor = Expand;

	$.fn.expand.noConflict = function() {
		$.fn.expand = old;
		return this;
	}

	$(document).on('click.phpsay.expand.data-api', '[data-expand]', function(ev) {
		$(this).expand();
		ev.preventDefault();
	});

}));