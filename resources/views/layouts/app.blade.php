<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: linear-gradient(to right, #0d6efd, #6610f2); /* Cool gradient */
        }

        .dropdown-item.text-danger:hover {
            background-color: rgba(228, 215, 248, 0.85);
            color: #ffffff;
        }

        .sidebar {
            min-height: 100vh;
            background-color: #212529; /* Dark Bootstrap gray */
        }

        .sidebar a {
            color: #fff;
            display: block;
            padding: 12px 16px;
            text-decoration: none;
            transition: background 0.3s;
        }

        .sidebar a:hover {
            background-color: #343a40;
        }

        footer {
            background-color: #212529;
            color: #fff;
            text-align: center;
            padding: 12px 0;
            margin-top: auto;
        }

        .dropdown-menu {
            background-color: #343a40;
        }

        .dropdown-menu a {
            color: #fff;
        }

        .dropdown-menu a:hover {
            background-color: #495057;
        }

        /* Custom DataTable Styles */
        table.dataTable {
            border-collapse: collapse !important;
            background-color: #f9fafd;
        }

        table.dataTable th,
        table.dataTable td {
            border: 1px solid #dee2e6;
            padding: 10px;
        }

        table.dataTable thead {
            background-color: #343a40;
            color: #fff;
        }

        table.dataTable tbody tr:hover {
            background-color: #e2f0ff;
            transition: background 0.3s;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            background: #e0e0e0;
            border-radius: 4px;
            padding: 6px 12px;
            margin: 2px;
            border: none;
            color: #333;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background-color: #0d6efd;
            color: #fff !important;
            font-weight: bold;
        }

    </style>
</head>
<body class="d-flex flex-column min-vh-100">
<div id="app">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="{{ route('contacts.index') }}">📇 Laravel CRM</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdown"
                               role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth

                    @guest
                        <li class="nav-item">
                            <a class="nav-link text-white" href="{{ route('login') }}">Login</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link text-white" href="{{ route('register') }}">Register</a>
                            </li>
                        @endif
                    @endguest
                </ul>
            </div>
        </div>
    </nav>


    <div class="container-fluid">
        <div class="row">
            @auth
                <!-- Sidebar -->
                <div class="col-md-3 col-lg-2 sidebar">
                    <h5 class="text-white p-3">📇 Menu</h5>
                    <a href="{{ route('contacts.index') }}">
                        <i class="bi bi-person-lines-fill me-1"></i> Contact List
                    </a>
                    <a href="{{ route('contacts.create') }}">➕ Create New Contact</a>
                </div>
            @endauth

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 py-4">
                @yield('content')
            </div>
        </div>
    </div>
    @auth
        <footer>
            <div class="container">
                <span>© {{ date('Y') }} Laravel CRM by Mahantesh-A-Policepatil. All rights reserved.</span>
            </div>
        </footer>
    @endauth

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    @stack('scripts')
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
</body>
</html>
