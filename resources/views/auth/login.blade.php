<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
@include('partials.electro-head', ['title' => 'إلكترو - تسجيل الدخول', 'accountPage' => true])
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body class="minimal-header-page" data-active-nav="">

@include('partials.electro-header', ['minimalHeader' => true])

<div class="account-auth-section">
	<div class="container">
		<div class="auth-card">
			<h2>Sign In</h2>

			<input type="email" class="input" id="login-email" placeholder="Email">
			<input type="password" class="input" id="login-password" placeholder="Password">
			<button type="button" class="primary-btn" onclick="login()">Sign In</button>

			<button type="button" class="primary-btn" id="resend-verification" onclick="resendVerificationEmail()" style="display:none; margin-top:10px;">
				Resend verification email
			</button>

			<div class="auth-link">
				<a href="/forgot-password">Forgot password?</a>
			</div>
			<div class="auth-link">
				<a href="/signup">Don't have an account? Sign Up</a>
			</div>

			<button type="button" class="primary-btn google-btn" onclick="googleLogin()">Sign In with Google</button>

			<div class="account-message" id="message"></div>
		</div>
	</div>
</div>

<script src="/js/jquery.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script>
const apiBase = '/api';

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
        window.location.href = role === 1 ? '/dashboard' : '/profile';
    })
    .catch(() => {
        window.location.href = '/profile';
    });
}

function login() {
    const email = document.getElementById('login-email').value.trim();
    const password = document.getElementById('login-password').value;
    const resendBtn = document.getElementById('resend-verification');

    resendBtn.style.display = 'none';

    if (!email) {
        showMessage('Please enter your email.', 'error');
        return;
    }

    if (!password) {
        showMessage('Please enter your password.', 'error');
        return;
    }

    showMessage('', '');

    axios.post(`${apiBase}/login`, { email, password })
        .then(res => {
            if (!res.data.token) {
                showMessage('Login failed. No token received. Please try again.', 'error');
                return;
            }
            redirectAfterLogin(res.data.token);
        })
        .catch(err => {
            const msg = getApiErrorMessage(err, 'Sign in failed. Please try again.');
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
        showMessage('Enter your email above, then click resend.', 'error');
        return;
    }

    axios.post(`${apiBase}/email/verification-notification`, { email })
        .then(res => {
            showMessage(res.data.message, 'success');
        })
        .catch(err => {
            showMessage(getApiErrorMessage(err, 'Failed to resend verification email.'), 'error');
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
    showMessage('Email verified successfully. You can sign in now.', 'success');
}

if (params.get('error') === 'google_auth_failed') {
    showMessage('Google sign-in failed. Please try again.', 'error');
}
</script>

@include('partials.electro-footer')
@include('partials.electro-scripts')

</body>
</html>
