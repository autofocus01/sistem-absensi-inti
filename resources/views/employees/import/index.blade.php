<x-app-layout>
    <div class="max-w-6xl mx-auto p-6 space-y-6">
        <div><h1 class="text-2xl font-semibold">Import Master Karyawan</h1><p class="text-sm opacity-70">Upload XLSX/XLS/CSV. Data divisi harus sudah tersedia di master.</p></div>
        @if(session('import_result'))
            <div class="p-4 rounded bg-green-50 text-green-800">Berhasil: {{ session('import_result.success') }} | Baru: {{ session('import_result.created') }} | Diperbarui: {{ session('import_result.updated') }} | Error: {{ count(session('import_result.errors', [])) }}</div>
        @endif
        @if($errors->any()) <div class="p-4 rounded bg-red-50 text-red-800">{{ $errors->first() }}</div> @endif
        <form method="POST" action="{{ route('employees.import.preview') }}" enctype="multipart/form-data" class="p-6 rounded border space-y-4">
            @csrf
            <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="block w-full">
            <button class="px-4 py-2 rounded bg-primary text-white">Preview Import</button>
        </form>
        <div class="text-sm opacity-80">Kolom wajib: <strong>NIPEG/NIP, Nama, Divisi</strong>. Kolom opsional: Jenis Kelamin, Jabatan, No HP, Alamat.</div>
    </div>
</x-app-layout>
