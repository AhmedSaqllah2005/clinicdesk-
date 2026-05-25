<?php require_once 'views/partials/header.php'; ?>
<?php require_once 'views/partials/navbar.php'; ?>
<?php require_once 'views/partials/sidebar.php'; ?>

<div class="content-wrapper">

    <!-- Header -->
    <section class="content-header">

        <div class="container-fluid">

            <div class="row mb-2">

                <div class="col-sm-6">

                    <h1>Edit Patient</h1>

                </div>

            </div>

        </div>

    </section>

    <!-- Main -->
    <section class="content">

        <div class="container-fluid">

            <div class="card card-primary">

                <div class="card-header">

                    <h3 class="card-title">

                        Update Patient Information

                    </h3>

                </div>

                <div class="card-body">

                    <form action="index.php?page=update_patient" method="POST">

                        <!-- CSRF -->
                        <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">

                        <!-- Patient ID -->
                        <input type="hidden" name="id" value="<?= $patient['id'] ?>">

                        <!-- Name -->
                        <div class="form-group">

                            <label>Name</label>

                            <input type="text" name="name" class="form-control"
                                value="<?= htmlspecialchars($patient['name']) ?>" required>

                        </div>

                        <!-- Email -->
                        <div class="form-group">

                            <label>Email</label>

                            <input type="email" name="email" class="form-control"
                                value="<?= htmlspecialchars($patient['email']) ?>" required>

                        </div>

                        <!-- Phone -->
                        <div class="form-group">

                            <label>Phone</label>

                            <input type="text" name="phone" class="form-control"
                                value="<?= htmlspecialchars($patient['phone'] ?? '') ?>">

                        </div>

                        <!-- Buttons -->
                        <div class="mt-3">

                            <button type="submit" class="btn btn-primary">

                                <i class="fas fa-save"></i>

                                Update Patient

                            </button>

                            <a href="index.php?page=patients" class="btn btn-secondary">

                                Cancel

                            </a>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </section>

</div>

<?php require_once 'views/partials/footer.php'; ?>