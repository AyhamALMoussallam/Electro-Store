<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
@include('partials.electro-head', ['title' => 'إلكترو - طلباتي', 'accountPage' => true])
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body class="page-orders hide-main-nav-page account-ui-ready" data-active-nav="">

@include('partials.electro-header', ['hideMainNav' => true])
@include('partials.electro-account-toolbar', ['toolbarTitle' => 'طلباتي', 'showLogout' => true, 'showNavProfile' => true])

<div class="account-page-section">
	<div class="container account-container-wide">
		<div id="loading" class="account-card account-loading">جاري تحميل الطلبات...</div>
		<div id="orders-list"></div>
		<div id="empty" class="account-card account-empty" style="display:none;">لا توجد طلبات بعد. <a href="/store">تصفّح المتجر</a></div>
	</div>
</div>

<script>
const apiBase = '/api';
const token = localStorage.getItem('auth_token');
if (!token) {
    window.location.href = '/login';
}
const headers = { Authorization: 'Bearer ' + token };

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

const statusLabels = {
    pending: 'قيد الانتظار',
    paid: 'مدفوع',
    shipped: 'تم الشحن',
    delivered: 'تم التسليم',
    canceled: 'ملغى',
};

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
                <td>${item.product?.name ?? 'منتج #' + item.product_id}</td>
                <td>${item.quantity}</td>
                <td>${formatPrice(item.price)}</td>
                <td>${formatPrice(lineTotal)}</td>
            </tr>
        `;
        }).join('');

        const areaFee = order.area?.fee != null
            ? parseFloat(order.area.fee)
            : Math.max(0, parseFloat(order.total_price) - subtotal);

        const noteHtml = order.note
            ? `<p class="order-note"><strong>ملاحظة:</strong> ${order.note}</p>`
            : '';

        list.innerHTML += `
            <div class="order-card">
                <h3>طلب ${orderNumbers[order.id]}</h3>
                <div class="order-meta">
                    <span><strong>الحالة:</strong>
                        <span class="order-status ${order.status}">${statusLabels[order.status] ?? order.status}</span>
                    </span>
                    <span><strong>تاريخ الطلب:</strong> ${formatDate(order.created_at)}</span>
                    <span><strong>التوصيل:</strong>
                        ${order.area?.name ?? '-'}, ${order.area?.city?.name ?? '-'}
                    </span>
                </div>
                <table class="order-items-table">
                    <thead>
                        <tr>
                            <th>المنتج</th>
                            <th>الكمية</th>
                            <th>السعر</th>
                            <th>المجموع</th>
                        </tr>
                    </thead>
                    <tbody>${itemsHtml}</tbody>
                </table>
                <div class="order-totals">
                    <p><strong>المجموع الفرعي:</strong> ${formatPrice(subtotal)}</p>
                    <p><strong>رسوم المنطقة:</strong> ${formatPrice(areaFee)}</p>
                    <p><strong>الإجمالي:</strong> ${formatPrice(order.total_price)}</p>
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
