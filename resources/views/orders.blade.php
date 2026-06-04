<!DOCTYPE html>
<html lang="en">
<head>
@include('partials.electro-head', ['title' => 'Electro - My Orders', 'accountPage' => true])
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body data-active-nav="">

@include('partials.electro-header', ['hideMainNav' => true])
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

function buildUserOrderNumbers(orders) {
    const sorted = [...orders].sort(
        (a, b) => new Date(a.created_at) - new Date(b.created_at)
    );
    const numbers = {};

    sorted.forEach((order, index) => {
        numbers[order.id] = index + 1;
    });

    return numbers;
}

function renderOrders(orders) {
    const list = document.getElementById('orders-list');
    list.innerHTML = '';

    const orderNumbers = buildUserOrderNumbers(orders);

    orders.forEach(order => {
        let subtotal = 0;

        const itemsHtml = (order.items || []).map(item => {
            const lineTotal = item.quantity * parseFloat(item.price);
            subtotal += lineTotal;

            return `
            <tr>
                <td>${item.product?.name ?? 'Product #' + item.product_id}</td>
                <td>${item.quantity}</td>
                <td>$${parseFloat(item.price).toFixed(2)}</td>
                <td>$${lineTotal.toFixed(2)}</td>
            </tr>
        `;
        }).join('');

        const areaFee = order.area?.fee != null
            ? parseFloat(order.area.fee)
            : Math.max(0, parseFloat(order.total_price) - subtotal);

        const noteHtml = order.note
            ? `<p class="order-note"><strong>Note:</strong> ${order.note}</p>`
            : '';

        list.innerHTML += `
            <div class="order-card">
                <h3>Order ${orderNumbers[order.id]}</h3>
                <div class="order-meta">
                    <span><strong>Status:</strong>
                        <span class="order-status ${order.status}">${order.status}</span>
                    </span>
                    <span><strong>Placed:</strong> ${formatDate(order.created_at)}</span>
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
                <div class="order-totals">
                    <p><strong>Subtotal:</strong> $${subtotal.toFixed(2)}</p>
                    <p><strong>Area fee:</strong> $${areaFee.toFixed(2)}</p>
                    <p><strong>Total:</strong> $${parseFloat(order.total_price).toFixed(2)}</p>
                </div>
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
