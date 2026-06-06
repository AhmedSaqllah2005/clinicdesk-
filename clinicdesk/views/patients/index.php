<?php require_once 'views/partials/header.php'; ?>
<?php require_once 'views/partials/navbar.php'; ?>
<?php require_once 'views/partials/sidebar.php'; ?>

<div class="content-wrapper">

    <section class="content-header">
        <div class="container-fluid">
            <h1>
        <i class="fas fa-user-injured mr-2"></i>
        Patients Management
    </h1>
        </div>
    </section>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success'] ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <section class="content">

        <div class="container-fluid">

            <!-- Add Patient -->
            <div class="card">

                <div class="card-header">
                    <h3 class="card-title">Add Patient</h3>
                </div>

                <div class="card-body">

                    <form action="index.php?page=store_patient" method="POST">

                        <!-- CSRF Token -->
                        <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">

                        <div class="row">

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                            </div>

                        </div>

                        <button type="submit" class="btn btn-primary">
                            Add Patient
                        </button>

                    </form>

                </div>

            </div>

            <!-- Patients Table -->
            <div class="card">

                <div class="card-header">
                    <h3 class="card-title">All Patients</h3>
                </div>

                <div class="card-body table-responsive">

                    <table class="table table-bordered table-striped table-hover" id="patientsTable">

                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($patients as $patient): ?>
                                <tr>

                                    <td><?= $patient['id'] ?></td>
                                    <td><?= htmlspecialchars($patient['name']) ?></td>
                                    <td><?= htmlspecialchars($patient['email']) ?></td>

                                    <td>

                                        <!-- Edit Button -->
                                        <a href="index.php?page=edit_patient&id=<?= $patient['id'] ?>"
                                            class="btn btn-warning btn-sm">
                                            Edit
                                        </a>

                                        <!-- Delete Form (POST + CSRF) -->
                                        <form action="index.php?page=delete_patient" method="POST" style="display:inline">
                                            <input type="hidden" name="id" value="<?= $patient['id'] ?>">
                                            <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this patient?')">
                                                Delete
                                            </button>
                                        </form>

                                    </td>

                                </tr>
                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </section>

</div>

<?php require_once 'views/partials/footer.php'; ?>

<script>
$(document).ready(function () {
    $('#patientsTable').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        pageLength: 10
    });
});
</script>
