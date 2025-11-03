@extends('layouts.otp')

@section('title', 'Verifikasi OTP')

@push('styles')
<style>
body {
  background: #ffffff !important;
  font-family: 'Poppins', sans-serif;
}
html, body { height: 100%; }

/* Page wrapper */
.otp-page-container {
  display: flex;
  justify-content: center;
  padding: 0 18px;
}

/* Card */
.otp-wrapper {
  width: 100%;
  max-width: 400px;
  margin-top: 55px;
  padding: 28px 22px;
  background: #fff;
  border-radius: 18px;
  border: 1px solid rgba(0,0,0,0.06);
  box-shadow: 0 8px 25px rgba(0,0,0,0.07);
  text-align: center;
}

/* Icon circle */
.otp-icon-box {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: #f5f6f8;
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 0 auto 8px;
  font-size: 29px;
  color: #25D366;
}

/* Success badge */
.otp-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  background: #e7f9eb;
  color: #2fa74b;
  font-size: 12px;
  font-weight: 500;
  padding: 4px 8px;
  border-radius: 6px;
  margin-bottom: 8px;
}

/* Title */
.otp-title {
  font-size: 21px;
  font-weight: 700;
  margin-bottom: 4px;
}
.otp-subtitle {
  font-size: 13.5px;
  color: #555;
  margin-bottom: 18px;
}

/* OTP Inputs */
.otp-inputs {
  display: flex;
  justify-content: center;
  gap: 8px;
  margin-bottom: 20px;
  padding: 0 6px;
}
.otp-inputs input {
  width: 42px;
  height: 50px;
  font-size: 20px;
  font-weight: 600;
  text-align: center;
  border-radius: 12px;
  border: 2px solid #e0e0e0;
  background: #fff;
  transition: .2s;
}
.otp-inputs input:focus {
  border-color: #000;
  box-shadow: 0 0 6px rgba(0,0,0,0.15);
}

/* Verify button */
.btn-verify {
  width: 100%;
  padding: 13px;
  font-size: 15px;
  font-weight: 600;
  color: #fff;
  background: #000;
  border-radius: 12px;
  border: none;
  cursor: pointer;
  transition: .22s;
}
.btn-verify:hover {
  background: #111;
  transform: scale(.98);
}

/* Bottom text */
.otp-info {
  font-size: 13px;
  color: #666;
  margin-top: 16px;
}

/* Timer */
.otp-timer {
  margin-top: 4px;
  font-size: 13.5px;
  font-weight: 500;
  color: #333;
}

/* Resend button */
.resend-btn {
  margin-top: 6px;
  font-size: 14px;
  font-weight: 600;
  color: #25D366;
  background: none;
  border: none;
  cursor: pointer;
  transition: .2s;
}
.resend-btn.disabled {
  opacity: .4;
  pointer-events: none;
}
</style>
@endpush


@section('content')
<div class="otp-page-container">
<div class="otp-wrapper">

  {{-- Icon --}}
  <div class="otp-icon-box">
    <i class="fab fa-whatsapp"></i>
  </div>

  {{-- Success badge --}}
  @if(session('success'))
    <div class="otp-badge">
      <i class="fas fa-check-circle"></i> OTP terkirim
    </div>
  @endif

  <h3 class="otp-title">Verifikasi WhatsApp</h3>
  <p class="otp-subtitle">Masukkan 6 digit kode dari WhatsApp</p>

  {{-- Error --}}
  @if(session('error'))
    <div style="color:#d9534f; margin-bottom:10px; font-size:13px;">
      <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
    </div>
  @endif

  <form action="{{ route('register.verifyOtp') }}" method="POST" id="otpForm">
    @csrf

    {{-- OTP Inputs --}}
    <div class="otp-inputs">
      @for ($i = 0; $i < 6; $i++)
        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*">
      @endfor
    </div>

    <input type="hidden" name="otp" id="otpValue">

    <button class="btn-verify">Verifikasi</button>

    <p class="otp-info">Belum menerima kode?</p>

    <p class="otp-timer" id="timerWrapper">
      Kirim ulang dalam <span id="timer">60</span> detik
    </p>

    <button type="button" class="resend-btn disabled" id="resendBtn">
      <i class="fa fa-redo"></i> Kirim Ulang Kode
    </button>

  </form>

</div>
</div>
@endsection


@push('scripts')
<script>
const inputs=document.querySelectorAll('.otp-inputs input');
const otpValue=document.getElementById('otpValue');
const resendBtn=document.getElementById('resendBtn');
const timerEl=document.getElementById('timer');
const timerWrapper=document.getElementById('timerWrapper');

let countdown;
const OTP_KEY="otp_expire_time";

function startTimer(d=60){
  let t=Date.now()+d*1000;
  localStorage.setItem(OTP_KEY,t);
  runTimer();
}
function runTimer(){
  clearInterval(countdown);
  countdown=setInterval(()=>{
    let t=localStorage.getItem(OTP_KEY);
    let left=Math.floor((t-Date.now())/1000);
    if(left<=0){
      clearInterval(countdown);
      timerWrapper.style.display="none";
      resendBtn.classList.remove("disabled");
      return;
    }
    timerEl.textContent=left;
  },1000);
}

let saved=localStorage.getItem(OTP_KEY);
(!saved||saved<Date.now())?startTimer():runTimer();

inputs[0].focus();
inputs.forEach((inp,i)=>{
  inp.addEventListener("input",()=>{
    inp.value=inp.value.replace(/\D/g,'');
    if(inp.value && i<5) inputs[i+1].focus();
    otpValue.value=[...inputs].map(x=>x.value).join('');
  });
  inp.addEventListener("keydown",e=>{
    if(e.key==="Backspace"&&!inp.value&&i>0) inputs[i-1].focus();
  });
});

resendBtn.addEventListener("click",function(){
  if(this.classList.contains("disabled")) return;

  this.textContent="Mengirim...";
  fetch("{{ route('register.resendOtp') }}",{
    method:"POST",
    headers:{ "X-CSRF-TOKEN":"{{ csrf_token() }}" }
  }).then(r=>r.json()).then(d=>{
    this.textContent="Kirim Ulang Kode";
    d.status?startTimer():alert(d.message);
  });
});
</script>
@endpush
