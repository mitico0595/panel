<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Grupo Berlu Cloud</title>
    <link rel="shortcut icon" href="{{asset('image/thumb.png')}}" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=dashboard" />
    <style>
        :root{
            --bg:#f5f7fb;
            --panel:#ffffff;
            --muted:#8b95b2;
            --text:#111827;
            --sub:#0f172a;
            --border:#e5e7eb;
            --shadow:0 18px 40px rgba(15,23,42,.08);
            --radius:18px;
            --accent:#6366f1;
            --accent-soft:#eef2ff;
            --good:#16a34a;
            --bad:#e11d48;
            --chip-bg:#f3f4f6;
        }
        *{box-sizing:border-box}
        input:focus-visible{outline:none}
        html,body{height:100%}
        body{
            margin:0;
            font-family:Inter,system-ui,-apple-system,"Segoe UI",sans-serif;
            background:#fefefe;
            color:var(--text);
        }
        a{color:inherit;text-decoration:none}
        input,select,button{font-family:inherit}

        .app{
            min-height:100vh;
            display:flex;
            flex-direction:column;
        }

        /* TOPBAR */
        .topbar{
            position:sticky;
            top:0;
            z-index:10;
            
            
            
        }
        .top-inner{
            
            margin:0 auto;
            padding:10px 18px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:12px;
            border: 1px solid rgba(226,232,240,.8);
            margin-top:20px;
            border-radius:10px;
            backdrop-filter:blur(8px);
            background:rgba(256, 256, 256, .4);
            width:95%;
        }
        .top-left{
            display:flex;
            flex-direction:column;
            gap:2px;
        }
        .page-title{
            font-weight:800;
            font-size:18px;
            color:var(--sub);
        }
        .page-sub{
            font-size:9px;
            color:#9c9c9c;
        }
        .top-right{
            display:flex;
            align-items:center;
            gap:10px;
        }
        .clock{
            font-size:12px;
            color:var(--muted);
            min-width:80px;
            text-align:right;
        }
        .conn-pill{
            font-size:11px;
            padding:4px 10px;
            border-radius:999px;
            border:1px solid transparent;
            white-space:nowrap;
        }
        .conn-on{
            background:#dcfce7;
            color:#166534;
            border-color:#bbf7d0;
        }
        .conn-off{
            background:#fee2e2;
            color:#b91c1c;
            border-color:#fecaca;
        }
        .logout-btn{
            border-radius:999px;
            border:1px solid #e5e7eb;
            background:#fff;
            padding:6px 12px;
            font-size:12px;
            cursor:pointer;
            color:#374151;
        }

        /* TOOLBAR */
        .toolbar{
            max-width:1200px;
            margin:12px auto 4px;
            padding:0 18px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:12px;
            flex-wrap:wrap;
        }
        .search-wrap{
            position:relative;
            flex:1 1 260px;
            max-width:420px;
        }
        .search{
            width:100%;
            background:#fff;
            border:1px solid #d1d5db;
            border-radius:999px;
            padding:12px 38px 12px 19px;
            font-size:13px;
            box-shadow:0 14px 30px rgba(148,163,184,.25);
        }
        .search-ico{
            position:absolute;
            right:8px;
            top:50%;
            transform:translateY(-50%);
            width:26px;
            height:26px;
            border-radius:999px;
            background:linear-gradient(135deg,#6366f1,#8b5cf6);
            display:grid;
            place-items:center;
            color:#fff;
            font-size:14px;
        }
        .btn-ghost{
            border-radius:999px;
            border:1px solid #e5e7eb;
            background:#fff;
            padding:7px 12px;
            font-size:12px;
            cursor:pointer;
            color:#374151;
            display:flex;
            align-items:center;
            gap:6px;
        }

        .toolbar-info{
            font-size:12px;
            color:var(--muted);
            white-space:nowrap;
        }
        .toolbar-dot{
            width:6px;height:6px;border-radius:999px;background:#4ade80;display:inline-block;margin-right:4px;
        }

        /* LAYOUT CENTRAL */
        .layout{
            
            margin:0 auto 24px;
            width:95%;
            display:grid;
            grid-template-columns:minmax(0,1.9fr) minmax(0,1.5fr);
            gap:18px;
            margin-top: 53px;
        }
        @media (max-width:960px){
            .layout{grid-template-columns:1fr;}
        }

        /* TABLA DE VENTAS */
        .table-panel{
           
            padding:10px 10px 12px;
        }
        .thead{
            display:grid;
            grid-template-columns:24px 80px 120px 1fr 90px 150px 110px;
            gap:10px;
            padding:4px 10px 8px;
            color:var(--muted);
            font-size:11px;
        }
        .rows{
            display:flex;
            flex-direction:column;
            gap:6px;
        }
        .row{
            cursor:pointer;
            background:linear-gradient(to right, #ffe7e7, #e8d4ff);
            border-radius:14px;
            display:grid;
            grid-template-columns:24px 80px 120px 1fr 90px 150px 110px;
            align-items:center;
            gap:10px;
            padding:16px 10px;
            border:1px solid transparent;
            transition:background .15s,box-shadow .15s,border-color .15s,transform .08s;
        }
        .row:hover{
            background:#f4f4ff;
            box-shadow:0 10px 26px rgba(148,163,184,.3);
            transform:translateY(-1px);
        }
        .row.is-active{
            background:#f5f3ff;
            border-color:#a5b4fc;
            box-shadow:0 12px 30px rgba(129,140,248,.4);
        }
        .row.is-active::before{
            content:'';
            position:absolute;
            left:-3px;
            top:10px;
            bottom:10px;
            width:3px;
            border-radius:999px;
            
        }
        .row-wrap{
            position:relative;
        }
        .dot{
            width:0px;height:8px;border-radius:999px;background:#cbd5f5;
        }
        .badge-origen{
            font-size:11px;
            padding:3px 8px;
            border-radius:999px;
            background:#fee2e2;
            color:#b91c1c;
            font-weight:600;
        }
        .badge-origen.amigu{
            background:#e0f2fe;
            color:#0369a1;
        }
        .code{
            font-weight:700;
            font-size:13px;
            color:#111827;
        }
        .dest{
            font-size:12px;
            color:#4b5563;
        }
        .total{
            font-weight:700;
            font-size:13px;
        }
        .chip{
            font-size:11px;
            padding:3px 7px;
            border-radius:999px;
            border:1px solid #e5e7eb;
            background:var(--chip-bg);
            display:inline-flex;
            align-items:center;
            gap:4px;
        }
        .chip.paid{border-color:#22c55e;color:#15803d;background:#dcfce7;}
        .chip.pending{border-color:#eab308;color:#92400e;background:#fef9c3;}
        .chip.refunded{border-color:#0ea5e9;color:#0369a1;background:#e0f2fe;}
        .chip.delivered{border-color:#22c55e;color:#15803d;background:#dcfce7;}
        .chip.in_transit{border-color:#0ea5e9;color:#0369a1;background:#e0f2fe;}
        .chip.ready_for_pickup,
        .chip.ready_to_ship{border-color:#a855f7;color:#6b21a8;background:#f3e8ff;}
        .chip.cancelled{border-color:#fecaca;color:#b91c1c;background:#fee2e2;}
        .chip.returned{border-color:#e0e7ff;color:#4338ca;background:#e0e7ff;}

        .empty{
            font-size:13px;
            color:var(--muted);
            text-align:center;
            padding:18px 8px;
        }
        .row-time{
            font-size:11px;
            color:#9ca3af;
        }

        /* ASIDE DETALLE */
        .aside{
            display:flex;
            flex-direction:column;
            gap:10px;
        }
        .right-title{
            font-size:13px;
            font-weight:600;
            color:var(--sub);
            margin-left:2px;
        }
        .right-empty{
            font-size:13px;
            color:#9ca3af;
            margin-top:6px;
            padding:10px 12px;
            border-radius:14px;
            border:1px dashed #e5e7eb;
            background:#f9fafb99;
        }
        .card{
            background:#fff;
            border-radius:var(--radius);
            border:1px solid #e5e7eb;
            box-shadow:var(--shadow);
            padding:12px 14px;
            font-size:13px;
        }
        .field-label{font-size:11px;color:#9ca3af;margin-bottom:2px;}
        .input, select{
            width:100%;
            border-radius:8px;
            border:1px solid #d1d5db;
            padding:10px 8px;
            font-size:12px;
            background:#fff;
        }
        .mt-1{margin-top:4px;}
        .mt-2{margin-top:8px;}
        .mt-3{margin-top:12px;}
        .flex{display:flex;}
        .gap-2{gap:8px;}
        .gap-3{gap:12px;}
        .items-center{align-items:center;}
        .justify-between{justify-content:space-between;}
        .w-1-2{width:50%;}
        .btn-primary{
            background:linear-gradient(135deg,#22c55e,#16a34a);
            color:#ecfdf5;
            border-radius:8px;
            border:none;
            padding:6px 10px;
            font-size:12px;
            cursor:pointer;
        }
        .btn-secondary{
            background:#0f172a;
            color:#e5e7eb;
            border-radius:8px;
            border:1px solid #4b5563;
            padding:6px 10px;
            font-size:12px;
            cursor:pointer;
        }
        .tag{
            font-size:10px;
            padding:2px 5px;
            border-radius:999px;
            border:1px solid #e5e7eb;
            margin-right:4px;
        }
                /* Grids reutilizables para formularios */
        .grid-2{
            display:grid;
            grid-template-columns:repeat(2,minmax(0,1fr));
            gap:8px;
            border-top: 1px solid #e8e8e8;
            margin-top: 12px;
            padding-top: 12px;
        }
        .grid-3{
            display:grid;
            grid-template-columns:repeat(3,minmax(0,1fr));
            gap:8px;
        }

        @media(max-width:900px){
            .grid-2,
            .grid-3{
                grid-template-columns:1fr;
            }
        }

        @media(max-width:900px){
            .thead{
                display:none;
            }
            .row{
                grid-template-columns:1.2fr 1fr;
                grid-auto-rows:auto;
            }
        }
    </style>
    <style>
.material-symbols-outlined {
  font-variation-settings:
  'FILL' 0,
  'wght' 400,
  'GRAD' 0,
  'opsz' 24
}
</style>
</head>
<body>
<div class="app">

    {{-- TOPBAR --}}
    <header class="topbar">
        <div class="top-inner">
            
            <div class="top-left" style="display:flex;flex-direction:row;gap:10px">
                <div class="top-left" ><span class="material-symbols-outlined " style="line-height:36px">dashboard</span></div>
                <div>
                <div class="page-title">Panel de ventas en tiempo real</div>
                <div class="page-sub">Ventas activas de todo el grupo empresarial</div>
                </div>
            </div>
            <div class="search-wrap">
                <input id="search" class="search" placeholder="Buscar por código, nombre, correo…">
                <span class="search-ico">⌕</span>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                <div class="toolbar-info">
                    <span class="toolbar-dot"></span>
                    <span id="total-count">0 ventas activas</span>
                </div>
                
            </div>
            <div class="top-right">
                <div id="clock" class="clock">--:--:--</div>
                <div id="conn-pill" class="conn-pill conn-on">Conexión estable (ON)</div>
                <form method="POST" action="{{ route('logout.perform') }}">
                    @csrf
                    <button type="submit" class="logout-btn">Cerrar sesión</button>
                </form>
            </div>
        </div>
    </header>

    {{-- TOOLBAR --}}
    <section class="toolbar">
        
    </section>

    {{-- LAYOUT CENTRAL --}}
    <section class="layout">
        {{-- TABLA --}}
        <div class="table-panel">
            <div class="thead">
                <div></div>
                <div>Origen</div>
                <div>Código</div>
                <div>Cliente</div>
                <div>Total</div>
                <div>Pago / Proceso</div>
                <div>Envío</div>
            </div>
            <div class="rows" id="tabla-ventas"></div>
        </div>

        {{-- DETALLE --}}
        <aside class="aside">
            <div class="right-title">Detalle de la venta</div>
            <div id="detalle-empty" class="right-empty">
                Selecciona una fila de la tabla para ver y editar los datos de la venta, el proceso de fulfillment y el envío.
            </div>
            <div id="detalle-container" style="display:none;"></div>
        </aside>
    </section>
</div>

<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const URL_DATA   = "{{ route('panel.ventas.data') }}";
    const URL_SHOW   = "{{ route('panel.ventas.show',['origen'=>'__O__','id'=>'__ID__']) }}";
    const URL_VUPD   = "{{ route('panel.ventas.update',['origen'=>'__O__','id'=>'__ID__']) }}";
    const URL_VFUL   = "{{ route('panel.ventas.fulfillment',['origen'=>'__O__','id'=>'__ID__']) }}";
    const URL_SMETA  = "{{ route('panel.shipments.meta',['origen'=>'__O__','id'=>'__ID__']) }}";
    const URL_SSTAT  = "{{ route('panel.shipments.status',['origen'=>'__O__','id'=>'__ID__']) }}";

    function buildUrl(template, origen, id) {
        return template.replace('__O__', origen).replace('__ID__', id);
    }

    let currentVenta = null;

    // Traducciones visibles
    const paymentLabels = {
        pending: 'Pendiente',
        paid: 'Pagado',
        refunded: 'Reembolsado'
    };
    const fulfillmentLabels = {
        pending: 'Pendiente',
        ready_for_pickup: 'Listo Recojo',
        ready_to_ship: 'Listo Envio',
        in_transit: 'En tránsito',
        delivered: 'Entregado',
        cancelled: 'Cancelado',
        returned: 'Devuelto'
    };
    const shipmentLabels = {
        label_created: 'Etiqueta creada',
        pickup_ready: 'Listo Recojo',
        in_transit: 'En tránsito',
        out_for_delivery: 'En reparto',
        delivered: 'Entregado',
        failed: 'Fallido',
        returned: 'Devuelto',
        pending: 'Pendiente'
    };

    function setConnection(ok) {
        const pill = document.getElementById('conn-pill');
        if (!pill) return;
        if (ok) {
            pill.textContent = 'Conexión estable (ON)';
            pill.classList.remove('conn-off');
            pill.classList.add('conn-on');
        } else {
            pill.textContent = 'Desconectado (OFF)';
            pill.classList.remove('conn-on');
            pill.classList.add('conn-off');
        }
    }

    async function loadVentas(force) {
        try {
            const q = document.getElementById('search').value || '';
            const res = await fetch(URL_DATA + '?q=' + encodeURIComponent(q), {
                headers:{'Accept':'application/json'}
            });
            if (!res.ok) throw new Error('Error HTTP');
            const ventas = await res.json();
            renderTabla(ventas);
            setConnection(true);
        } catch (e) {
            console.error(e);
            setConnection(false);
        }
    }

    function renderTabla(ventas) {
        const cont = document.getElementById('tabla-ventas');
        cont.innerHTML = '';

        const totalSpan = document.getElementById('total-count');
        if (totalSpan) {
            const n = ventas ? ventas.length : 0;
            totalSpan.textContent = n + (n === 1 ? ' venta activa' : ' ventas activas');
        }

        if (!ventas || !ventas.length) {
            const div = document.createElement('div');
            div.className = 'empty';
            div.textContent = 'No hay ventas abiertas. Disfruta este raro momento de paz.';
            cont.appendChild(div);
            return;
        }

        ventas.forEach(v => {
            const rowWrap = document.createElement('div');
            rowWrap.className = 'row-wrap';

            const row = document.createElement('div');
            row.className = 'row venta-row';
            row.dataset.origen = v.origen;
            row.dataset.id = v.id;
            row.onclick = () => abrirDetalle(v.origen, v.id);

            const dot = document.createElement('div');
            dot.className = 'dot';

            const tdOrigen = document.createElement('div');
            const b = document.createElement('span');
            b.classList.add('badge-origen');
            if (v.origen === 'amigurumis') b.classList.add('amigu');
            b.textContent = v.origen.toUpperCase();
            tdOrigen.appendChild(b);

            const tdCodigo = document.createElement('div');
            tdCodigo.className = 'code';
            tdCodigo.textContent = v.codigo || '';

            const tdCliente = document.createElement('div');
            tdCliente.className = 'dest';
            tdCliente.textContent = v.cliente || v.email || '';

            const tdTotal = document.createElement('div');
            tdTotal.className = 'total';
            tdTotal.textContent = 'S/ ' + Number(v.total_venta || 0).toFixed(2);

            const tdEstados = document.createElement('div');
            const chipPay = document.createElement('span');
            chipPay.classList.add('chip');
            const pay = v.payment_status || 'pending';
            chipPay.textContent = paymentLabels[pay] || pay;
            chipPay.classList.add(pay);

            const chipFul = document.createElement('span');
            chipFul.classList.add('chip');
            const ful = v.fulfillment_status || 'pending';
            chipFul.textContent = fulfillmentLabels[ful] || ful;
            chipFul.classList.add(ful);
            chipFul.style.marginLeft = '4px';

            tdEstados.appendChild(chipPay);
            tdEstados.appendChild(chipFul);

            const tdShip = document.createElement('div');
            const chipShip = document.createElement('span');
            chipShip.classList.add('chip');
            const sst = v.shipment_status || 'pending';
            chipShip.textContent = shipmentLabels[sst] || sst;
            chipShip.classList.add(sst);
            tdShip.appendChild(chipShip);

            const tdFecha = document.createElement('div');
            tdFecha.className = 'row-time';
            tdFecha.textContent = v.fecha_hora || '';

            row.appendChild(dot);
            row.appendChild(tdOrigen);
            row.appendChild(tdCodigo);
            row.appendChild(tdCliente);
            row.appendChild(tdTotal);
            row.appendChild(tdEstados);
            row.appendChild(tdShip);

            rowWrap.appendChild(row);
            rowWrap.appendChild(tdFecha);

            cont.appendChild(rowWrap);
        });

        marcarSeleccion();
    }

    function marcarSeleccion() {
        const rows = document.querySelectorAll('.venta-row');
        rows.forEach(r => {
            const match = currentVenta &&
                r.dataset.origen === currentVenta.origen &&
                r.dataset.id === String(currentVenta.id);
            r.classList.toggle('is-active', !!match);
            if (r.parentElement) {
                r.parentElement.classList.toggle('is-active', !!match);
            }
        });
    }

    async function abrirDetalle(origen,id) {
        currentVenta = { origen, id };
        marcarSeleccion();

        try {
            const url = buildUrl(URL_SHOW, origen, id);
            const res = await fetch(url, { headers:{'Accept':'application/json'} });
            if (!res.ok) throw new Error('Error HTTP');
            const data = await res.json();
            renderDetalle(data);
        } catch (e) {
            console.error(e);
            alert('Error cargando detalle');
        }
    }

        function renderDetalle(data) {
        document.getElementById('detalle-empty').style.display = 'none';
        const cont = document.getElementById('detalle-container');
        cont.style.display = 'block';

        const v = data.venta;
        const s = data.shipment;

        cont.innerHTML = `
            <!-- Resumen arriba -->
            <div class="card">
                <div class="flex justify-between items-center">
                    <div>
                        <div style="font-size:14px;font-weight:600;">
                            ${data.origen.toUpperCase()} · ${v.codigo}
                        </div>
                        <div style="font-size:12px;color:#9ca3af;">
                            ${v.nombre || ''} ${v.apellido || ''} · ${v.email || ''}
                        </div>
                    </div>
                    <div style="text-align:right;font-size:12px;">
                        <div>Total: <strong>S/ ${Number(v.total_venta || 0).toFixed(2)}</strong></div>
                        <div style="color:#9ca3af;">${v.fecha_hora || ''}</div>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 mt-3">
                <!-- CARD 1: Cliente / pago -->
                <div class="card w-1-2">
                    <div style="font-size:13px;font-weight:500;margin-bottom:6px;">Datos del cliente y pago</div>

                    <!-- Nombre + Apellido misma fila -->
                    <div class="grid-2">
                        <div>
                            <div class="field-label">Nombre</div>
                            <input id="v-nombre" class="input" value="${v.nombre || ''}">
                        </div>
                        <div>
                            <div class="field-label">Apellido</div>
                            <input id="v-apellido" class="input" value="${v.apellido || ''}">
                        </div>
                    </div>

                    <!-- Correo una sola línea -->
                    <div class="mt-1">
                        <div class="field-label">Correo electrónico</div>
                        <input id="v-email" class="input" value="${v.email || ''}">
                    </div>

                    <!-- Celular + Referencia misma fila -->
                    <div class="mt-1 grid-2">
                        <div>
                            <div class="field-label">Celular</div>
                            <input id="v-celular" class="input" value="${v.celular || ''}">
                        </div>
                        <div>
                            <div class="field-label">Referencia</div>
                            <input id="v-referencia" class="input" value="${v.referencia || ''}">
                        </div>
                    </div>

                    <!-- Dirección en una línea -->
                    <div class="mt-1">
                        <div class="field-label">Dirección</div>
                        <input id="v-domicilio" class="input" value="${v.domicilio || ''}">
                    </div>

                    <!-- Distrito / Provincia / Departamento en la misma fila -->
                    <div class="mt-1 grid-3">
                        <div>
                            <div class="field-label">Distrito</div>
                            <input id="v-distrito" class="input" value="${v.distrito || ''}">
                        </div>
                        <div>
                            <div class="field-label">Provincia</div>
                            <input id="v-provincia" class="input" value="${v.provincia || ''}">
                        </div>
                        <div>
                            <div class="field-label">Departamento</div>
                            <input id="v-departamento" class="input" value="${v.departamento || ''}">
                        </div>
                    </div>

                    <!-- Estado de pago + modo de cumplimiento en una fila -->
                    <div class="mt-1 grid-2">
                        <div>
                            <div class="field-label">Estado de pago</div>
                            <select id="v-payment" class="input">
                                <option value="">(sin cambio)</option>
                                <option value="pending"   ${v.payment_status==='pending'?'selected':''}>Pendiente</option>
                                <option value="paid"      ${v.payment_status==='paid'?'selected':''}>Pagado</option>
                                <option value="refunded"  ${v.payment_status==='refunded'?'selected':''}>Reembolsado</option>
                            </select>
                        </div>
                        <div>
                            <div class="field-label">Modo de cumplimiento</div>
                            <select id="v-method" class="input">
                                <option value="">(automático)</option>
                                <option value="pickup"   ${v.fulfillment_method==='pickup'?'selected':''}>Recojo en tienda</option>
                                <option value="shipping" ${v.fulfillment_method==='shipping'?'selected':''}>Envío</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button class="btn-primary" onclick="guardarVenta()">Guardar datos</button>
                    </div>
                </div>

                <!-- CARD 2: Fulfillment + envío -->
                <div class="card w-1-2">
                    <div style="font-size:13px;font-weight:500;margin-bottom:6px;">Fulfillment y envío</div>

                    <div class="field-label">Estado del fulfillment</div>
                    <select id="f-to" class="input mt-1">
                        ${['pending','ready_for_pickup','ready_to_ship','in_transit','delivered','cancelled','returned']
                            .map(st => `<option value="${st}" ${v.fulfillment_status===st?'selected':''}>${fulfillmentLabels[st]}</option>`)
                            .join('')}
                    </select>

                    <div class="mt-1">
                        <div class="field-label">Nota para el historial</div>
                        <input id="f-note" class="input" placeholder="Opcional (solo para el log interno)">
                    </div>

                    <div class="mt-2">
                        <button class="btn-secondary" onclick="guardarFulfillment()">Aplicar cambio de fulfillment</button>
                    </div>

                    <hr style="border:none;border-top:1px solid #eef2ff;margin:10px 0;">

                    ${s ? `
                    <div class="field-label">Estado del envío</div>
                    <select id="s-status" class="input mt-1">
                        ${['label_created','pickup_ready','in_transit','out_for_delivery','delivered','failed','returned']
                            .map(st => `<option value="${st}" ${s.status===st?'selected':''}>${shipmentLabels[st]}</option>`)
                            .join('')}
                    </select>

                    <!-- Courier + Servicio en la misma fila -->
                    <div class="mt-1 grid-2">
                        <div>
                            <div class="field-label">Courier</div>
                            <input id="s-carrier" class="input" value="${s.carrier || ''}">
                        </div>
                        <div>
                            <div class="field-label">Servicio</div>
                            <input id="s-service" class="input" value="${s.service || ''}">
                        </div>
                    </div>

                    <!-- Código de seguimiento + Peso en la misma fila -->
                    <div class="mt-1 grid-2">
                        <div>
                            <div class="field-label">Código de seguimiento</div>
                            <input id="s-tracking" class="input" value="${s.tracking_number || ''}">
                        </div>
                        <div>
                            <div class="field-label">Peso (kg)</div>
                            <input id="s-weight" class="input" value="${s.weight_kg || ''}">
                        </div>
                    </div>

                    <!-- Costo en una línea -->
                    <div class="mt-1">
                        <div class="field-label">Costo de envío</div>
                        <input id="s-cost" class="input" value="${s.shipping_cost || ''}">
                    </div>

                    <!-- URL de seguimiento ocupa toda la línea -->
                    <div class="mt-1">
                        <div class="field-label">URL de seguimiento</div>
                        <input id="s-url" class="input" value="${s.tracking_url || ''}">
                    </div>

                    <div class="mt-2">
                        <div class="field-label">Nota para el historial de envío</div>
                        <input id="s-note" class="input" placeholder="Opcional">
                    </div>

                    <div class="mt-3 flex gap-2">
                        <button class="btn-primary" onclick="guardarShipmentMeta(${s.id})">Guardar datos de envío</button>
                        <button class="btn-secondary" onclick="guardarShipmentStatus(${s.id})">Cambiar estado de envío</button>
                    </div>
                    ` : `
                    <div style="font-size:12px;color:#9ca3af;">
                        Esta venta aún no tiene envío asociado.<br>
                        Cuando el fulfillment cambie a <strong>Listo Recojo</strong> o <strong>Listo Envio</strong>
                        se generará automáticamente.
                    </div>
                    `}
                </div>
            </div>

            <div class="card mt-3">
                <div style="font-size:13px;font-weight:500;margin-bottom:6px;">Paquetes y productos</div>
                ${renderGrupos(data.grupos)}
            </div>
        `;
    }


    function renderGrupos(grupos) {
        if (!grupos || !grupos.length) {
            return `<div style="font-size:12px;color:#9ca3af;">Sin detalle de productos.</div>`;
        }
        return grupos.map(g => `
            <div class="mt-2">
                <div class="tag">Paquete #${g.idsub}</div>
                <div style="font-size:12px;color:#9ca3af;margin-bottom:4px;">
                    Total paquete: S/ ${Number(g.total || 0).toFixed(2)}
                </div>
                ${g.items.map(it => `
                    <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:2px;">
                        <div>
                            ${it.articulo?.name || ('Item '+it.idarticulo)}
                            <span style="color:#6b7280;">· x${it.qty}</span>
                        </div>
                        <div>S/ ${Number(it.subtotal || 0).toFixed(2)}</div>
                    </div>
                `).join('')}
            </div>
        `).join('');
    }

    async function guardarVenta() {
        if (!currentVenta) return;

        const body = {
            nombre      : document.getElementById('v-nombre').value,
            apellido    : document.getElementById('v-apellido').value,
            email       : document.getElementById('v-email').value,
            celular     : document.getElementById('v-celular').value,
            domicilio   : document.getElementById('v-domicilio').value,
            distrito    : document.getElementById('v-distrito').value,
            provincia   : document.getElementById('v-provincia').value,
            departamento: document.getElementById('v-departamento').value,
            referencia  : document.getElementById('v-referencia').value,
            payment_status   : document.getElementById('v-payment').value || null,
            fulfillment_method: document.getElementById('v-method').value || null,
        };

        try {
            const url = buildUrl(URL_VUPD, currentVenta.origen, currentVenta.id);
            const res = await fetch(url, {
                method:'POST',
                headers:{
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN':CSRF,
                    'Accept':'application/json',
                },
                body:JSON.stringify(body),
            });
            if (!res.ok) throw new Error('Respuesta no OK');
            await res.json();
            loadVentas();
        } catch(e){
            console.error(e);
            alert('Error guardando datos de la venta');
        }
    }

    async function guardarFulfillment() {
        if (!currentVenta) return;

        const body = {
            to  : document.getElementById('f-to').value,
            note: document.getElementById('f-note').value || null,
        };

        try {
            const url = buildUrl(URL_VFUL, currentVenta.origen, currentVenta.id);
            const res = await fetch(url, {
                method:'POST',
                headers:{
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN':CSRF,
                    'Accept':'application/json',
                },
                body:JSON.stringify(body),
            });
            if (res.status === 422) {
                const d = await res.json();
                alert(d.error || 'Transición de estado no permitida');
                return;
            }
            if (!res.ok) throw new Error('Respuesta no OK');
            await res.json();
            loadVentas();
            abrirDetalle(currentVenta.origen, currentVenta.id);
        } catch(e){
            console.error(e);
            alert('Error aplicando cambio de fulfillment');
        }
    }

    async function guardarShipmentMeta(id) {
        if (!currentVenta) return;

        const body = {
            carrier        : document.getElementById('s-carrier').value || null,
            service        : document.getElementById('s-service').value || null,
            tracking_number: document.getElementById('s-tracking').value || null,
            tracking_url   : document.getElementById('s-url').value || null,
            shipping_cost  : document.getElementById('s-cost').value || null,
            weight_kg      : document.getElementById('s-weight').value || null,
        };

        try {
            const url = buildUrl(URL_SMETA, currentVenta.origen, id);
            const res = await fetch(url, {
                method:'POST',
                headers:{
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN':CSRF,
                    'Accept':'application/json',
                },
                body:JSON.stringify(body),
            });
            if (!res.ok) throw new Error('Respuesta no OK');
            await res.json();
            loadVentas();
        } catch(e){
            console.error(e);
            alert('Error guardando datos de envío');
        }
    }

    async function guardarShipmentStatus(id) {
        if (!currentVenta) return;

        const body = {
            status: document.getElementById('s-status').value,
            note  : document.getElementById('s-note').value || null,
        };

        try {
            const url = buildUrl(URL_SSTAT, currentVenta.origen, id);
            const res = await fetch(url, {
                method:'POST',
                headers:{
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN':CSRF,
                    'Accept':'application/json',
                },
                body:JSON.stringify(body),
            });
            if (res.status === 422) {
                const d = await res.json();
                alert(d.error || 'Transición de estado de envío no permitida');
                return;
            }
            if (!res.ok) throw new Error('Respuesta no OK');
            await res.json();
            loadVentas();
            abrirDetalle(currentVenta.origen, currentVenta.id);
        } catch(e){
            console.error(e);
            alert('Error cambiando estado de envío');
        }
    }

    // Búsqueda rápida
    let searchTimer = null;
    document.getElementById('search').addEventListener('input', () => {
        if (searchTimer) clearTimeout(searchTimer);
        searchTimer = setTimeout(loadVentas, 300);
    });

    // Reloj
    function startClock() {
        const el = document.getElementById('clock');
        if (!el) return;
        setInterval(() => {
            const now = new Date();
            el.textContent = now.toLocaleTimeString();
        }, 1000);
    }

    // Polling
    function startLoop() {
        loadVentas();
        setInterval(loadVentas, 60000); // cada 5 segundos
    }

    document.addEventListener('DOMContentLoaded', () => {
        startClock();
        startLoop();
    });
</script>
</body>
</html>
