<?php

session_start();
if (!isset($_SESSION['username'])) { header("Location: login.php"); exit(); }
$namaDepan = htmlspecialchars($_SESSION['nama_depan'] ?? $_SESSION['username'] ?? '');
$role      = $_SESSION['role'] ?? 'petani';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Peta Sensor — SM Irigasi</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        fontFamily: { sans: ["'Plus Jakarta Sans'", 'sans-serif'] },
        colors: { emerald: { 950:'#022C22',900:'#064E3B',800:'#065F46',700:'#047857',600:'#059669',500:'#10B981',400:'#34D399' }}
      }
    }
  }
</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  body { font-family: 'Plus Jakarta Sans', sans-serif; }
  @keyframes livePulse { 0%,100%{opacity:1} 50%{opacity:0.3} }
  .live-dot { animation: livePulse 2s ease-in-out infinite; }
  .sensor-titik { cursor:pointer; transition:transform 0.2s ease; }
  .sensor-titik:hover { transform:scale(1.15); }
</style>
</head>
<body class="min-h-screen flex flex-col bg-slate-50" style="color:#0A2218;">

<!-- NAV -->
<nav class="sticky top-0 z-50" style="background:#064E3B;box-shadow:0 2px 20px rgba(0,0,0,0.15);">
  <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
    <a href="index.php" class="flex items-center gap-2.5 no-underline">
      <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(16,185,129,0.18);">
        <svg width="18" height="18" viewBox="0 0 44 44" fill="none">
          <path d="M22 7C22 7 13 18 13 24C13 29.52 17.03 34 22 34C26.97 34 31 29.52 31 24C31 18 22 7 22 7Z" fill="#10B981"/>
          <line x1="18" y1="24" x2="26" y2="24" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
      </div>
      <div>
        <div class="text-base font-extrabold text-white tracking-tight leading-none">SM Irigasi</div>
        <div class="text-xs font-semibold uppercase tracking-widest" style="color:rgba(255,255,255,0.35);">Monitoring</div>
      </div>
    </a>
    <div class="flex items-center gap-1">
      <a href="index.php#monitoring" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition-all hover:bg-white/10 no-underline" style="color:rgba(255,255,255,0.65);">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>Monitor
      </a>
      <a href="peta.php" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold transition-all no-underline" style="background:rgba(16,185,129,0.20);color:#34D399;border-radius:9px;">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"/></svg>Peta Sensor
      </a>
      <a href="riwayat.php" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition-all hover:bg-white/10 no-underline" style="color:rgba(255,255,255,0.65);">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>Riwayat
      </a>
      <?php if ($role === 'administrator'): ?>
      <a href="dashboard.php" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition-all hover:bg-white/10 no-underline" style="color:rgba(255,255,255,0.65);">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>Admin
      </a>
      <?php endif; ?>
      <a href="logout.php" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold transition-all hover:bg-red-500/20 no-underline" style="background:rgba(239,68,68,0.12);color:rgba(255,180,180,0.9);">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>Keluar
      </a>
    </div>
  </div>
</nav>

<main class="flex-1 max-w-7xl mx-auto w-full px-6 py-7">

  <!-- Page header -->
  <div class="mb-5">
    <h1 class="text-2xl font-extrabold tracking-tight" style="color:#0A2218;">Peta Sensor Interaktif</h1>
    <p class="text-sm text-slate-400 mt-0.5">Visualisasi jaringan irigasi sawah beserta posisi 8 sensor aktif di lapangan</p>
  </div>

  <!-- KPI Row -->
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
    <?php foreach ([
      ['cnt-normal', '#10B981', 'Sensor Normal'],
      ['cnt-rendah', '#F97316', 'Debit Rendah'],
      ['cnt-tinggi', '#3B82F6', 'Debit Tinggi'],
      ['cnt-kritis', '#EF4444', 'Status Kritis'],
    ] as [$id, $color, $lbl]): ?>
    <div class="bg-white rounded-2xl px-4 py-3.5 border flex items-center gap-3" style="border-color:rgba(6,78,59,0.08);box-shadow:0 1px 3px rgba(6,78,59,0.05),0 8px 24px rgba(6,78,59,0.06);">
      <div class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:<?= $color ?>;"></div>
      <div>
        <div class="text-2xl font-extrabold leading-none" style="color:#0A2218;" id="<?= $id ?>">0</div>
        <div class="text-xs font-semibold text-slate-400 mt-0.5"><?= $lbl ?></div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Map + Sidebar Layout -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">

    <!-- Map Card -->
    <div class="lg:col-span-2 bg-white rounded-2xl border overflow-hidden" style="border-color:rgba(6,78,59,0.08);box-shadow:0 1px 3px rgba(6,78,59,0.05),0 8px 24px rgba(6,78,59,0.06);">
      <div class="flex items-center justify-between px-5 py-3.5 border-b bg-slate-50/70" style="border-color:rgba(6,78,59,0.06);">
        <div class="flex items-center gap-2 font-bold text-slate-700 text-sm">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2"><polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"/></svg>
          Denah Jaringan Irigasi
        </div>
        <div class="flex items-center gap-1.5 text-xs text-slate-400 font-medium tabular-nums px-3 py-1.5 rounded-lg border bg-white" style="border-color:rgba(6,78,59,0.08);" id="waktu-peta">--:--:--</div>
      </div>

      <!-- SVG Map -->
      <div class="p-3" style="background:linear-gradient(135deg,#d4edda 0%,#b8dfc0 40%,#a3d9b1 70%,#c5e8d9 100%);">
        <svg viewBox="0 0 640 420" style="width:100%;max-height:480px;">
          <defs>
            <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
              <path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.25)" stroke-width="0.5"/>
            </pattern>
            <filter id="glow"><feGaussianBlur stdDeviation="3" result="coloredBlur"/><feMerge><feMergeNode in="coloredBlur"/><feMergeNode in="SourceGraphic"/></feMerge></filter>
          </defs>
          <rect width="640" height="420" fill="url(#grid)"/>

          <!-- Area sawah -->
          <rect x="80" y="65" width="130" height="85" rx="6" fill="rgba(34,197,94,0.22)" stroke="#16a34a" stroke-width="1.5"/>
          <text x="145" y="112" text-anchor="middle" font-size="10" fill="#15803D" font-weight="700" font-family="Plus Jakarta Sans, sans-serif">PETAK A</text>
          <rect x="235" y="65" width="130" height="85" rx="6" fill="rgba(34,197,94,0.22)" stroke="#16a34a" stroke-width="1.5"/>
          <text x="300" y="112" text-anchor="middle" font-size="10" fill="#15803D" font-weight="700" font-family="Plus Jakarta Sans, sans-serif">PETAK B</text>
          <rect x="390" y="65" width="130" height="85" rx="6" fill="rgba(34,197,94,0.22)" stroke="#16a34a" stroke-width="1.5"/>
          <text x="455" y="112" text-anchor="middle" font-size="10" fill="#15803D" font-weight="700" font-family="Plus Jakarta Sans, sans-serif">PETAK C</text>
          <rect x="80" y="235" width="130" height="85" rx="6" fill="rgba(34,197,94,0.22)" stroke="#16a34a" stroke-width="1.5"/>
          <text x="145" y="282" text-anchor="middle" font-size="10" fill="#15803D" font-weight="700" font-family="Plus Jakarta Sans, sans-serif">PETAK D</text>
          <rect x="235" y="235" width="130" height="85" rx="6" fill="rgba(34,197,94,0.22)" stroke="#16a34a" stroke-width="1.5"/>
          <text x="300" y="282" text-anchor="middle" font-size="10" fill="#15803D" font-weight="700" font-family="Plus Jakarta Sans, sans-serif">PETAK E</text>
          <rect x="390" y="235" width="130" height="85" rx="6" fill="rgba(34,197,94,0.22)" stroke="#16a34a" stroke-width="1.5"/>
          <text x="455" y="282" text-anchor="middle" font-size="10" fill="#15803D" font-weight="700" font-family="Plus Jakarta Sans, sans-serif">PETAK F</text>

          <!-- Saluran utama -->
          <line x1="30" y1="32" x2="610" y2="32" stroke="#1D4ED8" stroke-width="6" stroke-linecap="round"/>
          <text x="320" y="23" text-anchor="middle" font-size="9" fill="#1D4ED8" font-weight="700" font-family="Plus Jakarta Sans, sans-serif">SALURAN INDUK UTAMA</text>

          <!-- Saluran sekunder -->
          <line x1="145" y1="32" x2="145" y2="65"  stroke="#3B82F6" stroke-width="3" stroke-dasharray="6,3" opacity="0.6"/>
          <line x1="300" y1="32" x2="300" y2="65"  stroke="#3B82F6" stroke-width="3" stroke-dasharray="6,3" opacity="0.6"/>
          <line x1="455" y1="32" x2="455" y2="65"  stroke="#3B82F6" stroke-width="3" stroke-dasharray="6,3" opacity="0.6"/>
          <line x1="145" y1="150" x2="145" y2="235" stroke="#3B82F6" stroke-width="3" stroke-dasharray="6,3" opacity="0.6"/>
          <line x1="300" y1="150" x2="300" y2="235" stroke="#3B82F6" stroke-width="3" stroke-dasharray="6,3" opacity="0.6"/>
          <line x1="455" y1="150" x2="455" y2="235" stroke="#3B82F6" stroke-width="3" stroke-dasharray="6,3" opacity="0.6"/>
          <line x1="30" y1="190" x2="610" y2="190" stroke="#3B82F6" stroke-width="3" stroke-dasharray="6,3" opacity="0.6"/>

          <!-- Embung -->
          <ellipse cx="580" cy="320" rx="38" ry="24" fill="rgba(59,130,246,0.18)" stroke="#3B82F6" stroke-width="2"/>
          <text x="580" y="324" text-anchor="middle" font-size="9" fill="#1D4ED8" font-weight="700" font-family="Plus Jakarta Sans, sans-serif">EMBUNG</text>

          <!-- Sensor dots -->
          <g class="sensor-titik" onclick="pilihSensor('SNS-01')">
            <circle cx="68" cy="32" r="13" id="dot-SNS-01" fill="#10B981" stroke="white" stroke-width="2.5" filter="url(#glow)"/>
            <text x="68" y="36" text-anchor="middle" font-size="8" fill="white" font-weight="700" font-family="Plus Jakarta Sans, sans-serif">S1</text>
          </g>
          <g class="sensor-titik" onclick="pilihSensor('SNS-02')">
            <circle cx="145" cy="56" r="13" id="dot-SNS-02" fill="#10B981" stroke="white" stroke-width="2.5" filter="url(#glow)"/>
            <text x="145" y="60" text-anchor="middle" font-size="8" fill="white" font-weight="700" font-family="Plus Jakarta Sans, sans-serif">S2</text>
          </g>
          <g class="sensor-titik" onclick="pilihSensor('SNS-03')">
            <circle cx="300" cy="56" r="13" id="dot-SNS-03" fill="#F97316" stroke="white" stroke-width="2.5" filter="url(#glow)"/>
            <text x="300" y="60" text-anchor="middle" font-size="8" fill="white" font-weight="700" font-family="Plus Jakarta Sans, sans-serif">S3</text>
          </g>
          <g class="sensor-titik" onclick="pilihSensor('SNS-04')">
            <circle cx="455" cy="56" r="13" id="dot-SNS-04" fill="#3B82F6" stroke="white" stroke-width="2.5" filter="url(#glow)"/>
            <text x="455" y="60" text-anchor="middle" font-size="8" fill="white" font-weight="700" font-family="Plus Jakarta Sans, sans-serif">S4</text>
          </g>
          <g class="sensor-titik" onclick="pilihSensor('SNS-05')">
            <circle cx="145" cy="190" r="13" id="dot-SNS-05" fill="#10B981" stroke="white" stroke-width="2.5" filter="url(#glow)"/>
            <text x="145" y="194" text-anchor="middle" font-size="8" fill="white" font-weight="700" font-family="Plus Jakarta Sans, sans-serif">S5</text>
          </g>
          <g class="sensor-titik" onclick="pilihSensor('SNS-06')">
            <circle cx="300" cy="190" r="13" id="dot-SNS-06" fill="#EF4444" stroke="white" stroke-width="2.5" filter="url(#glow)"/>
            <text x="300" y="194" text-anchor="middle" font-size="8" fill="white" font-weight="700" font-family="Plus Jakarta Sans, sans-serif">S6</text>
          </g>
          <g class="sensor-titik" onclick="pilihSensor('SNS-07')">
            <circle cx="455" cy="190" r="13" id="dot-SNS-07" fill="#10B981" stroke="white" stroke-width="2.5" filter="url(#glow)"/>
            <text x="455" y="194" text-anchor="middle" font-size="8" fill="white" font-weight="700" font-family="Plus Jakarta Sans, sans-serif">S7</text>
          </g>
          <g class="sensor-titik" onclick="pilihSensor('SNS-08')">
            <circle cx="580" cy="296" r="13" id="dot-SNS-08" fill="#10B981" stroke="white" stroke-width="2.5" filter="url(#glow)"/>
            <text x="580" y="300" text-anchor="middle" font-size="8" fill="white" font-weight="700" font-family="Plus Jakarta Sans, sans-serif">S8</text>
          </g>

          <!-- Legend bar -->
          <rect x="20" y="385" width="600" height="26" rx="8" fill="rgba(255,255,255,0.38)"/>
          <circle cx="38" cy="398" r="5" fill="#10B981"/><text x="47" y="402" font-size="9" fill="#0A2218" font-weight="600" font-family="Plus Jakarta Sans,sans-serif">Normal</text>
          <circle cx="108" cy="398" r="5" fill="#F97316"/><text x="117" y="402" font-size="9" fill="#0A2218" font-weight="600" font-family="Plus Jakarta Sans,sans-serif">Rendah</text>
          <circle cx="178" cy="398" r="5" fill="#3B82F6"/><text x="187" y="402" font-size="9" fill="#0A2218" font-weight="600" font-family="Plus Jakarta Sans,sans-serif">Tinggi</text>
          <circle cx="248" cy="398" r="5" fill="#EF4444"/><text x="257" y="402" font-size="9" fill="#0A2218" font-weight="600" font-family="Plus Jakarta Sans,sans-serif">Kritis</text>
          <line x1="338" y1="398" x2="378" y2="398" stroke="#3B82F6" stroke-width="3" stroke-dasharray="5,3"/>
          <text x="383" y="402" font-size="9" fill="#0A2218" font-weight="600" font-family="Plus Jakarta Sans,sans-serif">Saluran</text>
          <rect x="464" y="393" width="12" height="9" rx="2" fill="rgba(34,197,94,0.3)" stroke="#16a34a" stroke-width="1"/>
          <text x="480" y="402" font-size="9" fill="#0A2218" font-weight="600" font-family="Plus Jakarta Sans,sans-serif">Area Sawah</text>
        </svg>
      </div>
    </div>

    <!-- Sidebar -->
    <div class="flex flex-col gap-4">

      <!-- Detail Card -->
      <div class="bg-white rounded-2xl border overflow-hidden" style="border-color:rgba(6,78,59,0.08);box-shadow:0 1px 3px rgba(6,78,59,0.05),0 8px 24px rgba(6,78,59,0.06);">
        <div class="flex items-center gap-2 px-4 py-3 border-b font-bold text-slate-700 text-sm" style="border-color:rgba(6,78,59,0.06);">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
          Detail Sensor
        </div>
        <div id="detail-sensor">
          <div class="px-4 py-8 text-center">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mx-auto mb-2 text-slate-200"><polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"/></svg>
            <p class="text-xs text-slate-300">Klik titik sensor pada peta untuk melihat detail</p>
          </div>
        </div>
      </div>

      <!-- Sensor List -->
      <div class="bg-white rounded-2xl border overflow-hidden" style="border-color:rgba(6,78,59,0.08);box-shadow:0 1px 3px rgba(6,78,59,0.05),0 8px 24px rgba(6,78,59,0.06);">
        <div class="flex items-center gap-2 px-4 py-3 border-b font-bold text-slate-700 text-sm" style="border-color:rgba(6,78,59,0.06);">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
          Semua Sensor Aktif
        </div>
        <div id="daftar-sensor" class="overflow-y-auto" style="max-height:300px;"></div>
      </div>
    </div>

  </div>
</main>

<footer class="text-center py-4 text-xs mt-4" style="background:#064E3B;color:rgba(255,255,255,0.40);">
  © 2026 SM Irigasi — Universitas Sebelas Maret · Sistem Monitoring Irigasi Sawah
</footer>

<script>
var dataSensor=[
  {id:"SNS-01",lokasi:"Saluran Induk Ngidul",debit:12.4,tma:42,suhu:26.8,lembap:68,status:"normal"},
  {id:"SNS-02",lokasi:"Percabangan Blok A",debit:8.7,tma:35,suhu:27.1,lembap:72,status:"normal"},
  {id:"SNS-03",lokasi:"Saluran Blok B",debit:3.2,tma:18,suhu:28.3,lembap:45,status:"rendah"},
  {id:"SNS-04",lokasi:"Bak Penampungan C1",debit:18.9,tma:71,suhu:26.2,lembap:80,status:"tinggi"},
  {id:"SNS-05",lokasi:"Saluran Ngalor D",debit:6.5,tma:28,suhu:27.8,lembap:63,status:"normal"},
  {id:"SNS-06",lokasi:"Saluran Ngetan E",debit:1.1,tma:10,suhu:29.0,lembap:31,status:"kritis"},
  {id:"SNS-07",lokasi:"Saluran Petak 12",debit:9.3,tma:38,suhu:26.5,lembap:70,status:"normal"},
  {id:"SNS-08",lokasi:"Embung Ngulon",debit:7.8,tma:32,suhu:27.4,lembap:66,status:"normal"}
];
var warna={normal:"#10B981",rendah:"#F97316",tinggi:"#3B82F6",kritis:"#EF4444"};
var label={normal:"Normal",rendah:"Rendah",tinggi:"Tinggi",kritis:"Kritis!"};
function sp(status){
  var c={normal:"background:#F0FDF4;color:#15803D;border:1px solid #BBF7D0;",rendah:"background:#FFF7ED;color:#C2410C;border:1px solid #FED7AA;",tinggi:"background:#EFF6FF;color:#1D4ED8;border:1px solid #BFDBFE;",kritis:"background:#FEF2F2;color:#B91C1C;border:1px solid #FCA5A5;"};
  return c[status]||c.normal;
}
function updateWaktu(){var n=new Date();document.getElementById('waktu-peta').textContent=String(n.getHours()).padStart(2,'0')+':'+String(n.getMinutes()).padStart(2,'0')+':'+String(n.getSeconds()).padStart(2,'0');}
function renderDaftar(){
  var html='';var cnt={normal:0,rendah:0,tinggi:0,kritis:0};
  dataSensor.forEach(function(s){
    cnt[s.status]=(cnt[s.status]||0)+1;
    html+='<div onclick="pilihSensor(\''+s.id+'\')" style="display:flex;align-items:center;gap:9px;padding:10px 16px;cursor:pointer;transition:background 0.15s;border-bottom:1px solid rgba(6,78,59,0.04);" onmouseover="this.style.background=\'#F0FDF4\'" onmouseout="this.style.background=\'\'">';
    html+='<div style="width:9px;height:9px;border-radius:50%;background:'+warna[s.status]+';flex-shrink:0;"></div>';
    html+='<div style="flex:1;min-width:0;">';
    html+='<div style="font-size:0.7rem;font-weight:700;color:#94A3B8;">'+s.id+'</div>';
    html+='<div style="font-size:0.8rem;font-weight:500;color:#0A2218;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">'+s.lokasi+'</div>';
    html+='</div>';
    html+='<span style="font-size:0.65rem;font-weight:700;padding:2px 7px;border-radius:10px;flex-shrink:0;'+sp(s.status)+'">'+label[s.status]+'</span>';
    html+='</div>';
  });
  document.getElementById('daftar-sensor').innerHTML=html;
  document.getElementById('cnt-normal').textContent=cnt.normal||0;
  document.getElementById('cnt-rendah').textContent=cnt.rendah||0;
  document.getElementById('cnt-tinggi').textContent=cnt.tinggi||0;
  document.getElementById('cnt-kritis').textContent=cnt.kritis||0;
}
function pilihSensor(id){
  var s=dataSensor.find(function(x){return x.id===id;});
  if(!s)return;
  var w=warna[s.status];
  var html='<div style="padding:1rem;">';
  html+='<div style="font-size:0.7rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#94A3B8;margin-bottom:4px;">'+s.id+'</div>';
  html+='<div style="font-size:1rem;font-weight:700;color:#0A2218;margin-bottom:8px;">'+s.lokasi+'</div>';
  html+='<span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:700;margin-bottom:12px;'+sp(s.status)+'"><svg width="6" height="6" viewBox="0 0 6 6"><circle cx="3" cy="3" r="3" fill="'+w+'"/></svg>'+label[s.status]+'</span>';
  html+='<div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">';
  html+='<div style="background:#F8FAFC;border-radius:10px;padding:9px 10px;"><div style="font-size:1rem;font-weight:700;color:#0A2218;">'+s.debit.toFixed(1)+'</div><div style="font-size:0.65rem;color:#94A3B8;font-weight:500;margin-top:2px;">Debit L/dtk</div></div>';
  html+='<div style="background:#F8FAFC;border-radius:10px;padding:9px 10px;"><div style="font-size:1rem;font-weight:700;color:#0A2218;">'+s.tma+'</div><div style="font-size:0.65rem;color:#94A3B8;font-weight:500;margin-top:2px;">TMA cm</div></div>';
  html+='<div style="background:#F8FAFC;border-radius:10px;padding:9px 10px;"><div style="font-size:1rem;font-weight:700;color:#0A2218;">'+s.suhu.toFixed(1)+'°</div><div style="font-size:0.65rem;color:#94A3B8;font-weight:500;margin-top:2px;">Suhu C</div></div>';
  html+='<div style="background:#F8FAFC;border-radius:10px;padding:9px 10px;"><div style="font-size:1rem;font-weight:700;color:#0A2218;">'+s.lembap+'%</div><div style="font-size:0.65rem;color:#94A3B8;font-weight:500;margin-top:2px;">Kelembapan</div></div>';
  html+='</div></div>';
  document.getElementById('detail-sensor').innerHTML=html;
  dataSensor.forEach(function(x){var dot=document.getElementById('dot-'+x.id);if(dot)dot.setAttribute('stroke-width',x.id===id?'4':'2.5');});
}
function simulasiUpdate(){
  dataSensor.forEach(function(s){
    s.debit=Math.max(0.5,s.debit+(Math.random()-0.5));
    s.tma=Math.max(5,s.tma+Math.round((Math.random()-0.5)*3));
    s.lembap=Math.min(100,Math.max(10,s.lembap+Math.round((Math.random()-0.5)*2)));
    if(s.tma<15)s.status='kritis';
    else if(s.tma<25)s.status='rendah';
    else if(s.tma>65)s.status='tinggi';
    else s.status='normal';
    var dot=document.getElementById('dot-'+s.id);
    if(dot)dot.setAttribute('fill',warna[s.status]);
  });
  renderDaftar();
}
renderDaftar();
setInterval(simulasiUpdate,4000);
setInterval(updateWaktu,1000);
updateWaktu();
</script>
</body>
</html>
