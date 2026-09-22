@extends('layouts.absensi')
@section('title','Import Absensi Production')
@section('content')
<div class="max-w-5xl mx-auto p-6 space-y-6">
    @if(session('import_result'))<div class="rounded-xl bg-emerald-50 p-4 text-sm text-emerald-800">Import selesai: {{ session('import_result.success') }} baris berhasil, {{ count(session('import_result.errors', [])) }} baris bermasalah.</div>@endif
    <div><h1 class="text-2xl font-semibold">Import Absensi Production</h1><p class="text-sm text-on-surface-variant mt-1">Upload → validasi → preview → commit. Database tidak berubah sebelum commit.</p></div>
    @if($errors->any()) <div class="rounded-lg bg-red-50 p-4 text-sm text-red-700">{{ $errors->first() }}</div> @endif
    <form method="POST" action="{{ route('attendance.import.production.preview') }}" enctype="multipart/form-data" class="rounded-xl bg-white p-6 shadow-sm space-y-4">
        @csrf
        <label class="block text-sm font-medium">File Excel/CSV</label>
        <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="block w-full rounded-lg border p-3">
        <p class="text-xs text-gray-500">Format yang didukung: NIP + Tanggal + Jam Masuk/Jam Pulang, atau NIP + DateTime scan.</p>
        <button class="rounded-lg bg-primary px-4 py-2 text-white">Validasi & Preview</button>
    </form>
</div>
@endsection
