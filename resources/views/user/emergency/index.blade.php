@extends('partials.container')

@section('title', 'Nomor Darurat')

@section('styles')
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: #f9fafb;
  color: #111;
  margin: 0;
  padding: 0;
}

/* ===== CONTAINER ===== */
.emergency-container {
  max-width: 500px;
  margin: 20px auto 80px;
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.06);
  padding: 20px 22px;
}

h2 {
  text-align: center;
  font-weight: 700;
  font-size: 20px;
  margin-bottom: 25px;
}

/* ===== LIST ITEM ===== */
.emergency-item {
  background: #f6f7f8;
  border-radius: 12px;
  padding: 14px 16px;
  margin-bottom: 12px;
  transition: 0.2s ease;
}
.emergency-item:hover { background: #e9ecef; transform: scale(1.01); }

.item-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.item-header h5 {
  font-size: 15px;
  font-weight: 600;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 6px;
}

.item-number a {
  color: #007bff;
  font-weight: 600;
  text-decoration: none;
  font-size: 14px;
}
.item-number a:hover { text-decoration: underline; }

.item-desc {
  font-size: 13px;
  color: #555;
  margin-top: 6px;
}
</style>
@endsection

@section('content')
<div class="emergency-container">
  <h2>Nomor Darurat</h2>

  @forelse ($numbers as $n)
    <div class="emergency-item">
      <div class="item-header">
        <h5>{{ $n->icon ?? '📞' }} {{ $n->keperluan }}</h5>
        <div class="item-number">
          <a href="tel:{{ $n->nomor }}">{{ $n->nomor }}</a>
        </div>
      </div>
      @if($n->keterangan)
        <p class="item-desc">{{ $n->keterangan }}</p>
      @endif
    </div>
  @empty
    <p class="text-center text-muted">Belum ada nomor darurat.</p>
  @endforelse
</div>

@include('partials.bottom-navbar')
@endsection
