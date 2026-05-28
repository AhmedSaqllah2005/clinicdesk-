<?php
$pageTitle = "My Profile";

require_once 'views/partials/header.php';
require_once 'views/partials/navbar.php';
require_once 'views/partials/sidebar.php';
?>

<div class="content-wrapper">

    <div class="content-header">
        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center">

                <h1>My Profile</h1>
            </div>

        </div>
    </div>

    <section class="content">

        <div class="container-fluid">

            <?php require_once 'views/partials/alerts.php'; ?>

            <div class="card card-primary">

                <div class="card-header">
                    <h3 class="card-title">Edit Profile</h3>
                </div>

                <form method="POST"
                      action="index.php?page=patient_update_profile">

                    <input type="hidden"
                           name="csrf_token"
                           value="<?= CSRF::generateToken() ?>">

                    <div class="card-body">

                        <div class="form-group">

                            <label>Name</label>

                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   value="<?= sanitize($patient['name']) ?>"
                                   required>

                        </div>

                        <div class="form-group">

                            <label>Email</label>

                            <input type="email"
                                   name="email"
                                   class="form-control"
                                   value="<?= sanitize($patient['email']) ?>"
                                   required>

                        </div>

                        <div class="form-group">

                            <label>Phone</label>

                            <input type="text"
                                   name="phone"
                                   class="form-control"
                                   value="<?= sanitize($patient['phone']) ?>">

                        </div>

                    </div>

                    <div class="card-footer">

                        <button type="submit"
                                class="btn btn-primary">

                            <i class="fas fa-save"></i>
                            Update Profile

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </section>

</div>

<?php require_once 'views/partials/footer.php'; ?>