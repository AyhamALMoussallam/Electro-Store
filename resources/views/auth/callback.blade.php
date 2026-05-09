<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Google Callback</title>
<script>
    const token = new URLSearchParams(window.location.search).get('token');
    if(token) localStorage.setItem('auth_token', token);
    window.location.href = '/dashboard';
</script>
</head>
<body></body>
</html>
