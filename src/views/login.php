<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Quero Passagem</title>
    <link rel="icon" type="image/png" href="/images/favicon.ico"></head>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Sora:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        /* Reset e Fontes */
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Sora', sans-serif;
            overflow: hidden;
        }

        .wrapper { display: flex; height: 100vh; width: 100vw; }

        /* Lado Esquerdo - Barra Azul (470px conforme solicitado) */
        .sidebar {
            width: 470px;
            background-color: #0D2240;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 30px;
            flex-shrink: 0;
        }

        .sidebar img {
            max-width: 140px;
            height: auto;
        }

        /* Lado Direito */
        .main-content {
            flex: 1;
            background-color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
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
        /* Botão Continuar */
        .btn-continue {
            width: 100%;
            padding: 16px;
            background: #bfc4cd; /* Cor cinza padrão (inativo) */
            color: white;
            border: none;
            border-radius: 4px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            letter-spacing: 0.5px;
            font-family: 'Sora', sans-serif;
            text-transform: uppercase;
            transition: background 0.3s ease, transform 0.1s ease; /* Transição suave */
        }

        /* Hover no Botão Continuar */
        .btn-continue:hover {
            background-color: #0D2240; /* Azul vibrante estilo Google/Quero Passagem */
            box-shadow: 0 4px 12px rgba(26, 115, 232, 0.2); /* Sombra leve para profundidade */
        }

        /* Feedback de clique (opcional, mas recomendado) */
        .btn-continue:active {
            transform: scale(0.98); /* Botão "afunda" levemente ao clicar */
        }
        /* Divisor 'ou' */
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

        /* BOTÕES SOCIAIS - CORREÇÃO DE ALINHAMENTO */
        .social-buttons {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn-social {
            width: 100%;
            height: 52px;
            border: 1px solid #0D2240;
            background: white;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            /* Padding lateral fixo para alinhar as logos em coluna */
            padding: 0 40px;
            font-family: 'Sora', sans-serif;
            font-weight: 600;
            font-size: 13px;
            color: #0D2240;
            transition: background 0.2s;
        }

        .btn-social:hover { background: #f8f9fa; }

        /* Container da Logo com largura fixa */
        .icon-box {
            width: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 10px;
        }

        .btn-social img {
            max-height: 22px;
            width: auto;
            object-fit: contain;
        }

        /* Ajuste fino para a Apple que tem SVG desproporcional */
        .img-apple {
            height: 26px !important;
            margin-bottom: 4px;
        }

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
        <span style="color: #666; font-size: 14px; font-family: 'Sora', sans-serif;">
            Não tem uma conta?
        </span>
                    <a href="/register" style="color: #0D2240; font-weight: 700; text-decoration: none; font-size: 14px; font-family: 'Sora', sans-serif; margin-left: 5px;">
                        Faça seu cadastro
                    </a>
                </div>
            </form>

            <div class="divider">
                <span>ou</span>
            </div>

            <div class="social-buttons">
                <button class="btn-social" type="button">
                    <div class="icon-box">
                        <img src="https://assets.queropassagem.com.br/static/Images/Icones/facebook.svg" alt="Facebook">
                    </div>
                    Continue com o Facebook
                </button>

                <button class="btn-social" type="button">
                    <div class="icon-box">
                        <img src="https://assets.queropassagem.com.br/static/Images/Icones/google.svg" alt="Google">
                    </div>
                    Continue com o Google
                </button>

                <button class="btn-social" type="button">
                    <div class="icon-box">
                        <img src="https://www.logo.wine/a/logo/Apple_Inc./Apple_Inc.-Logo.wine.svg" class="img-apple" alt="Apple">
                    </div>
                    Continue com a Apple
                </button>
            </div>
        </div>
    </main>
</div>

</body>
</html>