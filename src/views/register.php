
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Quero Passagem</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body, html {
            margin: 0; padding: 0; height: 100%;
            font-family: 'Sora', sans-serif; overflow-x: hidden;
        }

        .wrapper { display: flex; min-height: 100vh; width: 100vw; }

        /* Sidebar fixa em 470px */
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
            padding: 40px 20px;
            position: relative;
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

        .login-box { width: 100%; max-width: 420px; }

        .login-box h2 { font-size: 22px; color: #1c2b44; margin-bottom: 8px; font-weight: 700; }
        .login-box p { font-size: 14px; color: #4a4a4a; margin-bottom: 25px; }

        .input-group { margin-bottom: 15px; }

        .input-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #1c2b44;
            margin-bottom: 5px;
        }

        .input-group input {
            width: 100%;
            padding: 14px;
            border: 1px solid #dcdcdc;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
            outline: none;
            font-family: 'Sora', sans-serif;
        }

        /* Botão com o hover azul da sidebar */
        .btn-continue {
            width: 100%;
            padding: 16px;
            background: #bfc4cd;
            color: white;
            border: none;
            border-radius: 4px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            text-transform: uppercase;
            font-family: 'Sora', sans-serif;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-continue:hover {
            background-color: #0D2240;
            box-shadow: 0 4px 12px rgba(13, 34, 64, 0.2);
        }

        .footer-text {
            font-size: 12px;
            color: #666;
            margin-top: 20px;
            text-align: center;
        }

        .footer-text a { color: #0D2240; font-weight: 600; text-decoration: none; }

        /* Divisor e Botões Sociais */
        .divider { display: flex; align-items: center; margin: 25px 0; color: #9b9b9b; }
        .divider::before, .divider::after { content: ''; flex: 1; border-bottom: 1px solid #e0e0e0; }
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

        .icon-box { width: 30px; display: flex; justify-content: center; align-items: center; margin-right: 10px; }
        .btn-social img { max-height: 22px; width: auto; object-fit: contain; }
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
            <h2>Crie sua conta</h2>
            <p>Cadastre-se para gerenciar suas viagens com facilidade.</p>

            <form action="/register" method="POST">
                <div class="input-group">
                    <label>Nome Completo</label>
                    <input type="text" name="name" placeholder="Seu nome aqui" required>
                </div>

                <div class="input-group">
                    <label>E-mail</label>
                    <input type="email" name="email" placeholder="seu@gmail.com" required>
                </div>

                <div class="input-group">
                    <label>Senha</label>
                    <input type="password" name="password" placeholder="Mínimo 6 caracteres" required>
                </div>

                <button type="submit" class="btn-continue">CRIAR CONTA</button>
            </form>

            <div class="footer-text">
                Já tem uma conta? <a href="/login">Fazer login</a>
            </div>

            <div class="divider"><span>ou cadastre-se com</span></div>

            <div class="social-buttons">
                <button class="btn-social" type="button">
                    <div class="icon-box"><img src="https://assets.queropassagem.com.br/static/Images/Icones/facebook.svg" alt="Facebook"></div>
                    Facebook
                </button>
                <button class="btn-social" type="button">
                    <div class="icon-box"><img src="https://assets.queropassagem.com.br/static/Images/Icones/google.svg" alt="Google"></div>
                    Google
                </button>
                <button class="btn-social" type="button">
                    <div class="icon-box">
                        <img src="https://www.logo.wine/a/logo/Apple_Inc./Apple_Inc.-Logo.wine.svg" class="img-apple" alt="Apple">
                    </div>
                    Apple
                </button>
            </div>
        </div>
    </main>
</div>

</body>
</html>