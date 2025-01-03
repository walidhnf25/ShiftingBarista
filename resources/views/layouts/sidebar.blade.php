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

    <li class="sidebar-divider large text-center text-white mt-3 mb-3">
        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-letter-m-small"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 16v-8l3 5l3 -5v8" /></svg>
        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-letter-a-small"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 16v-6a2 2 0 1 1 4 0v6" /><path d="M10 13h4" /></svg>
        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-letter-n-small"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 16v-8l4 8v-8" /></svg>
        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-letter-a-small"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 16v-6a2 2 0 1 1 4 0v6" /><path d="M10 13h4" /></svg>
        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-letter-g-small"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 8h-2a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2v-4h-1" /></svg>
        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-letter-e-small"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 8h-4v8h4" /><path d="M10 12h2.5" /></svg>
        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-letter-r-small"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12h2a2 2 0 1 0 0 -4h-2v8m4 0l-3 -4" /></svg>
    </li>

    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ Request::is('managerdashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('managerdashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard Manager</span>
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

    @if (auth()->check() && auth()->user()->role === 'Manager')
    <hr class="sidebar-divider my-0">

    <li class="nav-item {{ Request::is('cekgajioutlet') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('cekgajioutlet') }}">
            <i class="fas fa-fw fa fa-university"></i>
            <span>Cek Gaji Karyawan</span>
        </a>
    </li>
    @endif

    @if (auth()->check() && auth()->user()->role === 'Manager')
    <hr class="sidebar-divider my-0">

    <li class="sidebar-divider large text-center text-white mt-3 mb-3">
        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-letter-s-small"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 15a1 1 0 0 0 1 1h2a1 1 0 0 0 1 -1v-2a1 1 0 0 0 -1 -1h-2a1 1 0 0 1 -1 -1v-2a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1" /></svg>
        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-letter-t-small"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 8h4" /><path d="M12 8v8" /></svg>
        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-letter-a-small"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 16v-6a2 2 0 1 1 4 0v6" /><path d="M10 13h4" /></svg>
        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-letter-f-small"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12h3" /><path d="M14 8h-4v8" /></svg>
        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-letter-f-small"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12h3" /><path d="M14 8h-4v8" /></svg>
    </li>

    <hr class="sidebar-divider my-0">

    <li class="nav-item {{ Request::is('staffdashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('staffdashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard Staff</span>
        </a>
    </li>
    @endif

    @if (auth()->check() && auth()->user()->role === 'Manager')
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

    <li class="nav-item {{ Request::is('waktushift') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('waktushift') }}">
            <i class="fas fa-fw fa fa-clock"></i>
            <span>Jadwal Shift</span>
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

    
    @if (auth()->check() && auth()->user()->role === 'Staff')
    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ Request::is('staffcekgaji') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('staffcekgaji') }}">
            <i class="fas fa-fw fa fa-university" aria-hidden="true"></i>
            <span>Cek Gaji</span>
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
