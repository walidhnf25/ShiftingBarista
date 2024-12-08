<!-- Sidebar -->
<aside class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    @if (auth()->check() && auth()->user()->role === 'Staff')
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('staffdashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-mug-hot"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Shifting Barista</div>
    </a>
    @endif

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    @if (auth()->check() && auth()->user()->role === 'Staff')
    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ Request::is('staffdashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('staffdashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>
    @endif

    <!-- Sidebar - Brand -->
    @if (auth()->check() && auth()->user()->role === 'Manager')
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('managerdashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-mug-hot"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Shifting Barista</div>
    </a>
    @endif

    @if (auth()->check() && auth()->user()->role === 'Manager')

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ Request::is('managerdashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('managerdashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>
    @endif

    @if (auth()->check() && auth()->user()->role === 'Manager')
    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Tipe Pekerjaan -->
    <li class="nav-item {{ Request::is('tipepekerjaan') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('tipepekerjaan') }}">
            <i class="fas fa-fw fa-briefcase"></i>
            <span>Tipe Pekerjaan</span>
        </a>
    </li>
    @endif

    @if (auth()->check() && auth()->user()->role === 'Manager')
    <hr class="sidebar-divider my-0">

    <li class="nav-item {{ Request::is('addpegawai') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('addpegawai') }}">
            <i class="fa fa-fw fa-users"></i>
            <span>Tambah Pegawai</span>
        </a>
    </li>
    @endif

    @if (auth()->check() && auth()->user()->role === 'Manager')
    <hr class="sidebar-divider my-0">

    <li class="nav-item {{ Request::is('jamshift') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('jamshift') }}">
            <i class="fas fa-fw fa-clock"></i>
            <span>Jam Shift</span>
        </a>
    </li>
    @endif

    @if (auth()->check() && auth()->user()->role === 'Manager')
    <hr class="sidebar-divider my-0">

    <li class="nav-item {{ Request::is('outlet') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('outlet') }}">
            <i class="fas fa-fw fa fa-calendar"></i>
            <span>Tambah Jadwal Shift</span>
        </a>
    </li>
    @endif

    @if (auth()->check() && auth()->user()->role === 'Staff')
    <hr class="sidebar-divider my-0">

    <li class="nav-item {{ Request::is('waktushift') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('waktushift') }}">
            <i class="fas fa-fw fa fa-clock"></i>
            <span>Jadwal Shift</span>
        </a>
    </li>
    @endif

    @if (auth()->check() && auth()->user()->role === 'Manager')
    <hr class="sidebar-divider my-0">

    <li class="nav-item {{ Request::is('requestshift') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('requestshift') }}">
            <i class="fas fa-fw fa fa-address-book"></i>
            <span>ACC Jadwal Shift</span>
        </a>
    </li>
    @endif

    @if (auth()->check() && auth()->user()->role === 'Manager')
    <hr class="sidebar-divider my-0">

    <li class="nav-item {{ Request::is('resetavail') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('resetavail') }}">
            <i class="fas fa-fw fa fa-undo"></i>
            <span>Reset Availability</span>
        </a>
    </li>
    @endif

    @if (auth()->check() && auth()->user()->role === 'Staff')
    <hr class="sidebar-divider my-0">

    <li class="nav-item {{ Request::is('applyshift') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('applyshift') }}">
            <i class="fas fa-fw fa fa-hand-pointer"></i>
            <span>Pilih Shift</span>
        </a>
    </li>
    @endif

    @if (auth()->check() && auth()->user()->role === 'Manager')
    <hr class="sidebar-divider my-0">

    <li class="nav-item {{ Request::is('tukarshift') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('tukarshift') }}">
            <i class="fas fa-fw fa fa-retweet"></i>
            <span>Tukar Shift</span>
        </a>
    </li>
    @endif

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</aside>
