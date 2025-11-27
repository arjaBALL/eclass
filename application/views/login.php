<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eClassRecord Login</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="icon" type="image/png" href="<?= base_url('public/logo.png') ?>">
    <script>
    const BASE_URL = "<?= base_url() ?>";
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="module" src="<?= base_url('/public/js/login.js') ?>"></script>
    <link rel="stylesheet" href="<?= base_url('/public/css/main.css') ?>">
</head>

<body class="bg-light d-flex justify-content-center align-items-center" style="min-height: 100vh;">

    <div class="container d-flex justify-content-center">
        <div class="row login-wrapper w-100">

            <!-- Left Column -->
            <div class="col-md-6 left-box d-flex flex-column justify-content-center text-center">
                <div class="d-flex justify-content-center">
                    <div class="rounded-circle bg-white d-flex justify-content-center align-items-center"
                        style="width:120px; height:120px;">
                        <img src="<?= base_url('public/logo.png') ?>" alt="Logo" style="width:100px; height:100px;">
                    </div>
                </div>

                <h1>Welcome to eClassRecord</h1>
                <p class="mt-3">
                    Manage your class schedules, student information, grades, and more.
                    Please log in with your teacher account.
                </p>
            </div>

            <!-- Right Column -->
            <div class="col-md-6 p-5">
                <h3 class="text-center mb-4">Login</h3>

                <form id="loginForm">
                    <div class="mb-3">
                        <label class="form-label"><i class="fa-solid fa-user"></i> Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Enter username" required>
                    </div>

                    <!-- Password with eye icon -->
                    <div class="mb-3">
                        <label class="form-label"><i class="fa-solid fa-user-lock"></i> Password</label>
                        <div class="input-group">
                            <input type="password" id="password" name="password" class="form-control"
                                placeholder="Enter password" required>
                            <span class="input-group-text" style="cursor: pointer;">
                                <i id="togglePassword" class="fa-solid fa-eye"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-2">
                        Login
                    </button>
                </form>
            </div>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>