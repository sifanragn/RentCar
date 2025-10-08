<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-black flex justify-center">
  <div class="w-[360px] bg-white min-h-screen px-6 py-10 rounded-t-[40px]">
    <!-- Logo -->
    <div class="flex justify-center mb-8">
      <img src="https://placehold.co/235x83" alt="Logo" class="w-40">
    </div>

    <!-- Judul -->
    <h1 class="text-4xl font-extrabold text-teal-950 text-center mb-2">Get Started Free!</h1>
    <p class="text-lg text-teal-950 text-center mb-8">Ayo Buat Akun Dan Cari Mobilmu</p>

    <!-- Form -->
    <form action="#" method="POST" class="flex flex-col gap-5">
      <!-- Nama Lengkap -->
      <div>
        <label for="nama" class="block text-2xl font-semibold text-zinc-700 mb-2">Nama Lengkap</label>
        <input type="text" id="nama" name="nama" placeholder="Masukkan Nama Lengkap"
               class="w-full bg-gray-200 rounded-2xl py-3 px-4 text-xl placeholder-teal-900 opacity-70 focus:outline-none">
      </div>

      <!-- Username -->
      <div>
        <label for="username" class="block text-2xl font-semibold text-zinc-700 mb-2">Username</label>
        <input type="text" id="username" name="username" placeholder="Masukkan Username"
               class="w-full bg-gray-200 rounded-2xl py-3 px-4 text-xl placeholder-teal-900 opacity-70 focus:outline-none">
      </div>

      <!-- Email -->
      <div>
        <label for="email" class="block text-2xl font-semibold text-zinc-700 mb-2">Email</label>
        <input type="email" id="email" name="email" placeholder="Masukkan Email Anda"
               class="w-full bg-gray-200 rounded-2xl py-3 px-4 text-xl placeholder-teal-900 opacity-70 focus:outline-none">
      </div>

      <!-- Foto KTP -->
      <div>
        <label for="ktp" class="block text-2xl font-semibold text-zinc-700 mb-2">Foto KTP</label>
        <input type="file" id="ktp" name="ktp"
               class="w-full bg-gray-200 rounded-2xl py-3 px-4 text-xl text-teal-900 focus:outline-none">
      </div>

      <!-- Foto KK -->
      <div>
        <label for="kk" class="block text-2xl font-semibold text-zinc-700 mb-2">Foto KK</label>
        <input type="file" id="kk" name="kk"
               class="w-full bg-gray-200 rounded-2xl py-3 px-4 text-xl text-teal-900 focus:outline-none">
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="block text-2xl font-semibold text-zinc-700 mb-2">Password</label>
        <input type="password" id="password" name="password" placeholder="Masukkan Password"
               class="w-full bg-gray-200 rounded-2xl py-3 px-4 text-xl placeholder-teal-900 opacity-70 focus:outline-none">
      </div>

      <!-- Tombol -->
      <button type="submit"
              class="w-full bg-black text-white py-4 text-2xl font-bold rounded-2xl mt-6 hover:bg-neutral-800 transition">
        Daftar
      </button>

      <!-- Sudah Punya Akun -->
      <p class="text-center text-2xl font-medium text-black mt-4">Sudah Punya Akun?
        <a href="/login" class="text-blue-600 font-semibold">Login</a>
      </p>
    </form>
  </div>
</body>
</html>
