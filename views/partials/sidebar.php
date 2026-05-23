<?php $currentPage = $_GET['page'] ?? 'dashboard'; ?>
<?php $userRole = Auth::role(); ?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="index.php?page=dashboard" class="brand-link">
        <i class="fas fa-clinic-medical brand-image ml-2" style="font-size: 1.5rem;"></i>
        <span class="brand-text font-weight-light">ClinicDesk</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <i class="fas fa-user-circle fa-2x text-white"></i>
            </div>
            <div class="info">
                <a href="#" class="d-block"><?= sanitize(Auth::currentUser()['name'] ?? 'User') ?></a>
                <small class="text-white-50"><?= ucfirst($userRole) ?></small>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                <li class="nav-item">
                    <a href="index.php?page=dashboard"
                        class="nav-link <?= $currentPage == 'dashboard' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <?php if ($userRole == 'patient'): ?>

                    <li class="nav-item">
                        <a href="index.php?page=my_appointments"
                            class="nav-link <?= $currentPage == 'my_appointments' ? 'active' : '' ?>">

                            <i class="nav-icon fas fa-calendar-check"></i>

                            <p>My Appointments</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="index.php?page=my_prescriptions"
                            class="nav-link <?= $currentPage == 'my_prescriptions' ? 'active' : '' ?>">

                            <i class="nav-icon fas fa-file-medical"></i>

                            <p>My Prescriptions</p>
                        </a>
                    </li>

                <?php endif; ?>

                <?php if ($userRole == 'admin'): ?>

                    <li class="nav-item">
                        <a href="index.php?page=appointments"
                            class="nav-link <?= $currentPage == 'appointments' ? 'active' : '' ?>">

                            <i class="nav-icon fas fa-calendar-check"></i>
                            <p>All Appointments</p>

                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="index.php?page=doctors" class="nav-link <?= $currentPage == 'doctors' ? 'active' : '' ?>">

                            <i class="nav-icon fas fa-user-md"></i>
                            <p>Manage Doctors</p>

                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="index.php?page=users" class="nav-link <?= $currentPage == 'users' ? 'active' : '' ?>">

                            <i class="nav-icon fas fa-users"></i>
                            <p>Manage Users</p>

                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?page=reports" class="nav-link <?= $currentPage == 'reports' ? 'active' : '' ?>">

                            <i class="nav-icon fas fa-file-medical-alt"></i>

                            <p>Appointment Reports</p>

                        </a>
                    </li>

                <?php endif; ?>
                <?php if ($userRole == 'doctor'): ?>


                    <li class="nav-item">

                        <a href="index.php?page=dashboard"
                            class="nav-link <?= $currentPage == 'My Appointments' ? 'active' : '' ?>">

                            <i class="nav-icon fas fa-calendar-check"></i>

                            <p>My Appointments</p>

                        </a>

                    </li>

                    <li class="nav-item">

                        <a href="index.php?page=doctor_profile"
                            class="nav-link <?= $currentPage == 'doctor_profile' ? 'active' : '' ?>">

                            <i class="nav-icon fas fa-user-md"></i>

                            <p>My Profile</p>

                        </a>

                    </li>

                <?php endif; ?>
                <li class="nav-item">
                    <form action="index.php?page=logout" method="POST" id="logout-form">
                        <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                        <a href="#" class="nav-link"
                            onclick="document.getElementById('logout-form').submit(); return false;">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>Logout</p>
                        </a>
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>