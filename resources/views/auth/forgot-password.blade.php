<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.electro-head', ['title' => 'Electro - Forgot Password', 'accountPage' => true])
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body data-active-nav="">

@include('partials.electro-header', ['minimalHeader' => true])

<div class="account-auth-section">
	<div class="container">
		<div class="auth-card">
			<h2>Forgot Password</h2>
			<p class="auth-subtitle" style="text-align:center;">Enter your email and we'll send you a link to reset your password.</p>

			<input type="email" class="input" id="forgot-email" placeholder="Email">
			<button type="button" class="primary-btn" onclick="sendResetLink()">Send Reset Link</button>

			<div class="auth-link">
				<a href="/login">Back to Sign In</a>
			</div>

			<div class="account-message" id="message"></div>
		</div>
	</div>
</div>

<script>
const apiBase = '/api';

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
        showMessage('Please enter your email.', 'error');
        return;
    }

    showMessage('', '');

    axios.post(`${apiBase}/forgot-password`, { email })
        .then(res => {
            showMessage(
                res.data.message || 'If an account exists for that email, we have sent a password reset link. Check your inbox.',
                'success'
            );
        })
        .catch(err => {
            showMessage(getApiErrorMessage(err, 'Something went wrong. Try again later.'), 'error');
        });
}
</script>

@include('partials.electro-footer')
@include('partials.electro-scripts')

</body>
</html>
