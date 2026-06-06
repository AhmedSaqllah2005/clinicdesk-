<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ClinicDesk | Login</title>
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1.0/dist/css/adminlte.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
        }
        .login-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .login-logo h1 {
            font-size: 2rem;
            font-weight: 600;
            margin: 0;
        }
    </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="card login-card">
        <div class="card-body login-card-body">
            <div class="login-logo text-center mb-4">
                <i class="fas fa-clinic-medical text-primary" style="font-size: 3rem;"></i>
                <h1 class="text-primary"><b>Clinic</b>Desk</h1>
                <p class="text-muted">Clinic Management System</p>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?= sanitize($_SESSION['error']) ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form action="index.php?page=login" method="POST">
                <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                
                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-sign-in-alt"></i> Sign In
                        </button>
                    </div>
                </div>
            </form>

            <hr>
            <div class="text-center text-muted small">
                <p class="mb-1"><strong>Demo Accounts:</strong></p>
                <p class="mb-0">📧 admin@clinic.com / <strong>Admin@1234</strong></p>
                <p class="mb-0">📧 doctor@clinic.com / <strong>Doctor@1234</strong></p>
                <p class="mb-0">📧 patient@clinic.com / <strong>Patient@1234</strong></p>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1.0/dist/js/adminlte.min.js"></script>
</body>
</html>