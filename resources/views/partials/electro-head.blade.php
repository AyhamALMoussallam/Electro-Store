<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $title ?? 'Electro' }}</title>
<link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700" rel="stylesheet">
<link type="text/css" rel="stylesheet" href="/css/bootstrap.min.css"/>
<link rel="stylesheet" href="/css/font-awesome.min.css">
<link type="text/css" rel="stylesheet" href="/css/style.css"/>
@if(!empty($accountPage))
<link type="text/css" rel="stylesheet" href="/css/account.css"/>
@endif
@stack('head-extra')
