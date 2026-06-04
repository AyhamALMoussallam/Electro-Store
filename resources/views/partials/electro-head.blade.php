<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
@include('partials.electro-favicon')
<script src="/js/electro-preferences-boot.js"></script>
@include('partials.electro-exchange-rate')
<link type="text/css" rel="stylesheet" href="/css/electro-preferences.css"/>
<title>{{ $title ?? 'Electro' }}</title>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
<link type="text/css" rel="stylesheet" href="/css/bootstrap.min.css"/>
<link type="text/css" rel="stylesheet" href="/css/rtl.css"/>
<link rel="stylesheet" href="/css/font-awesome.min.css">
<link type="text/css" rel="stylesheet" href="/css/style.css"/>
<link type="text/css" rel="stylesheet" href="/css/electro-dialog.css"/>
@if(!empty($accountPage))
<link type="text/css" rel="stylesheet" href="/css/account.css"/>
<style id="account-layout-critical">
	/* No flash: hide UI until layout/role is ready (see body classes on each page) */
	body.page-profile.account-ui-pending #header .header-ctn > .dropdown,
	body.admin-profile-page #header .header-ctn > .dropdown,
	body.minimal-header-page #header .header-ctn > .dropdown {
		display: none !important;
	}

	body.hide-main-nav-page #navigation {
		display: none !important;
	}

	body.minimal-header-page #navigation {
		display: none !important;
	}

	body.account-ui-pending .account-toolbar-left a[id^="nav-"]:not(.nav-visible) {
		visibility: hidden;
	}
</style>
@endif
@stack('head-extra')
