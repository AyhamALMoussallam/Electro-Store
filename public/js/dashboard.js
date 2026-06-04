// =========================
// API
// =========================
const API_CATEGORIES = "/api/categories";
const API_BRANDS     = "/api/brands";
const API_PRODUCTS   = "/api/products";
const API_CITIES     = "/api/cities";
const API_AREAS      = "/api/areas";

const token = localStorage.getItem("auth_token");
if (!token) window.location.href = "/login";

function t(key, replacements) {
	if (window.ElectroI18n && typeof window.ElectroI18n.t === 'function') {
		return window.ElectroI18n.t(key, replacements);
	}
	return key;
}

function displayPrice(usd) {
	if (typeof window.formatPrice === 'function') {
		return window.formatPrice(usd);
	}
	return usd;
}

function priceToInput(usd) {
	if (window.ElectroCurrency && typeof window.ElectroCurrency.toDisplay === 'function') {
		const amount = window.ElectroCurrency.toDisplay(usd);
		return Math.round(amount * 100) / 100;
	}
	return usd;
}

function priceFromInput(value) {
	if (window.ElectroCurrency && typeof window.ElectroCurrency.displayToUsd === 'function') {
		const usd = window.ElectroCurrency.displayToUsd(value);
		return usd != null ? usd : value;
	}
	return value;
}

function updateAdminCurrencyHints() {
	const isSp = window.ElectroCurrency && window.ElectroCurrency.get() === 'sp';
	const pricePh = isSp ? t('pricePlaceholderSp') : t('pricePlaceholderUsd');
	const feePh = isSp ? t('feePlaceholderSp') : t('feePlaceholderUsd');

	['product-price', 'edit-product-price'].forEach(function (id) {
		const el = document.getElementById(id);
		if (el) {
			el.placeholder = pricePh;
		}
	});

	['area-fee', 'edit-area-fee'].forEach(function (id) {
		const el = document.getElementById(id);
		if (el) {
			el.placeholder = feePh;
		}
	});
}

function orderStatusOptions(selected) {
	const statuses = ['pending', 'paid', 'shipped', 'delivered', 'canceled'];
	return statuses.map(function (status) {
		const sel = selected === status ? ' selected' : '';
		return '<option value="' + status + '"' + sel + '>' + t(status) + '</option>';
	}).join('');
}

function sortByIdDesc(items) {
	return [...(items || [])].sort(function (a, b) {
		return Number(b.id) - Number(a.id);
	});
}


// =========================
// ADMIN GUARD
// =========================
async function guardAdmin() {
    try {
        const res = await fetch("/api/user", {
            headers: {
                Authorization: "Bearer " + token,
                Accept: "application/json",
            },
        });

        if (!res.ok) {
            throw new Error("Unauthorized");
        }

        const data = await res.json();

        if (Number(data.user.role) !== 1) {
            window.location.href = "/profile";
            return false;
        }

        return true;
    } catch {
        window.location.href = "/login";
        return false;
    }
}


// =========================
// TAB SYSTEM
// =========================
function showTab(tabId) {
    document.querySelectorAll(".tab-content").forEach(t => {
        t.classList.remove("active");
        t.style.display = "none";
    });

    let tab = document.getElementById(tabId);
    if (tab) {
        tab.style.display = "block";
        tab.classList.add("active");
    }

    localStorage.setItem("activeTab", tabId);
}

window.addEventListener("load", async () => {
    if (!(await guardAdmin())) {
        return;
    }

    showTab(localStorage.getItem("activeTab") || "categories");

    boot();
});





async function loadBrands() {

    let res = await fetch(API_BRANDS);

    let data = await res.json();

    let html = "";

    sortByIdDesc(data.data).forEach(b => {

        html += `
            <tr>
                <td>${b.id}</td>

                <td>${b.name}</td>

                <td>
                    <button onclick='startEdit("brand", ${JSON.stringify(b).replace(/'/g, "&apos;")})'>
                        ${t('edit')}
                    </button>

                    <button
                        onclick="deleteBrand(${b.id})"
                    >
                        ${t('delete')}
                    </button>
                </td>
            </tr>
        `;
    });

    document.getElementById(
        "brands-table-body"
    ).innerHTML = html;
}




async function saveBrand() {
    const name = document.getElementById("brand-name").value.trim();

    if (!name) {
        return;
    }

    await fetch(API_BRANDS, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Authorization": "Bearer " + token,
        },
        body: JSON.stringify({ name }),
    });

    document.getElementById("brand-name").value = "";

    await loadBrands();
    await loadProductBrands();
}




async function deleteBrand(id) {

    await fetch(`${API_BRANDS}/${id}`, {
        method: "DELETE",
        headers: {
            "Authorization": "Bearer " + token
        }
    });

    await loadBrands();
    await loadProductBrands();
}


async function loadProductBrands() {

    let res = await fetch(API_BRANDS);

    let data = await res.json();

    let html =
        '<option value="">' + t('selectBrand') + '</option>';

    data.data.forEach(b => {

        html += `
            <option value="${b.id}">
                ${b.name}
            </option>
        `;
    });

    document.getElementById(
        "product-brand"
    ).innerHTML = html;
}

async function loadExchangeRateSetting() {
    const input = document.getElementById('usd-to-sp-rate');

    try {
        const res = await fetch('/api/settings/currency', {
            headers: { Accept: 'application/json' },
        });

        if (!res.ok) {
            throw new Error('Failed to load rate');
        }

        const data = await res.json();

        if (window.ElectroCurrency) {
            window.ElectroCurrency.setRate(data.usd_to_sp_rate);
        }

        if (input) {
            input.value = data.usd_to_sp_rate;
        }
    } catch (e) {
        if (input && window.ElectroCurrency) {
            input.value = window.ElectroCurrency.getRate();
        }
    }
}

async function saveExchangeRate() {
    const input = document.getElementById('usd-to-sp-rate');
    const rate = Number(input?.value);

    if (!rate || rate < 1) {
        alert(t('required'));
        return;
    }

    try {
        const res = await fetch('/api/settings/currency', {
            method: 'PUT',
            headers: {
                Authorization: 'Bearer ' + token,
                Accept: 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ usd_to_sp_rate: rate }),
        });

        const data = await res.json().catch(function () { return {}; });

        if (!res.ok) {
            alert(data.message || t('exchangeRateFailed'));
            return;
        }

        if (window.ElectroCurrency) {
            window.ElectroCurrency.setRate(data.usd_to_sp_rate);
        }

        if (input) {
            input.value = data.usd_to_sp_rate;
        }

        alert(t('exchangeRateSaved'));
        await boot();
    } catch (e) {
        alert(t('exchangeRateFailed'));
    }
}

function boot() {
    updateAdminCurrencyHints();
    loadExchangeRateSetting();
    loadCategories();
    initProductsSearch();
    loadProducts();
    loadCities();
    loadAreas();
    loadProductCategories();
    loadCitySelect();
    loadOrders();
    loadBrands();
    loadProductBrands();
}


// =========================
// EDIT STATE
// =========================
let editState = {
    type: null,
    id: null,
    data: null
};


// =========================
// CATEGORIES
// =========================
async function loadCategories() {
    let res = await fetch(API_CATEGORIES);
    let data = await res.json();

    let html = "";

    sortByIdDesc(data.data).forEach(c => {
        html += `
        <tr>
            <td>${c.id}</td>
            <td>${c.name}</td>
            <td>
                <button onclick='startEdit("category", ${JSON.stringify(c)})'>${t('edit')}</button>
                <button onclick="deleteCategory(${c.id})">${t('delete')}</button>
            </td>
        </tr>`;
    });

    document.getElementById("categories-table-body").innerHTML = html;
}

async function saveCategory() {
    let name = document.getElementById("category-name").value;
    if (!name.trim()) return alert(t('required'));

    let url = API_CATEGORIES;
    let method = "POST";

    if (editState.type === "category") {
        url = `${API_CATEGORIES}/${editState.id}`;
        method = "PUT";
    }

    await fetch(url, {
        method,
        headers: {
            "Content-Type": "application/json",
            "Authorization": "Bearer " + token
        },
        body: JSON.stringify({ name })
    });

    resetEdit();
    await loadCategories();
    await loadProductCategories();
    await loadProducts();
}

async function deleteCategory(id) {
    await fetch(`${API_CATEGORIES}/${id}`, {
        method: "DELETE",
        headers: { "Authorization": "Bearer " + token }
    });

    await loadCategories();
    await loadProductCategories();
    await loadProducts();
}


// =========================
// PRODUCTS
// =========================
let allProducts = [];

function escapeHtml(text) {
    if (text == null) return "";
    return String(text)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;");
}

function productMatchesSearch(product, query) {
    if (!query) return true;

    const q = query.toLowerCase();

    return (
        String(product.id).includes(q) ||
        (product.name || "").toLowerCase().includes(q) ||
        (product.category?.name || "").toLowerCase().includes(q) ||
        (product.brand?.name || "").toLowerCase().includes(q)
    );
}

function renderProductsTable() {
    const searchInput = document.getElementById("products-search");
    const query = searchInput ? searchInput.value.trim() : "";
    const items = sortByIdDesc(
        allProducts.filter(p => productMatchesSearch(p, query))
    );

    if (!items.length) {
        document.getElementById("products-table-body").innerHTML =
            `<tr><td colspan="9" class="text-center">${t('noProducts')}</td></tr>`;
        return;
    }

    let html = "";

    items.forEach(p => {
        const productJson = JSON.stringify(p).replace(/'/g, "&apos;");
        const imageSrc = `/storage/${p.image}`;
        const sales = p.sales ?? 0;

        html += `
        <tr>
            <td>${p.id}</td>
            <td><img src="${imageSrc}" width="50" alt=""></td>
            <td>
                <a class="products-table-link" href="/product/?id=${p.id}">
                    ${escapeHtml(p.name)}
                </a>
            </td>
            <td>${escapeHtml(p.category?.name ?? "-")}</td>
            <td>${escapeHtml(p.brand?.name ?? "-")}</td>
            <td>${displayPrice(p.price)}</td>
            <td>${p.stock}</td>
            <td>${sales}</td>
            <td>
                <button onclick='startEdit("product", ${productJson})'>
                    ${t('edit')}
                </button>
                <button onclick="deleteProduct(${p.id})">${t('delete')}</button>
            </td>
        </tr>`;
    });

    document.getElementById("products-table-body").innerHTML = html;
}

function initProductsSearch() {
    const searchInput = document.getElementById("products-search");

    if (!searchInput || searchInput.dataset.bound) {
        return;
    }

    searchInput.dataset.bound = "1";
    searchInput.addEventListener("input", renderProductsTable);
}

async function loadProducts() {
    let res = await fetch(API_PRODUCTS, {
        headers: { Authorization: "Bearer " + token },
    });
    let data = await res.json();

    allProducts = sortByIdDesc(data.data ?? data);
    renderProductsTable();
}

async function saveProduct() {

    let formData = new FormData();

    formData.append("category_id", document.getElementById("product-category").value);
    formData.append("brand_id", document.getElementById("product-brand").value);
    formData.append("name", document.getElementById("product-name").value);
    formData.append("description_en", document.getElementById("product-description-en").value);
    formData.append("description_ar", document.getElementById("product-description-ar").value);
    formData.append(
        "price",
        priceFromInput(document.getElementById("product-price").value)
    );
    formData.append("stock", document.getElementById("product-stock").value);
    formData.append("image", document.getElementById("product-image").files[0]);

    let url = API_PRODUCTS;
    let method = "POST";

    if (editState.type === "product") {
        url = `${API_PRODUCTS}/${editState.id}`;
        method = "POST"; // حسب API عندك
    }

    await fetch(url, {
        method,
        headers: {
            "Authorization": "Bearer " + token
        },
        body: formData
    });

    resetEdit();
    await loadProducts();
}


// =========================
// DELETE PRODUCT
// =========================
async function deleteProduct(id) {
    await fetch(`${API_PRODUCTS}/${id}`, {
        method: "DELETE",
        headers: { "Authorization": "Bearer " + token }
    });

    await loadProducts();
}


// =========================
// PRODUCT CATEGORIES SELECT
// =========================
async function loadProductCategories() {
    let res = await fetch(API_CATEGORIES);
    let data = await res.json();

    let html = '<option value="">' + t('selectCategory') + '</option>';

    data.data.forEach(c => {
        html += `<option value="${c.id}">${c.name}</option>`;
    });

    document.getElementById("product-category").innerHTML = html;
}


// =========================
// CITIES
// =========================
async function loadCities() {
    let res = await fetch(API_CITIES);
    let data = await res.json();

    let html = "";

    sortByIdDesc(data.data).forEach(c => {
        html += `
        <tr>
            <td>${c.id}</td>
            <td>${c.name}</td>
            <td>
                <button onclick='startEdit("city", ${JSON.stringify(c)})'>${t('edit')}</button>
                <button onclick="deleteCity(${c.id})">${t('delete')}</button>
            </td>
        </tr>`;
    });

    document.getElementById("cities-table-body").innerHTML = html;
}

async function saveCity() {

    let name = document.getElementById("city-name").value;

    if (!name.trim()) {
        return alert(t('required'));
    }

    let url = API_CITIES;
    let method = "POST";

    if (editState.type === "city") {
        url = `${API_CITIES}/${editState.id}`;
        method = "PUT";
    }

    await fetch(url, {
        method,
        headers: {
            "Content-Type": "application/json",
            "Authorization": "Bearer " + token
        },
        body: JSON.stringify({
            name
        })
    });

    resetEdit();

    document.getElementById("city-name").value = "";

    await loadCities();
    await loadCitySelect();
    await loadAreas();
}

async function loadCitySelect() {
    let res = await fetch(API_CITIES);
    let data = await res.json();

    let html = '<option value="">' + t('selectCity') + '</option>';

    data.data.forEach(c => {
        html += `<option value="${c.id}">${c.name}</option>`;
    });

    document.getElementById("area-city").innerHTML = html;
}

async function deleteCity(id) {
    await fetch(`${API_CITIES}/${id}`, {
        method: "DELETE",
        headers: { "Authorization": "Bearer " + token }
    });

    await loadCities();
    await loadCitySelect();
    await loadAreas();
}


// =========================
// AREAS
// =========================
async function loadAreas() {
    let res = await fetch(API_AREAS);
    let data = await res.json();

    let html = "";

    sortByIdDesc(data.data).forEach(a => {
        html += `
        <tr>
            <td>${a.id}</td>
            <td>${a.name}</td>
            <td>${a.city?.name ?? '-'}</td>
            <td>${displayPrice(a.fee ?? 0)}</td>
            <td>
                <button onclick='startEdit("area", ${JSON.stringify(a).replace(/'/g, "&apos;")})'>
                    ${t('edit')}
                </button>
                <button onclick="deleteArea(${a.id})">${t('delete')}</button>
            </td>
        </tr>`;
    });

    document.getElementById("areas-table-body").innerHTML = html;
}

async function saveArea() {

    let cityId = document.getElementById("area-city").value;
    let name = document.getElementById("area-name").value;
    let fee = document.getElementById("area-fee").value;

    let url = API_AREAS;
    let method = "POST";

    if (editState.type === "area") {
        url = `${API_AREAS}/${editState.id}`;
        method = "PUT";
    }

    await fetch(url, {
        method,
        headers: {
            "Content-Type": "application/json",
            "Authorization": "Bearer " + token
        },
        body: JSON.stringify({
            city_id: cityId,
            name,
            fee: priceFromInput(fee)
        })
    });

    resetEdit();
    await loadAreas();
}

async function deleteArea(id) {
    await fetch(`${API_AREAS}/${id}`, {
        method: "DELETE",
        headers: { "Authorization": "Bearer " + token }
    });

    await loadAreas();
}


// =========================
// EDIT SYSTEM
// =========================
function startEdit(type, item) {

    editState = { type, id: item.id, data: item };

    closeAllModals();

    if (type === "category") {
        document.getElementById("edit-category-name").value = item.name;
        document.getElementById("categoryModal").style.display = "flex";
    }

    if (type === "brand") {
        document.getElementById("edit-brand-name").value = item.name;
        document.getElementById("brandModal").style.display = "flex";
    }

    if (type === "city") {
        document.getElementById("edit-city-name").value = item.name;
        document.getElementById("cityModal").style.display = "flex";
    }

    if (type === "area") {

    document.getElementById("edit-area-name").value = item.name;

    document.getElementById("edit-area-fee").value =
        priceToInput(item.fee ?? 0);

    loadCitiesForEdit().then(() => {
        document.getElementById("edit-area-city").value =
            item.city_id;
    });

    document.getElementById("areaModal").style.display = "flex";
    updateAdminCurrencyHints();
    }

    if (type === "product") {

        document.getElementById("edit-product-name").value = item.name;
        document.getElementById("edit-product-price").value = priceToInput(item.price);
        document.getElementById("edit-product-stock").value = item.stock;
        document.getElementById("edit-product-desc-en").value = item.description_en || "";
        document.getElementById("edit-product-desc-ar").value = item.description_ar || "";

        loadCategoriesForEdit().then(() => {
            document.getElementById("edit-product-category").value = item.category_id;
        });

        loadBrandsForEdit().then(() => {
            document.getElementById(
                "edit-product-brand"
            ).value = item.brand_id;
        });

        document.getElementById("productModal").style.display = "flex";
        updateAdminCurrencyHints();
    }
}

function closeAllModals() {
    document.getElementById("categoryModal").style.display = "none";
    document.getElementById("brandModal").style.display = "none";
    document.getElementById("cityModal").style.display = "none";
    document.getElementById("areaModal").style.display = "none";
    document.getElementById("productModal").style.display = "none";
    closeOrderDetailsModal();
    closeLogsModal();
}



async function loadCategoriesForEdit() {
    let res = await fetch(API_CATEGORIES);
    let data = await res.json();

    let html = "";

    data.data.forEach(c => {
        html += `<option value="${c.id}">${c.name}</option>`;
    });

    document.getElementById("edit-product-category").innerHTML = html;
}


async function loadBrandsForEdit() {

    let res = await fetch(API_BRANDS);

    let data = await res.json();

    let html = "";

    data.data.forEach(b => {

        html += `
            <option value="${b.id}">
                ${b.name}
            </option>
        `;
    });

    document.getElementById(
        "edit-product-brand"
    ).innerHTML = html;
}




async function loadCitiesForEdit() {
    let res = await fetch(API_CITIES);
    let data = await res.json();

    let html = "";

    data.data.forEach(c => {
        html += `<option value="${c.id}">${c.name}</option>`;
    });

    document.getElementById("edit-area-city").innerHTML = html;
}


// =========================
// SAVE EDIT
// =========================
async function saveEdit() {

    let body = {};
    let url = "";

    // CATEGORY
    if (editState.type === "category") {

        body = {
            name: document.getElementById("edit-category-name").value
        };

        url = `${API_CATEGORIES}/${editState.id}`;
    }

    // BRAND
    if (editState.type === "brand") {

        body = {
            name: document.getElementById("edit-brand-name").value,
        };

        url = `${API_BRANDS}/${editState.id}`;
    }

    // CITY
    if (editState.type === "city") {

        body = {
            name: document.getElementById("edit-city-name").value
        };

        url = `${API_CITIES}/${editState.id}`;
    }

    // AREA
    if (editState.type === "area") {

        body = {
            name: document.getElementById("edit-area-name").value,
            city_id: document.getElementById("edit-area-city").value,
            fee: priceFromInput(document.getElementById("edit-area-fee").value)
        };

        url = `${API_AREAS}/${editState.id}`;
    }

    // PRODUCT
    if (editState.type === "product") {

        body = {
            name: document.getElementById("edit-product-name").value,
            price: priceFromInput(document.getElementById("edit-product-price").value),
            stock: document.getElementById("edit-product-stock").value,
            description_en: document.getElementById("edit-product-desc-en").value,
            description_ar: document.getElementById("edit-product-desc-ar").value,
            category_id: document.getElementById("edit-product-category").value,
            brand_id: document.getElementById("edit-product-brand").value
        };

        url = `${API_PRODUCTS}/${editState.id}`;
    }

    let res = await fetch(url, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
            "Authorization": "Bearer " + token
        },
        body: JSON.stringify(body)
    });

    if (!res.ok) {
        console.error(await res.text());
        return;
    }

    closeAllModals();
    resetEdit();

    await loadCategories();
    await loadBrands();
    await loadProductBrands();
    await loadCities();
    await loadAreas();
    await loadProducts();
}


// =========================
// RESET
// =========================
function resetEdit() {
    editState = { type: null, id: null, data: null };

    document.getElementById("category-name").value = "";
    document.getElementById("brand-name").value = "";
    document.getElementById("city-name").value = "";
    document.getElementById("area-name").value = "";
}


// =========================
// CITY → AREAS
// =========================
document.addEventListener("change", async (e) => {

    if (e.target.id !== "city-select") return;

    let res = await fetch(`/api/cities/${e.target.value}/areas`);
    let data = await res.json();

    let html = '<option value="">' + t('selectArea') + '</option>';

    data.data.forEach(a => {
        html += `<option value="${a.id}">${a.name}</option>`;
    });

    document.getElementById("area-select").innerHTML = html;
}); 









// =========================
// PROFILE
// =========================
function goProfile() {
    window.location.href = "/profile";
}


// =========================
// LOGOUT
// =========================
async function confirmLogout() {

    const ok = await ElectroDialog.confirm(
        t('logoutConfirm'),
        { title: t('logout'), confirmText: t('logout') }
    );

    if (!ok) return;

    try {

        await fetch("/api/logout", {
            method: "POST",
            headers: {
                "Authorization": "Bearer " + token,
                "Content-Type": "application/json"
            }
        });

    } catch (err) {
        console.error(err);
    }

    localStorage.removeItem("auth_token");
    localStorage.removeItem("activeTab");

    window.location.href = "/login";
}



let allOrders = [];

async function loadOrders() {

    const token = localStorage.getItem("auth_token");

    const res = await fetch("/api/orders", {
        headers: {
            "Authorization": "Bearer " + token,
            "Accept": "application/json"
        }
    });

    const data = await res.json();

    allOrders = sortByIdDesc(data.data ?? []);

    renderOrders();
}



function renderOrders() {

    const tbody =
        document.getElementById(
            "orders-table-body"
        );

    tbody.innerHTML = "";

    allOrders.forEach(order => {

        tbody.innerHTML += `
            <tr class="order-row-clickable" onclick="showOrderDetails(${order.id})">

                <td>#${order.id}</td>

                <td>
                    ${order.user?.name ?? '-'}
                </td>

                <td>
                    ${order.area?.city?.name ?? '-'}
                </td>

                <td>
                    ${order.area?.name ?? '-'}
                </td>

                <td>
                    ${displayPrice(order.total_price)}
                </td>

                <td>
                    <span class="label label-info">
                        ${t(order.status) || order.status}
                    </span>
                </td>

                <td>
                    ${formatDate(order.created_at)}
                </td>

                <td>
                    ${formatDate(order.updated_at)}
                </td>

                <td onclick="event.stopPropagation()">

                    

                    <br><br>

                    <select
                        id="status-${order.id}"
                        class="form-control"
                    >
                        ${orderStatusOptions(order.status)}
                    </select>

                    <br>

                    <button
                        class="btn btn-primary btn-sm"
                        onclick="updateOrderStatus(${order.id})"
                    >
                        ${t('update')}
                    </button>


                    <button
                        class="btn btn-info btn-sm"
                        onclick="showOrderDetails(${order.id})"
                    >
                        ${t('view')}
                    </button>

                    <button
                        class="btn btn-default btn-sm"
                        onclick="showOrderLogs(${order.id})"
                    >
                        ${t('logs')}
                    </button>


                </td>

            </tr>
        `;

        setTimeout(() => {

            document.getElementById(
                `status-${order.id}`
            ).value = order.status;

        }, 0);
    });
}




async function updateOrderStatus(orderId) {

    const token =
        localStorage.getItem("auth_token");

    const status =
        document.getElementById(
            `status-${orderId}`
        ).value;

    const res = await fetch(
        `/api/orders/${orderId}/status`,
        {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "Authorization": "Bearer " + token,
                "Accept": "application/json"
            },
            body: JSON.stringify({
                status: status
            })
        }
    );

    const data = await res.json();

    if (!res.ok) {
        alert(data.message || t('updateFailed'));
        return;
    }

    alert(t('statusUpdated'));

    loadOrders();
}



async function showOrderDetails(orderId) {

    let order = allOrders.find(o => o.id == orderId);

    if (!order) {
        return;
    }

    if (!order.items || !order.items.length) {
        const token = localStorage.getItem("auth_token");

        try {
            const res = await fetch(`/api/orders/${orderId}`, {
                headers: {
                    "Authorization": "Bearer " + token,
                    "Accept": "application/json"
                }
            });

            const data = await res.json();

            if (res.ok && data.data) {
                order = data.data;
            }
        } catch (err) {
            console.error(err);
        }
    }

    renderOrderDetails(order);
}

function renderOrderDetails(order) {

    const items = order.items || [];
    let subtotal = 0;

    const itemsRows = items.map(item => {
        const lineTotal = item.quantity * parseFloat(item.price);
        subtotal += lineTotal;

        return `
            <tr>
                <td>${item.product?.name ?? t('product') + ' #' + item.product_id}</td>
                <td>${item.quantity}</td>
                <td>${displayPrice(item.price)}</td>
                <td>${displayPrice(lineTotal)}</td>
            </tr>
        `;
    }).join('');

    const shippingFee = order.area?.fee != null
        ? parseFloat(order.area.fee)
        : Math.max(0, parseFloat(order.total_price) - subtotal);

    const noteHtml = order.note
        ? '<p><strong>' + t('note') + ':</strong> ' + order.note + '</p>'
        : '<p><strong>' + t('note') + ':</strong> —</p>';

    document.getElementById("order-details-title").textContent =
        t('orderNumber', { id: order.id });

    document.getElementById("order-details-content").innerHTML = `
        <div class="order-details-meta">
            <p><strong>${t('status')}:</strong> ${t(order.status) || order.status}</p>
            <p><strong>${t('customer')}:</strong> ${order.user?.name ?? '-'}</p>
            <p><strong>${t('email')}:</strong> ${order.user?.email ?? '-'}</p>
            <p><strong>${t('phone')}:</strong> ${order.user?.phone ?? '-'}</p>
            <p><strong>${t('city')}:</strong> ${order.area?.city?.name ?? '-'}</p>
            <p><strong>${t('area')}:</strong> ${order.area?.name ?? '-'}</p>
            <p><strong>${t('createdAt')}:</strong> ${formatDate(order.created_at)}</p>
            <p><strong>${t('updatedAt')}:</strong> ${formatDate(order.updated_at)}</p>
        </div>

        ${noteHtml}

        <table class="order-details-table">
            <thead>
                <tr>
                    <th>${t('product')}</th>
                    <th>${t('qty')}</th>
                    <th>${t('unitPrice')}</th>
                    <th>${t('lineTotal')}</th>
                </tr>
            </thead>
            <tbody>
                ${itemsRows || '<tr><td colspan="4">' + t('noItems') + '</td></tr>'}
            </tbody>
        </table>

        <div class="order-details-totals">
            <p><strong>${t('subtotal')}:</strong> ${displayPrice(subtotal)}</p>
            <p><strong>${t('shipping')}:</strong> ${displayPrice(shippingFee)}</p>
            <p><strong>${t('total')}:</strong> ${displayPrice(order.total_price)}</p>
        </div>
    `;

    document.getElementById("orderDetailsModal").style.display = "flex";
}

function closeOrderDetailsModal() {

    document.getElementById("orderDetailsModal").style.display = "none";
}

function showOrderLogs(orderId) {

    const order =
        allOrders.find(o => o.id == orderId);

    if (!order) return;

    let html = "";

    (order.logs || []).forEach(log => {

        html += `

            <div style="
                border:1px solid #ddd;
                padding:10px;
                margin-bottom:10px;
                border-radius:8px;
            ">

                <p>
                    <strong>${t('admin')}:</strong>
                    ${log.admin?.name ?? '-'}
                </p>

                <p>
                    <strong>${t('action')}:</strong>
                    ${log.action}
                </p>

                <p>
                    <strong>${t('from')}:</strong>
                    ${t(log.old_status) || log.old_status}
                </p>

                <p>
                    <strong>${t('to')}:</strong>
                    ${t(log.new_status) || log.new_status}
                </p>

                <p>
                    <strong>${t('at')}:</strong>
                    ${formatDate(log.created_at)}
                </p>

            </div>
        `;
    });

    if (!html) {
        html = '<p>' + t('noLogs') + '</p>';
    }

    document.getElementById(
        "logs-content"
    ).innerHTML = html;

    closeOrderDetailsModal();

    document.getElementById(
        "logsModal"
    ).style.display = "flex";
}


function closeLogsModal() {

    document.getElementById(
        "logsModal"
    ).style.display = "none";
}



function formatDate(dateString) {

    const date = new Date(dateString);

    return date.toLocaleString();
}

