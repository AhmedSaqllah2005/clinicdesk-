<?php
$pageTitle = "Edit Doctor";
require_once 'views/partials/header.php';
require_once 'views/partials/navbar.php';
require_once 'views/partials/sidebar.php';
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Doctor</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="index.php?page=doctors">Doctors</a></li>
                        <li class="breadcrumb-item active">Edit Doctor</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php require_once 'views/partials/alerts.php'; ?>
            
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-md mr-1"></i>
                        Edit Doctor: <?= htmlspecialchars($user['name'] ?? '') ?>
                    </h3>
                </div>
                <form action="index.php?page=update_doctor" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                    <input type="hidden" name="role" value="doctor">
                    
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Full Name *</label>
                                    <input type="text" name="name" class="form-control" 
                                           value="<?= htmlspecialchars($user['name']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Email *</label>
                                    <input type="email" name="email" class="form-control" 
                                           value="<?= htmlspecialchars($user['email']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" name="phone" class="form-control" 
                                           value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h5>Professional Information</h5>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Specialization</label>
                                    <select name="specialization_id" class="form-control" required>
                                        <option value="">Select Specialization</option>
                                        <?php foreach ($specializations as $spec): ?>
                                            <option value="<?= $spec['id'] ?>" 
                                                <?= ($doctor['specialization_id'] == $spec['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($spec['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Consultation Fee (USD)</label>
                                    <input type="number" name="consultation_fee" class="form-control" step="0.01" 
                                           value="<?= $doctor['consultation_fee'] ?? 0 ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Years of Experience</label>
                                    <input type="number" name="years_experience" class="form-control" 
                                           value="<?= $doctor['years_experience'] ?? 0 ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Available Days</label>
                                    <?php
                                    $availableDaysArray = explode(',', $doctor['available_days'] ?? 'Sun,Mon,Tue,Wed,Thu');
                                    $dayNames = [
                                        'Sun' => 'Sunday', 
                                        'Mon' => 'Monday', 
                                        'Tue' => 'Tuesday', 
                                        'Wed' => 'Wednesday', 
                                        'Thu' => 'Thursday', 
                                        'Fri' => 'Friday', 
                                        'Sat' => 'Saturday'
                                    ];
                                    ?>
                                    <div class="row">
                                        <?php foreach ($dayNames as $key => $name): ?>
                                            <div class="col-md-2">
                                                <div class="form-check">
                                                    <input type="checkbox" name="available_days[]" value="<?= $key ?>" class="form-check-input" 
                                                           <?= in_array($key, $availableDaysArray) ? 'checked' : '' ?>>
                                                    <label class="form-check-label"><?= $name ?></label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Bio / Education</label>
                                    <textarea name="bio" class="form-control" rows="4"><?= htmlspecialchars($doctor['bio'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Doctor
                        </button>
                        <a href="index.php?page=doctors" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<?php require_once 'views/partials/footer.php'; ?>