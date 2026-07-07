<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Pharmaseek SIMRS' }}</title>
    <style>
        :root{--blue:#075eaa;--blue-2:#dceafe;--dark:#0f1f36;--muted:#667085;--line:#c9d2e3;--bg:#f6f8fd;--card:#fff;--red:#c1121f;--green:#00796b;--amber:#92400e;--soft-red:#ffe7e5;--soft-green:#d8fff2;--soft-amber:#fff2c7;}
        *{box-sizing:border-box} body{margin:0;font-family:Inter,Segoe UI,Arial,sans-serif;background:var(--bg);color:var(--dark)} a{text-decoration:none;color:inherit} .app{display:flex;min-height:100vh}.sidebar{width:280px;background:#f4f7fd;border-right:1px solid var(--line);display:flex;flex-direction:column;position:fixed;inset:0 auto 0 0}.brand{display:flex;gap:12px;align-items:center;padding:30px 26px}.logo{width:42px;height:42px;border-radius:8px;background:var(--blue);color:#fff;display:grid;place-items:center;font-weight:900;font-size:24px}.brand h1{font-size:23px;line-height:1.05;margin:0;color:#07509a}.brand span{font-size:14px;color:#4b5563}.primary-btn{margin:8px 26px 24px;padding:13px 18px;border-radius:4px;background:var(--blue);color:white;text-align:center;font-weight:700;border:0;display:block}.nav{padding:0 18px}.nav a{display:flex;align-items:center;gap:14px;padding:14px 18px;margin:4px 0;border-radius:4px;color:#3f4654;font-size:17px}.nav a.active{background:var(--blue-2);color:#004f97;font-weight:800;border-right:5px solid var(--blue)}.nav small{font-size:20px;width:22px;text-align:center}.side-bottom{margin-top:auto;border-top:1px solid var(--line);padding:20px 18px}.main{margin-left:280px;width:calc(100% - 280px);padding-bottom:44px}.topbar{height:68px;background:white;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;padding:0 26px}.search{width:440px;max-width:46vw;border:1px solid var(--line);background:#f7f9fe;border-radius:5px;padding:12px 16px;color:#667085}.top-actions{display:flex;align-items:center;gap:16px}.logout-form{margin:0}.logout-btn{border:1px solid var(--line);background:#fff;color:#3f4654;border-radius:7px;padding:8px 12px;font-weight:800;cursor:pointer}.emergency{border:1px solid var(--red);color:var(--red);background:white;border-radius:7px;padding:9px 16px;font-weight:800}.icon{font-size:22px}.avatar{width:34px;height:34px;border-radius:50%;display:grid;place-items:center;background:#d5e6fb;color:#00549e;font-weight:800}.content{padding:34px 26px}.page-head{display:flex;align-items:end;justify-content:space-between;gap:20px;margin-bottom:22px}.page-head h2{font-size:36px;margin:0 0 6px}.page-head p{margin:0;color:#4b5563;font-size:17px}.actions{display:flex;gap:10px;flex-wrap:wrap}.btn{padding:11px 16px;border-radius:5px;border:1px solid var(--blue);background:#fff;color:#004f97;font-weight:700;cursor:pointer}.btn.primary{background:var(--blue);color:#fff}.btn.red{background:var(--red);border-color:var(--red);color:#fff}.grid{display:grid;gap:16px}.grid-4{grid-template-columns:repeat(4,minmax(0,1fr))}.grid-3{grid-template-columns:repeat(3,minmax(0,1fr))}.grid-2{grid-template-columns:2fr 1fr}.card{background:var(--card);border:1px solid var(--line);border-radius:8px;overflow:hidden}.card.pad{padding:22px}.card h3{font-size:24px;margin:0 0 14px}.metric-title{text-transform:uppercase;letter-spacing:.08em;color:#6b7280;font-weight:800;font-size:13px}.metric-value{font-size:34px;font-weight:900;margin:26px 0 4px}.muted{color:var(--muted)}.green{color:var(--green)}.red{color:var(--red)}.amber{color:var(--amber)}.head-row{display:flex;align-items:center;justify-content:space-between;padding:20px 22px;border-bottom:1px solid var(--line)}.table{width:100%;border-collapse:collapse}.table th{background:#f2f6ff;color:#667085;text-align:left;font-size:13px;text-transform:uppercase;letter-spacing:.06em;padding:14px 16px}.table td{padding:16px;border-top:1px solid var(--line);vertical-align:middle}.badge{display:inline-block;border-radius:14px;padding:6px 10px;font-weight:800;font-size:12px}.badge.blue{background:#d8eaff;color:#00539b}.badge.red{background:#ffe1de;color:#b91c1c}.badge.green{background:#d5fff1;color:#047857}.badge.amber{background:#fff0c4;color:#a65a00}.status-critical{background:#ffe1de;color:#b91c1c}.status-optimal{background:#d5fff1;color:#047857}.status-warn{background:#ffedd5;color:#9a3412}.footer{position:fixed;left:280px;right:0;bottom:0;height:26px;border-top:1px solid var(--line);background:#eaf3ff;color:#006b68;display:flex;justify-content:space-between;align-items:center;padding:0 26px;font-size:13px;font-weight:800}.form{display:grid;gap:12px}.form-row{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.input, select{width:100%;padding:11px 12px;border:1px solid var(--line);border-radius:5px;background:white;color:var(--dark)} label{font-weight:800;font-size:13px;color:#596174;letter-spacing:.04em}.alert-success{background:#e8fff4;border:1px solid #92e6c1;color:#006b4c;border-radius:7px;padding:12px 14px;margin-bottom:16px}.alert-red{background:#c1121f;color:#fff;border-radius:6px;padding:18px 22px;margin-bottom:22px}.audit{border-left:3px solid #cdd5e5;margin-left:12px;padding-left:16px}.audit-item{margin-bottom:20px;position:relative}.audit-item:before{content:'';position:absolute;left:-24px;top:2px;width:9px;height:9px;border-radius:50%;background:var(--blue)}.bars{height:270px;border:1px solid var(--line);border-radius:5px;background:linear-gradient(to bottom,#eef4ff 0,#eef4ff 25%,#fff 25%,#fff 50%,#eef4ff 50%,#eef4ff 75%,#fff 75%);display:flex;align-items:end;gap:28px;padding:20px 26px}.bar-pair{display:flex;align-items:end;gap:8px;height:100%;flex:1}.bar{width:42px;background:#b8cee8;border-top:3px solid var(--blue);border-radius:2px 2px 0 0}.bar.exp{background:#d5c8c5;border-top-color:#873d05}.mini-bar{height:10px;border-radius:8px;background:#dbe8ff;overflow:hidden}.mini-bar span{display:block;height:100%;background:#0a579e}.mini-bar.red span{background:#c1121f}.mini-bar.green span{background:#00796b}.stack{display:grid;gap:12px}.right-card{padding:20px}.note{background:#fff4cf;border:1px solid #f3b144;color:#9a5100;border-radius:8px;padding:18px}.money{font-variant-numeric:tabular-nums}.screening{display:grid;grid-template-columns:1.35fr .95fr;gap:22px}.checkbox-list div{padding:10px 0;border-bottom:1px solid var(--line)}.checkbox-list input{accent-color:var(--blue)}@media(max-width:1050px){.sidebar{position:static;width:100%}.app{display:block}.main{margin-left:0;width:100%}.footer{left:0}.grid-4,.grid-3,.grid-2,.screening{grid-template-columns:1fr}.topbar{height:auto;padding:16px}.search{max-width:none;width:100%}.page-head{display:block}.content{padding:22px}.sidebar{min-height:auto}.side-bottom{display:none}}
    </style>
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <div class="brand">
            <div class="logo">✚</div>
            <div><h1>Pharmaseek<br>SIMRS</h1><span>Central Pharmacy Depo</span></div>
        </div>
        <a class="primary-btn" href="{{ route('prescriptions.index') }}">＋ New Prescription</a>
        <nav class="nav">
            <a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><small>▦</small>Dashboard</a>
            <a class="{{ request()->routeIs('prescriptions.*') ? 'active' : '' }}" href="{{ route('prescriptions.index') }}"><small>✚</small>Prescriptions</a>
            <a class="{{ request()->routeIs('inventory.*') ? 'active' : '' }}" href="{{ route('inventory.index') }}"><small>▰</small>Inventory</a>
            <a class="{{ request()->routeIs('procurement.*') ? 'active' : '' }}" href="{{ route('procurement.index') }}"><small>🛒</small>Procurement</a>
            <a class="{{ request()->routeIs('billing.*') ? 'active' : '' }}" href="{{ route('billing.index') }}"><small>▣</small>Billing</a>
            <a class="{{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}"><small>▥</small>Reports</a>
        </nav>
        <div class="side-bottom nav">
            <a href="#"><small>◇</small>Emergency Protocols</a>
            <a href="#"><small>▭</small>System Status</a>
        </div>
    </aside>
    <main class="main">
        <div class="topbar">
            <input class="search" placeholder="Search patients, reports, medications...">
            <div class="top-actions">
                <span class="emergency">⚠ Emergency Mode</span>
                <span class="icon">🔔</span><span class="icon">⚙</span><span class="icon">?</span>
                <span class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'PH', 0, 2)) }}</span>
                <form class="logout-form" method="post" action="{{ route('logout') }}">@csrf<button class="logout-btn" type="submit">Logout</button></form>
            </div>
        </div>
        <section class="content">
            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert-red">{{ $errors->first() }}</div>
            @endif
            @yield('content')
        </section>
    </main>
</div>
<footer class="footer"><span>©  RKM linical Pharmacy Systems | v2.4.0-stable</span><span>System Health: Operational &nbsp;&nbsp; Emergency Protocols &nbsp;&nbsp; Help Desk</span></footer>
</body>
</html>
