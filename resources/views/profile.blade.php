<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
@include('partials.electro-head', ['title' => 'Electro - Profile', 'accountPage' => true])
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body class="page-profile account-ui-pending hide-main-nav-page" data-active-nav="" data-hide-header-cart="pending">

@include('partials.electro-header', ['hideMainNav' => true, 'showHeaderUserName' => true])
@include('partials.electro-account-toolbar', ['showLogout' => true, 'hideToolbarTitle' => true])

<div class="account-page-section">
	<div class="container account-container">
		<div id="loading" class="account-card account-loading" data-i18n="loadingProfile"></div>

		<div id="profile-content" style="display:none;">
			<div class="account-card">
				<h3 data-i18n="accountInfo"></h3>
				<div class="account-info-row">
					<label data-i18n="name"></label>
					<div class="value" id="profile-name"></div>
				</div>
				<div class="account-info-row">
					<label data-i18n="email"></label>
					<div class="value" id="profile-email"></div>
				</div>
				<div class="account-info-row">
					<label for="profile-phone" data-i18n="phoneNumber"></label>
					<input type="text" class="input" id="profile-phone" data-i18n-placeholder="phonePlaceholder" placeholder="09XXXXXXXX" maxlength="10" inputmode="numeric">
					<p data-i18n="phoneHint"></p>
					<button type="button" class="primary-btn" onclick="updatePhone()" data-i18n="savePhone"></button>
					<div class="account-message" id="phone-message"></div>
				</div>
			</div>

			<div class="account-card" id="orders-link-card" style="display:none;">
				<h3 data-i18n="orders"></h3>
				<p data-i18n="viewOrders"></p>
				<a href="/orders" class="primary-btn account-btn-inline" data-i18n="myOrders"></a>
			</div>

			<div class="account-card">
				<h3 data-i18n="changePassword"></h3>
				<input type="password" class="input" id="current-password" data-i18n-placeholder="currentPassword" autocomplete="current-password">
				<input type="password" class="input" id="new-password" data-i18n-placeholder="newPassword" autocomplete="new-password">
				<input type="password" class="input" id="new-password-confirm" data-i18n-placeholder="confirmPassword" autocomplete="new-password">
				<button type="button" class="primary-btn" onclick="changePassword()" data-i18n="updatePassword"></button>
				<div class="account-message" id="password-message"></div>
			</div>
		</div>
	</div>
</div>

@include('partials.electro-footer')
@include('partials.electro-scripts')

<script>
const apiBase = '/api';
const token = localStorage.getItem('auth_token');

function t(key) {
	return window.ElectroI18n ? window.ElectroI18n.t(key) : key;
}

if (!token) {
	window.location.href = '/login';
}

const headers = { Authorization: 'Bearer ' + token };

(function initProfilePage() {
	const headerUserName = document.getElementById('header-user-name');
	if (headerUserName) {
		headerUserName.textContent = t('loading');
	}
})();

document.getElementById('profile-phone').addEventListener('input', function () {
	this.value = this.value.replace(/\D/g, '').slice(0, 10);
});

function setMessage(el, text, type) {
	el.textContent = text;
	el.className = 'account-message' + (type ? ' ' + type : '');
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

function logout() {
	axios.post(`${apiBase}/logout`, {}, { headers })
		.then(() => {
			localStorage.removeItem('auth_token');
			window.location.href = '/login';
		})
		.catch(() => { window.location.href = '/login'; });
}

function loadProfile() {
	axios.get(`${apiBase}/user`, { headers })
		.then(res => {
			const user = res.data.user;
			const headerUserName = document.getElementById('header-user-name');
			if (headerUserName) {
				headerUserName.removeAttribute('data-i18n');
				headerUserName.textContent = user.name;
			}
			document.getElementById('profile-name').textContent = user.name;
			document.getElementById('profile-email').textContent = user.email;
			document.getElementById('profile-phone').value = user.phone ?? '';

			document.body.classList.remove('account-ui-pending');
			document.body.classList.add('profile-layout-ready');
			document.body.removeAttribute('data-hide-header-cart');

			if (Number(user.role) === 1) {
				const navDashboard = document.getElementById('nav-dashboard');
				if (navDashboard) {
					navDashboard.style.display = 'inline';
					navDashboard.classList.add('nav-visible');
				}
				document.body.classList.add('admin-profile-page');
			} else {
				document.body.classList.add('customer-profile');
				const navOrders = document.getElementById('nav-orders');
				if (navOrders) {
					navOrders.style.display = 'inline';
					navOrders.classList.add('nav-visible');
				}
				document.getElementById('orders-link-card').style.display = 'block';
				if (typeof window.reloadSiteLayout === 'function') {
					window.reloadSiteLayout();
				}
			}

			document.getElementById('loading').style.display = 'none';
			document.getElementById('profile-content').style.display = 'block';
		})
		.catch(err => {
			if (err.response && (err.response.status === 401 || err.response.status === 403)) {
				localStorage.removeItem('auth_token');
				window.location.href = '/login';
			} else {
				document.getElementById('loading').textContent = t('profileLoadFailed');
			}
		});
}

function updatePhone() {
	const phone = document.getElementById('profile-phone').value.trim();
	const msgEl = document.getElementById('phone-message');

	if (!phone) {
		setMessage(msgEl, t('enterPhone'), 'error');
		return;
	}

	if (phone.length !== 10) {
		setMessage(msgEl, t('phoneDigitsError'), 'error');
		return;
	}

	setMessage(msgEl, '', '');

	axios.put(`${apiBase}/profile`, { phone }, { headers })
		.then(res => {
			setMessage(msgEl, res.data.message || t('success'), 'success');
		})
		.catch(err => {
			setMessage(msgEl, getApiErrorMessage(err, t('phoneUpdateFailed')), 'error');
		});
}

function changePassword() {
	const current = document.getElementById('current-password').value;
	const newPass = document.getElementById('new-password').value;
	const confirmPass = document.getElementById('new-password-confirm').value;
	const msgEl = document.getElementById('password-message');

	if (!current) {
		setMessage(msgEl, t('enterCurrentPassword'), 'error');
		return;
	}
	if (!newPass) {
		setMessage(msgEl, t('enterNewPassword'), 'error');
		return;
	}
	if (newPass.length < 6) {
		setMessage(msgEl, t('passwordMinLength'), 'error');
		return;
	}
	if (newPass !== confirmPass) {
		setMessage(msgEl, t('passwordMismatch'), 'error');
		return;
	}

	setMessage(msgEl, '', '');

	axios.put(`${apiBase}/profile/password`, {
		current_password: current,
		password: newPass,
		password_confirmation: confirmPass
	}, { headers })
		.then(() => {
			setMessage(msgEl, t('passwordUpdated'), 'success');
			document.getElementById('current-password').value = '';
			document.getElementById('new-password').value = '';
			document.getElementById('new-password-confirm').value = '';
		})
		.catch(err => {
			setMessage(msgEl, getApiErrorMessage(err, t('passwordUpdateFailed')), 'error');
		});
}

loadProfile();
</script>

</body>
</html>
