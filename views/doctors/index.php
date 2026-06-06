<?php
$pageTitle = "Manage Doctors";
require_once 'views/partials/header.php';
require_once 'views/partials/navbar.php';
require_once 'views/partials/sidebar.php';
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manage Doctors</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Doctors</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php require_once 'views/partials/alerts.php'; ?>
            <!-- ADD DOCTOR -->
            <div class="card card-primary">

                <div class="card-header">
                    <h3 class="card-title">
                        Add Doctor
                    </h3>
                </div>

                <form action="index.php?page=store_doctor" method="POST" enctype="multipart/form-data">

                    <div class="card-body">

                        <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">

                        <div class="row">

                            <div class="col-md-4">
                                <div class="form-group">

                                    <label>Doctor Name</label>

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

                                    <input type="password" name="password" class="form-control" required minlength="6" placeholder="Min 6 characters">

                                </div>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-4">
                                <div class="form-group">

                                    <label>Specialization</label>

                                    <select name="specialization_id" class="form-control" required>

                                        <option value="">
                                            Select Specialization
                                        </option>

                                        <?php foreach ($specializations as $spec): ?>

                                            <option value="<?= $spec['id'] ?>">

                                                <?= sanitize($spec['name']) ?>

                                            </option>

                                        <?php endforeach; ?>

                                    </select>

                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">

                                    <label>Consultation Fee</label>

                                    <input type="number" step="0.01" name="consultation_fee" class="form-control">

                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">

                                    <label>Years Experience</label>

                                    <input type="number" name="years_experience" class="form-control">

                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Doctor Profile Photo <small class="text-muted">(JPEG/PNG, max 1MB — optional)</small></label>
                                    <input type="file" name="doctor_photo" class="form-control-file"
                                           accept="image/jpeg,image/png">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Available Days</label>

                            <div>

                                <?php
                                $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                                ?>

                                <?php foreach ($days as $day): ?>

                                    <div class="form-check form-check-inline">

                                        <input class="form-check-input" type="checkbox" name="available_days[]"
                                            value="<?= $day ?>">

                                        <label class="form-check-label">

                                            <?= $day ?>

                                        </label>

                                    </div>

                                <?php endforeach; ?>

                            </div>

                        </div>

                    </div>

                    <div class="card-footer">

                        <button type="submit" class="btn btn-primary">

                            <i class="fas fa-plus"></i>
                            Add Doctor

                        </button>

                    </div>

                </form>

            </div>
            <div class="card">
                <!-- Search Filter -->
                <div class="card-header">
                    <form action="index.php" method="GET" class="form-inline">
                        <input type="hidden" name="page" value="doctors">
                        <div class="input-group" style="max-width:520px;">
                            <input type="text" name="search" class="form-control"
                                   placeholder="Search by ID, Name, Email or Specialization..."
                                   value="<?= htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES) ?>">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i> Search
                                </button>
                                <?php if (!empty($_GET['search'])): ?>
                                    <a href="index.php?page=doctors" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Clear
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Doctor Name</th>
                                <th>Email</th>
                                <th>Specialization</th>
                                <th>Consultation Fee</th>
                                <th>Available Days</th>
                                <th>Experience</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($doctors as $doc): ?>
                                <tr>
                                    <td><?= $doc['id'] ?></td>
                                    <td><?= sanitize($doc['name']) ?></td>
                                    <td><?= sanitize($doc['email']) ?></td>
                                    <td><?= sanitize($doc['specialization_name'] ?? '-') ?></td>
                                    <td><?= number_format($doc['consultation_fee'] ?? 0, 2) ?> USD</td>
                                    <td>
                                        <?php
                                        $days = explode(',', $doc['available_days'] ?? '');
                                        $dayNames = ['Sun' => 'S', 'Mon' => 'M', 'Tue' => 'T', 'Wed' => 'W', 'Thu' => 'R', 'Fri' => 'F', 'Sat' => 'Sa'];
                                        $display = [];
                                        foreach ($days as $day) {
                                            if (isset($dayNames[$day]))
                                                $display[] = $dayNames[$day];
                                        }
                                        echo implode(' ', $display);
                                        ?>
                                    </td>
                                    <td><?= $doc['years_experience'] ?? 0 ?> years</td>
                                    <td>
                                        <a href="index.php?page=doctors&action=edit&id=<?= $doc['user_id'] ?>"
                                            class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="index.php?page=delete_doctor" method="POST" style="display:inline"
                                            onsubmit="return confirm('Delete Dr. <?= sanitize($doc['name']) ?>?')">
                                            <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                                            <input type="hidden" name="user_id" value="<?= $doc['user_id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    <?php $searchParam = urlencode($_GET['search'] ?? ''); ?>
                    <ul class="pagination pagination-sm m-0 float-right">

                        <?php if ($paginator->hasPrev()): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=doctors&p=<?= $paginator->getPrevPage() ?>&search=<?= $searchParam ?>">« Prev</a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $paginator->totalPages(); $i++): ?>
                            <li class="page-item <?= $i === $paginator->currentPage() ? 'active' : '' ?>">
                                <a class="page-link" href="?page=doctors&p=<?= $i ?>&search=<?= $searchParam ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($paginator->hasNext()): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=doctors&p=<?= $paginator->getNextPage() ?>&search=<?= $searchParam ?>">Next »</a>
                            </li>
                        <?php endif; ?>

                    </ul>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once 'views/partials/footer.php'; ?>