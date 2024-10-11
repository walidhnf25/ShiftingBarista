<!-- Sidebar -->
<aside class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('index') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-mug-hot"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Shifting Barista</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ Request::is('index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('index') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Nav Item - Tipe Pekerjaan -->
    <li class="nav-item {{ Request::is('tipepekerjaan') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('tipepekerjaan') }}">
            <i class="fas fa-fw fa-briefcase"></i>
            <span>Tipe Pekerjaan</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <li class="nav-item {{ Request::is('addpegawai') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('addpegawai') }}">
            <i class="fas fa-fw fa-users"></i>
            <span>Tambah Pegawai</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</aside>