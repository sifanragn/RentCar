@extends('partials.container')

@section('styles')
<style>
 body {
  background: #f8f8f8;
  font-family: 'Poppins', sans-serif;
}

/* Fullscreen override */
.container-wrapper,
.container {
  max-width: 100% !important;
  padding: 0 !important;
  margin: 0 !important;
}

.otp-wrapper {
  max-width: 380px;
  margin: 0 auto;
  margin-top: 55px;
  padding: 30px 20px;
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.08);
  text-align: center;
}

.otp-title {
  font-size: 22px;
  font-weight: 700;
  color: #000;
  margin-bottom: 6px;
}

.otp-subtitle {
  font-size: 14px;
  color: #555;
  margin-bottom: 22px;
}

/* ✅ OTP Boxes */
.otp-inputs {
  display: flex;
  justify-content: center;
  gap: 10px;
  margin-bottom: 22px;
}

.otp-inputs input {
  width: 48px;
  height: 54px;
  font-size: 20px;
  text-align: center;
  border: 2px solid #ddd;
  border-radius: 10px;
  background: #fafafa;
  font-weight: 600;
  outline: none;
  transition: .2s ease;
}

.otp-inputs input:focus {
  border-color: #000;
  background: #fff;
}

/* Button verify */
.btn-verify {
  width: 100%;
  background: #000;
  color: #fff;
  font-weight: 600;
  padding: 12px;
  border-radius: 14px;
  border: none;
  cursor: pointer;
  transition: .2s;
}

.btn-verify:hover {
  background: #111;
}

/* Text info */
.otp-info {
  font-size: 13px;
  color: #666;
  margin-top: 18px;
}

/* Resend */
.resend-btn {
  margin-top: 4px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  border: none;
  background: none;
  color: #000;
  transition: .2s;
}

.resend-btn.disabled {
  color: #aaa;
  pointer-events: none;
}

/* Timer */
.otp-timer {
  margin-top: 5px;
  font-size: 14px;
  color: #444;
  font-weight: 500;
  opacity: .95;
}

.otp-timer span {
  font-weight: 700;
  color: #000;
  animation: tick 1s linear infinite;
}

@keyframes tick {
  0% { opacity: 1 }
  50% { opacity: .5 }
  100% { opacity: 1 }
}
</style>
@endsection


@section('content')
<div class="otp-wrapper">

  <h3 class="otp-title">Verifikasi WhatsApp</h3>
  <p class="otp-subtitle">Masukkan 6 digit kode yang kami kirim ke WhatsApp kamu</p>

  @if(session('error'))
    <div style="color:#d9534f;margin-bottom:10px;font-weight:500">
      ⚠️ {{ session('error') }}
    </div>
  @endif

  @if(session('success'))
    <div style="color:#28a745;margin-bottom:10px;font-weight:500">
      ✅ {{ session('success') }}
    </div>
  @endif

  <form action="{{ route('register.verifyOtp') }}" method="POST" id="otpForm">
    @csrf

    <div class="otp-inputs">
      @for ($i = 0; $i < 6; $i++)
        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-box">
      @endfor
    </div>

    <input type="hidden" name="otp" id="otpValue">

    <button class="btn-verify">Verifikasi</button>

    <p class="otp-info">Belum menerima kode?</p>

<p class="otp-timer" id="timerWrapper">
  Kirim ulang dalam <span id="timer">60</span> detik
</p>

<button type="button" class="resend-btn disabled" id="resendBtn">
  Kirim Ulang Kode
</button>

  </form>
</div>

<script>
  const inputs = document.querySelectorAll('.otp-box');
const otpValue = document.getElementById('otpValue');
const resendBtn = document.getElementById('resendBtn');
const timerEl = document.getElementById('timer');
const timerWrapper = document.getElementById('timerWrapper');

let countdown;
const OTP_KEY = "otp_expire_time";
const OTP_CAN_RESEND = "otp_can_resend";

// ===== TIMER =====
function startTimer(duration = 60) {
  const expireTime = Date.now() + duration * 1000;
  localStorage.setItem(OTP_KEY, expireTime);
  localStorage.setItem(OTP_CAN_RESEND, "0"); // 🚫 belum bisa resend
  runTimer();
}

function runTimer() {
  clearInterval(countdown);

  countdown = setInterval(() => {
    const expireTime = localStorage.getItem(OTP_KEY);
    const canResend = localStorage.getItem(OTP_CAN_RESEND) === "1";
    const diff = Math.floor((expireTime - Date.now()) / 1000);

    // ✅ Sudah boleh resend (expired)
    if (canResend) {
      timerWrapper.style.display = "none";
      resendBtn.classList.remove("disabled");
      return;
    }

    // ✅ Countdown habis
    if (diff <= 0) {
      clearInterval(countdown);
      timerWrapper.style.display = "none";
      resendBtn.classList.remove("disabled");
      localStorage.setItem(OTP_CAN_RESEND, "1"); // tandai siap resend
      return;
    }

    // ⏳ Countdown jalan
    timerWrapper.style.display = "block";
    timerEl.textContent = diff;
    resendBtn.classList.add("disabled");

  }, 1000);
}

// ===== INIT ON PAGE LOAD =====
let savedTime = localStorage.getItem(OTP_KEY);
let canResend = localStorage.getItem(OTP_CAN_RESEND) === "1";

if (savedTime && savedTime > Date.now() && !canResend) {
  runTimer();
} else {
  // langsung siap resend (jika waktu habis sebelumnya)
  timerWrapper.style.display = "none";
  resendBtn.classList.remove("disabled");
  localStorage.setItem(OTP_CAN_RESEND, "1");
}

// ===== INPUT BOXES =====
inputs[0].focus();
inputs.forEach((input, index) => {
  input.addEventListener('input', () => {
    input.value = input.value.replace(/\D/g, '');
    if (input.value && index < 5) inputs[index + 1].focus();
    otpValue.value = [...inputs].map(i => i.value).join('');
  });

  input.addEventListener('keydown', e => {
    if (e.key === 'Backspace' && !input.value && index > 0) {
      inputs[index - 1].focus();
    }
  });
});

// ===== RESEND ACTION =====
resendBtn.addEventListener('click', function () {
  if (resendBtn.classList.contains("disabled")) return;

  resendBtn.textContent = "Mengirim...";
  resendBtn.classList.add("disabled");

  fetch("{{ route('register.resendOtp') }}", {
    method: "POST",
    headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === true) {
      resendBtn.textContent = "Kirim Ulang Kode";
      startTimer(60); // Reset timer
    } else {
      resendBtn.textContent = "Kirim Ulang Kode";
      alert(data.message);
    }
  })
  .catch(() => {
    resendBtn.textContent = "Kirim Ulang Kode";
    resendBtn.classList.remove("disabled");
    alert("Gagal mengirim OTP, coba lagi.");
  });
});

</script>
@endsection
