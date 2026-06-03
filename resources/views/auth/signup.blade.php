<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.electro-head', ['title' => 'Electro - Sign Up', 'accountPage' => true])
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body data-active-nav="">

@include('partials.electro-header')

<div class="account-auth-section">
	<div class="container">
		<div class="auth-card">
			<h2>Sign Up</h2>

			<input type="text" class="input" id="signup-name" placeholder="Name">
			<input type="email" class="input" id="signup-email" placeholder="Email">
			<input type="text" class="input" id="signup-phone" placeholder="09XXXXXXXX" maxlength="10" inputmode="numeric">
			<input type="password" class="input" id="signup-password" placeholder="Password (min. 6 characters)">
			<input type="password" class="input" id="signup-password-confirm" placeholder="Confirm password">
			<button type="button" class="primary-btn" onclick="signup()">Sign Up</button>

			<button type="button" class="primary-btn" id="resend-verification" onclick="resendVerificationEmail()" style="display:none; margin-top:10px;">
				Resend verification email
			</button>

			<div class="auth-link">
				<a href="/login">Already have an account? Sign In</a>
			</div>

			<div class="account-message" id="message"></div>
		</div>
	</div>
</div>

<script src="/js/jquery.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script>
const apiBase = '/api';

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
        showMessage('Please enter your name.', 'error');
        return;
    }

    if (!email) {
        showMessage('Please enter your email.', 'error');
        return;
    }

    if (!phone) {
        showMessage('Please enter your phone number.', 'error');
        return;
    }

    if (phone.length !== 10) {
        showMessage('Phone number must be exactly 10 digits (e.g. 09XXXXXXXX).', 'error');
        return;
    }

    if (!password) {
        showMessage('Please enter a password.', 'error');
        return;
    }

    if (password.length < 6) {
        showMessage('Password must be at least 6 characters.', 'error');
        return;
    }

    if (password !== passwordConfirm) {
        showMessage('Password and confirmation do not match.', 'error');
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
            res.data.message || 'Registration successful! A verification email has been sent to your inbox.',
            'success'
        );
        document.getElementById('resend-verification').style.display = 'block';
    })
    .catch(err => {
        showMessage(getApiErrorMessage(err, 'Registration failed. Please check your details and try again.'), 'error');
    });
}

function resendVerificationEmail() {
    const email = document.getElementById('signup-email').value.trim();

    if (!email) {
        showMessage('Please enter your email first.', 'error');
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
</script>

@include('partials.electro-footer')
@include('partials.electro-scripts')

</body>
</html>
