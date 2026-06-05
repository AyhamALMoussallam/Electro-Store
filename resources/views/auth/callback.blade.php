<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
@include('partials.electro-favicon')
<title>Electro - Sign In</title>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    const token = new URLSearchParams(window.location.search).get('token');

    if (!token) {
        window.location.href = '/login';
    } else {
        localStorage.setItem('auth_token', token);

        axios.get('/api/user', {
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
</script>
</head>
<body></body>
</html>
