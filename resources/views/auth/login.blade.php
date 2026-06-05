<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
@include('partials.electro-head', ['title' => 'Electro - Sign In', 'accountPage' => true])
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body class="minimal-header-page" data-active-nav="">

@include('partials.electro-header', ['minimalHeader' => true])

<div class="account-auth-section">
	<div class="container">
		<div class="auth-card">
			<h2 data-i18n="signIn"></h2>

			<input type="email" class="input" id="login-email" data-i18n-placeholder="email" placeholder="Email">
			<input type="password" class="input" id="login-password" data-i18n-placeholder="password" placeholder="Password">
			<button type="button" class="primary-btn" onclick="login()" data-i18n="signIn"></button>

			<button type="button" class="primary-btn" id="resend-verification" onclick="resendVerificationEmail()" style="display:none; margin-top:10px;" data-i18n="resendVerification"></button>

			<div class="auth-link">
				<a href="/forgot-password" data-i18n="forgotPassword"></a>
			</div>
			<div class="auth-link">
				<a href="/signup" data-i18n="noAccountSignUp"></a>
			</div>

			<button type="button" class="primary-btn google-btn" onclick="googleLogin()" data-i18n="signInWithGoogle"></button>

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

function redirectAfterLogin(token) {
	localStorage.setItem('auth_token', token);

	axios.get(`${apiBase}/user`, {
		headers: { Authorization: 'Bearer ' + token }
	})
	.then(res => {
		const role = Number(res.data.user.role);
		window.location.href = role === 1 ? '/dashboard/' : '/home/';
	})
	.catch(() => {
		window.location.href = '/home/';
	});
}

function login() {
	const email = document.getElementById('login-email').value.trim();
	const password = document.getElementById('login-password').value;
	const resendBtn = document.getElementById('resend-verification');

	resendBtn.style.display = 'none';

	if (!email) {
		showMessage(t('enterEmail'), 'error');
		return;
	}

	if (!password) {
		showMessage(t('enterPassword'), 'error');
		return;
	}

	showMessage('', '');

	axios.post(`${apiBase}/login`, { email, password })
		.then(res => {
			if (!res.data.token) {
				showMessage(t('loginNoToken'), 'error');
				return;
			}
			redirectAfterLogin(res.data.token);
		})
		.catch(err => {
			const msg = getApiErrorMessage(err, t('signInFailed'));
			showMessage(msg, 'error');

			if (err.response?.status === 403 &&
				msg.toLowerCase().includes('not verified')) {
				resendBtn.style.display = 'block';
			}
		});
}

function resendVerificationEmail() {
	const email = document.getElementById('login-email').value.trim();

	if (!email) {
		showMessage(t('enterEmailThenResend'), 'error');
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

function googleLogin() {
	window.location.href = '/auth/google/redirect';
}

['login-email', 'login-password'].forEach(id => {
	document.getElementById(id).addEventListener('keydown', e => {
		if (e.key === 'Enter') login();
	});
});

const params = new URLSearchParams(window.location.search);

if (params.get('verified') === '1') {
	showMessage(t('emailVerifiedSignIn'), 'success');
}

if (params.get('error') === 'google_auth_failed') {
	showMessage(t('googleSignInFailed'), 'error');
}
</script>

</body>
</html>
