@extends('layouts.admin.app')

@section('title', 'Kelola Nomor Darurat')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/emergency.css?v=' . time()) }}">
@endsection

@section('content')
<div class="container mt-4">

  {{-- 🔹 HEADER --}}
  <div class="emergency-header">
    <h3 class="fw-bold">📞 Daftar Nomor Darurat</h3>

    <a href="{{ route('admin.emergency.create') }}" class="btn-add-emergency">
      + Tambah Nomor
    </a>
  </div>

  {{-- 🔔 ALERT --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  {{-- 🔹 CARD --}}
  <div class="emergency-card">
    <div class="card-body">

      @if($numbers->count())
        <div class="table-responsive">
          <table class="emergency-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Icon</th>
                <th>Nama Kontak</th>
                <th>Keperluan</th>
                <th>Nomor</th>
                <th>Keterangan</th>
                <th>Aksi</th>
              </tr>
            </thead>

            <tbody>
              @foreach ($numbers as $n)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="icon-col">{{ $n->icon ?? '📞' }}</td>

                <td>{{ $n->nama }}</td>
                <td>{{ $n->keperluan ?? '-' }}</td>

                <td>
                  <a href="tel:{{ $n->nomor }}" class="phone-link">
                    {{ $n->nomor }}
                  </a>
                </td>

                <td>{{ $n->keterangan ?? '-' }}</td>

                <td class="emergency-actions">

                  <a href="{{ route('admin.emergency.edit', $n->id) }}" class="btn-edit">
                    <i class="fas fa-edit"></i> Edit
                  </a>

                  <form action="{{ route('admin.emergency.destroy', $n->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-delete"
                      onclick="return confirm('Hapus nomor darurat ini?')">
                      <i class="fas fa-trash-alt"></i> Hapus
                    </button>
                  </form>

                </td>
              </tr>
              @endforeach
            </tbody>

          </table>
        </div>

      @else
        <p class="empty-state">Belum ada nomor darurat.</p>
      @endif

    </div>
  </div>

</div>
@endsection