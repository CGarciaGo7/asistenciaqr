<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Iniciar sesión | Sistema de Asistencia</title>

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            min-height: 100%;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(8, 145, 178, 0.12), transparent 35%),
                linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
            color: #0f172a;
        }

        .login-container {
            width: 100%;
            max-width: 1050px;
            min-height: 620px;
            background: #ffffff;
            border-radius: 22px;
            overflow: hidden;
            display: flex;
            box-shadow:
                0 25px 60px rgba(15, 23, 42, 0.15),
                0 8px 25px rgba(8, 145, 178, 0.08);
        }

        /* =====================================================
           PANEL IZQUIERDO
        ===================================================== */

        .login-info {
            width: 48%;
            position: relative;
            overflow: hidden;
            padding: 55px 48px;
            color: #ffffff;

            background:
                linear-gradient(
                    145deg,
                    #075985 0%,
                    #087f9f 50%,
                    #0891b2 100%
                );
        }

        .login-info::before {
            content: "";
            position: absolute;
            width: 430px;
            height: 430px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 50%;
            right: -190px;
            top: 65px;
        }

        .login-info::after {
            content: "";
            position: absolute;
            width: 260px;
            height: 260px;
            border: 1px solid rgba(255, 255, 255, 0.10);
            border-radius: 50%;
            right: -100px;
            top: 150px;
        }

        .brand {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 65px;
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #ffffff;
            color: #0785a5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.10);
        }

        .info-content {
            position: relative;
            z-index: 2;
        }

        .info-label {
            color: #a5f3fc;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 1.3px;
            margin-bottom: 15px;
        }

        .info-title {
            margin: 0;
            max-width: 430px;
            font-size: 39px;
            line-height: 1.12;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .info-description {
            max-width: 430px;
            margin-top: 20px;
            color: rgba(255, 255, 255, 0.88);
            font-size: 14px;
            line-height: 1.7;
        }

        .security-box {
            position: absolute;
            z-index: 3;
            left: 48px;
            right: 48px;
            bottom: 40px;

            padding: 15px 18px;
            border-radius: 14px;

            background: rgba(255, 255, 255, 0.10);
            border: 1px solid rgba(255, 255, 255, 0.20);
            backdrop-filter: blur(8px);

            display: flex;
            align-items: center;
            gap: 13px;
        }

        .security-icon {
            width: 38px;
            height: 38px;
            border-radius: 9px;
            background: rgba(255, 255, 255, 0.16);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .security-icon svg {
            width: 19px;
            height: 19px;
        }

        .security-title {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 3px;
        }

        .security-text {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.78);
        }

        /* =====================================================
           PANEL DERECHO
        ===================================================== */

        .login-form-container {
            width: 52%;
            padding: 70px 75px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .welcome {
            color: #0891b2;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .login-title {
            margin: 0;
            font-size: 32px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.7px;
        }

        .login-description {
            margin-top: 8px;
            margin-bottom: 32px;
            color: #64748b;
            font-size: 13px;
        }

        .form-group {
            margin-bottom: 21px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-size: 12px;
            font-weight: 700;
            color: #334155;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 17px;
            height: 17px;
            color: #0891b2;
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            height: 48px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            background: #eff6ff;
            color: #0f172a;
            padding: 0 44px;
            font-size: 13px;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-input::placeholder {
            color: #94a3b8;
        }

        .form-input:focus {
            background: #ffffff;
            border-color: #0891b2;
            box-shadow: 0 0 0 3px rgba(8, 145, 178, 0.12);
        }

        .password-toggle {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            cursor: pointer;
            color: #64748b;
            padding: 4px;
        }

        .password-toggle:hover {
            color: #0891b2;
        }

        .password-toggle svg {
            width: 17px;
            height: 17px;
        }

        .error-message {
            margin-top: 6px;
            color: #dc2626;
            font-size: 11px;
        }

        /* =====================================================
           REMEMBER / FORGOT
        ===================================================== */

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 3px;
            margin-bottom: 25px;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            color: #64748b;
            font-size: 12px;
        }

        .remember-checkbox {
            width: 15px;
            height: 15px;
            accent-color: #0891b2;
            cursor: pointer;
        }

        .forgot-link {
            color: #64748b;
            font-size: 12px;
            text-decoration: underline;
            transition: color 0.2s ease;
        }

        .forgot-link:hover {
            color: #0891b2;
        }

        /* =====================================================
           BOTÓN
        ===================================================== */

        .login-button {
            width: 100%;
            height: 48px;
            border: none;
            border-radius: 10px;

            background: linear-gradient(
                135deg,
                #075985,
                #0891b2
            );

            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;

            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;

            box-shadow: 0 8px 20px rgba(8, 145, 178, 0.22);

            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease,
                filter 0.2s ease;
        }

        .login-button:hover {
            transform: translateY(-1px);
            filter: brightness(1.05);
            box-shadow: 0 11px 25px rgba(8, 145, 178, 0.30);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .login-button svg {
            width: 17px;
            height: 17px;
        }

        .access-message {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;

            margin-top: 22px;

            color: #94a3b8;
            font-size: 10px;
        }

        .access-message svg {
            width: 12px;
            height: 12px;
            color: #0891b2;
        }

        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 850px) {

            body {
                padding: 20px;
            }

            .login-container {
                max-width: 560px;
                min-height: auto;
                flex-direction: column;
            }

            .login-info {
                width: 100%;
                min-height: 330px;
                padding: 35px;
            }

            .brand {
                margin-bottom: 40px;
            }

            .info-title {
                font-size: 30px;
            }

            .security-box {
                position: relative;
                left: auto;
                right: auto;
                bottom: auto;
                margin-top: 30px;
            }

            .login-form-container {
                width: 100%;
                padding: 45px 35px;
            }
        }

        @media (max-width: 500px) {

            body {
                padding: 0;
                background: #ffffff;
            }

            .login-container {
                width: 100%;
                min-height: 100vh;
                border-radius: 0;
                box-shadow: none;
            }

            .login-info {
                min-height: 300px;
                padding: 30px 25px;
            }

            .brand {
                margin-bottom: 35px;
            }

            .info-title {
                font-size: 28px;
            }

            .info-description {
                font-size: 13px;
            }

            .security-box {
                left: 0;
                right: 0;
                padding: 12px;
            }

            .login-form-container {
                padding: 40px 25px;
            }

            .login-title {
                font-size: 29px;
            }

            .form-options {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
        }
    </style>
</head>

<body>

    <div class="login-container">

        <!-- =================================================
             PANEL IZQUIERDO
        ================================================== -->

        <div class="login-info">

            <div class="brand">

                <div class="brand-icon">
                    +
                </div>

                <span>
                    Sistema de Asistencia
                </span>

            </div>

            <div class="info-content">

                <div class="info-label">
                    GESTIÓN DE PERSONAL
                </div>

                <h1 class="info-title">
                    Organiza tu jornada, controla tu asistencia.
                </h1>

                <p class="info-description">
                    Gestiona los horarios, turnos y asistencia del personal
                    de manera rápida, segura y organizada.
                </p>

            </div>

            <div class="security-box">

                <div class="security-icon">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v2h8z" />

                    </svg>

                </div>

                <div>

                    <div class="security-title">
                        Acceso seguro
                    </div>

                    <div class="security-text">
                        Información protegida para personal autorizado
                    </div>

                </div>

            </div>

        </div>


        <!-- =================================================
             PANEL DERECHO
        ================================================== -->

        <div class="login-form-container">

            <div class="welcome">
                BIENVENIDO DE NUEVO
            </div>

            <h2 class="login-title">
                Inicia sesión
            </h2>

            <p class="login-description">
                Ingresa tus datos para acceder al sistema.
            </p>


            <!-- Session Status -->

            @if (session('status'))

                <div class="error-message"
                     style="color: #0891b2; margin-bottom: 20px;">

                    {{ session('status') }}

                </div>

            @endif


            <form method="POST" action="{{ route('login') }}">

                @csrf


                <!-- EMAIL -->

                <div class="form-group">

                    <label
                        for="email"
                        class="form-label">

                        Correo electrónico

                    </label>

                    <div class="input-wrapper">

                        <svg
                            class="input-icon"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />

                        </svg>

                        <input
                            id="email"
                            class="form-input"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="Ingrese su correo electrónico"
                            required
                            autofocus
                            autocomplete="username">

                    </div>


                    @if ($errors->has('email'))

                        <div class="error-message">

                            {{ $errors->first('email') }}

                        </div>

                    @endif

                </div>


                <!-- PASSWORD -->

                <div class="form-group">

                    <label
                        for="password"
                        class="form-label">

                        Contraseña

                    </label>

                    <div class="input-wrapper">

                        <svg
                            class="input-icon"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v2h8z" />

                        </svg>


                        <input
                            id="password"
                            class="form-input"
                            type="password"
                            name="password"
                            placeholder="Ingrese su contraseña"
                            required
                            autocomplete="current-password">


                        <!-- Mostrar contraseña -->

                        <button
                            type="button"
                            class="password-toggle"
                            id="togglePassword"
                            aria-label="Mostrar contraseña">

                            <svg
                                id="eyeIcon"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />

                            </svg>

                        </button>

                    </div>


                    @if ($errors->has('password'))

                        <div class="error-message">

                            {{ $errors->first('password') }}

                        </div>

                    @endif

                </div>


                <!-- OPCIONES -->

                <div class="form-options">

                    <label
                        for="remember_me"
                        class="remember-label">

                        <input
                            id="remember_me"
                            type="checkbox"
                            class="remember-checkbox"
                            name="remember">

                        <span>
                            Recordarme
                        </span>

                    </label>


                    @if (Route::has('password.request'))

                        <a
                            class="forgot-link"
                            href="{{ route('password.request') }}">

                            ¿Olvidaste tu contraseña?

                        </a>

                    @endif

                </div>


                <!-- BOTÓN -->

                <button
                    type="submit"
                    class="login-button">

                    <span>
                        Ingresar
                    </span>

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M13 7l5 5m0 0l-5 5m5-5H6" />

                    </svg>

                </button>

            </form>


            <!-- MENSAJE INFERIOR -->

            <div class="access-message">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v2h8z" />

                </svg>

                <span>
                    Acceso exclusivo para personal autorizado
                </span>

            </div>

        </div>

    </div>


    <!-- =====================================================
         MOSTRAR / OCULTAR CONTRASEÑA
    ====================================================== -->

    <script>

        document.addEventListener('DOMContentLoaded', function () {

            const togglePassword =
                document.getElementById('togglePassword');

            const password =
                document.getElementById('password');

            if (togglePassword && password) {

                togglePassword.addEventListener('click', function () {

                    const type =
                        password.getAttribute('type') === 'password'
                            ? 'text'
                            : 'password';

                    password.setAttribute('type', type);

                    if (type === 'text') {

                        togglePassword.innerHTML = `
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19
                                       c-4.478 0-8.268-2.943-9.542-7
                                       a9.97 9.97 0 013.362-4.425
                                       m3.14-1.91A9.956 9.956 0 0112 5
                                       c4.478 0 8.268 2.943 9.542 7
                                       a9.97 9.97 0 01-4.132 5.411
                                       M15 12a3 3 0 11-6 0 3 3 0 016 0z
                                       M3 3l18 18" />

                            </svg>
                        `;

                    } else {

                        togglePassword.innerHTML = `
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5
                                       c4.478 0 8.268 2.943 9.542 7
                                       -1.274 4.057-5.064 7-9.542 7
                                       -4.477 0-8.268-2.943-8.268-7-9.542-7z" />

                            </svg>
                        `;

                    }

                });

            }

        });

    </script>

</body>

</html>