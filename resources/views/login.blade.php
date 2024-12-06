<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Shifting Baristas</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>



<body class="">

    <div class=" min-vh-100 d-flex justify-content-center align-items-center position-relative  ">
        <div class="position-absolute w-100 h-50 bottom-0 " style="
            background-image: url('/img/lakesidefnb.png'); 
            background-size:contain; 
            background-position: center;
            background-repeat:no-repeat;
            bottom:0;
            ">a</div>

    <!-- Outer Row -->
    <div class="col-lg-3">

        <div class="card o-hidden border-1 my-5">

            <!-- Nested Row within Card Body -->
            <div class="col-lg-12 ">
                <div class="py-5 px-4">
                    <div class="text-center">
                        <h1 class="h4 text-gradient-primary mb-3" id="typing-text"></h1>
                    </div>
                    <div class="text-center text-gradient-primary">
                        <i class="fas fa-mug-hot fa-3x rotate-n-15 mb-3"></i>
                    </div>
                    <div class="text-center">
                        @php
                            $messagewarning = Session::get('warning');
                            $successwarning = Session::get('success');
                        @endphp
                        @if ($messagewarning)
                            <div class="alert alert-warning">
                                {{ $messagewarning }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if ($successwarning)
                        <div class="alert alert-warning">
                            {{ $successwarning }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif


                    </div>
                    <form class="user" action="{{ route('proseslogin') }}" method="POST">
                        @csrf <!-- Token CSRF untuk keamanan -->
                        <div class="form-group">
                            <label for="username" class="text-black">Username</label>
                            <input type="username" name="username" id="username" class="form-control"
                                id="exampleInputusername" aria-describedby="emailHelp"
                                placeholder="Masukan Username" required>
                        </div>
                        <div class="form-group position-relative">
                            <label for="password" class="text-black">Password</label>
                            <input type="password" name="password" id="password" class="form-control"
                                id="exampleInputPassword" placeholder="Password" required>
                                <button type="button" onclick="togglePassword()" style="position: absolute; right: 10px; top: 75%; transform: translateY(-50%); border: none; background: none;">
                                    <i id="toggleIcon" class="fas fa-eye"></i>
                                </button>
                        </div>
                        <button type="submit" class="btn bg-gradient-primary text-white  btn-block mt-4">Masuk</button>
                        <hr>
                        <a href="{{ route('registersso')}}" class="btn btn-google btn-block ">
                            Daftar melalui SSO
                        </a>
                    </form>

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

        function togglePassword() {
        const passwordInput = document.getElementById("password");
        const toggleIcon = document.getElementById("toggleIcon");
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            toggleIcon.classList.remove("fa-eye");
            toggleIcon.classList.add("fa-eye-slash");
        } else {
            passwordInput.type = "password";
            toggleIcon.classList.remove("fa-eye-slash");
            toggleIcon.classList.add("fa-eye");
        }
    }
    </script>

</body>

</html>
