<?php
session_start();

// Store error message if exists
$error_message = '';
if (isset($_SESSION['error'])) {
    $error_message = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Parkir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1E3A8A;
            --secondary-color: #3B82F6;
            --light-bg: #F1F5F9;
        }
        
        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            max-width: 450px;
            width: 100%;
            padding: 20px;
        }
        
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        
        .login-header {
            background: var(--primary-color);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .login-header i {
            font-size: 60px;
            margin-bottom: 15px;
        }
        
        .login-header h2 {
            margin: 0;
            font-weight: 700;
            font-size: 28px;
        }
        
        .login-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        
        .login-body {
            padding: 40px 30px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .form-control {
            border: 2px solid #E2E8F0;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(30, 58, 138, 0.1);
        }
        
        .input-group-text {
            background: var(--light-bg);
            border: 2px solid #E2E8F0;
            border-right: none;
            border-radius: 10px 0 0 10px;
            color: var(--primary-color);
        }
        
        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        
        .btn-login {
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            background: #1e40af;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(30, 58, 138, 0.3);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .default-accounts {
            background: var(--light-bg);
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            font-size: 12px;
        }
        
        .default-accounts h6 {
            font-size: 13px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .default-accounts .account-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #CBD5E1;
        }
        
        .default-accounts .account-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <i class="fas fa-parking"></i>
                <h2>Sistem Parkir</h2>
                <p>Silakan login untuk melanjutkan</p>
            </div>
            
            <div class="login-body">
                <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                <?php
endif; ?>
                
                <form method="POST" action="process_login.php">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user me-1"></i> Username
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" class="form-control" name="username" placeholder="Masukkan username" required autofocus>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-lock me-1"></i> Password
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control" name="password" placeholder="Masukkan password" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i> Login
                    </button>
                </form>
                
                <div class="default-accounts">
                    <h6><i class="fas fa-info-circle me-1"></i> Akun Default:</h6>
                    <div class="account-item">
                        <span><strong>Admin:</strong> admin / admin123</span>
                    </div>
                    <div class="account-item">
                        <span><strong>Petugas:</strong> petugas / petugas123</span>
                    </div>
                    <div class="account-item">
                        <span><strong>Owner:</strong> owner / owner123</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
