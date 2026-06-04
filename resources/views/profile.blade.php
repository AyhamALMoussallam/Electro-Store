<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.electro-head', ['title' => 'Electro - Profile', 'accountPage' => true])
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body data-active-nav="">

@include('partials.electro-header', ['hideMainNav' => true, 'showHeaderUserName' => true])
@include('partials.electro-account-toolbar', ['showLogout' => true, 'hideToolbarTitle' => true])

<div class="account-page-section">
	<div class="container account-container">
		<div id="loading" class="account-card account-loading">Loading profile...</div>

		<div id="profile-content" style="display:none;">
			<div class="account-card">
				<h3>Account info</h3>
				<div class="account-info-row">
					<label>Name</label>
					<div class="value" id="profile-name"></div>
				</div>
				<div class="account-info-row">
					<label>Email</label>
					<div class="value" id="profile-email"></div>
				</div>
				<div class="account-info-row">
					<label for="profile-phone">Phone number</label>
					<input type="text" class="input" id="profile-phone" placeholder="09XXXXXXXX" maxlength="10" inputmode="numeric">
					<p>10 digits, e.g. 09XXXXXXXX</p>
					<button type="button" class="primary-btn" onclick="updatePhone()">Save phone number</button>
					<div class="account-message" id="phone-message"></div>
				</div>
			</div>

			<div class="account-card" id="orders-link-card" style="display:none;">
				<h3>Orders</h3>
				<p>View your order history and current status.</p>
				<a href="/orders" class="primary-btn" style="display:inline-block; width:auto;">My Orders</a>
			</div>

			<div class="account-card">
				<h3>Change password</h3>
				<input type="password" class="input" id="current-password" placeholder="Current password" autocomplete="current-password">
				<input type="password" class="input" id="new-password" placeholder="New password" autocomplete="new-password">
				<input type="password" class="input" id="new-password-confirm" placeholder="Confirm new password" autocomplete="new-password">
				<button type="button" class="primary-btn" onclick="changePassword()">Update password</button>
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
if (!token) {
    window.location.href = '/login';
}
const headers = { Authorization: 'Bearer ' + token };

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
                headerUserName.textContent = user.name;
            }
            document.getElementById('profile-name').textContent = user.name;
            document.getElementById('profile-email').textContent = user.email;
            document.getElementById('profile-phone').value = user.phone ?? '';

            if (Number(user.role) === 1) {
                document.getElementById('nav-dashboard').style.display = 'inline';
            } else {
                document.getElementById('nav-orders').style.display = 'inline';
                document.getElementById('orders-link-card').style.display = 'block';
            }

            document.getElementById('loading').style.display = 'none';
            document.getElementById('profile-content').style.display = 'block';
        })
        .catch(err => {
            if (err.response && (err.response.status === 401 || err.response.status === 403)) {
                localStorage.removeItem('auth_token');
                window.location.href = '/login';
            } else {
                document.getElementById('loading').textContent = 'Failed to load profile.';
            }
        });
}

function updatePhone() {
    const phone = document.getElementById('profile-phone').value.trim();
    const msgEl = document.getElementById('phone-message');

    if (!phone) {
        setMessage(msgEl, 'Please enter your phone number.', 'error');
        return;
    }

    if (phone.length !== 10) {
        setMessage(msgEl, 'Phone number must be exactly 10 digits (e.g. 09XXXXXXXX).', 'error');
        return;
    }

    setMessage(msgEl, '', '');

    axios.put(`${apiBase}/profile`, { phone }, { headers })
        .then(res => {
            setMessage(msgEl, res.data.message || 'Phone number saved successfully.', 'success');
        })
        .catch(err => {
            setMessage(msgEl, getApiErrorMessage(err, 'Failed to update phone number.'), 'error');
        });
}

function changePassword() {
    const current = document.getElementById('current-password').value;
    const newPass = document.getElementById('new-password').value;
    const confirmPass = document.getElementById('new-password-confirm').value;
    const msgEl = document.getElementById('password-message');

    if (!current) {
        setMessage(msgEl, 'Please enter your current password.', 'error');
        return;
    }
    if (!newPass) {
        setMessage(msgEl, 'Please enter a new password.', 'error');
        return;
    }
    if (newPass.length < 6) {
        setMessage(msgEl, 'New password must be at least 6 characters.', 'error');
        return;
    }
    if (newPass !== confirmPass) {
        setMessage(msgEl, 'New password and confirmation do not match.', 'error');
        return;
    }

    setMessage(msgEl, '', '');

    axios.put(`${apiBase}/profile/password`, {
        current_password: current,
        password: newPass,
        password_confirmation: confirmPass
    }, { headers })
        .then(() => {
            setMessage(msgEl, 'Password updated successfully.', 'success');
            document.getElementById('current-password').value = '';
            document.getElementById('new-password').value = '';
            document.getElementById('new-password-confirm').value = '';
        })
        .catch(err => {
            setMessage(msgEl, getApiErrorMessage(err, 'Failed to update password.'), 'error');
        });
}

loadProfile();
</script>

</body>
</html>
