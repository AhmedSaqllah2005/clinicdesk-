<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="index.php?page=<?= Auth::role() === 'doctor' ? 'doctor_dashboard' : 'dashboard' ?>" class="nav-link">Home</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="#" class="nav-link"><?= date('l, d M Y') ?></a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-user-circle"></i>
                <?= sanitize(Auth::currentUser()['name'] ?? 'User') ?>
                <span class="badge badge-primary ml-1"><?= ucfirst(Auth::role()) ?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="#" class="dropdown-item text-danger"
                    onclick="event.preventDefault(); event.stopPropagation(); if(confirm('Are you sure you want to logout?')){ document.getElementById('navbar-logout-form').submit(); }">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
            </div>

            <!-- [FIX-1] إضافة csrf_token للـ logout form — كان ناقصاً فكانت عملية الـ logout تفشل -->
            <form id="navbar-logout-form" action="index.php?page=logout" method="POST" style="display:none;">
                <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
            </form>
        </li>
    </ul>
</nav>
<!-- /.navbar -->
