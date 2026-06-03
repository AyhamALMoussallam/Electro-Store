<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.electro-head', ['title' => 'Electro - My Orders', 'accountPage' => true])
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body data-active-nav="">

@include('partials.electro-header')
@include('partials.electro-account-toolbar', ['toolbarTitle' => 'My Orders', 'showLogout' => true])

<div class="account-page-section">
	<div class="container account-container-wide">
		<div id="loading" class="account-card account-loading">Loading orders...</div>
		<div id="orders-list"></div>
		<div id="empty" class="account-card account-empty" style="display:none;">You have no orders yet. <a href="/store">Browse the store</a></div>
	</div>
</div>

<script>
const apiBase = '/api';
const token = localStorage.getItem('auth_token');
if (!token) {
    window.location.href = '/login';
}
const headers = { Authorization: 'Bearer ' + token };

document.getElementById('nav-profile').style.display = 'inline';

function logout() {
    axios.post(`${apiBase}/logout`, {}, { headers })
        .then(() => {
            localStorage.removeItem('auth_token');
            window.location.href = '/login';
        })
        .catch(() => { window.location.href = '/login'; });
}

function formatDate(value) {
    return new Date(value).toLocaleString();
}

function renderOrders(orders) {
    const list = document.getElementById('orders-list');
    list.innerHTML = '';

    orders.forEach(order => {
        const itemsHtml = (order.items || []).map(item => `
            <tr>
                <td>${item.product?.name ?? 'Product #' + item.product_id}</td>
                <td>${item.quantity}</td>
                <td>$${item.price}</td>
                <td>$${(item.quantity * item.price).toFixed(2)}</td>
            </tr>
        `).join('');

        const noteHtml = order.note
            ? `<p class="order-note"><strong>Note:</strong> ${order.note}</p>`
            : '';

        list.innerHTML += `
            <div class="order-card">
                <h3>Order #${order.id}</h3>
                <div class="order-meta">
                    <span><strong>Status:</strong>
                        <span class="order-status ${order.status}">${order.status}</span>
                    </span>
                    <span><strong>Placed:</strong> ${formatDate(order.created_at)}</span>
                    <span><strong>Total:</strong> $${order.total_price}</span>
                    <span><strong>Delivery:</strong>
                        ${order.area?.name ?? '-'}, ${order.area?.city?.name ?? '-'}
                    </span>
                </div>
                <table class="order-items-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>${itemsHtml}</tbody>
                </table>
                ${noteHtml}
            </div>
        `;
    });
}

function loadOrders() {
    axios.get(`${apiBase}/orders/my`, { headers })
        .then(res => {
            const orders = res.data.data ?? [];
            document.getElementById('loading').style.display = 'none';

            if (!orders.length) {
                document.getElementById('empty').style.display = 'block';
                return;
            }

            renderOrders(orders);
        })
        .catch(err => {
            if (err.response && (err.response.status === 401 || err.response.status === 403)) {
                localStorage.removeItem('auth_token');
                window.location.href = '/login';
            } else {
                document.getElementById('loading').textContent = 'Failed to load orders.';
            }
        });
}

axios.get(`${apiBase}/user`, { headers })
    .then(res => {
        if (Number(res.data.user.role) === 1) {
            window.location.href = '/dashboard';
        } else {
            loadOrders();
        }
    })
    .catch(() => {
        localStorage.removeItem('auth_token');
        window.location.href = '/login';
    });
</script>

@include('partials.electro-footer')
@include('partials.electro-scripts')

</body>
</html>
