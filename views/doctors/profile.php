<?php
$pageTitle = "Doctor Profile";

require_once 'views/partials/header.php';
require_once 'views/partials/navbar.php';
require_once 'views/partials/sidebar.php';
?>

<div class="content-wrapper">

    <section class="content-header">

        <div class="container-fluid">

            <div class="row mb-2">

                <div class="col-sm-6">
                    <h1>Edit Profile</h1>
                </div>

            </div>

        </div>

    </section>

    <section class="content">

        <div class="container-fluid">

            <?php require_once 'views/partials/alerts.php'; ?>

            <div class="card card-primary">

                <div class="card-header">
                    <h3 class="card-title">
                        Doctor Information
                    </h3>
                </div>

                <form action="index.php?page=update_doctor_profile" method="POST">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= CSRF::generateToken() ?>">

                    <div class="card-body">

                        <div class="form-group">

                            <label>Full Name</label>

                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                value="<?= sanitize($user['name']) ?>"
                                required>

                        </div>

                        <div class="form-group">

                            <label>Email</label>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                value="<?= sanitize($user['email']) ?>"
                                required>

                        </div>

                        <div class="form-group">

                            <label>Phone</label>

                            <input
                                type="text"
                                name="phone"
                                class="form-control"
                                value="<?= sanitize($user['phone']) ?>">

                        </div>

                    </div>

                    <div class="card-footer">

                        <button type="submit" class="btn btn-primary">
                            Update Profile
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </section>

</div>

<?php require_once 'views/partials/footer.php'; ?>