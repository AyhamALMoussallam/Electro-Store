<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
@include('partials.electro-favicon')
<script src="/js/electro-preferences-boot.js"></script>
@include('partials.electro-exchange-rate')
<link rel="stylesheet" href="/css/electro-preferences.css"/>
<link rel="stylesheet" href="/css/admin-dashboard.css"/>
<title>Electro - Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/bootstrap.min.css"/>
<link rel="stylesheet" href="/css/font-awesome.min.css"/>
<link rel="stylesheet" href="/css/style.css"/>
<link rel="stylesheet" href="/css/rtl.css"/>
<link rel="stylesheet" href="/css/electro-dialog.css"/>

<style>
body {
    background: #f5f5f5;
    font-family: 'Montserrat', sans-serif;
}

.admin-wrapper {
    display: flex;
}

/* Sidebar — sticky positioning in admin-dashboard.css */
.sidebar {
    background: #15161D;
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

.products-search {
    max-width: 360px;
    margin-bottom: 15px;
}

.products-table-link {
    color: #D10024;
    font-weight: 500;
    text-decoration: none;
}

.products-table-link:hover {
    color: #a8001c;
    text-decoration: underline;
}

/* Tabs */
.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}


/* =========================
   MODAL BACKDROP
========================= */
.modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    overflow-y: auto;
    padding: 20px;
    box-sizing: border-box;

    justify-content: center;
    align-items: center;
}

/* =========================
   MODAL BOX
========================= */
.modal > div {
    background: #fff;
    width: 420px;
    max-width: 95%;
    max-height: calc(100vh - 40px);
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    padding: 20px;
    border-radius: 12px;
    margin: auto;

    box-shadow: 0 10px 30px rgba(0,0,0,0.3);

    animation: pop 0.2s ease;
}

/* =========================
   INPUTS
========================= */
.modal input,
.modal select,
.modal textarea {
    width: 100%;
    padding: 10px;
    margin-top: 10px;

    border: 1px solid #ddd;
    border-radius: 6px;

    font-size: 14px;
}

/* =========================
   BUTTONS (inside modal)
========================= */
.modal button {
    margin-top: 15px;
    padding: 8px 14px;
    border: none;
    border-radius: 6px;

    cursor: pointer;
}

.modal button:first-of-type {
    background: #D10024;
    color: white;
}

.modal button:last-of-type {
    background: #ccc;
}

/* =========================
   ANIMATION
========================= */
#orderDetailsModal > div {
    width: 760px;
    max-width: 95%;
}

#productModal > div {
    width: 480px;
}

.order-details-meta {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px 20px;
    margin-bottom: 20px;
    font-size: 14px;
}

.order-details-meta p {
    margin: 0;
}

.order-details-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 16px;
    font-size: 14px;
}

.order-details-table th,
.order-details-table td {
    border: 1px solid #ddd;
    padding: 8px 10px;
    text-align: left;
}

.order-details-table th {
    background: #f5f5f5;
}

.order-details-totals {
    text-align: right;
    font-size: 14px;
}

.order-details-totals p {
    margin: 4px 0;
}

.order-row-clickable {
    cursor: pointer;
}

.order-row-clickable:hover {
    background-color: #f9f9f9;
}

@keyframes pop {
    from {
        transform: scale(0.9);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}
</style>
</head>

<body class="admin-dashboard">

<div class="admin-wrapper">

    <!-- SIDEBAR -->
    <div class="sidebar">
    <h3 data-i18n="adminPanel">الإدارة</h3>

    <div class="sidebar-prefs">
        <label data-i18n="langLabel"></label>
        <select class="electro-lang-select" aria-label="Language">
            <option value="ar">العربية</option>
            <option value="en">English</option>
        </select>
        <label data-i18n="currencyLabel"></label>
        <select class="electro-currency-select" aria-label="Currency">
            <option value="sp">SP</option>
            <option value="usd">USD</option>
        </select>
        <label data-i18n="exchangeRateLabel">سعر الصرف</label>
        <div class="sidebar-exchange-rate">
            <span class="sidebar-exchange-prefix">1 USD =</span>
            <input type="number" id="usd-to-sp-rate" class="form-control" min="1" step="1" value="135">
            <span class="sidebar-exchange-suffix">SP</span>
        </div>
        <button type="button" class="btn btn-primary btn-sm sidebar-save-rate" onclick="saveExchangeRate()" data-i18n="saveExchangeRate">حفظ السعر</button>
    </div>

    <hr style="border-color:#333;">

    <a onclick="showTab('orders')" data-i18n="ordersNav">الطلبات</a>
    
    <hr style="border-color:#333;">

    <a onclick="showTab('categories')" data-i18n="categoriesNav">التصنيفات</a>
    <a onclick="showTab('brands')" data-i18n="brandsNav">العلامات</a>
    <a onclick="showTab('products')" data-i18n="productsNav">المنتجات</a>
    <a onclick="showTab('cities')" data-i18n="citiesNav">المدن</a>
    <a onclick="showTab('areas')" data-i18n="areasNav">المناطق</a>

    <hr style="border-color:#333;">

    <a href="/home/">🏠 <span data-i18n="home"></span></a>
    <a onclick="goProfile()">👤 <span data-i18n="profile"></span></a>
    <a onclick="confirmLogout()">🚪 <span data-i18n="logout"></span></a>
    </div>

    <!-- CONTENT -->
    <div class="content">



        <!-- ORDERS -->
<div id="orders" class="tab-content">

    <div class="card-box">

        <h3 data-i18n="ordersManagement">إدارة الطلبات</h3>

        <table class="table table-striped">

            <thead>
                <tr>
                    <th data-i18n="id">الرقم</th>
                    <th data-i18n="user">المستخدم</th>
                    <th data-i18n="city">المدينة</th>
                    <th data-i18n="area">المنطقة</th>
                    <th data-i18n="total">الإجمالي</th>
                    <th data-i18n="status">الحالة</th>
                    <th data-i18n="createdAt">تاريخ الإنشاء</th>
                    <th data-i18n="updatedAt">تاريخ التحديث</th>
                    <th width="220" data-i18n="actions">إجراءات</th>
                </tr>
            </thead>

            <tbody id="orders-table-body">

            </tbody>

        </table>

    </div>

</div>

<!-- CATEGORIES -->
<div id="categories" class="tab-content">

    <!-- ADD CATEGORY -->
    <div class="card-box">

        <h3 data-i18n="categoriesTitle">التصنيفات</h3>
        <br>
        <h4 id="category-form-title" data-i18n="addCategory">إضافة تصنيف</h4>

        <input
            type="text"
            id="category-name"
            class="form-control"
            data-i18n-placeholder="categoryName"
            placeholder="اسم التصنيف"
        >

        <br>

        <button class="btn btn-primary" onclick="saveCategory()" data-i18n="save">حفظ</button>

        <button
            class="btn btn-secondary"
            id="cancel-edit-btn"
            style="display:none;"
            onclick="cancelEdit()"
            data-i18n="cancel"
        >إلغاء</button>
    </div>

    <!-- LIST -->
    <div class="card-box">
        <h4 data-i18n="categoriesList">قائمة التصنيفات</h4>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th data-i18n="id">الرقم</th>
                    <th data-i18n="name">الاسم</th>
                    <th width="180" data-i18n="actions">إجراءات</th>
                </tr>
            </thead>

            <tbody id="categories-table-body">

            </tbody>
        </table>
    </div>

</div>


        <!-- BRANDS -->
<div id="brands" class="tab-content">

    <div class="card-box">

        <h3 data-i18n="brandsTitle">العلامات</h3>
        <br>

        <input
            type="text"
            id="brand-name"
            class="form-control"
            data-i18n-placeholder="brandNameLabel"
            placeholder="اسم العلامة"
        >

        <br>

        <button class="btn btn-primary" onclick="saveBrand()" data-i18n="save">حفظ</button>

    </div>

    <div class="card-box">

        <h4 data-i18n="brandsList">قائمة العلامات</h4>

        <table class="table table-striped">

            <thead>
                <tr>
                    <th data-i18n="id">الرقم</th>
                    <th data-i18n="name">الاسم</th>
                    <th width="180" data-i18n="actions">إجراءات</th>
                </tr>
            </thead>

            <tbody id="brands-table-body"></tbody>

        </table>

    </div>

</div>
        <!-- PRODUCTS -->
<div id="products" class="tab-content">

    <!-- ADD / EDIT PRODUCT -->
    <div class="card-box">

                <h3 data-i18n="productsTitle">المنتجات</h3>
        <br>

        <h4 id="product-form-title" data-i18n="addProduct">إضافة منتج</h4>
        <p class="text-muted" style="font-size:12px;" data-i18n="storedAsUsd"></p>

        <select id="product-category" class="form-control">
            <option value="" data-i18n="selectCategory">اختر التصنيف</option>
        </select>

        <br>

        <select id="product-brand" class="form-control">
            <option value="" data-i18n="selectBrand">اختر العلامة</option>
        </select>

        <br>

        <input type="text" id="product-name" class="form-control" data-i18n-placeholder="productName" placeholder="اسم المنتج">

        <br>

        <label data-i18n="descriptionEn">الوصف (إنجليزي)</label>
        <textarea id="product-description-en" class="form-control" rows="5"></textarea>

        <br>

        <label data-i18n="descriptionAr">الوصف (عربي)</label>
        <textarea id="product-description-ar" class="form-control" rows="5" dir="rtl"></textarea>

        <br>

        <input type="number" id="product-price" class="form-control" step="0.01" placeholder="السعر (USD)">

        <br>

        <input type="number" id="product-stock" class="form-control" data-i18n-placeholder="stock" placeholder="المخزون">

        <br>

        <input type="file" id="product-image" class="form-control" accept="image/*">

        <br>

        <button class="btn btn-primary" onclick="saveProduct()" data-i18n="save">حفظ</button>

        <button class="btn btn-secondary" id="cancel-product-edit-btn" style="display:none;" onclick="cancelProductEdit()" data-i18n="cancel">إلغاء</button>

    </div>

    <!-- PRODUCTS TABLE -->
    <div class="card-box">

        <h4 data-i18n="productsList">قائمة المنتجات</h4>

        <input type="text" id="products-search" class="form-control products-search" data-i18n-placeholder="searchProducts" placeholder="بحث بالاسم، التصنيف، العلامة، أو الرقم...">

        <table class="table table-striped">

            <thead>
                <tr>
                    <th data-i18n="id">الرقم</th>
                    <th data-i18n="image">الصورة</th>
                    <th data-i18n="name">الاسم</th>
                    <th data-i18n="category">التصنيف</th>
                    <th data-i18n="brand">العلامة</th>
                    <th data-i18n="price">السعر</th>
                    <th data-i18n="stock">المخزون</th>
                    <th data-i18n="sales">المبيعات</th>
                    <th width="180" data-i18n="actions">إجراءات</th>
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

                <h3 data-i18n="citiesTitle">المدن</h3>
        <br>

        <h4 data-i18n="addCity">إضافة مدينة</h4>

        <input type="text" id="city-name" class="form-control" data-i18n-placeholder="cityName" placeholder="اسم المدينة">

        <br>

        <button class="btn btn-primary" onclick="saveCity()" data-i18n="save">حفظ</button>

    </div>

    <!-- LIST -->
    <div class="card-box">

        <h4 data-i18n="citiesList">قائمة المدن</h4>

        <table class="table table-striped">

            <thead>
                <tr>
                    <th data-i18n="id">الرقم</th>
                    <th data-i18n="name">الاسم</th>
                    <th width="150" data-i18n="actions">إجراءات</th>
                </tr>
            </thead>

            <tbody id="cities-table-body">

            </tbody>

        </table>

    </div>

</div>

        <!-- AREAS -->
<div id="areas" class="tab-content">

    <!-- ADD AREA -->
    <div class="card-box">

                <h3 data-i18n="areasTitle">المناطق</h3>
        <br>

        <h4 data-i18n="addArea">إضافة منطقة</h4>

        <select id="area-city" class="form-control">
            <option value="" data-i18n="selectCity">اختر المدينة</option>
        </select>

        <br>

        <input type="text" id="area-name" class="form-control" data-i18n-placeholder="areaName" placeholder="اسم المنطقة">

        <br>

        <input type="number" id="area-fee" class="form-control" step="0.01" placeholder="رسوم التوصيل (USD)">

        <br>

        <button class="btn btn-primary" onclick="saveArea()" data-i18n="save">حفظ</button>

    </div>

    <!-- AREAS LIST -->
    <div class="card-box">

        <h4 data-i18n="areasList">قائمة المناطق</h4>

        <table class="table table-striped">

            <thead>
                <tr>
                    <th data-i18n="id">الرقم</th>
                    <th data-i18n="area">المنطقة</th>
                    <th data-i18n="city">المدينة</th>
                    <th data-i18n="fee">الرسوم</th>
                    <th width="150" data-i18n="actions">إجراءات</th>
                </tr>
            </thead>

            <tbody id="areas-table-body">

            </tbody>

        </table>

    </div>
    

</div>



<div id="categoryModal" class="modal">
    <div>
        <h3 data-i18n="editCategory">تعديل التصنيف</h3>
        <label data-i18n="categoryName">اسم التصنيف</label>
        <input id="edit-category-name">
        <button onclick="saveEdit()" data-i18n="save">حفظ</button>
        <button onclick="closeAllModals()" data-i18n="cancel">إلغاء</button>
    </div>
</div>


<div id="brandModal" class="modal">
    <div>
        <h3 data-i18n="editBrand">تعديل العلامة</h3>
        <label data-i18n="brandNameLabel">اسم العلامة</label>
        <input id="edit-brand-name" class="form-control">
        <button onclick="saveEdit()" data-i18n="save">حفظ</button>
        <button onclick="closeAllModals()" data-i18n="cancel">إلغاء</button>
    </div>
</div>


<div id="productModal" class="modal">
    <div>
        <h3 data-i18n="editProduct">تعديل المنتج</h3>
        <label data-i18n="productName">اسم المنتج</label>
        <input id="edit-product-name">
        <label data-i18n="category">التصنيف</label>
        <select id="edit-product-category"></select>
        <label data-i18n="brand">العلامة</label>
        <select id="edit-product-brand"></select>
        <label data-i18n="price">السعر</label>
        <input id="edit-product-price" type="number" step="0.01">
        <label data-i18n="stock">المخزون</label>
        <input id="edit-product-stock" type="number">
        <label data-i18n="descriptionEn">الوصف (إنجليزي)</label>
        <textarea id="edit-product-desc-en" rows="5"></textarea>
        <label data-i18n="descriptionAr">الوصف (عربي)</label>
        <textarea id="edit-product-desc-ar" rows="5" dir="rtl"></textarea>
        <label data-i18n="image">الصورة</label>
        <input type="file" id="edit-product-image">
        <button onclick="saveEdit()" data-i18n="save">حفظ</button>
        <button onclick="closeAllModals()" data-i18n="cancel">إلغاء</button>
    </div>
</div>

<div id="cityModal" class="modal">
    <div>
        <h3 data-i18n="editCity">تعديل المدينة</h3>
        <label data-i18n="cityName">اسم المدينة</label>
        <input id="edit-city-name">
        <button onclick="saveEdit()" data-i18n="save">حفظ</button>
        <button onclick="closeAllModals()" data-i18n="cancel">إلغاء</button>
    </div>
</div>


<div id="areaModal" class="modal">
    <div>
        <h3 data-i18n="editArea">تعديل المنطقة</h3>
        <label data-i18n="areaName">اسم المنطقة</label>
        <input id="edit-area-name">
        <label data-i18n="city">المدينة</label>
        <select id="edit-area-city"></select>
        <label data-i18n="shippingFee">رسوم التوصيل</label>
        <input type="number" id="edit-area-fee" step="0.01">
        <button onclick="saveEdit()" data-i18n="save">حفظ</button>
        <button onclick="closeAllModals()" data-i18n="cancel">إلغاء</button>
    </div>
</div>



<div id="logsModal" class="modal">
    <div style="width:700px; max-height:80vh; overflow:auto;">
        <h3 data-i18n="orderLogs">سجل الطلب</h3>
        <div id="logs-content"></div>
        <button onclick="closeLogsModal()" data-i18n="close">إغلاق</button>
    </div>
</div>

<div id="orderDetailsModal" class="modal">
    <div>
        <h3 id="order-details-title" data-i18n="orderDetails">تفاصيل الطلب</h3>
        <div id="order-details-content"></div>
        <button type="button" onclick="closeOrderDetailsModal()" data-i18n="close">إغلاق</button>
    </div>
</div>



@include('partials.electro-dialog')
<script src="/js/electro-i18n.js"></script>
<script src="/js/electro-currency.js"></script>
<script src="/js/electro-preferences.js"></script>
<script src="/js/electro-dialog.js"></script>
<script src="/js/electro-logout.js"></script>
<script src="/js/dashboard.js"></script>
</body>
</html>