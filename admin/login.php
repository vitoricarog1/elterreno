<?php
require_once 'config.php';

$error = '';

if ($_POST['action'] ?? '' === 'login') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username === ADMIN_USER && $password === ADMIN_PASS) {
        $_SESSION['admin_logged'] = true;
        $_SESSION['admin_user'] = $username;
        $_SESSION['login_time'] = time();
        header('Location: index.php');
        exit;
    } else {
        $error = 'Usuário ou senha incorretos!';
    }
}

// Se já está logado, redirecionar
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - El Terreno Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #6d5db2, #ff248e, #7f67db);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Lato', sans-serif;
        }
        .login-card {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            border: 1px solid rgba(255,255,255,0.2);
            max-width: 400px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(135deg, #6d5db2, #ff248e);
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 2rem;
            text-align: center;
        }
        .btn-login {
            background: linear-gradient(135deg, #6d5db2, #ff248e);
            border: none;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(109, 93, 178, 0.3);
        }
        .form-control {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #6d5db2;
            box-shadow: 0 0 0 0.2rem rgba(109, 93, 178, 0.25);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <i class="fas fa-user-shield fa-3x mb-3"></i>
            <h3 class="mb-0">El Terreno</h3>
            <p class="mb-0 opacity-75">Painel Administrativo</p>
        </div>
        <div class="p-4">
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?= $error ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <input type="hidden" name="action" value="login">
                
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        <i class="fas fa-user me-2"></i>Usuário
                    </label>
                    <input type="text" name="username" class="form-control" 
                           placeholder="Digite seu usuário" required>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold">
                        <i class="fas fa-lock me-2"></i>Senha
                    </label>
                    <input type="password" name="password" class="form-control" 
                           placeholder="Digite sua senha" required>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-login text-white">
                        <i class="fas fa-sign-in-alt me-2"></i>Entrar
                    </button>
                </div>
            </form>
            
            <div class="text-center mt-4">
                <small class="text-muted">
                    <i class="fas fa-shield-alt me-1"></i>
                    Acesso restrito a administradores
                </small>
            </div>
        </div>
    </div>
</body>
</html>