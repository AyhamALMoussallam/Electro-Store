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

            <div class="card-box">
                <h4>Add Category</h4>
                <input class="form-control" placeholder="Category name">
                <br>
                <button class="btn btn-primary">Save</button>
            </div>

            <div class="card-box">
                <h4>Categories List</h4>

                <table class="table table-striped">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>

                    <tr>
                        <td>1</td>
                        <td>Laptops</td>
                        <td>
                            <button class="btn btn-warning btn-sm">Edit</button>
                            <button class="btn btn-danger btn-sm">Delete</button>
                        </td>
                    </tr>
                </table>
            </div>

        </div>

        <!-- PRODUCTS -->
        <div id="products" class="tab-content">

            <div class="card-box">
                <h4>Add Product</h4>
                <input class="form-control" placeholder="Product name"><br>
                <input class="form-control" placeholder="Price"><br>
                <button class="btn btn-primary">Save</button>
            </div>

            <div class="card-box">
                <h4>Products List</h4>
                <p>Products table here...</p>
            </div>

        </div>

        <!-- CITIES -->
        <div id="cities" class="tab-content">

            <div class="card-box">
                <h4>Add City</h4>
                <input class="form-control" placeholder="City name"><br>
                <button class="btn btn-primary">Save</button>
            </div>

            <div class="card-box">
                <h4>Cities List</h4>
                <p>Table here...</p>
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

<script>
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


function goProfile() {
    window.location.href = "/profile";
}

function confirmLogout() {
    let ok = confirm("Are you sure you want to logout?");

    if (ok) {
        fetch("/api/logout", {
            method: "POST",
            headers: {
                "Authorization": "Bearer " + localStorage.getItem("auth_token"),
                "Content-Type": "application/json"
            }
        }).finally(() => {
            localStorage.removeItem("auth_token");
            window.location.href = "/";
        });
    }
}
</script>

</body>
</html>