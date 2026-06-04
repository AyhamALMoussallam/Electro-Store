<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
@include('partials.electro-head', ['title' => 'Electro - Forgot Password', 'accountPage' => true])
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body class="minimal-header-page" data-active-nav="">

@include('partials.electro-header', ['minimalHeader' => true])

<div class="account-auth-section">
	<div class="container">
		<div class="auth-card">
			<h2 data-i18n="forgotPasswordTitle"></h2>
			<p class="auth-subtitle" style="text-align:center;" data-i18n="forgotPasswordHint"></p>

			<input type="email" class="input" id="forgot-email" data-i18n-placeholder="email" placeholder="Email">
			<button type="button" class="primary-btn" onclick="sendResetLink()" data-i18n="sendResetLink"></button>

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

function sendResetLink() {
	const email = document.getElementById('forgot-email').value.trim();

	if (!email) {
		showMessage(t('enterEmail'), 'error');
		return;
	}

	showMessage('', '');

	axios.post(`${apiBase}/forgot-password`, { email })
		.then(res => {
			showMessage(
				res.data.message || t('resetLinkSent'),
				'success'
			);
		})
		.catch(err => {
			showMessage(getApiErrorMessage(err, t('somethingWrongTryAgain')), 'error');
		});
}
</script>

</body>
</html>
