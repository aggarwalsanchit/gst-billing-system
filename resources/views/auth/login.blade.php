<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — Billing System</title>

    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />

    <style>
        html, body {
            height: 100%;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 420px;
            padding: 40px 35px;
        }

        .login-card h1 {
            font-size: 24px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 8px;
            color: #333;
        }

        .login-card .subtitle {
            text-align: center;
            color: #999;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .login-card .form-label {
            font-weight: 600;
            font-size: 13px;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .login-card .form-control {
            height: 48px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            font-size: 15px;
            padding: 10px 14px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .login-card .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        }

        .login-card .btn-login {
            height: 48px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: #fff;
            width: 100%;
            transition: opacity 0.2s;
        }

        .login-card .btn-login:hover {
            opacity: 0.9;
            color: #fff;
        }

        .login-card .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .alert {
            border-radius: 8px;
            font-size: 14px;
        }

        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #666;
        }

        .footer-note {
            text-align: center;
            font-size: 12px;
            color: #aaa;
            margin-top: 25px;
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">

        <h1>Billing System</h1>
        <p class="subtitle">Sign in to continue</p>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}" id="loginForm">
            @csrf

            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input type="email"
                       id="email"
                       name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}"
                       autocomplete="username"
                       autofocus
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label" for="password">Password</label>
                <input type="password"
                       id="password"
                       name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       autocomplete="current-password"
                       required>
            </div>

            <div class="remember-row">
                <input type="checkbox" id="remember" name="remember" value="1">
                <label for="remember" class="mb-0">Remember me</label>
            </div>

            <button type="submit" class="btn btn-login" id="loginBtn">
                Sign In
            </button>
        </form>

        <p class="footer-note">
            &copy; {{ date('Y') }} — Authorised access only
        </p>
    </div>
</div>

<script>
    document.getElementById('loginForm').addEventListener('submit', function () {
        const btn = document.getElementById('loginBtn');
        btn.disabled = true;
        btn.textContent = 'Signing in...';
    });
</script>

</body>
</html>