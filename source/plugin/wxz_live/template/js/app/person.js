jQuery(function() {
	var Accordion = function(el, multiple) {
		this.el = el || {};
		this.multiple = multiple || false;

		// Variables privadas
		var links = this.el.find('.link');
		// Evento
		links.on('click', {el: this.el, multiple: this.multiple}, this.dropdown)
	}

	Accordion.prototype.dropdown = function(e) {
		var jQueryel = e.data.el;
			jQuerythis = jQuery(this),
			jQuerynext = jQuerythis.next();

		jQuerynext.slideToggle();
		jQuerythis.parent().toggleClass('open');

		if (!e.data.multiple) {
			jQueryel.find('.submenu').not(jQuerynext).slideUp().parent().removeClass('open');
		};
	}	

	var accordion = new Accordion(jQuery('#accordion'), false);
});