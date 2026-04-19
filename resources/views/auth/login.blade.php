<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Toko Sembako</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@300;400;600&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Lato', sans-serif;
            background-color: #d4f4d4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .login-box {
            background-color: #c5d9c5;
            padding: 80px 60px;
            border-radius: 30px;
            width: 95%;
            max-width: 1200px;
            min-height: 85vh;
            display: flex;
            gap: 80px;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }
        .left-section {
            flex: 1;
        }
        .right-section {
            flex: 0 0 0px;
            position: relative;
            text-align: center;
        }
        .logo-placeholder {
            width: 600px;
            height: 600px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            right: -40px;
            top: 50%;
            transform: translateY(-50%);
        }

        /* Form wrapper untuk mengatur alignment */
        .form-wrapper {
            /* Lebar = label (140px) + input (320px) = 460px */
            width: 460px;
        }

        h1 {
            color: #4a5d4a;
            font-family: 'Playfair Display', serif;
            font-size: 4em;
            margin-bottom: 60px;
            font-weight: 400;
            letter-spacing: 4px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 35px;
            display: flex;
            align-items: center;
        }
        label {
            display: inline-block;
            width: 140px;
            flex-shrink: 0;
            color: #4a5d4a;
            font-family: 'Lato', sans-serif;
            font-size: 1.2em;
            font-weight: 700;
            letter-spacing: 1px;
        }
        input[type="text"],
        input[type="password"] {
            width: 320px;
            padding: 14px 18px;
            border: 2px solid #4a5d4a;
            border-radius: 5px;
            font-family: 'Lato', sans-serif;
            font-size: 1.05em;
            background-color: white;
        }

        /* Tombol sejajar dengan textbox: margin-left = lebar label */
        .button-group {
            display: flex;
            gap: 0px;
            margin-top: 50px;
            margin-left: 140px; /* sama dengan lebar label */
            width: 320px;       /* sama dengan lebar input */
            justify-content: space-between;
        }
        button {
            padding: 15px 0;
            width: 47%;
            border: 2px solid #4a5d4a;
            border-radius: 5px;
            font-family: 'Lato', sans-serif;
            font-size: 1.1em;
            cursor: pointer;
            color: #4a5d4a;
            font-weight: 700;
            letter-spacing: 2px;
            background-color: #a8c5a8;
        }
        button:hover {
            opacity: 0.8;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="left-section">
            <div class="form-wrapper">
                <h1>LOGIN</h1>

                @if ($errors->has('login'))
                    <div class="error-message">
                        {{ $errors->first('login') }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="success-message">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="error-message">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus>
                    </div>
                    @error('username')
                        <div style="margin-left: 140px; margin-top: -25px; margin-bottom: 15px;">
                            <small style="color: red;">{{ $message }}</small>
                        </div>
                    @enderror

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    @error('password')
                        <div style="margin-left: 140px; margin-top: -25px; margin-bottom: 15px;">
                            <small style="color: red;">{{ $message }}</small>
                        </div>
                    @enderror

                    <div class="button-group">
                        <button type="submit">LOGIN</button>
                        <button type="reset">CANCEL</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="right-section">
            <div class="logo-placeholder">
                <img src="{{ asset('images/logo-sembako.webp') }}"
                     alt="Logo Toko Sembako"
                     style="width: 100%; height: 100%; object-fit: contain;">
            </div>
        </div>
    </div>
</body>
</html>