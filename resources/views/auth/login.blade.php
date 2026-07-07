<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Pharmaseek SIMRS</title>
    <style>
        :root{--blue:#075eaa;--dark:#0f1f36;--muted:#667085;--line:#c9d2e3;--bg:#f6f8fd;--red:#c1121f;--green:#00796b}
        *{box-sizing:border-box} body{margin:0;font-family:Inter,Segoe UI,Arial,sans-serif;background:linear-gradient(135deg,#eef5ff,#f8fbff);color:var(--dark);min-height:100vh;display:grid;place-items:center;padding:24px}.login-card{width:100%;max-width:980px;display:grid;grid-template-columns:1.1fr .9fr;background:white;border:1px solid var(--line);border-radius:18px;box-shadow:0 20px 60px rgba(15,31,54,.12);overflow:hidden}.hero{background:#075eaa;color:white;padding:54px}.logo{width:58px;height:58px;border-radius:12px;background:white;color:#075eaa;display:grid;place-items:center;font-weight:900;font-size:30px;margin-bottom:24px}.hero h1{font-size:38px;line-height:1.08;margin:0 0 14px}.hero p{font-size:17px;line-height:1.6;opacity:.92}.hero ul{margin-top:30px;padding-left:20px;line-height:2}.form-side{padding:54px}.form-side h2{font-size:32px;margin:0 0 8px}.muted{color:var(--muted)}.form{display:grid;gap:14px;margin-top:28px}label{font-weight:800;font-size:13px;color:#596174;letter-spacing:.04em}.input{width:100%;padding:13px 14px;border:1px solid var(--line);border-radius:8px;background:#f8fbff;color:var(--dark);font-size:15px}.btn{border:0;background:var(--blue);color:white;border-radius:8px;padding:13px 18px;font-weight:800;font-size:16px;cursor:pointer}.alert{padding:12px 14px;border-radius:8px;margin-top:16px}.alert.error{background:#ffe7e5;color:var(--red);border:1px solid #ffb3ad}.alert.success{background:#e8fff4;color:var(--green);border:1px solid #92e6c1}.row{display:flex;justify-content:space-between;align-items:center;gap:12px}.sample{margin-top:22px;padding:14px;border-radius:8px;background:#f2f6ff;border:1px solid var(--line);font-size:14px}.code{font-family:Consolas,monospace;font-weight:800}@media(max-width:850px){.login-card{grid-template-columns:1fr}.hero{padding:34px}.form-side{padding:34px}}
    </style>
</head>
<body>
<div class="login-card">
    <section class="hero">
        <div class="logo">✚</div>
        <h1>Pharmaseek SIMRS</h1>
        <p>Sistem informasi rumah sakit khusus depo farmasi. Modul utama mencakup skrining resep, manajemen stok, procurement PBF, billing BPJS, dan laporan analisis.</p>
        <ul>
            <li>Login petugas farmasi</li>
            <li>Dashboard operasional</li>
            <li>Alur resep sampai billing</li>
            <li>Laporan analisis pendapatan dan stok</li>
        </ul>
    </section>
    <section class="form-side">
        <h2>Login</h2>
        <p class="muted">Masuk sebagai admin/apoteker untuk mengelola data farmasi.</p>

        @if(session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert error">{{ $errors->first() }}</div>
        @endif

        <form class="form" method="POST" action="{{ route('login.process') }}">
            @csrf
            <div>
                <label>Email</label>
                <input class="input" type="email" name="email" value="{{ old('email', 'admin@pharmaseek.test') }}" required autofocus>
            </div>
            <div>
                <label>Password</label>
                <input class="input" type="password" name="password" placeholder="Masukkan password" required>
            </div>
            <div class="row">
                <label style="display:flex;gap:8px;align-items:center;text-transform:none;letter-spacing:0;font-weight:600"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
            </div>
            <button class="btn" type="submit">Masuk ke Sistem</button>
        </form>

        <div class="sample">
            Akun demo:<br>
            Email: <span class="code">admin@pharmaseek.test</span><br>
            Password: <span class="code">password</span>
        </div>
    </section>
</div>
</body>
</html>
