@extends('layouts.admin.app')

@section('title', 'Kelola Nomor Darurat')

@section('content')
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="fw-bold">📞 Daftar Nomor Darurat</h3>
    <a href="{{ route('admin.emergency.create') }}" class="btn btn-dark">+ Tambah Nomor</a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card shadow-sm">
    <div class="card-body">
      @if($numbers->count())
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-dark">
              <tr>
                <th style="width: 40px;">#</th>
                <th style="width: 60px;">Icon</th>
                <th>Nama Kontak</th>
                <th>Keperluan</th>
                <th>Nomor</th>
                <th>Keterangan</th>
                <th style="width: 150px;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($numbers as $n)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td style="font-size: 22px;">{{ $n->icon ?? '📞' }}</td>
                  <td>{{ $n->nama }}</td>
                  <td>{{ $n->keperluan ?? '-' }}</td>
                  <td>
                    <a href="tel:{{ $n->nomor }}" class="text-decoration-none fw-semibold text-dark">
                      {{ $n->nomor }}
                    </a>
                  </td>
                  <td>{{ $n->keterangan ?? '-' }}</td>
                  <td>
                    <a href="{{ route('admin.emergency.edit', $n->id) }}" class="btn btn-sm btn-warning">
                      <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('admin.emergency.destroy', $n->id) }}" method="POST" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-danger"
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
        <p class="text-muted text-center mb-0">Belum ada nomor darurat.</p>
      @endif
    </div>
  </div>
</div>
@endsection
