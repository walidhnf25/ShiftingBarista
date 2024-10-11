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
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>



<body class="">

    <div class="container min-vh-100 d-flex justify-content-center align-items-center">

    <!-- Outer Row -->
    <div class="col-md-9 col-lg-6">

        <div class="card o-hidden border-0 shadow shadow-blue my-5">

            <!-- Nested Row within Card Body -->
            <div class="col-lg-12">
                <div class="py-5 px-4">
                    <div class="text-center">
                        <h1 class="h4 text-gray-900 mb-4" id="typing-text"></h1>
                    </div>
                    <div class="text-center">
                        @php
                            $messagewarning = Session::get('warning');
                        @endphp
                        @if ($messagewarning)
                            <div class="alert alert-warning">
                                {{ $messagewarning }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                    </div>
                    <form class="user" action="{{ route('proseslogin') }}" method="POST">
                        @csrf <!-- Token CSRF untuk keamanan -->
                        <div class="form-group">
                            <input type="email" name="email" class="form-control"
                                id="exampleInputEmail" aria-describedby="emailHelp"
                                placeholder="Enter Email Address" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" class="form-control"
                                id="exampleInputPassword" placeholder="Password" required>
                        </div>
                        <div class="text-right">
                            <a class="small" href="forgot-password.html">Forgot Password?</a>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox small">
                                <input type="checkbox" class="custom-control-input" id="customCheck">
                                <label class="custom-control-label" for="customCheck">Remember Me</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                        <hr>
                        <a href="index.html" class="btn btn-google btn-block">
                            <i class="fab fa-google fa-fw"></i> Login with SSO
                        </a>
                    </form>

                    <hr>
                    <div class="text-center">
                        <a class="small" href="register.html">Create an Account!</a>
                    </div>
                </div>
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

    <script>
        const text = "Welcome to Shifting Barista!";
        let index = 0;
        const speed = 100;

        function typeEffect() {
            if (index < text.length) {
                document.getElementById("typing-text").innerHTML += text.charAt(index);
                index++;
                setTimeout(typeEffect, speed);
            }
        }

        // Memulai efek mengetik saat halaman dimuat
        window.onload = typeEffect;
</script>

</body>

</html>