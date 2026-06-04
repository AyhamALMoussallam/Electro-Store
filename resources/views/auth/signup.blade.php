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
			<h2 data-i18n="signUp"></h2>

			<input type="text" class="input" id="signup-name" data-i18n-placeholder="name" placeholder="Name">
			<input type="email" class="input" id="signup-email" data-i18n-placeholder="email" placeholder="Email">
			<input type="text" class="input" id="signup-phone" data-i18n-placeholder="phonePlaceholder" placeholder="09XXXXXXXX" maxlength="10" inputmode="numeric">
			<input type="password" class="input" id="signup-password" data-i18n-placeholder="passwordMinPlaceholder" placeholder="Password">
			<input type="password" class="input" id="signup-password-confirm" data-i18n-placeholder="confirmPassword" placeholder="Confirm password">
			<button type="button" class="primary-btn" onclick="signup()" data-i18n="signUp"></button>

			<button type="button" class="primary-btn" id="resend-verification" onclick="resendVerificationEmail()" style="display:none; margin-top:10px;" data-i18n="resendVerification"></button>

			<div class="auth-link">
				<a href="/login" data-i18n="hasAccountSignIn"></a>
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
	document.title = t('signupPageTitle');
}

document.getElementById('signup-phone').addEventListener('input', function () {
	this.value = this.value.replace(/\D/g, '').slice(0, 10);
});

function getApiErrorMessage(err, fallback) {
	const data = err.response?.data;
	if (!data) return fallback;
	if (data.errors) {
		const first = Object.values(data.errors).flat()[0];
		if (first) return first;
	}
	return data.message || fallback;
}

function showMessage(text, type) {
	const msgEl = document.getElementById('message');
	msgEl.textContent = text;
	msgEl.className = 'account-message' + (type ? ' ' + type : '');
}

function signup() {
	const name = document.getElementById('signup-name').value.trim();
	const email = document.getElementById('signup-email').value.trim();
	const phone = document.getElementById('signup-phone').value.trim();
	const password = document.getElementById('signup-password').value;
	const passwordConfirm = document.getElementById('signup-password-confirm').value;

	if (!name) {
		showMessage(t('enterName'), 'error');
		return;
	}

	if (!email) {
		showMessage(t('enterEmail'), 'error');
		return;
	}

	if (!phone) {
		showMessage(t('enterPhone'), 'error');
		return;
	}

	if (phone.length !== 10) {
		showMessage(t('phoneDigitsError'), 'error');
		return;
	}

	if (!password) {
		showMessage(t('enterPasswordSignup'), 'error');
		return;
	}

	if (password.length < 6) {
		showMessage(t('passwordMin6'), 'error');
		return;
	}

	if (password !== passwordConfirm) {
		showMessage(t('passwordConfirmMismatch'), 'error');
		return;
	}

	showMessage('', '');

	axios.post(`${apiBase}/signup`, {
		name,
		email,
		phone,
		password,
		password_confirmation: passwordConfirm,
	})
	.then(res => {
		showMessage(
			res.data.message || t('registrationSuccess'),
			'success'
		);
		document.getElementById('resend-verification').style.display = 'block';
	})
	.catch(err => {
		showMessage(getApiErrorMessage(err, t('registrationFailed')), 'error');
	});
}

function resendVerificationEmail() {
	const email = document.getElementById('signup-email').value.trim();

	if (!email) {
		showMessage(t('enterEmailFirst'), 'error');
		return;
	}

	axios.post(`${apiBase}/email/verification-notification`, { email })
		.then(res => {
			showMessage(res.data.message, 'success');
		})
		.catch(err => {
			showMessage(getApiErrorMessage(err, t('resendVerificationFailed')), 'error');
		});
}
</script>

</body>
</html>
