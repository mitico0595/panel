<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Grupo Berlu Cloud</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{asset('image/thumb.png')}}" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        html,body{height:100%}
        body{
            font-family:Inter,system-ui,-apple-system,"Segoe UI",sans-serif;
            display:flex;
            align-items:center;
            justify-content:center;
            background:
                radial-gradient(circle at 20% 20%, rgba(191,219,254,.9), transparent 55%),
                radial-gradient(circle at 80% 0%, rgba(244,244,245,.9), transparent 55%),
                linear-gradient(to bottom,#dbeafe,#eff6ff 40%,#e0f2fe);
            color:#0f172a;
            padding:16px;
        }

        .shell{
            position:relative;
            max-width:420px;
            width:100%;
        }

        .card{
            background:rgba(255,255,255,0.92);
            border-radius:26px;
            padding:26px 26px 22px;
            box-shadow:0 24px 60px rgba(15,23,42,.18);
            border:1px solid rgba(226,232,240,.9);
            backdrop-filter:blur(20px);
        }

        .card-header{
            display:flex;
            flex-direction:column;
            align-items:center;
            gap:10px;
            margin-bottom:18px;
        }

        .icon-wrap{
            width:70px;
            height:70px;
            border-radius:40px;
            background:linear-gradient(145deg,#eef2ff,#e0f2fe);
            display:flex;
            align-items:center;
            justify-content:center;
            box-shadow:0 14px 30px rgba(148,163,184,.35);
            overflow:hidden;
        }
        .icon-inner{
            width:34px;
            height:34px;
            border-radius:14px;
            background:#111827;
            display:flex;
            align-items:center;
            justify-content:center;
            color:#e5e7eb;
            font-size:18px;
        }

        h1{
            font-size:20px;
            font-weight:700;
        }
        .subtitle{
            font-size:12px;
            color:#6b7280;
            text-align:center;
            max-width:260px;
        }

        .error{
            background:#fee2e2;
            border-radius:10px;
            border:1px solid #fecaca;
            color:#b91c1c;
            font-size:12px;
            padding:8px 10px;
            margin-bottom:10px;
        }

        .field{
            margin-bottom:10px;
        }
        .field-label{
            font-size:12px;
            color:#6b7280;
            margin-bottom:4px;
        }
        .field-inner{
            position:relative;
        }
        .field-icon{
            position:absolute;
            left:10px;
            top:50%;
            transform:translateY(-50%);
            font-size:13px;
            color:#9ca3af;
        }
        input{
            width:100%;
            border-radius:12px;
            border:1px solid #e5e7eb;
            background:#f9fafb;
            padding:9px 10px 9px 30px;
            font-size:13px;
            color:#111827;
        }
        input:focus{
            outline:none;
            border-color:#6366f1;
            background:#ffffff;
            box-shadow:0 0 0 1px rgba(129,140,248,.5);
        }

        .actions{
            margin-top:12px;
        }
        .btn-primary{
            width:100%;
            border-radius:999px;
            border:none;
            padding:10px 12px;
            font-size:13px;
            font-weight:600;
            cursor:pointer;
            background:linear-gradient(145deg,#111827,#020617);
            color:#f9fafb;
            box-shadow:0 14px 30px rgba(15,23,42,.45);
        }
        .btn-primary:hover{
            filter:brightness(1.03);
        }

        .hint{
            margin-top:10px;
            font-size:11px;
            color:#9ca3af;
            text-align:center;
        }
        .hint code{
            background:#f3f4f6;
            padding:2px 5px;
            border-radius:6px;
            font-size:11px;
        }

        .footer-note{
            margin-top:10px;
            font-size:10px;
            color:#a1a1aa;
            text-align:center;
        }

        @media (max-width:480px){
            .card{
                padding:22px 18px 18px;
                border-radius:22px;
            }
        }
    </style>
</head>
<body>
<div class="shell">
    <div class="card">
        <div class="card-header">
            <div class="icon-wrap">
                <img src="{{asset('image/logo.png')}}" alt="" style="width:100%">
            </div>
            <h1>Acceso a la nube</h1>
            <div class="subtitle">
                Control Cloud
            </div>
        </div>

        @if ($errors->any())
            <div class="error">
                Usuario o contraseña incorrectos.
            </div>
        @endif

        <form method="POST" action="{{ route('login.perform') }}">
            @csrf

            <div class="field">
                <div class="field-label">Usuario</div>
                <div class="field-inner">
                    <span class="field-icon">@</span>
                    <input
                        type="text"
                        name="username"
                        id="username"
                        value=""
                        autocomplete="off"
                        autofocus
                    >
                </div>
            </div>

            <div class="field">
                <div class="field-label">Contraseña</div>
                <div class="field-inner">
                    <span class="field-icon">• •</span>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        autocomplete="off"
                    >
                </div>
            </div>

            <div class="actions">
                <button class="btn-primary" type="submit">
                    Entrar al panel
                </button>
            </div>
        </form>

        
        <div class="footer-note">
            Panel de monitoreo empresarial
        </div>
    </div>
</div>
</body>
</html>
