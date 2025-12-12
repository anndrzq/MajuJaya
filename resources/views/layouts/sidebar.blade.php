<div id="removeNotificationModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    id="NotificationModalbtn-close"></button>
            </div>
            <div class="modal-body">
                <div class="mt-2 text-center">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                        colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                    <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                        <h4>Are you sure ?</h4>
                        <p class="text-muted mx-4 mb-0">Are you sure you want to remove this Notification ?</p>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                    <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn w-sm btn-danger" id="delete-notification">Yes, Delete It!</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="app-menu navbar-menu">
    <div class="navbar-brand-box">
        <a href="{{ url('index') }}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset('assets/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('assets/images/logo-dark.png') }}" alt="" height="17">
            </span>
        </a>

        <a href="{{ url('index') }}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ asset('assets/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('assets/images/logo-light.png') }}" alt="" height="17">
            </span>
        </a>

        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu">Menu</span></li>
                <li class="nav-item">
                    <a class="nav-link menu-link {{ Request::is('Dashboard.index') ? 'active' : '' }}"
                        href="{{ route('Dashboard.index') }}">
                        <i class="ri-dashboard-2-line"></i> <span data-key="t-widgets">Dashboard</span>
                    </a>
                </li>

                <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-pages">Data Master</span>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link {{ Request::is('userData*') ? 'active' : '' }}"
                        href="{{ route('userData.index') }}">
                        <i class="ri-group-fill"></i> <span data-key="t-widgets"> Data Pengguna</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link {{ Request::is('products*') ? 'active' : '' }}"
                        href="{{ route('products.index') }}">
                        <i class="ri-stack-line"></i> <span data-key="t-widgets">Data Produk</span>
                    </a>
                </li>

                <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-components">Transaksi</span></li>
                <li class="nav-item">
                    <a class="nav-link menu-link {{ Request::is('sale*') ? 'active' : '' }}"
                        href="{{ route('sale.index') }}">
                        <i class="ri-shopping-cart-2-line"></i> <span data-key="t-layouts">Penjualan Kasir</span>
                    </a>
                </li>

                {{-- <li class="nav-item">
                    <a class="nav-link menu-link {{ Request::is('transaction-history*') ? 'active' : '' }}"
                        href="#">
                        <i class="ri-history-line"></i> <span data-key="t-history">Riwayat Transaksi</span>
                    </a>
                </li> --}}
            </ul>
        </div>
    </div>
</div>
<div class="vertical-overlay"></div>
