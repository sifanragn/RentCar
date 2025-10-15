@extends('layouts.app')

@section('title', 'Hubungi Kami')
@section('page_title', 'Hubungi Kami')

@section('content')
<style>
  body {
    font-family: 'Poppins', sans-serif;
    background: #fff;
    margin: 0;
    padding: 0;
  }

  .contact-section {
    text-align: center;
    padding: 40px 20px;
  }

  .contact-section h1 {
    font-size: 26px;
    font-weight: 700;
    margin-bottom: 8px;
    color: #111;
  }

  .contact-section p.sub {
    font-size: 14px;
    color: #555;
    margin-bottom: 35px;
  }

  .info-card {
    background: #000;
    color: white;
    border-radius: 20px;
    padding: 30px;
    max-width: 420px;
    margin: auto;
    text-align: left;
    position: relative;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
  }

  .info-card::before,
  .info-card::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    width: 80px; height: 80px;
    background: linear-gradient(135deg, #111, #444);
    opacity: 0.4;
  }
  .info-card::before { top: -20px; left: -30px; }
  .info-card::after { bottom: -20px; right: -30px; }

  .info-card h2 {
    margin: 0 0 15px;
    font-size: 18px;
    color: #fff;
  }

  .info-card p {
    font-size: 13px;
    line-height: 1.6;
    color: #ddd;
    margin-bottom: 20px;
  }

  .info-item {
    display: flex;
    align-items: center;
    margin: 8px 0;
  }

  .info-item i {
    font-size: 16px;
    margin-right: 10px;
    color: #0d6efd;
  }

  .socials {
    margin: 35px 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 15px;
  }

  .social-btn {
    width: 250px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 12px 0;
    border-radius: 10px;
    font-weight: 600;
    color: white;
    text-decoration: none;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .social-btn i {
    margin-right: 8px;
    font-size: 18px;
  }

  .social-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.25);
  }

  .wa { background: linear-gradient(90deg, #25D366, #128C7E); }
  .ig { background: linear-gradient(90deg, #f09433, #dc2743, #bc1888); }
  .fb { background: linear-gradient(90deg, #1877f2, #145dbf); }

  .footer-logo { margin-top: 40px; }
  .footer-logo img { width: 120px; opacity: 0.9; }

  @media (max-width: 480px) {
    .info-card { width: 90%; padding: 25px; }
  }
</style>

<div class="contact-section">
  <h1>Hubungi Kami</h1>
  <p class="sub">Punya Pertanyaan Atau Kendala? Silakan Hubungi Kami!</p>

  <div class="info-card">
    <h2>Kontak Informasi</h2>
    <p>Kami siap membantu Anda. Jika ada pertanyaan atau membutuhkan informasi lebih lanjut, silakan hubungi kami melalui:</p>

    <div class="info-item"><i class="fa fa-envelope"></i> rentalcarid@gmail.com</div>
    <div class="info-item"><i class="fa fa-phone"></i> +62 823 0001 0991</div>
    <div class="info-item"><i class="fa fa-map-marker"></i> Jl. Cigar Tengah, Abdurrahman</div>
  </div>

  <div class="socials">
    <a href="https://wa.me/6282300010991" class="social-btn wa" target="_blank"><i class="fa fa-whatsapp"></i> WhatsApp</a>
    <a href="https://instagram.com/" class="social-btn ig" target="_blank"><i class="fa fa-instagram"></i> Instagram</a>
    <a href="https://facebook.com/" class="social-btn fb" target="_blank"><i class="fa fa-facebook"></i> Facebook</a>
  </div>
  @if(session('success'))
  <div style="background:#d1e7dd;color:#0f5132;padding:10px;border-radius:6px;margin-bottom:10px;">
    {{ session('success') }}
  </div>
@endif

<form action="{{ route('user.kontak.store') }}" method="POST" style="max-width:500px;margin:40px auto;text-align:left;">
  @csrf
  <label>Subjek (Opsional)</label>
  <input type="text" name="subject" placeholder="Contoh: Kendala Pembayaran"
         style="width:100%;padding:10px;margin-top:5px;border-radius:8px;border:1px solid #ccc;margin-bottom:15px;">

  <label>Pesan Anda</label>
  <textarea name="message" required rows="4" placeholder="Tuliskan pesan Anda..."
         style="width:100%;padding:10px;border-radius:8px;border:1px solid #ccc;margin-top:5px;"></textarea>

  <button type="submit"
          style="margin-top:15px;background:#0d6efd;color:white;padding:10px 20px;border:none;border-radius:8px;cursor:pointer;">
    Kirim Pesan
  </button>
</form>


  <div class="footer-logo">
    <img src="{{ asset('img/logo_rental.png') }}" alt="Rental Logo">
  </div>
</div>

<script src="https://kit.fontawesome.com/a2e8f1f6f0.js" crossorigin="anonymous"></script>
@endsection
