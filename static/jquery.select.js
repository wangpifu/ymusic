(function(factory) {
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else {
        factory(jQuery);
    }
}(function($, undefined) {

	"use strict";

	var trigger = '[data-toggle="select"]',
		openCls = 'open';

	function ISelect(element, options) {
		var self = this;

		self.element = element;
		self.options = $.extend({}, ISelect.defaults, options || {});

		self.init();
	}

	ISelect.prototype = {
		constructor: ISelect,
		init: function() {
			var $this	= $(this instanceof ISelect ? this.element : this),
				$parent = getParent($this);

			$this.data('placeholder', $this.find('span').html());

			$parent.on('click.phpsay.select', '[data-item]', $.proxy(doSelect, $this));
		},
		toggle: function() {
			var $this	= $(this instanceof ISelect ? this.element : this),
				$parent = getParent($this),
				isOpen	= $parent.hasClass(openCls),
				relatedTarget, e;

			if ($this.is('.disabled, :disabled')) {
				return;
			}

			doHidePane();

			if (!isOpen) {
				ISelect.prototype.show.call($this[0], $parent);
			}

			return false;
		},
		show: function($parent) {
			var $this			= $(this instanceof ISelect ? this.element : this),
				relatedTarget 	= {relatedTarget: this},
				e;

			$parent = $parent || getParent($this);

			$parent.trigger(e = $.Event('show.phpsay.select', relatedTarget));
			if (e.isDefaultPrevented()) {
				return;
			}
			
			$parent
				.addClass(openCls)
				.trigger('shown.phpsay.select', relatedTarget);

			var firstItem	= $parent.find('[data-item]').first(),
				placeholder = $this.data('placeholder');

			if (firstItem[0] && firstItem.html() != placeholder) {
				firstItem.before(firstItem.clone().attr('data-item', '').html(placeholder).addClass('selected'));
			}
		}
	};

	ISelect.defaults = {
		tmpl: null,
		render: null
	};

	function doToggle(ev) {
		ev.preventDefault();
		ev.stopPropagation();

		$(this).iselect('toggle');
	}

	function doSelect(ev) {
		ev.preventDefault();

		var trigger = this,
			$parent = $(ev.delegateTarget),
			$active = $(ev.currentTarget),

			field = $(trigger.data('field')),

			oldVal = field.val(),
			newVal;

		trigger
			.find('span')
			.html($active.html());

		$parent
			.find('[data-item]')
			.removeClass('selected');

		field.val(newVal = $active.addClass('selected').data('item'));

		if (oldVal != newVal) {	
			$parent.trigger('valchanged.phpsay.select', {oldVal: oldVal, newVal: newVal});
		}
	}

	function doHidePane(ev) {
		$(trigger).each(function() {
			var $this			= $(this),
				relatedTarget 	= {relatedTarget: this},
				e;

			var $parent = getParent($this);

			$parent.trigger(e = $.Event('hide.phpsay.select', relatedTarget))
			if (e.isDefaultPrevented()) {
				return;
			}

			$parent
				.removeClass(openCls)
				.trigger('hidden.phpsay.select', relatedTarget);
		});
	}

	function getParent($this) {
		var selector = $this.attr('data-target');

		if (!selector) {
			selector = $this.attr('href');
			selector = selector && /#[A-Za-z]/.test(selector) && selector.replace(/.*(?=#[^\s]*$)/, ''); //strip for ie7
		}

		var $parent = selector && $(selector)

		return $parent && $parent.length ? $parent : $this.parent();
	}

	var old = $.fn.iselect;

	$.fn.iselect = function(option) {
		return this.each(function () {
			var $this 	= $(this),
				data  	= $this.data('phpsay.select'),
				options = typeof option == 'object' ? option : {};

			if (!data) {
				$this.data('phpsay.select', (data = new ISelect(this, options)));
			}

			if (typeof option == 'string') {
				data[option].call($this);
			}
		});
	};

	$.fn.iselect.Constructor = ISelect;

	$.fn.iselect.noConflict = function() {
		$.fn.iselect = old;
		return this;
	};

	var valchanged = 'valchanged.phpsay.select',
		   cascade = 'cascade.phpsay.select';
	
	$('.J-cascades')
		.on(valchanged, '[data-level]', function(ev, data) {
			var $this 		= $(this),
				$parent		= $(ev.delegateTarget),

				fromlevel	= $this.data('level') || 0,
				toLevel		= fromlevel + 1,

				newVal		= data && data.newVal,
				oldVal		= data && data.oldVal;

			$parent
				.find('[data-level="'+ toLevel +'"]')
				.each(function(i, elem) {

					var $next 	= $(elem),
						superNewVal = newVal,
						superOldVal = oldVal;

					function next(selfNewVal, selfOldVal) {
						if (selfNewVal != selfOldVal) {
							$next.trigger(valchanged, {newVal: selfNewVal, oldVal: selfOldVal});
						}

						$next = null;
					}

					$next.trigger($.Event(cascade, {newVal: superNewVal, oldVal: superOldVal, next: next}));
				});
		});

	$(document)
		.on('click.phpsay.select', doHidePane)
		.on('click.phpsay.select', trigger, doToggle);
}));