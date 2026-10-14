<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ __($page['title']) }} — Hôtelio</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,600;1,9..144,300;1,9..144,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
:root{
  --ocean:#0C2340;--ocean-deep:#061422;
  --sand:#F8F0E3;--sand-warm:#EFE3CC;
  --coral:orange;--coral-light:#F0876F;
  --white:#FFFFFF;--text:#1A1A1A;--text-2:#4A4A4A;--text-3:#7A7A7A;
  --border:#E8E0D4;--shadow:rgba(12,35,64,0.12);
  --primary:#F77F00;--primary-light:#e06e00;
  --secondary:#009A44;--secondary-light:#007a35;
}
html{scroll-behavior:smooth}
a{text-decoration:none;color:inherit}
body::before{content:"";position:fixed;top:0;left:0;width:100%;height:4px;z-index:999;background:linear-gradient(to right,#F77F00 33%,#FFFFFF 33% 66%,#009E60 66%)}

body{font-family:'Plus Jakarta Sans',sans-serif;background:#F8F5F0;color:var(--text);padding-top:76px;transition:background .3s,color .3s}
[data-theme="dark"] body{background:#0f172a;color:#e2e8f0}

.content-wrap{max-width:760px;margin:0 auto;padding:56px 24px 40px}
.page-icon{width:56px;height:56px;border-radius:16px;background:rgba(247,127,0,.12);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:22px;margin-bottom:20px}
.content-wrap h1{font-family:'Fraunces',serif;font-weight:600;font-size:2.1rem;color:var(--ocean);margin-bottom:14px}
[data-theme="dark"] .content-wrap h1{color:#e2e8f0}
.lead{font-size:1.02rem;color:var(--text-2);line-height:1.7;margin-bottom:40px;max-width:600px}
[data-theme="dark"] .lead{color:#94a3b8}
.section{padding:22px 0;border-top:1px solid var(--border)}
[data-theme="dark"] .section{border-color:rgba(148,163,184,.15)}
.section h2{font-size:1.05rem;font-weight:700;color:var(--ocean);margin-bottom:8px}
[data-theme="dark"] .section h2{color:#e2e8f0}
.section p{font-size:.94rem;color:var(--text-2);line-height:1.75}
[data-theme="dark"] .section p{color:#94a3b8}

@media (max-width:720px){
  .content-wrap{padding:36px 20px 40px}
  .content-wrap h1{font-size:1.6rem}
}
</style>
</head>
<body>

@include('partials.site-navbar')

<div class="content-wrap">
  <div class="page-icon"><i class="fa-solid {{ $page['icon'] }}"></i></div>
  <h1>{{ __($page['title']) }}</h1>
  <p class="lead">{{ __($page['lead']) }}</p>

  @foreach($page['sections'] as $s)
  <div class="section">
    <h2>{{ __($s['h']) }}</h2>
    <p>{{ __($s['b']) }}</p>
  </div>
  @endforeach
</div>

@include('partials.public-footer')
@include('partials.cookie-banner')

</body>
</html>
