<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Page Not Found | ClinicDesk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg,

            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-card {
            background: white;
            border-radius: 15px;
            padding: 50px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
        }
        .error-code {
            font-size: 80px;
            font-weight: bold;
            color:

        }
        .error-title {
            font-size: 24px;
            margin: 20px 0;
        }
        .btn-home {
            background: linear-gradient(135deg,

            border: none;
            color: white;
            padding: 10px 30px;
            border-radius: 25px;
        }
        .btn-home:hover {
            opacity: 0.9;
            color: white;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <i class="fas fa-search fa-4x text-muted mb-3"></i>
        <div class="error-code">404</div>
        <div class="error-title">Page Not Found</div>
        <p class="text-muted">The page you are looking for does not exist or has been moved.</p>
        <a href="index.php?page=dashboard" class="btn btn-home">
            <i class="fas fa-home"></i> Back to Dashboard
        </a>
    </div>
</body>
</html>