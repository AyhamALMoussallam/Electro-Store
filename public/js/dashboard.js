// =========================
// API
// =========================
const API_CATEGORIES = "/api/categories";
const API_PRODUCTS   = "/api/products";
const API_CITIES     = "/api/cities";
const API_AREAS      = "/api/areas";

const token = localStorage.getItem("auth_token");
if (!token) window.location.href = "/login";


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

window.addEventListener("load", () => {
    showTab(localStorage.getItem("activeTab") || "categories");

    boot();
});

function boot() {
    loadCategories();
    loadProducts();
    loadCities();
    loadAreas();
    loadProductCategories();
    loadCitySelect();
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

    data.data.forEach(c => {
        html += `
        <tr>
            <td>${c.id}</td>
            <td>${c.name}</td>
            <td>
                <button onclick='startEdit("category", ${JSON.stringify(c)})'>Edit</button>
                <button onclick="deleteCategory(${c.id})">Delete</button>
            </td>
        </tr>`;
    });

    document.getElementById("categories-table-body").innerHTML = html;
}

async function saveCategory() {
    let name = document.getElementById("category-name").value;
    if (!name.trim()) return alert("Required");

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
async function loadProducts() {

    let res = await fetch(API_PRODUCTS);
    let data = await res.json();

    let items = data.data ?? data;

    let html = "";

    items.forEach(p => {
        html += `
        <tr>
            <td>${p.id}</td>
            <td><img src="/storage/${p.image}" width="50"></td>
            <td>${p.name}</td>
            <td>${p.category?.name ?? '-'}</td>
            <td>${p.price}</td>
            <td>${p.stock}</td>
            <td>
                <button onclick='startEdit("product", ${JSON.stringify(p)})'>Edit</button>
                <button onclick="deleteProduct(${p.id})">Delete</button>
            </td>
        </tr>`;
    });

    document.getElementById("products-table-body").innerHTML = html;
}

async function addProduct() {

    let formData = new FormData();

    formData.append("category_id", document.getElementById("product-category").value);
    formData.append("name", document.getElementById("product-name").value);
    formData.append("description", document.getElementById("product-description").value);
    formData.append("price", document.getElementById("product-price").value);
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

    let html = `<option value="">Select Category</option>`;

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

    data.data.forEach(c => {
        html += `
        <tr>
            <td>${c.id}</td>
            <td>${c.name}</td>
            <td>
                <button onclick='startEdit("city", ${JSON.stringify(c)})'>Edit</button>
                <button onclick="deleteCity(${c.id})">Delete</button>
            </td>
        </tr>`;
    });

    document.getElementById("cities-table-body").innerHTML = html;
}

async function loadCitySelect() {
    let res = await fetch(API_CITIES);
    let data = await res.json();

    let html = `<option value="">Select City</option>`;

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

    data.data.forEach(a => {
        html += `
        <tr>
            <td>${a.id}</td>
            <td>${a.name}</td>
            <td>${a.city?.name ?? '-'}</td>
            <td>
                <button onclick='startEdit("area", ${JSON.stringify(a)})'>Edit</button>
                <button onclick="deleteArea(${a.id})">Delete</button>
            </td>
        </tr>`;
    });

    document.getElementById("areas-table-body").innerHTML = html;
}

async function addArea() {

    let cityId = document.getElementById("area-city").value;
    let name = document.getElementById("area-name").value;

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
            name
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

    if (type === "city") {
        document.getElementById("edit-city-name").value = item.name;
        document.getElementById("cityModal").style.display = "flex";
    }

    if (type === "area") {
        document.getElementById("edit-area-name").value = item.name;

        loadCitiesForEdit().then(() => {
            document.getElementById("edit-area-city").value = item.city_id;
        });

        document.getElementById("areaModal").style.display = "flex";
    }

    if (type === "product") {

        document.getElementById("edit-product-name").value = item.name;
        document.getElementById("edit-product-price").value = item.price;
        document.getElementById("edit-product-stock").value = item.stock;
        document.getElementById("edit-product-desc").value = item.description || "";

        loadCategoriesForEdit().then(() => {
            document.getElementById("edit-product-category").value = item.category_id;
        });

        document.getElementById("productModal").style.display = "flex";
    }
}

function closeAllModals() {
    document.getElementById("categoryModal").style.display = "none";
    document.getElementById("cityModal").style.display = "none";
    document.getElementById("areaModal").style.display = "none";
    document.getElementById("productModal").style.display = "none";
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
            city_id: document.getElementById("edit-area-city").value
        };

        url = `${API_AREAS}/${editState.id}`;
    }

    // PRODUCT
    if (editState.type === "product") {

        body = {
            name: document.getElementById("edit-product-name").value,
            price: document.getElementById("edit-product-price").value,
            stock: document.getElementById("edit-product-stock").value,
            description: document.getElementById("edit-product-desc").value,
            category_id: document.getElementById("edit-product-category").value
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

    await loadCategories();
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

    let html = `<option value="">Select Area</option>`;

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

    let ok = confirm("Are you sure you want to logout?");

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