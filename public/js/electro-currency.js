/**
 * Currency display: USD (stored in DB) or SP using configurable USD→SP rate.
 */
(function () {
	'use strict';

	var STORAGE_KEY = 'electro_currency';
	var DEFAULT_RATE = 135;

	function getRate() {
		var rate = parseFloat(window.__ELECTRO_USD_TO_SP);

		if (Number.isNaN(rate) || rate <= 0) {
			return DEFAULT_RATE;
		}

		return rate;
	}

	function setRate(rate) {
		var parsed = parseFloat(rate);

		if (!Number.isNaN(parsed) && parsed > 0) {
			window.__ELECTRO_USD_TO_SP = parsed;
		}
	}

	function getCurrency() {
		try {
			return localStorage.getItem(STORAGE_KEY) === 'usd' ? 'usd' : 'sp';
		} catch (e) {
			return 'sp';
		}
	}

	function setCurrency(code) {
		localStorage.setItem(STORAGE_KEY, code === 'usd' ? 'usd' : 'sp');
	}

	/** Prices in the database are stored in USD. */
	function toDisplayAmount(usdAmount) {
		var usd = parseFloat(usdAmount);

		if (Number.isNaN(usd)) {
			return 0;
		}

		return getCurrency() === 'usd' ? usd : usd * getRate();
	}

	function formatPrice(usdAmount) {
		var amount = toDisplayAmount(usdAmount);

		if (getCurrency() === 'usd') {
			return '$' + amount.toLocaleString('en-US', {
				minimumFractionDigits: 2,
				maximumFractionDigits: 2,
			});
		}

		return amount.toLocaleString('en-US', {
			minimumFractionDigits: 0,
			maximumFractionDigits: 0,
		}) + ' SP';
	}

	/** Convert a value entered in the current display currency to USD (DB). */
	function displayToUsd(displayAmount) {
		var amount = parseFloat(displayAmount);

		if (Number.isNaN(amount)) {
			return null;
		}

		return getCurrency() === 'usd' ? amount : amount / getRate();
	}

	function refreshRateFromApi() {
		return fetch('/api/settings/currency', {
			headers: { Accept: 'application/json' },
		})
			.then(function (res) {
				if (!res.ok) {
					throw new Error('Failed to load exchange rate');
				}

				return res.json();
			})
			.then(function (data) {
				if (data && data.usd_to_sp_rate) {
					setRate(data.usd_to_sp_rate);
				}

				return getRate();
			})
			.catch(function () {
				return getRate();
			});
	}

	window.ElectroCurrency = {
		getRate: getRate,
		setRate: setRate,
		get: getCurrency,
		set: setCurrency,
		toDisplay: toDisplayAmount,
		displayToUsd: displayToUsd,
		format: formatPrice,
		refreshRateFromApi: refreshRateFromApi,
	};

	Object.defineProperty(window.ElectroCurrency, 'USD_TO_SP', {
		get: getRate,
	});

	window.formatPrice = formatPrice;
})();
