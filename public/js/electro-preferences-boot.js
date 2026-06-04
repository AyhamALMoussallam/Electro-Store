/**
 * Apply saved language direction before first paint (include in <head>).
 */
(function () {
	try {
		var lang = localStorage.getItem('electro_lang') || 'ar';
		document.documentElement.setAttribute('lang', lang);
		document.documentElement.setAttribute('dir', lang === 'ar' ? 'rtl' : 'ltr');
	} catch (e) {
		document.documentElement.setAttribute('lang', 'ar');
		document.documentElement.setAttribute('dir', 'rtl');
	}
})();
