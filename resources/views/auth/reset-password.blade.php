<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
@include('partials.electro-head', ['title' => 'Electro', 'accountPage' => true])
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body class="minimal-header-page" data-active-nav="">

@include('partials.electro-header', ['minimalHeader' => true])

<div class="account-auth-section">
	<div class="container">
		<div class="auth-card">
			<h2 data-i18n="setNewPassword"></h2>

			<div id="invalid-link" class="auth-error-box" style="display:none;">
				<span data-i18n="invalidResetLink"></span>
				<a href="/forgot-password" data-i18n="requestNewResetLink"></a>
			</div>

			<form id="reset-form" style="display:none;">
				<input type="hidden" id="reset-token" name="token">
				<input type="email" class="input" id="reset-email" name="email" data-i18n-placeholder="email" placeholder="Email" readonly style="background:#FBFBFC;">
				<input type="password" class="input" id="reset-password" name="password" data-i18n-placeholder="newPasswordPlaceholder" placeholder="New password" minlength="6" required>
				<input type="password" class="input" id="reset-password-confirm" name="password_confirmation" data-i18n-placeholder="confirmNewPasswordPlaceholder" placeholder="Confirm new password" minlength="6" required>
				<button type="submit" class="primary-btn" data-i18n="resetPasswordBtn"></button>
			</form>

			<div class="auth-link">
				<a href="/login" data-i18n="backToSignIn"></a>
			</div>

			<div class="account-message" id="message"></div>
		</div>
	</div>
</div>

@include('partials.electro-footer')
@include('partials.electro-scripts')

<script>
const apiBase = '/api';

function t(key) {
	return window.ElectroI18n ? window.ElectroI18n.t(key) : key;
}

if (window.ElectroI18n) {
	document.title = t('resetPageTitle');
}

function showMessage(text, type) {
	const msgEl = document.getElementById('message');
	msgEl.textContent = text;
	msgEl.className = 'account-message' + (type ? ' ' + type : '');
}

function getApiErrorMessage(err, fallback) {
	const data = err.response?.data;
	if (!data) return fallback;
	if (data.errors) {
		const first = Object.values(data.errors).flat()[0];
		if (first) return first;
	}
	return data.message || fallback;
}

(function init() {
	const params = new URLSearchParams(window.location.search);
	const token = params.get('token');
	const email = params.get('email');

	if (!token || !email) {
		document.getElementById('invalid-link').style.display = 'block';
		return;
	}

	document.getElementById('reset-form').style.display = 'block';
	document.getElementById('reset-token').value = token;
	document.getElementById('reset-email').value = email;
})();

document.getElementById('reset-form').addEventListener('submit', function (e) {
	e.preventDefault();

	const password = document.getElementById('reset-password').value;
	const confirm = document.getElementById('reset-password-confirm').value;

	if (password.length < 6) {
		showMessage(t('passwordMin6'), 'error');
		return;
	}
	if (password !== confirm) {
		showMessage(t('passwordConfirmMismatch'), 'error');
		return;
	}

	showMessage('', '');

	axios.post(`${apiBase}/reset-password`, {
		token: document.getElementById('reset-token').value,
		email: document.getElementById('reset-email').value,
		password: password,
		password_confirmation: confirm,
	})
		.then(res => {
			showMessage(res.data.message || t('resetSuccess'), 'success');
			setTimeout(function () {
				window.location.href = '/login';
			}, 2500);
		})
		.catch(err => {
			showMessage(getApiErrorMessage(err, t('resetFailed')), 'error');
		});
});
</script>

</body>
</html>
