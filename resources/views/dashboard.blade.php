<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Admin Dashboard</title>

<link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700" rel="stylesheet">
<link rel="stylesheet" href="/css/bootstrap.min.css"/>
<link rel="stylesheet" href="/css/font-awesome.min.css"/>
<link rel="stylesheet" href="/css/style.css"/>

<style>
body {
    background: #f5f5f5;
    font-family: 'Montserrat', sans-serif;
}

.admin-wrapper {
    display: flex;
}

/* Sidebar */
.sidebar {
    width: 220px;
    background: #15161D;
    min-height: 100vh;
    padding: 20px;
}

.sidebar h3 {
    color: #D10024;
    margin-bottom: 20px;
}

.sidebar a {
    display: block;
    color: white;
    padding: 10px;
    margin-bottom: 5px;
    border-radius: 5px;
    cursor: pointer;
    text-decoration: none;
}

.sidebar a:hover {
    background: #D10024;
}

/* Content */
.content {
    flex: 1;
    padding: 20px;
}

.card-box {
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 15px;
    box-shadow: 0 0 10px rgba(0,0,0,0.05);
}

.btn-primary {
    background: #D10024;
    border: none;
}

.btn-primary:hover {
    background: #a8001c;
}

/* Tabs */
.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}
</style>
</head>

<body>

<div class="admin-wrapper">

    <!-- SIDEBAR -->
    <div class="sidebar">
    <h3>ADMIN</h3>

    <a onclick="showTab('dashboard')">Dashboard</a>
    <a onclick="showTab('categories')">Categories</a>
    <a onclick="showTab('products')">Products</a>
    <a onclick="showTab('cities')">Cities</a>
    <a onclick="showTab('areas')">Areas</a>

    <hr style="border-color:#333;">

    <a onclick="goProfile()">👤 Profile</a>
    <a onclick="confirmLogout()">🚪 Logout</a>
    </div>

    <!-- CONTENT -->
    <div class="content">

        <!-- DASHBOARD -->
        <div id="dashboard" class="tab-content active">
            <div class="card-box">
                <h3>Welcome Admin 🔥</h3>
                <p>Manage your ecommerce system from here.</p>
            </div>
        </div>

<!-- CATEGORIES -->
<div id="categories" class="tab-content">

    <!-- ADD CATEGORY -->
    <div class="card-box">
        <h4 id="category-form-title">Add Category</h4>

        <input
            type="text"
            id="category-name"
            class="form-control"
            placeholder="Category name"
            
        >

        <br>

        <button class="btn btn-primary" onclick="addCategory()">
            Save
        </button>

        <button
            class="btn btn-secondary"
            id="cancel-edit-btn"
            style="display:none;"
            onclick="cancelEdit()"
        >
            Cancel
        </button>
    </div>

    <!-- LIST -->
    <div class="card-box">
        <h4>Categories List</h4>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th width="180">Actions</th>
                </tr>
            </thead>

            <tbody id="categories-table-body">

            </tbody>
        </table>
    </div>

</div>
        <!-- PRODUCTS -->
<div id="products" class="tab-content">

    <!-- ADD / EDIT PRODUCT -->
    <div class="card-box">

        <h4 id="product-form-title">
            Add Product
        </h4>

        <!-- CATEGORY -->
        <select
            id="product-category"
            class="form-control"
        >
            <option value="">
                Select Category
            </option>
        </select>

        <br>


        <!-- Product -->
        <!-- NAME -->
        <input
            type="text"
            id="product-name"
            class="form-control"
            placeholder="Product name"
        >

        <br>

        <!-- DESCRIPTION -->
        <textarea
            id="product-description"
            class="form-control"
            placeholder="Description"
        ></textarea>

        <br>

        <!-- PRICE -->
        <input
            type="number"
            id="product-price"
            class="form-control"
            placeholder="Price"
        >

        <br>

        <!-- STOCK -->
        <input
            type="number"
            id="product-stock"
            class="form-control"
            placeholder="Stock"
        >

        <br>

        <!-- IMAGE -->
        <input
            type="file"
            id="product-image"
            class="form-control"
            accept="image/*"
        >

        <br>

        <button
            class="btn btn-primary"
            onclick="addProduct()"
        >
            Save
        </button>

        <button
            class="btn btn-secondary"
            id="cancel-product-edit-btn"
            style="display:none;"
            onclick="cancelProductEdit()"
        >
            Cancel
        </button>

    </div>

    <!-- PRODUCTS TABLE -->
    <div class="card-box">

        <h4>Products List</h4>

        <table class="table table-striped">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th width="180">Actions</th>
                </tr>
            </thead>

            <tbody id="products-table-body">

            </tbody>

        </table>

    </div>

</div>

        <!-- CITIES -->
<div id="cities" class="tab-content">

    <!-- ADD CITY -->
    <div class="card-box">

        <h4>Add City</h4>

        <input
            type="text"
            id="city-name"
            class="form-control"
            placeholder="City name"
        >

        <br>

        <button
            class="btn btn-primary"
            onclick="addCity()"
        >
            Save
        </button>

    </div>

    <!-- LIST -->
    <div class="card-box">

        <h4>Cities List</h4>

        <table class="table table-striped">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>

            <tbody id="cities-table-body">

            </tbody>

        </table>

    </div>

</div>

        <!-- AREAS -->
        <div id="areas" class="tab-content">

            <div class="card-box">
                <h4>Add Area</h4>
                <input class="form-control" placeholder="Area name"><br>
                <button class="btn btn-primary">Save</button>
            </div>

            <div class="card-box">
                <h4>Areas List</h4>
                <p>Table here...</p>
            </div>

        </div>

    </div>
    

</div>
<script src="/js/dashboard.js"></script>
</body>
</html>