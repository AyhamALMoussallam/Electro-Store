/**
 * Electro themed alert & confirm dialogs.
 * Usage:
 *   await ElectroDialog.alert('Saved!');
 *   if (await ElectroDialog.confirm('Delete this item?')) { ... }
 */
(function () {
	'use strict';

	const TYPE_ICONS = {
		info: 'fa-info-circle',
		success: 'fa-check-circle',
		error: 'fa-exclamation-circle',
		confirm: 'fa-question-circle',
	};

	let root = null;
	let iconEl = null;
	let titleEl = null;
	let messageEl = null;
	let cancelBtn = null;
	let confirmBtn = null;
	let resolveCurrent = null;
	let mode = 'alert';

	function ensureElements() {
		if (root) {
			return;
		}

		root = document.getElementById('electro-dialog');
		if (!root) {
			return;
		}

		iconEl = root.querySelector('.electro-dialog__icon');
		titleEl = root.querySelector('.electro-dialog__title');
		messageEl = root.querySelector('.electro-dialog__message');
		cancelBtn = root.querySelector('[data-electro-dialog-cancel]');
		confirmBtn = root.querySelector('[data-electro-dialog-confirm]');

		root.querySelector('.electro-dialog__backdrop')
			?.addEventListener('click', onBackdropClick);

		cancelBtn?.addEventListener('click', () => close(false));
		confirmBtn?.addEventListener('click', () => close(true));

		document.addEventListener('keydown', onKeydown);
	}

	function onBackdropClick() {
		if (mode === 'confirm') {
			close(false);
		} else {
			close(true);
		}
	}

	function onKeydown(e) {
		if (!root?.classList.contains('is-open')) {
			return;
		}

		if (e.key === 'Escape') {
			close(mode === 'confirm' ? false : true);
		}
	}

	function close(result) {
		if (!root || !resolveCurrent) {
			return;
		}

		root.classList.remove('is-open');
		root.setAttribute('aria-hidden', 'true');
		document.body.classList.remove('electro-dialog-open');

		const resolve = resolveCurrent;
		resolveCurrent = null;
		resolve(result);
	}

	function openDialog(options) {
		ensureElements();

		if (!root) {
			console.warn('ElectroDialog: markup not found');
			return Promise.resolve(options.confirm ? false : undefined);
		}

		mode = options.confirm ? 'confirm' : 'alert';
		const type = options.type || (options.confirm ? 'confirm' : 'info');
		const iconClass = TYPE_ICONS[type] || TYPE_ICONS.info;

		iconEl.className = 'electro-dialog__icon electro-dialog__icon--' + type;
		iconEl.innerHTML = '<i class="fa ' + iconClass + '"></i>';
		const t = window.ElectroI18n ? window.ElectroI18n.t.bind(window.ElectroI18n) : function (k) { return k; };
		titleEl.textContent = options.title || (options.confirm ? t('pleaseConfirm') : t('notice'));
		messageEl.textContent = options.message || '';

		cancelBtn.style.display = options.confirm ? '' : 'none';
		confirmBtn.textContent = options.confirmText || (options.confirm ? t('yes') : t('ok'));
		cancelBtn.textContent = options.cancelText || t('cancel');

		root.classList.add('is-open');
		root.setAttribute('aria-hidden', 'false');
		document.body.classList.add('electro-dialog-open');

		setTimeout(() => confirmBtn.focus(), 0);

		return new Promise(function (resolve) {
			resolveCurrent = resolve;
		});
	}

	function alert(message, options) {
		options = options || {};
		return openDialog({
			message: String(message),
			title: options.title,
			type: options.type || 'info',
			confirm: false,
			confirmText: options.okText,
		});
	}

	function confirm(message, options) {
		options = options || {};
		return openDialog({
			message: String(message),
			title: options.title,
			type: options.type || 'confirm',
			confirm: true,
			confirmText: options.confirmText,
			cancelText: options.cancelText,
		});
	}

	window.ElectroDialog = {
		alert: alert,
		confirm: confirm,
	};

	// Replace native browser alerts site-wide
	window.alert = function (message) {
		return alert(message);
	};
})();
