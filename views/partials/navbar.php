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
            <a href="index.php?page=dashboard" class="nav-link">Home</a>
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
                <form action="index.php?page=logout" method="POST" style="display: inline;">
                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                    <button type="submit" class="dropdown-item text-danger">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </button>
                </form>
            </div>
        </li>
    </ul>
</nav>
<!-- /.navbar -->