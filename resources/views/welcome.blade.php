<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SIFITKOP &mdash; Sistem Informasi Fit & Proper Koperasi</title>
<meta name="description" content="SIFITKOP adalah platform Uji Kelayakan dan Kepatuhan Koperasi di Jawa Tengah connecting givers directly to verified causes worldwide.">

<!-- Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Nunito+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Bootstrap -->
<link href="{{ asset('front_assets/bootstrap.min.css') }}" rel="stylesheet">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<!-- AOS -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<link href="{{ asset('front_assets/style.css') }}" rel="stylesheet" />
{{-- <link rel="icon" type="image/x-icon" href="{{ asset('front_assets/hand-heart.png') }}"> --}}
<link rel="icon" href="{{ asset('sifitkop.png') }}" type="image/x-icon"/>




</head>
<body>

<!-- ============================================
     PRELOADER
============================================ -->
<div id="preloader" aria-hidden="true">
  <div class="preload-ring"><i class="bi bi-heart-fill preload-heart"></i></div>
  <div class="preload-text">MEMUAT SIFITKOP&hellip;</div>
</div>

<!-- ============================================
     NAVBAR
============================================ -->
<nav class="navbar navbar-expand-lg navbar-hw" id="mainNav">
  <div class="container">
    <a class="hw-logo" href="#home">
      <span class="hw-logo-mark"><img src="{{ asset('sifitkop.png') }}" alt=""></span>
      SIFIT<span class="dot">KOP</span>
    </a>
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-label="Toggle navigation">
      <i class="bi bi-list fs-2"></i>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav nav-hw mx-auto mt-3 mt-lg-0">
        <li class="nav-item"><a class="nav-link" href="#home">Beranda</a></li>
        <li class="nav-item"><a class="nav-link" href="#about">Tentang</a></li>
        <li class="nav-item"><a class="nav-link" href="#causes">Layanan</a></li>
        <li class="nav-item"><a class="nav-link" href="#volunteers">Asesor</a></li>
        <li class="nav-item"><a class="nav-link" href="#testimonials">Testimoni</a></li>
        <li class="nav-item d-lg-none mt-2"><a class="nav-link" href="/koperasi/register"><i class="bi bi-person-plus"></i> Daftar Koperasi</a></li>
        <li class="nav-item d-lg-none"><a class="nav-link" href="/koperasi/login"><i class="bi bi-box-arrow-in-right"></i> Masuk Portal</a></li>
      </ul>
      <div class="d-none d-lg-flex align-items-center gap-2">
        <a href="/koperasi/register" class="btn-hw-outline py-2 px-3" style="font-size: .875rem;"><i class="bi bi-person-plus"></i> Daftar Koperasi</a>
        <a href="/koperasi/login" class="btn-hw-primary py-2 px-3" style="font-size: .875rem;">Masuk Portal <i class="bi bi-arrow-right"></i></a>
      </div>
    </div>
  </div>
</nav>

<!-- ============================================
     HERO
=========================================== -->
<header class="hero" id="home">
  <div class="hero-glow"></div>
  <span class="blob blob-1"></span>
  <span class="blob blob-2"></span>
  <span class="blob blob-3"></span>
  <div class="container position-relative">
    <div class="row align-items-center g-5">
      <div class="col-lg-6" data-aos="fade-right">
        <span class="eyebrow"><i class="bi bi-stars"></i> {{ $landingPage->hero_stats_title ?? 'Mendukung Ratusan Koperasi di Jawa Tengah' }}</span>
        <h1 class="hero-title mt-4">{!! $landingPage->hero_title ?? 'Tingkatkan tata kelola <span class="accent-underline">Koperasi<svg viewBox="0 0 200 14" preserveAspectRatio="none"><path d="M2 10 Q 50 2 100 8 T 198 6" stroke="#F59E0B" stroke-width="5" fill="none" stroke-linecap="round"/></svg></span> Anda hari ini.' !!}</h1>
        <p class="hero-lead mt-4">{{ $landingPage->hero_subtitle ?? 'SIFITKOP hadir untuk membantu koperasi di Jawa Tengah melakukan Uji Kelayakan dan Kepatuhan secara transparan, akuntabel, dan terintegrasi untuk mewujudkan koperasi yang modern dan sehat.' }}</p>
        <div class="d-flex flex-wrap gap-3 mt-4">
          <a href="/koperasi/register" class="btn-hw-primary"><i class="bi bi-person-plus"></i> Daftar Koperasi <i class="bi bi-arrow-right"></i></a>
          <a href="/koperasi/login" class="btn-hw-outline"><i class="bi bi-box-arrow-in-right"></i> Masuk Portal</a>
        </div>
        <div class="d-flex align-items-center gap-3 mt-5">
          <div class="hero-avatars">
            <img src="https://images.unsplash.com/photo-1633332755192-727a05c4013d?w=100&h=100&fit=crop&auto=format" alt="Donor">
            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&h=100&fit=crop&auto=format" alt="Donor">
            <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&h=100&fit=crop&auto=format" alt="Donor">
          </div>
          <div>
            <div class="fw-bold font-display" style="font-size:.9rem;">500+ Koperasi</div>
            <div class="text-muted" style="font-size:.8rem;">telah tergabung di SIFITKOP</div>
          </div>
        </div>
      </div>
      <div class="col-lg-6" data-aos="fade-left" data-aos-delay="120">
        <div class="hero-media">
          <div class="hero-media-frame">
            <img src="{{ $landingPage && $landingPage->hero_image ? Storage::url($landingPage->hero_image) : 'https://images.unsplash.com/photo-1556761175-5973dc0f32b7?w=900&h=1000&fit=crop&auto=format' }}" alt="Koperasi SIFITKOP" loading="lazy">
          </div>
          <div class="float-card float-card-1">
            <span class="fc-icon"><i class="bi bi-cup-hot-fill"></i></span>
            <div><div class="fc-val">1,204</div><div class="fc-label">Koperasi Dinilai</div></div>
          </div>
          <div class="float-card float-card-2">
            <span class="fc-icon"><i class="bi bi-shield-check"></i></span>
            <div><div class="fc-val">Aman</div><div class="fc-label">Data Terenkripsi</div></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>

<!-- ============================================
     IMPACT STATS
============================================ -->
<section class="section-pad pt-0" id="impact">
  <div class="container">
    <div class="stats-band" data-aos="zoom-in">
      <div class="row g-0">
        <div class="col-6 col-md-3 stat-item">
          <div class="stat-num"><span class="counter" data-target="1250">0</span>+</div>
          <div class="stat-label">Koperasi Terdaftar</div>
        </div>
        <div class="col-6 col-md-3 stat-item">
          <div class="stat-num"><span class="counter" data-target="480">0</span>+</div>
          <div class="stat-label">Sertifikat Terbit</div>
        </div>
        <div class="col-6 col-md-3 stat-item">
          <div class="stat-num"><span class="counter" data-target="120">0</span>+</div>
          <div class="stat-label">Asesor Ahli</div>
        </div>
        <div class="col-6 col-md-3 stat-item">
          <div class="stat-num"><span class="counter" data-target="100">0</span></div>
          <div class="stat-label">Program Pelatihan</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============================================
     ABOUT
============================================ -->
<section class="section-pad" id="about">
  <div class="container contact-card about-card">
    <div class="row align-items-center g-5">
      <div class="col-lg-6" data-aos="fade-right">
        <div class="about-collage">
          <div class="about-badge">
            <div class="ab-ring"><span>13yrs</span></div>
            <div>
              <div class="fw-bold font-display" style="font-size:.9rem;">Trusted Since</div>
              <div class="text-muted" style="font-size:.78rem;">2013 &middot; 40 countries</div>
            </div>
          </div>
          <div class="img-a"><img src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=800&h=650&fit=crop&auto=format" alt="Office handshake" loading="lazy"></div>
          <div class="img-b"><img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=500&h=500&fit=crop&auto=format" alt="Team meeting" loading="lazy"></div>
        </div>
      </div>
      <div class="col-lg-6" data-aos="fade-left">
        <span class="eyebrow"><i class="bi bi-flag-fill"></i> Tentang Kami</span>
        <h2 class="section-title mt-3 mb-3" style="font-size:clamp(1.7rem,3vw,2.4rem);">{{ $landingPage->about_title ?? 'Membangun Koperasi yang Sehat, Kuat, dan Mandiri.' }}</h2>
        <p class="section-sub">{{ $landingPage->about_description ?? 'SIFITKOP diinisiasi oleh Dinas Koperasi dan UKM Jawa Tengah untuk memfasilitasi pelaksanaan uji kelayakan (fit and proper test) bagi pengurus dan pengawas koperasi.' }}</p>
        <ul class="about-list mt-3">
          <li><span class="ic"><i class="bi bi-eye-fill"></i></span><div><strong>Penilaian Objektif</strong><div class="text-muted small">Sistem penilaian yang terstandarisasi dan dapat dipantau perkembangannya.</div></div></li>
          <li><span class="ic"><i class="bi bi-patch-check-fill"></i></span><div><strong>Terintegrasi ODS</strong><div class="text-muted small">Terhubung langsung dengan Online Data System (ODS) Kementerian Koperasi.</div></div></li>
          <li><span class="ic"><i class="bi bi-globe-americas"></i></span><div><strong>Sertifikasi Resmi</strong><div class="text-muted small">Hasil uji kelayakan divalidasi oleh asesor tersertifikasi dan berwenang.</div></div></li>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- ============================================
     CAUSES
============================================ -->
<section class="section-pad bg-white" id="causes">
  <div class="container">
    <div class="text-center mx-auto mb-5" style="max-width:640px;" data-aos="fade-up">
      <span class="eyebrow"><i class="bi bi-heart-fill"></i> Layanan Kami</span>
      <h2 class="section-title mt-3" style="font-size:clamp(1.8rem,3.2vw,2.5rem);">Modul Penilaian & Evaluasi</h2>
      <p class="section-sub mx-auto mt-2">Berbagai instrumen pengukuran untuk memastikan koperasi Anda memenuhi standar operasional dan kesehatan yang ditetapkan pemerintah.</p>
    </div>
    <div class="row g-4">
    
    @if($landingPage && $landingPage->services)
        @foreach($landingPage->services as $index => $service)
      <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
        <div class="cause-card">
          <div class="p-5 bg-light border-bottom text-center position-relative d-flex flex-column align-items-center justify-content-center" style="border-radius: 12px 12px 0 0; min-height: 220px;">
            <i class="bi {{ $service['icon'] ?? 'bi-clipboard-check-fill' }}" style="font-size:4.5rem; color:var(--clr-primary); line-height: 1;"></i>
            <span class="position-absolute" style="bottom:-14px; left:50%; transform:translateX(-50%); white-space:nowrap; padding: 6px 20px; background:var(--clr-primary); color:white; border-radius:30px; font-size:0.8rem; font-weight:700; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);">{{ $service['tag'] ?? 'Modul' }}</span>
          </div>
          <div class="cause-body pt-5">
            <h5 class="font-display fw-bold text-center">{{ $service['title'] ?? '' }}</h5>
            <p class="text-muted small mb-0">{{ $service['description'] ?? '' }}</p>
            <div class="mt-4">
              <a href="{{ url('/koperasi') }}" class="cause-donate w-100 d-block text-center" style="text-decoration: none;">Akses Portal Koperasi</a>
            </div>
          </div>
        </div>
      </div>
        @endforeach
    @endif

    </div>
  </div>
</section>

<!-- ============================================
     WHY CHOOSE US
============================================ -->
<section class="section-pad" id="why" style="background:var(--clr-bg);">
  <div class="container">
    <div class="text-center mx-auto mb-5" style="max-width:640px;" data-aos="fade-up">
      <span class="eyebrow"><i class="bi bi-award-fill"></i> Keunggulan SIFITKOP</span>
      <h2 class="section-title mt-3" style="font-size:clamp(1.8rem,3.2vw,2.5rem);">Sistem Terpercaya untuk Koperasi</h2>
    </div>
    <div class="row g-4">
      @if($landingPage && $landingPage->features)
        @foreach($landingPage->features as $index => $feature)
      <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $index * 80 }}">
        <div class="why-card">
          <div class="why-ic"><i class="bi {{ $feature['icon'] ?? 'bi-check-circle' }}"></i></div>
          <h5 class="font-display fw-bold">{{ $feature['title'] ?? '' }}</h5>
          <p class="text-muted mb-0">{{ $feature['description'] ?? '' }}</p>
        </div>
      </div>
        @endforeach
      @endif
    </div>
  </div>
</section>

<!-- ============================================
     HOW IT WORKS
============================================ -->
<section class="section-pad bg-white" id="how">
  <div class="container contact-card">
    <div class="text-center mx-auto mb-5" style="max-width:640px;" data-aos="fade-up">
      <span class="eyebrow"><i class="bi bi-signpost-split-fill"></i> Alur Pengajuan</span>
      <h2 class="section-title mt-3" style="font-size:clamp(1.8rem,3.2vw,2.5rem);">Proses Fit & Proper Test</h2>
    </div>
    <div class="hiw-wrap">
      <div class="hiw-line"></div>
      <div class="row g-4">
        <div class="col-md-4 hiw-step" data-aos="fade-up">
          <div class="hiw-num">1</div>
          <h5 class="font-display fw-bold">Pendaftaran Peserta</h5>
          <p class="text-muted">Koperasi mendaftarkan kandidat pengurus/pengawas melalui portal koperasi untuk mendapatkan token asesmen.</p>
        </div>
        <div class="col-md-4 hiw-step" data-aos="fade-up" data-aos-delay="120">
          <div class="hiw-num">2</div>
          <h5 class="font-display fw-bold">Isi Self-Assessment</h5>
          <p class="text-muted">Kandidat melengkapi form evaluasi mandiri dan mengunggah dokumen paparan via link token.</p>
        </div>
        <div class="col-md-4 hiw-step" data-aos="fade-up" data-aos-delay="240">
          <div class="hiw-num">3</div>
          <h5 class="font-display fw-bold">Evaluasi & Hasil</h5>
          <p class="text-muted">Asesor melakukan penilaian dan menerbitkan sertifikat kelayakan atau rekomendasi.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============================================
     VOLUNTEERS
============================================ -->
<section class="section-pad d-none" id="volunteers">
  <div class="container">
    <div class="text-center mx-auto mb-5" style="max-width:640px;" data-aos="fade-up">
      <span class="eyebrow"><i class="bi bi-people-fill"></i> Tim Kami</span>
      <h2 class="section-title mt-3" style="font-size:clamp(1.8rem,3.2vw,2.5rem);">Tim Asesor Tersertifikasi</h2>
    </div>
    <div class="row g-4">
      @if($landingPage && $landingPage->team)
        @foreach($landingPage->team as $index => $member)
      <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="{{ $index * 80 }}">
        <div class="vol-card">
          <div class="vol-img"><img src="{{ !empty($member['image']) ? Storage::url($member['image']) : 'https://img.magnific.com/free-photo/portrait-young-handsome-guy-chrsitmas_114579-78899.jpg' }}" alt="{{ $member['name'] ?? '' }}" loading="lazy"></div>
          <div class="vol-overlay">
            <div class="fw-bold font-display">{{ $member['name'] ?? '' }}</div>
            <div class="small opacity-75">{{ $member['role'] ?? '' }}</div>
            <div class="vol-social"><a href="#"><i class="bi bi-facebook"></i></a><a href="#"><i class="bi bi-instagram"></i></a><a href="#"><i class="bi bi-linkedin"></i></a></div>
          </div>
        </div>
      </div>
        @endforeach
      @endif
    </div>
  </div>
</section>

<!-- ============================================
     TESTIMONIALS
============================================ -->
<section class="section-pad bg-white d-none" id="testimonials">
  <div class="container">
    <div class="testi-section p-4 p-md-5" data-aos="zoom-in">
      <div class="text-center mx-auto mb-5" style="max-width:600px;">
        <span class="eyebrow" style="background:rgba(255,255,255,.15);color:#fff;"><i class="bi bi-chat-quote-fill"></i> Testimoni Koperasi</span>
        <h2 class="section-title mt-3 text-white" style="font-size:clamp(1.7rem,3vw,2.3rem);">Pengalaman Bersama SIFITKOP</h2>
      </div>
      <div id="testiCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
          @if($landingPage && is_array($landingPage->testimonials) && count($landingPage->testimonials) > 0)
            @foreach(array_chunk($landingPage->testimonials, 3) as $chunkIndex => $chunk)
            <div class="carousel-item {{ $chunkIndex === 0 ? 'active' : '' }}">
              <div class="row g-4 justify-content-center">
                @foreach($chunk as $testi)
                <div class="col-md-6 col-lg-4">
                  <div class="testi-card">
                    <div class="testi-stars"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                    <p class="testi-quote">"{{ $testi['quote'] ?? '' }}"</p>
                    <div class="testi-person">
                      <img src="https://images.unsplash.com/photo-1633332755192-727a05c4013d?w=100&h=100&fit=crop&auto=format" alt="{{ $testi['name'] ?? '' }}">
                      <div><div class="testi-name">{{ $testi['name'] ?? '' }}</div><div class="testi-role">{{ $testi['role'] ?? '' }}</div></div>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
            </div>
            @endforeach
          @else
            <div class="text-center text-muted">Belum ada testimoni.</div>
          @endif
        </div>
        <div class="carousel-indicators">
          @if($landingPage && is_array($landingPage->testimonials) && count($landingPage->testimonials) > 0)
            @foreach(array_chunk($landingPage->testimonials, 3) as $chunkIndex => $chunk)
            <button type="button" data-bs-target="#testiCarousel" data-bs-slide-to="{{ $chunkIndex }}" class="{{ $chunkIndex === 0 ? 'active' : '' }}" aria-label="Slide {{ $chunkIndex + 1 }}"></button>
            @endforeach
          @endif
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============================================
     CTA BANNER
============================================ -->
<section class="section-pad" id="cta">
  <div class="container">
    <div class="cta-banner" data-aos="zoom-in">
      <i class="bi bi-heart-fill" style="font-size:2.4rem;"></i>
      <h2 class="mt-3">Wujudkan Koperasi yang Kompeten, Transparan, dan Modern Bersama Kami.</h2>
      <p class="mb-4" style="opacity:.92;">Tingkatkan kualitas SDM dan daya saing Koperasi Anda melalui Uji Kelayakan SIFITKOP.</p>
      <a href="{{ url('/koperasi') }}" class="btn-cta" style="text-decoration:none;"><i class="bi bi-heart-fill me-2"></i>Mulai Penilaian</a>
    </div>
  </div>
</section>

<!-- ============================================
     CONTACT
============================================ -->
<section class="section-pad bg-white" id="contact">
  <div class="container">
    <div class="text-center mx-auto mb-5" style="max-width:640px;" data-aos="fade-up">
      <span class="eyebrow"><i class="bi bi-envelope-fill"></i> Hubungi Kami</span>
      <h2 class="section-title mt-3" style="font-size:clamp(1.8rem,3.2vw,2.5rem);">Dinas Koperasi dan UKM Jateng</h2>
    </div>
    <div class="row g-5">
      <div class="col-lg-5 contact-card" data-aos="fade-right">
        <div class="contact-info-item">
          <span class="ic"><i class="bi bi-geo-alt-fill"></i></span>
          <div><strong>Kantor Kami</strong><div class="text-muted small">{{ $landingPage->contact_address ?? 'Jl. Sisingamangaraja No. 3A, Semarang, Jawa Tengah' }}</div></div>
        </div>
        <div class="contact-info-item">
          <span class="ic"><i class="bi bi-envelope-fill"></i></span>
          <div><strong>Email Kami</strong><div class="text-muted small">{{ $landingPage->contact_email ?? 'diskopukm@jatengprov.go.id' }}</div></div>
        </div>
        <div class="contact-info-item">
          <span class="ic"><i class="bi bi-telephone-fill"></i></span>
          <div><strong>Telepon</strong><div class="text-muted small">{{ $landingPage->contact_phone ?? '(024) 8318771' }}</div></div>
        </div>
        <div class="map-placeholder"><i class="bi bi-pin-map-fill me-2"></i> Map Embed Placeholder</div>
      </div>
      <div class="col-lg-7" data-aos="fade-left">
        <div class="contact-card">
          <form id="contactForm" novalidate>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label small fw-semibold" for="cf-name">Nama Lengkap</label>
                <input type="text" class="form-control form-control-hw" id="cf-name" placeholder="Jane Doe" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold" for="cf-email">Alamat Email</label>
                <input type="email" class="form-control form-control-hw" id="cf-email" placeholder="jane@example.com" required>
              </div>
              <div class="col-12">
                <label class="form-label small fw-semibold" for="cf-subject">Subjek</label>
                <input type="text" class="form-control form-control-hw" id="cf-subject" placeholder="How can we help?">
              </div>
              <div class="col-12">
                <label class="form-label small fw-semibold" for="cf-message">Pesan</label>
                <textarea class="form-control form-control-hw" id="cf-message" rows="4" placeholder="Tuliskan pertanyaan Anda di sini&hellip;" required></textarea>
              </div>
              <div class="col-12">
                <button type="submit" class="btn-hw-primary w-100 justify-content-center">Send Pesan <i class="bi bi-send"></i></button>
              </div>
              <div class="col-12" id="formStatus" role="status" aria-live="polite"></div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============================================
     FOOTER
============================================ -->
<footer class="footer-hw">
  <svg class="footer-wave" viewBox="0 0 1440 70" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <path fill="#0B1220" d="M0,32 C240,70 480,0 720,18 C960,36 1200,70 1440,26 L1440,70 L0,70 Z"></path>
  </svg>
  <span class="footer-glow"></span>
  <span class="footer-glow-2"></span>
  <div class="container footer-top">
    <div class="row g-4">
      <div class="col-lg-4 col-md-6">
        <a class="hw-logo text-white" href="#home">
          <span class="hw-logo-mark"><i class="bi bi-heart-fill"></i></span>
          SIFIT<span class="dot" style="color:var(--clr-primary);">KOP</span>
        </a>
        <p class="mt-3">{{ $landingPage->footer_description ?? 'SIFITKOP adalah Sistem Informasi Fasilitasi Uji Kelayakan dan Kepatuhan Koperasi yang dikembangkan untuk memastikan tata kelola koperasi yang baik, berintegritas, dan akuntabel di Provinsi Jawa Tengah.' }}</p>
        <div class="footer-social mt-3">
          <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
          <a href="#" aria-label="Twitter / X"><i class="bi bi-twitter-x"></i></a>
          <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
          <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
          <a href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
        </div>
        <div class="footer-badges">
          <span class="footer-badge"><i class="bi bi-patch-check-fill"></i> Pemerintah Provinsi</span>
          <span class="footer-badge"><i class="bi bi-shield-lock-fill"></i> Data Dilindungi</span>
        </div>
      </div>

      <div class="col-6 col-md-3 col-lg-2">
        <h6>Tautan Cepat</h6>
        <ul>
          <li><a class="footer-link" href="#about"><i class="bi bi-caret-right-fill"></i>About Us</a></li>
          <li><a class="footer-link" href="#causes"><i class="bi bi-caret-right-fill"></i>Causes</a></li>
          <li><a class="footer-link" href="#why"><i class="bi bi-caret-right-fill"></i>Keunggulan SIFITKOP</a></li>
          <li><a class="footer-link" href="#volunteers"><i class="bi bi-caret-right-fill"></i>Volunteers</a></li>
          <li><a class="footer-link" href="#testimonials"><i class="bi bi-caret-right-fill"></i>Testimonials</a></li>
          <li><a class="footer-link" href="#contact"><i class="bi bi-caret-right-fill"></i>Contact</a></li>
        </ul>
      </div>

      <div class="col-6 col-md-3 col-lg-2">
        <h6>Causes</h6>
        <ul>
          <li><a class="footer-link" href="#causes"><i class="bi bi-caret-right-fill"></i>Penilaian</a></li>
          <li><a class="footer-link" href="#causes"><i class="bi bi-caret-right-fill"></i>Kesehatan</a></li>
          <li><a class="footer-link" href="#causes"><i class="bi bi-caret-right-fill"></i>Sertifikasi</a></li>
          <li><a class="footer-link" href="#causes"><i class="bi bi-caret-right-fill"></i>Hukum</a></li>
          <li><a class="footer-link" href="#causes"><i class="bi bi-caret-right-fill"></i>Keuangan</a></li>
          <li><a class="footer-link" href="#causes"><i class="bi bi-caret-right-fill"></i>Pemeringkatan</a></li>
        </ul>
      </div>

      <div class="col-md-6 col-lg-4">
        <h6>Hubungi Kami</h6>
        <div class="footer-contact-item">
          <span class="ic"><i class="bi bi-geo-alt-fill"></i></span>
          <span>Jl. Sisingamangaraja No. 3A, Semarang, Jawa Tengah</span>
        </div>
        <div class="footer-contact-item">
          <span class="ic"><i class="bi bi-envelope-fill"></i></span>
          <span>diskopukm@jatengprov.go.id</span>
        </div>
        <div class="footer-contact-item">
          <span class="ic"><i class="bi bi-telephone-fill"></i></span>
          <span>(024) 8318771</span>
        </div>
        <h6 class="mt-4">Buletin Informasi</h6>
        <div class="newsletter-input">
          <input type="email" placeholder="Alamat email Anda" aria-label="Email for newsletter">
          <button type="button" aria-label="Subscribe"><i class="bi bi-arrow-right"></i></button>
        </div>
        <div class="newsletter-note"><i class="bi bi-shield-check"></i> Dapatkan info terbaru dari Dinas Koperasi.</div>
      </div>
    </div>

    <div class="footer-divider"></div>
    <div class="footer-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
      <div>
        <div>&copy; 2026 Dinas Koperasi UKM Provinsi Jawa Tengah. All rights reserved by <a href="https://www.templatemonster.com/authors/k29solutions/" target="_blank" rel="noopener noreferrer">K29Solutions</a></div>
        <div class="footer-bottom-links mt-2">
          <a href="privacy-policy.html">Privacy Policy</a>
          <a href="terms-of-service.html">Terms of Service</a>
          <a href="financial-reports.html">Financial Reports</a>
          <a href="faq.html">FAQs</a>
        </div>
      </div>
      <div class="text-md-end">
        
        <div>Made with <i class="bi bi-heart-fill text-primary-hw"></i> for a better world &bull; Pengembangan oleh Tim IT Diskop UKM Jateng</div>
      </div>
    </div>
  </div>
</footer>

<!-- ============================================
     MOBILE STICKY DONATE BAR
============================================ -->
<div class="mobile-donate-bar">
  <a href="#causes" class="btn-hw-primary">Mulai Penilaian <i class="bi bi-heart-fill"></i></a>
</div>

<!-- ============================================
     SCROLL TO TOP
============================================ -->
<div id="scrollTop" role="button" aria-label="Scroll to top" tabindex="0">
  <svg viewBox="0 0 52 52"><circle cx="26" cy="26" r="23"></circle><circle class="progress" cx="26" cy="26" r="23"></circle></svg>
  <i class="bi bi-arrow-up"></i>
</div>

<!-- ============================================
     WATCH STORY MODAL (demo)
============================================ -->
<div class="modal fade" id="storyModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" style="border-radius:20px;border:none;">
      <div class="modal-header border-0">
        <h5 class="modal-title font-display fw-bold">Tentang Kami</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pb-4">
        <div class="ratio ratio-16x9 rounded-4 overflow-hidden">
          <img src="https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=900&h=500&fit=crop&auto=format" alt="Hopewell story preview" style="object-fit:cover;">
        </div>
        <p class="text-muted mt-3 mb-0">Video placeholder &mdash; connect your hosted story video here.</p>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap Bundle -->
<script src="{{ asset('front_assets/bootstrap.bundle.min.js') }}"></script>
<!-- AOS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="{{ asset('front_assets/script.js') }}"></script>

</body>
</html>
