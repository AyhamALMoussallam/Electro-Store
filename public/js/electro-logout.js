/**
 * Log out with confirmation (ElectroDialog or native confirm fallback).
 */
(function () {
	'use strict';

	const API = '/api';
	const t = window.ElectroI18n
		? window.ElectroI18n.t.bind(window.ElectroI18n)
		: function (k) { return k; };

	async function performLogout() {
		const token = localStorage.getItem('auth_token');

		try {
			if (token) {
				await fetch(API + '/logout', {
					method: 'POST',
					headers: {
						Authorization: 'Bearer ' + token,
						'Content-Type': 'application/json',
						Accept: 'application/json',
					},
				});
			}
		} catch (err) {
			console.error(err);
		}

		localStorage.removeItem('auth_token');
		localStorage.removeItem('activeTab');
		window.location.href = '/login';
	}

	async function confirmLogout() {
		let ok = false;

		if (window.ElectroDialog) {
			ok = await ElectroDialog.confirm(t('logoutConfirm'), {
				title: t('logout'),
				confirmText: t('logout'),
			});
		} else {
			ok = window.confirm(t('logoutConfirm'));
		}

		if (ok) {
			await performLogout();
		}
	}

	window.confirmLogout = confirmLogout;
	window.logout = confirmLogout;
})();
