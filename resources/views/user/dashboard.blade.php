<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            color: #1f2937;
        }
        .wrap {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            width: 100%;
            max-width: 560px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 12px 40px rgba(15, 23, 42, 0.08);
        }
        h1 {
            margin: 0 0 8px;
            font-size: 28px;
        }
        p {
            margin: 0 0 16px;
            line-height: 1.6;
            color: #4b5563;
        }
        .actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }
        .btn {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
        }
        .btn-primary {
            background: #2563eb;
            color: #ffffff;
        }
        .btn-secondary {
            background: #eef2ff;
            color: #1e40af;
        }
        form {
            margin: 0;
        }
        button {
            border: 0;
            cursor: pointer;
        }
    </style>
</head>
<body>
    @php
        $currentUser = $user ?? auth()->user();
    @endphp

    @if (!$currentUser)
        <script>window.location = "{{ route('login') }}";</script>
    @else
    <div class="wrap">
        <div class="card">
            <h1>Selamat datang, {{ $currentUser->name }}</h1>
            <p>Anda berhasil login. Halaman ini menggantikan welcome bawaan Laravel supaya alur aplikasi langsung fokus ke sistem login.</p>
            <p>Email: {{ $currentUser->email }}</p>

            <div class="actions">
                <a class="btn btn-secondary" href="{{ route('home') }}">Refresh</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">Logout</button>
                </form>
            </div>
        </div>
    </div>
    @endif
</body>
</html>
