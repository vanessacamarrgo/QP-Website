<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Lógica de captura do erro (o seu "caçador" de mensagens)
$erro = null;
if (isset($_SESSION['flash'])) {
    if (is_array($_SESSION['flash'])) {
        // Pega a mensagem dentro daquela estrutura [0]['message'] que vimos no seu print_r
        $erro = $_SESSION['flash'][0]['message'] ?? ($_SESSION['flash']['error'] ?? null);
    } else {
        $erro = $_SESSION['flash'];
    }
    unset($_SESSION['flash']); // Limpa para não repetir ao dar F5
}

// Caso você tenha usado a variável direta no teste anterior
if (!$erro && isset($_SESSION['erro_login_direto'])) {
    $erro = $_SESSION['erro_login_direto'];
    unset($_SESSION['erro_login_direto']);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Quero Passagem</title>
    <link rel="icon" type="image/png" href="/images/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Sora:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        /* Reset e Fontes */
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Sora', sans-serif;
            overflow-x: hidden;
        }

        .wrapper { display: flex; min-height: 100vh; width: 100vw; }

        .sidebar {
            width: 470px;
            background-color: #0D2240;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 30px;
            flex-shrink: 0;
        }

        .sidebar img { max-width: 140px; height: auto; }

        .main-content {
            flex: 1;
            background-color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 40px 0;
        }

        .help-link {
            position: absolute;
            top: 25px;
            right: 40px;
            color: #0D2240;
            font-weight: bold;
            text-decoration: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .login-box {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .login-box h2 {
            font-size: 22px;
            color: #1c2b44;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .login-box p {
            font-size: 14px;
            color: #4a4a4a;
            margin-bottom: 25px;
        }

        /* ESTILO DO AVISO DE ERRO */
        .error-alert {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
            font-weight: 600;
            border-left: 5px solid #dc3545;
            animation: shake 0.4s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-8px); }
            75% { transform: translateX(8px); }
        }

        .input-group input {
            width: 100%;
            padding: 16px;
            border: 1px solid #dcdcdc;
            border-radius: 4px;
            font-size: 15px;
            margin-bottom: 15px;
            box-sizing: border-box;
            outline: none;
            font-family: 'Sora', sans-serif;
        }

        .alert-info {
            background: #f0f4ff;
            color: #3b7ced;
            padding: 12px 15px;
            border-radius: 4px;
            font-size: 12px;
            margin-bottom: 20px;
            line-height: 1.5;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-continue {
            width: 100%;
            padding: 16px;
            background: #0D2240;
            color: white;
            border: none;
            border-radius: 4px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            letter-spacing: 0.5px;
            font-family: 'Sora', sans-serif;
            text-transform: uppercase;
            transition: background 0.3s;
        }

        .btn-continue:hover { background-color: #16325c; }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 25px 0;
            color: #9b9b9b;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e0e0e0;
        }
        .divider span { padding: 0 15px; font-size: 12px; font-weight: 600; }

        .social-buttons { display: flex; flex-direction: column; gap: 12px; }

        .btn-social {
            width: 100%;
            height: 52px;
            border: 1px solid #0D2240;
            background: white;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            padding: 0 40px;
            font-family: 'Sora', sans-serif;
            font-weight: 600;
            font-size: 13px;
            color: #0D2240;
            transition: background 0.2s;
        }

        .btn-social:hover { background: #f8f9fa; }

        .icon-box { width: 30px; display: flex; justify-content: center; margin-right: 10px; }
        .btn-social img { max-height: 22px; width: auto; }
    </style>
</head>
<body>

<div class="wrapper">
    <aside class="sidebar">
        <img src="https://assets.queropassagem.com.br/static/Images/Logos/logo-branca.png" alt="Quero Passagem">
    </aside>

    <main class="main-content">
        <a href="#" class="help-link">
            <span style="border: 1.5px solid #0D2240; border-radius: 50%; width: 18px; height: 18px; display: inline-flex; align-items: center; justify-content: center; font-size: 11px;">?</span>
            Central de ajuda
        </a>

        <div class="login-box">
            <h2>Acesse suas viagens</h2>
            <p>Digite seu e-mail ou número de celular para continuar</p>

            <?php if ($erro): ?>
                <div class="error-alert">
                    <?= htmlspecialchars((string)$erro) ?>
                </div>
            <?php endif; ?>

            <form action="/login" method="POST">
                <div class="input-group">
                    <input type="text" name="email" placeholder="E-mail ou número de celular" required>
                </div>

                <div class="input-group">
                    <input type="password" name="password" placeholder="Sua senha" required>
                </div>

                <div class="alert-info">
                    <span style="font-size: 16px;">ⓘ</span>
                    <span>Se você é o comprador, utilize o mesmo e-mail ou número de celular da compra.</span>
                </div>

                <button type="submit" class="btn-continue">CONTINUAR</button>

                <div class="register-footer" style="margin-top: 25px; text-align: center;">
                    <span style="color: #666; font-size: 14px;">Não tem uma conta?</span>
                    <a href="/register" style="color: #0D2240; font-weight: 700; text-decoration: none; font-size: 14px; margin-left: 5px;">
                        Faça seu cadastro
                    </a>
                </div>
            </form>

            <div class="divider"><span>ou</span></div>

            <div class="social-buttons">
                <button class="btn-social" type="button">
                    <div class="icon-box"><img src="https://assets.queropassagem.com.br/static/Images/Icones/facebook.svg" alt="Facebook"></div>
                    Continue com o Facebook
                </button>
                <button class="btn-social" type="button">
                    <div class="icon-box"><img src="https://assets.queropassagem.com.br/static/Images/Icones/google.svg" alt="Google"></div>
                    Continue com o Google
                </button>
                <button class="btn-social" type="button">
                    <div class="icon-box"><img src="https://www.logo.wine/a/logo/Apple_Inc./Apple_Inc.-Logo.wine.svg" style="height: 26px" alt="Apple"></div>
                    Continue com a Apple
                </button>
            </div>
        </div>
    </main>
</div>

</body>
</html>