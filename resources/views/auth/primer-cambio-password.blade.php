<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar contraseña | Sis.Asistencia</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        body {
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 12px;
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            font-family: "Segoe UI", Arial, sans-serif;
        }

        .password-card {
            width: 100%;
            max-width: 460px;
            overflow: hidden;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 12px 40px rgba(7, 89, 133, .14);
        }

        .password-header {
            padding: 30px 28px 25px;
            text-align: center;
            color: #fff;
            background: linear-gradient(135deg, #075985, #0891b2);
        }

        .password-header .header-icon {
            display: inline-flex;
            width: 66px;
            height: 66px;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            border: 1px solid rgba(255,255,255,.35);
            border-radius: 50%;
            background: rgba(255,255,255,.13);
            font-size: 27px;
        }

        .password-header h1 {
            margin: 0 0 8px;
            font-size: 23px;
            font-weight: 700;
        }

        .password-header p {
            margin: 0;
            font-size: 14px;
            opacity: .92;
        }

        .password-body {
            padding: 28px;
        }

        .notice {
            padding: 13px 14px;
            margin-bottom: 22px;
            border-left: 4px solid #0891b2;
            border-radius: 8px;
            background: #f0f9ff;
            color: #075985;
            font-size: 13px;
            line-height: 1.5;
        }

        .form-label {
            display: block;
            margin-bottom: 7px;
            color: #334155;
            font-size: 13px;
            font-weight: 600;
        }

        .input-group {
            margin-bottom: 5px;
        }

        .form-control {
            height: 45px;
            border-color: #cbd5e1;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #0891b2;
            box-shadow: 0 0 0 3px rgba(8,145,178,.12);
        }

        .input-group-append .btn {
            border-color: #cbd5e1;
            color: #64748b;
            background: #fff;
        }

        .input-group-append .btn:hover {
            color: #075985;
            background: #f0f9ff;
        }

        .field-error {
            display: block;
            margin: 5px 0 13px;
            color: #dc2626;
            font-size: 12px;
        }

        .btn-save {
            width: 100%;
            padding: 12px;
            margin-top: 8px;
            border: 0;
            border-radius: 8px;
            color: #fff;
            background: linear-gradient(135deg, #075985, #0891b2);
            font-size: 15px;
            font-weight: 600;
            transition: opacity .2s;
        }

        .btn-save:hover {
            color: #fff;
            opacity: .92;
        }

        .password-footer {
            padding: 0 28px 24px;
            color: #64748b;
            text-align: center;
            font-size: 12px;
        }

        @media (max-width: 480px) {
            .password-body {
                padding: 22px;
            }

            .password-header {
                padding: 25px 20px;
            }
        }
    </style>
</head>

<body>
    <main class="password-card">
        <header class="password-header">
            <div class="header-icon">
                <i class="fas fa-shield-alt"></i>
            </div>

            <h1>Cambia tu contraseña</h1>
            <p>Protege tu cuenta de Sis.Asistencia</p>
        </header>

        <section class="password-body">
            <div class="notice">
                <i class="fas fa-info-circle mr-1"></i>
                Por seguridad, debes cambiar tu contraseña inicial antes de
                continuar utilizando el sistema.
            </div>

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    Revisa los campos señalados y vuelve a intentarlo.
                </div>
            @endif

            <form method="POST" action="{{ route('password.first-change.update') }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label" for="password_actual">
                        Contraseña actual
                    </label>

                    <div class="input-group">
                        <input type="password" name="password_actual" id="password_actual"
                            class="form-control"
                            placeholder="Ingresa tu contraseña actual"
                            autocomplete="current-password"
                            required>

                        <div class="input-group-append">
                            <button type="button"
                                    class="btn toggle-password"
                                    data-target="password_actual"
                                    aria-label="Mostrar contraseña">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    @error('password_actual')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">
                        Nueva contraseña
                    </label>

                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control"
                            placeholder="Crea una nueva contraseña"
                            minlength="8"
                            autocomplete="new-password"
                            required
                        >

                        <div class="input-group-append">
                            <button type="button"
                                    class="btn toggle-password"
                                    data-target="password"
                                    aria-label="Mostrar contraseña">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    @error('password')
                        <span class="field-error">{{ $message }}</span>
                    @enderror

                    <small class="text-muted">
                        Utiliza al menos 8 caracteres.
                    </small>
                </div>

                <div class="form-group mt-3">
                    <label class="form-label"
                           for="password_confirmation">
                        Confirmar nueva contraseña
                    </label>

                    <div class="input-group">
                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            class="form-control"
                            placeholder="Escribe nuevamente tu contraseña"
                            minlength="8"
                            autocomplete="new-password"
                            required
                        >

                        <div class="input-group-append">
                            <button type="button"
                                    class="btn toggle-password"
                                    data-target="password_confirmation"
                                    aria-label="Mostrar contraseña">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-save">
                    <i class="fas fa-lock mr-2"></i>
                    Guardar nueva contraseña
                </button>
            </form>
        </section>

        <footer class="password-footer">
            <i class="fas fa-user-shield mr-1"></i>
            Tu contraseña es personal y confidencial.
        </footer>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.toggle-password').forEach(function (button) {
                button.addEventListener('click', function () {
                    const input = document.getElementById(this.dataset.target);
                    const icon = this.querySelector('i');

                    if (!input || !icon) {
                        return;
                    }

                    const mostrar = input.type === 'password';

                    input.type = mostrar ? 'text' : 'password';

                    icon.classList.toggle('fa-eye', !mostrar);
                    icon.classList.toggle('fa-eye-slash', mostrar);

                    this.setAttribute(
                        'aria-label',
                        mostrar ? 'Ocultar contraseña' : 'Mostrar contraseña'
                    );
                });
            });
        });
    </script>
</body>
</html>
```