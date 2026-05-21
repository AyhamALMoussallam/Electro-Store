const API_CATEGORIES = "/api/categories";
const API_PRODUCTS = "/api/products";





// =========================
// AUTH CHECK
// =========================

const token = localStorage.getItem("auth_token");

if (!token) {
    window.location.href = "/login";
}


// tabs
function showTab(tabId) {

    let tabs = document.querySelectorAll('.tab-content');

    tabs.forEach(t => {
        t.classList.remove('active');
        t.style.display = 'none';
    });

    let activeTab = document.getElementById(tabId);

    activeTab.style.display = 'block';
    activeTab.classList.add('active');
}


// profile
function goProfile() {
    window.location.href = "/profile";
}


// logout
async function confirmLogout() {

    let ok = confirm("Are you sure you want to logout?");

    if (!ok) return;

    try {

        await fetch("/api/logout", {
            method: "POST",

            headers: {
                "Authorization": "Bearer " + localStorage.getItem("auth_token"),
                "Content-Type": "application/json"
            }
        });

    } catch (e) {
        console.log(e);
    }

    localStorage.removeItem("auth_token");

    window.location.href = "/login";
}
// =========================
// CATEGORY
// =========================

loadCategories();


// LOAD CATEGORIES
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
                <button
                    class="btn btn-danger btn-sm"
                    onclick="deleteCategory(${category.id})">
                    Delete
                </button>
            </td>
        </tr>
        `;
    });

    document.getElementById("categories-table-body").innerHTML = html;}



// ADD CATEGORY
async function addCategory() {

    let name = document.getElementById("category-name").value;

    if(name.trim() == "") {
        alert("Category name required");
        return;
    }

    await fetch(API_CATEGORIES, {
        method: "POST",

        headers: {
            "Content-Type": "application/json",
            "Authorization": "Bearer " + localStorage.getItem("auth_token")
        },

        body: JSON.stringify({
            name: name
        })
    });

    document.getElementById("category-name").value = "";

    loadCategories();
}



// DELETE CATEGORY
async function deleteCategory(id) {

    let ok = confirm("Delete category?");

    if(!ok) return;

    await fetch(API_CATEGORIES + "/" + id, {

        method: "DELETE",

        headers: {
            "Authorization": "Bearer " + localStorage.getItem("auth_token")
        }
    });

    loadCategories();
}



// =========================
// PRODUCTS
// =========================

loadProducts();
loadProductCategories();



// LOAD CATEGORIES INTO SELECT
async function loadProductCategories() {

    let res = await fetch(API_CATEGORIES);
    let data = await res.json();

    let html = `
        <option value="">
            Select Category
        </option>
    `;

    data.data.forEach(category => {

        html += `
            <option value="${category.id}">
                ${category.name}
            </option>
        `;
    });

    document.getElementById("product-category").innerHTML = html;
}




// LOAD PRODUCTS
async function loadProducts() {

    let res = await fetch(API_PRODUCTS);
    let data = await res.json();

    let html = "";

    data.data.forEach(product => {

        html += `
        <tr>

            <td>${product.id}</td>

            <td>
                <img
                    src="/storage/${product.image}"
                    width="50">
            </td>

            <td>${product.name}</td>

            <td>${product.category?.name ?? '-'}</td>

            <td>${product.price}$</td>

            <td>${product.stock}</td>

            <td>
                <button
                    onclick="deleteProduct(${product.id})"
                    class="btn btn-danger btn-sm">
                    Delete
                </button>
            </td>

        </tr>
        `;
    });

    document.getElementById("products-table-body").innerHTML = html;
}




// ADD PRODUCT
async function addProduct() {

    let imageInput = document.getElementById("product-image");

    if(imageInput.files.length === 0) {
        alert("Choose image");
        return;
    }

    let formData = new FormData();

    formData.append(
        "category_id",
        document.getElementById("product-category").value
    );

    formData.append(
        "name",
        document.getElementById("product-name").value
    );

    formData.append(
        "description",
        document.getElementById("product-description").value
    );

    formData.append(
        "price",
        document.getElementById("product-price").value
    );

    formData.append(
        "stock",
        document.getElementById("product-stock").value
    );

    formData.append(
        "image",
        imageInput.files[0]
    );


    let res = await fetch(API_PRODUCTS, {

        method: "POST",

        headers: {
            "Authorization": "Bearer " + localStorage.getItem("auth_token")
        },

        body: formData
    });

    let data = await res.json();

    console.log(data);

    if(!res.ok) {
        alert(data.message || "Error");
        return;
    }

    alert("Product added");

    document.getElementById("product-name").value = "";
    document.getElementById("product-description").value = "";
    document.getElementById("product-price").value = "";
    document.getElementById("product-stock").value = "";
    document.getElementById("product-image").value = "";

    loadProducts();
}




// DELETE PRODUCT
async function deleteProduct(id) {

    let ok = confirm("Delete product?");

    if(!ok) return;

    await fetch(API_PRODUCTS + "/" + id, {

        method: "DELETE",

        headers: {
            "Authorization": "Bearer " + localStorage.getItem("auth_token")
        }
    });

    loadProducts();
}



// =========================
// CITIES
// =========================

const API_CITIES = "/api/cities";

loadCities();



// LOAD CITIES
async function loadCities() {

    let res = await fetch(API_CITIES);
    let data = await res.json();

    let html = "";

    data.data.forEach(city => {

        html += `
        <tr>

            <td>${city.id}</td>

            <td>${city.name}</td>

            <td>
                <button
                    onclick="deleteCity(${city.id})"
                    class="btn btn-danger btn-sm">
                    Delete
                </button>
            </td>

        </tr>
        `;
    });

    document.getElementById(
        "cities-table-body"
    ).innerHTML = html;
}




// ADD CITY
async function addCity() {

    let name = document
        .getElementById("city-name")
        .value;

    if(name.trim() == "") {
        alert("City name required");
        return;
    }

    let res = await fetch(API_CITIES, {

        method: "POST",

        headers: {
            "Content-Type": "application/json",
            "Authorization":
                "Bearer " +
                localStorage.getItem("auth_token")
        },

        body: JSON.stringify({
            name: name
        })
    });

    let data = await res.json();

    if(!res.ok) {
        alert(data.message || "Error");
        return;
    }

    document.getElementById(
        "city-name"
    ).value = "";

    loadCities();
}




// DELETE CITY
async function deleteCity(id) {

    let ok = confirm("Delete city?");

    if(!ok) return;

    await fetch(API_CITIES + "/" + id, {

        method: "DELETE",

        headers: {
            "Authorization":
                "Bearer " +
                localStorage.getItem("auth_token")
        }
    });

    loadCities();
}



// =========================
// AREAS
// =========================

const API_AREAS = "/api/areas";

loadAreas();
loadAreaCities();



// LOAD CITIES INTO SELECT
async function loadAreaCities() {

    let res = await fetch(API_CITIES);
    let data = await res.json();

    let html = `
        <option value="">
            Select City
        </option>
    `;

    data.data.forEach(city => {

        html += `
            <option value="${city.id}">
                ${city.name}
            </option>
        `;
    });

    document.getElementById(
        "area-city"
    ).innerHTML = html;
}




// LOAD AREAS
async function loadAreas() {

    let res = await fetch(API_AREAS);
    let data = await res.json();

    let html = "";

    data.data.forEach(area => {

        html += `
        <tr>

            <td>${area.id}</td>

            <td>${area.name}</td>

            <td>${area.city?.name ?? '-'}</td>

            <td>
                <button
                    onclick="deleteArea(${area.id})"
                    class="btn btn-danger btn-sm">
                    Delete
                </button>
            </td>

        </tr>
        `;
    });

    document.getElementById(
        "areas-table-body"
    ).innerHTML = html;
}




// ADD AREA
async function addArea() {

    let cityId = document
        .getElementById("area-city")
        .value;

    let name = document
        .getElementById("area-name")
        .value;

    if(cityId == "") {
        alert("Select city");
        return;
    }

    if(name.trim() == "") {
        alert("Area name required");
        return;
    }

    let res = await fetch(API_AREAS, {

        method: "POST",

        headers: {
            "Content-Type": "application/json",
            "Authorization":
                "Bearer " +
                localStorage.getItem("auth_token")
        },

        body: JSON.stringify({
            city_id: cityId,
            name: name
        })
    });

    let data = await res.json();

    if(!res.ok) {
        alert(data.message || "Error");
        return;
    }

    document.getElementById(
        "area-name"
    ).value = "";

    loadAreas();
}




// DELETE AREA
async function deleteArea(id) {

    let ok = confirm("Delete area?");

    if(!ok) return;

    await fetch(API_AREAS + "/" + id, {

        method: "DELETE",

        headers: {
            "Authorization":
                "Bearer " +
                localStorage.getItem("auth_token")
        }
    });

    loadAreas();
}