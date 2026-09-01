<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Billing System')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    {{-- ===== CUSTOM BILLING CSS ===== --}}
    <link href="{{ asset('css/billing.css') }}" rel="stylesheet">

    @stack('styles')

    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background: #2c3e50;
            color: white;
            padding: 0;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 2px 10px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: #34495e;
            color: white;
        }
        .sidebar .nav-link i {
            width: 25px;
        }
        .sidebar .brand {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #34495e;
        }
        .sidebar .brand h4 {
            color: #fff;
        }
        .content-area {
            padding: 20px;
            min-height: 100vh;
        }
        .card {
            border: none;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            border-radius: 12px;
        }
        .card-header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 15px 20px;
            border-radius: 12px 12px 0 0 !important;
        }
        .table thead {
            background: #f8f9fa;
        }
        .table thead th {
            border-bottom: none;
            font-weight: 600;
        }
        .badge-status {
            padding: 5px 12px;
            border-radius: 20px;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            .sidebar {
                display: none !important;
            }
            .content-area {
                margin-left: 0 !important;
                padding: 0 !important;
            }
            .card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-md-2 col-lg-2 sidebar no-print" style="position: sticky; top: 0; height: 100vh; overflow-y: auto;">
                <div class="brand">
                    <h4><i class="fas fa-file-invoice"></i> Billing</h4>
                    <small class="text-muted">v2.0</small>
                </div>
                <nav class="nav flex-column mt-3">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-chart-pie"></i> Dashboard
                    </a>
                    <a href="{{ route('bills.create') }}" class="nav-link {{ request()->routeIs('bills.create') ? 'active' : '' }}">
                        <i class="fas fa-plus-circle"></i> New Bill
                    </a>
                    <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> Customers
                    </a>
                    <a href="{{ route('bills.index') }}" class="nav-link {{ request()->routeIs('bills.index') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice"></i> All Bills
                    </a>
                    <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <i class="fas fa-boxes"></i> Inventory
                    </a>
                    <a href="{{ route('all-products.index') }}" class="nav-link {{ request()->routeIs('all-products.*') ? 'active' : '' }}">
                        <i class="fas fa-box"></i> Catalog
                    </a>
                    <a href="{{ route('settings.gst') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                    <a href="{{ route('catalog.index') }}" class="nav-link {{ request()->routeIs('catalog.*') ? 'active' : '' }}">
                        <i class="fas fa-book"></i> Product Catalog
                    </a>
                    <a href="{{ route('stickers.index') }}" class="nav-link {{ request()->routeIs('stickers.*') ? 'active' : '' }}">
                        <i class="fas fa-tags"></i> Stickers
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 col-lg-10">
                <div class="content-area">
                    <!-- Top Bar -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">@yield('page-title', 'Dashboard')</h4>
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-muted">{{ date('d M Y') }}</span>
                            <a href="#" class="btn btn-outline-secondary btn-sm no-print" onclick="window.print()">
                                <i class="fas fa-print"></i> Print
                            </a>
                        </div>
                    </div>

                    <!-- Alerts -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="fas fa-info-circle me-2"></i> {{ session('info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>

                <!-- Footer -->
                <div class="text-center text-muted py-3 no-print">
                    <small>&copy; {{ date('Y') }} Billing System. All rights reserved.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('js/billing-common.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize DataTables
            $('.datatable').DataTable({
                responsive: true,
                pageLength: 25,
                order: [[0, 'desc']]
            });

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });

        // Confirm delete
        function confirmDelete(url, message = 'Are you sure you want to delete this item?') {
            if (confirm(message)) {
                window.location.href = url;
            }
            return false;
        }
    </script>

    @stack('scripts')
</body>
</html>