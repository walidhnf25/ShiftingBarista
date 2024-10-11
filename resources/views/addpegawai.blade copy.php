<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Shifting Barista</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

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
            <li class="nav-item">
                <a class="nav-link" href="{{ route('index') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Nav Item - Tipe Pekerjaan -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('tipepekerjaan') }}">
                    <i class="fas fa-fw fa-briefcase"></i>
                    <span>Tipe Pekerjaan</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('addpegawai') }}">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Tambah Pegawai</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Interface
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Components</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Custom Components:</h6>
                        <a class="collapse-item" href="{{ route('buttons') }}">Buttons</a>
                        <a class="collapse-item" href="cards.html">Cards</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Utilities</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Custom Utilities:</h6>
                        <a class="collapse-item" href="utilities-color.html">Colors</a>
                        <a class="collapse-item" href="utilities-border.html">Borders</a>
                        <a class="collapse-item" href="utilities-animation.html">Animations</a>
                        <a class="collapse-item" href="utilities-other.html">Other</a>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Addons
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
                    aria-expanded="true" aria-controls="collapsePages">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Pages</span>
                </a>
                <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Login Screens:</h6>
                        <a class="collapse-item" href="login.html">Login</a>
                        <a class="collapse-item" href="register.html">Register</a>
                        <a class="collapse-item" href="forgot-password.html">Forgot Password</a>
                        <div class="collapse-divider"></div>
                        <h6 class="collapse-header">Other Pages:</h6>
                        <a class="collapse-item" href="{{ route('404') }}">404 Page</a>
                        <a class="collapse-item" href="{{ route('blank') }}">Blank Page</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Charts -->
            <li class="nav-item">
                <a class="nav-link" href="charts.html">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Charts</span></a>
            </li>

            <!-- Nav Item - Tables -->
            <li class="nav-item">
                <a class="nav-link" href="tables.html">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Tables</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <div class="topbar-divider d-none d-sm-block"></div>
                                <img class="img-profile rounded-circle mr-3"
                                    src="img/undraw_profile.svg">
                                <div class="small text-muted">
                                    {{Auth::guard('user')->user()->name}}<br>
                                    {{ Auth::guard('user')->user()->role->nama }}
                                </div>
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Activity Log
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Tambah Pegawai</h1>
                    </div>

                    @if (session('success'))
                    <div class="alert alert-success col-md-6 col-lg-3" role="alert" >
                        {{ session('success') }}
                    </div>
                    @endif

        
                    

                    <!-- Content Row -->
                    <div class="row">
                        <div class="col-lg-12">
                            <form action="{{ route('addpegawai.store') }}" method="POST">
                                @csrf
                                <div class="form-row">
                                    <!-- Name -->
                                    <div class="form-group col-md-6">
                                        <label for="name">Nama Pegawai</label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan nama pegawai">
                                    </div>
                    
                                    <!-- Email -->
                                    <div class="form-group col-md-6">
                                        <label for="email">Email Pegawai</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email pegawai" required>
                                    </div>
                                </div>
                    
                                <div class="form-row">
                                    <!-- Password -->
                                    <div class="form-group col-md-6">
                                        <label for="password">Password Pegawai</label>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
                                    </div>
                    
                                    <!-- Role -->
                                    <div class="form-group col-md-6">
                                        <label for="id_role">Role Pegawai</label>
                                        <select class="form-control" id="id_role" name="id_role" required>
                                            <option value="" disabled selected>Pilih Role</option>
                                            @foreach($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                    
                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-primary mt-3">Tambah Pegawai</button>
                            </form>
                        </div>
                    </div>

                    @if ($errors->any())
                    <div class="alert alert-danger mt-3" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    


                    <!-- Content Row -->
                    

                    <h2 class="h4 mt-5">Data Pegawai</h2>
                    
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nama</th>
                                <th scope="col">Email</th>
                                <th scope="col">Role</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->role->nama}}</td>
                                <td>
                                    <!-- Tombol untuk Read, Update, Delete -->
                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#ReadModal">
                                        Read
                                      </button>
                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#EditModal">
                                        Edit
                                    </button>
                                    <form action="{{ route('addpegawai.destroy', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">Delete</button>
                                    </form>                                    
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Read Modal -->
                @foreach($users as $user)
                <div class="modal fade" id="ReadModal" tabindex="-1" aria-labelledby="ReadModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Detail Pegawai <strong>{{$user->name}}</strong></h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Nama :</strong> {{$user->name}} </p>
                                <p><strong>Email :</strong> {{$user->email}} </p>
                                <p><strong>Role :</strong> Manager</span></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                
                <!-- Edit Modal -->
                @foreach($users as $user)
                <div class="modal fade" id="EditModal" tabindex="-1" aria-labelledby="EditModal" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Edit Modal Detail</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                           <form action=" {{ route('addpegawai.update', $user->id) }}}}" method="POST">
                            @csrf
                            @method('PUT') 
                            <div class="form-row">
                                <!-- Name -->
                                <div class="form-group col-md-6">
                                    <label for="name">Nama Pegawai</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{$user->name}}">
                                </div>
                
                                <!-- Email -->
                                <div class="form-group col-md-6">
                                    <label for="email">Email Pegawai</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{$user->email}}">
                                </div>
                            </div>
                
                            <div class="form-row">
                               
                
                                <!-- Role -->
                                <div class="form-group col-12">
                                    <label for="id_role">Role Pegawai</label>
                                    <select class="form-control" id="id_role" name="id_role" required>
                                        <option value="" disabled selected>Pilih Role</option>
                                        @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>
                           </form>
                        </div>
                       
                        </div>
                    </div>
                    </div>
                @endforeach

                
            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Your Website 2021</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="login.html">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>

</body>

</html>