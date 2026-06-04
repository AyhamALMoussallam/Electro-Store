/**
 * Language & currency selectors; applies data-i18n on load.
 */
(function () {
	'use strict';

	function syncSelects() {
		var lang = window.ElectroI18n.getLang();
		var currency = window.ElectroCurrency.get();

		document.querySelectorAll('.electro-lang-select').forEach(function (sel) {
			sel.value = lang;
		});

		document.querySelectorAll('.electro-currency-select').forEach(function (sel) {
			sel.value = currency;
		});
	}

	function bindSelects() {
		document.querySelectorAll('.electro-lang-select').forEach(function (sel) {
			sel.addEventListener('change', function () {
				if (window.ElectroI18n.getLang() !== sel.value) {
					window.ElectroI18n.setLang(sel.value);
					window.location.reload();
				}
			});
		});

		document.querySelectorAll('.electro-currency-select').forEach(function (sel) {
			sel.addEventListener('change', function () {
				if (window.ElectroCurrency.get() !== sel.value) {
					window.ElectroCurrency.set(sel.value);
					window.location.reload();
				}
			});
		});
	}

	function init() {
		window.ElectroI18n.applyDocument();
		window.ElectroI18n.apply();
		syncSelects();
		bindSelects();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
