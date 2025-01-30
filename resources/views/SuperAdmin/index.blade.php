<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Responsive Admin &amp; Dashboard Template based on Bootstrap 5">
	<meta name="author" content="AdminKit">
	<meta name="keywords" content="adminkit, bootstrap, bootstrap 5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">

	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link rel="shortcut icon" href="img/icons/icon-48x48.png" />

	<link rel="canonical" href="https://demo-basic.adminkit.io/" />

	<title>Super Admin</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet">

	<link href="{{asset('superadmin_asset/css/app.css')}}" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>
	<div class="wrapper">
		<nav id="sidebar" class="sidebar js-sidebar">
			@include('SuperAdmin.components.sidebar')
		</nav>

		<div class="main">
			<nav class="navbar navbar-expand navbar-light navbar-bg">
				@include('SuperAdmin.components.navbar')
			</nav>

			<main class="content">
				<div class="container-fluid p-0">
                    @yield('content')
				</div>
			</main>

			<footer class="footer">
				@include('SuperAdmin.components.footer')
			</footer>
		</div>
	</div>

	<script src="{{asset('superadmin_asset/js/app.js')}}"></script>

</body>

</html>