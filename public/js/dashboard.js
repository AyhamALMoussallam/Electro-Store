// =========================
// API ENDPOINTS
// =========================
const API_CATEGORIES = "/api/categories";
const API_PRODUCTS   = "/api/products";
const API_CITIES     = "/api/cities";
const API_AREAS      = "/api/areas";


// =========================
// AUTH CHECK
// =========================
const token = localStorage.getItem("auth_token");

if (!token) {
    window.location.href = "/login";
}


// =========================
// TAB SYSTEM (WITH PERSISTENCE)
// =========================
function showTab(tabId) {

    let tabs = document.querySelectorAll('.tab-content');

    tabs.forEach(t => {
        t.classList.remove('active');
        t.style.display = 'none';
    });

    let activeTab = document.getElementById(tabId);

    if (activeTab) {
        activeTab.style.display = 'block';
        activeTab.classList.add('active');
    }

    localStorage.setItem("activeTab", tabId);
}

window.addEventListener("load", function () {

    let savedTab = localStorage.getItem("activeTab");

    if (savedTab) {
        showTab(savedTab);
    } else {
        showTab("categories");
    }

});


// =========================
// LOGOUT
// =========================
async function confirmLogout() {

    if (!confirm("Are you sure?")) return;

    try {
        await fetch("/api/logout", {
            method: "POST",
            headers: {
                "Authorization": "Bearer " + token,
                "Content-Type": "application/json"
            }
        });
    } catch (e) {
        console.log(e);
    }

    localStorage.removeItem("auth_token");
    localStorage.removeItem("activeTab");

    window.location.href = "/login";
}


// =======================================================
// CATEGORIES
// =======================================================

loadCategories();

async function loadCategories() {

    let res = await fetch(API_CATEGORIES);
    let data = await res.json();

    let html = "";

    data.data.forEach(category => {

        html += `
        <tr>
            <td>${category.id}</td>
            <td>${category.name}</td>
            <td>
                <button class="btn btn-danger btn-sm"
                    onclick="deleteCategory(${category.id})">
                    Delete
                </button>
            </td>
        </tr>`;
    });

    document.getElementById("categories-table-body").innerHTML = html;
}


// ADD CATEGORY
async function addCategory() {

    let name = document.getElementById("category-name").value;

    if (!name.trim()) return alert("Category required");

    await fetch(API_CATEGORIES, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Authorization": "Bearer " + token
        },
        body: JSON.stringify({ name })
    });

    document.getElementById("category-name").value = "";

    loadCategories();
    loadProductCategories(); // 🔥 مهم
}


// DELETE CATEGORY
async function deleteCategory(id) {

    if (!confirm("Delete category?")) return;

    await fetch(API_CATEGORIES + "/" + id, {
        method: "DELETE",
        headers: { "Authorization": "Bearer " + token }
    });

    loadCategories();
    loadProductCategories();
}



// =======================================================
// PRODUCTS
// =======================================================

loadProducts();
loadProductCategories();

async function loadProductCategories() {

    let res = await fetch(API_CATEGORIES);
    let data = await res.json();

    let html = `<option value="">Select Category</option>`;

    data.data.forEach(c => {
        html += `<option value="${c.id}">${c.name}</option>`;
    });

    document.getElementById("product-category").innerHTML = html;
}


async function loadProducts() {

    let res = await fetch(API_PRODUCTS);
    let data = await res.json();

    let html = "";

    data.data.forEach(p => {

        html += `
        <tr>
            <td>${p.id}</td>
            <td><img src="/storage/${p.image}" width="50"></td>
            <td>${p.name}</td>
            <td>${p.category?.name ?? '-'}</td>
            <td>${p.price}$</td>
            <td>${p.stock}</td>
            <td>
                <button class="btn btn-danger btn-sm"
                    onclick="deleteProduct(${p.id})">
                    Delete
                </button>
            </td>
        </tr>`;
    });

    document.getElementById("products-table-body").innerHTML = html;
}


// =======================================================
// CITIES
// =======================================================

loadCities();
loadCitySelect();

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
                <button class="btn btn-danger btn-sm"
                    onclick="deleteCity(${c.id})">
                    Delete
                </button>
            </td>
        </tr>`;
    });

    document.getElementById("cities-table-body").innerHTML = html;
}


// dropdown (city → areas page)
async function loadCitySelect() {

    try {
        let res = await fetch(API_CITIES);
        let data = await res.json();

        console.log("CITIES SELECT:", data);

        let html = `<option value="">Select City</option>`;

        data.data.forEach(c => {
            html += `<option value="${c.id}">${c.name}</option>`;
        });

        const citySelect = document.getElementById("area-city");

        if (!citySelect) {
            console.error("area-city not found in DOM");
            return;
        }

        citySelect.innerHTML = html;

    } catch (err) {
        console.error("CITY SELECT ERROR:", err);
    }
}


// ADD CITY
async function addCity() {

    let name = document.getElementById("city-name").value;

    if (!name.trim()) return alert("City required");

    await fetch(API_CITIES, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Authorization": "Bearer " + token
        },
        body: JSON.stringify({ name })
    });

    document.getElementById("city-name").value = "";

    loadCities();
    loadCitySelect();
}



// =======================================================
// AREAS (DEPENDENT ON CITY)
// =======================================================

loadAreas();

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
                <button class="btn btn-danger btn-sm"
                    onclick="deleteArea(${a.id})">
                    Delete
                </button>
            </td>
        </tr>`;
    });

    document.getElementById("areas-table-body").innerHTML = html;
}


// CITY → AREAS dropdown (checkout / form)
document.addEventListener("DOMContentLoaded", function () {

    const citySelect = document.getElementById("area-city");
    const areaSelect = document.getElementById("area-select") 
        || document.getElementById("area-city"); // fallback

    if (!citySelect) {
        console.error("city select missing");
        return;
    }

    citySelect.addEventListener("change", async function () {

        let cityId = this.value;

        console.log("CITY ID:", cityId);

        if (!cityId) {
            areaSelect.innerHTML = `<option value="">Select Area</option>`;
            return;
        }

        let res = await fetch(`/api/cities/${cityId}/areas`);
        let data = await res.json();

        console.log("AREAS RAW:", data);

        let html = `<option value="">Select Area</option>`;

        data.forEach(a => {
            html += `<option value="${a.id}">${a.name}</option>`;
        });

        areaSelect.innerHTML = html;
    });

});



// ADD AREA
async function addArea() {

    let cityId = document.getElementById("area-city").value;
    let name = document.getElementById("area-name").value;

    if (!cityId) return alert("Select city");
    if (!name.trim()) return alert("Area required");

    await fetch(API_AREAS, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Authorization": "Bearer " + token
        },
        body: JSON.stringify({
            city_id: cityId,
            name
        })
    });

    document.getElementById("area-name").value = "";

    loadAreas();
}