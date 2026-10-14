@extends('layouts.absensi')

@section('title', 'Import Lembur (SPKL)')

@section('content')
<div class="inti-page p1-hr-governance">

    <div class="space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <div class="inti-eyebrow">
                    DATA LEMBUR • HR
                </div>

                <h1 class="inti-title">
                    Import Lembur (SPKL)
                </h1>

                <p class="inti-subtitle">
                    Upload file SPKL (Surat Perintah Kerja Lembur) untuk divalidasi
                    sebelum pengajuan lembur masuk ke sistem.
                </p>
            </div>

            <a
                href="{{ route('overtime.index') }}"
                class="inti-btn inti-btn-ghost shrink-0"
            >
                <span class="material-symbols-outlined text-[18px]">
                    arrow_back
                </span>
                Kembali
            </a>
        </div>

        {{-- SUCCESS --}}
        @if(session('import_result'))
            <div
                class="rounded-xl border border-emerald-200 bg-emerald-50 p-4"
                role="status"
            >
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined mt-0.5 text-emerald-700">
                        check_circle
                    </span>

                    <div class="min-w-0">
                        <div class="text-sm font-semibold text-emerald-900">
                            Import selesai
                        </div>

                        <p class="mt-1 text-xs leading-5 text-emerald-800">
                            {{ session('import_result.success') }} pengajuan berhasil dibuat
                            dan
                            {{ count(session('import_result.errors', [])) }} baris bermasalah.
                            @if (! empty(session('import_result.skipped')))
                                {{ session('import_result.skipped') }} baris dilewati (duplikat).
                            @endif
                        </p>

                        @if (session('import_result.peringatan'))
                            <p class="mt-2 rounded-lg bg-amber-50 px-3 py-2 text-xs leading-5 text-amber-800">
                                {{ session('import_result.peringatan') }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- ERROR --}}
        @if($errors->any())
            <div
                class="rounded-xl border border-red-200 bg-red-50 p-4"
                role="alert"
            >
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined mt-0.5 text-red-700">
                        error
                    </span>

                    <div class="min-w-0">
                        <div class="text-sm font-semibold text-red-900">
                            File belum dapat diproses
                        </div>

                        <p class="mt-1 text-xs leading-5 text-red-800">
                            {{ $errors->first() }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- UPLOAD --}}
        <section class="inti-card overflow-hidden">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-base font-semibold text-slate-900">
                    Upload File SPKL
                </h2>

                <p class="mt-1 text-xs leading-5 text-slate-500">
                    Database belum berubah pada tahap ini. File divalidasi dan
                    ditampilkan pada halaman preview sebelum di-commit.
                </p>
            </div>

            <div class="p-5 sm:p-6">
                <form
                    method="POST"
                    action="{{ route('overtime.import.preview') }}"
                    enctype="multipart/form-data"
                    id="overtime-import-form"
                >
                    @csrf

                    <label
                        for="overtime-file"
                        class="group block cursor-pointer rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 p-6 text-center transition hover:border-slate-400 hover:bg-slate-100 focus-within:border-blue-500 focus-within:ring-4 focus-within:ring-blue-100 sm:p-8"
                    >
                        <div class="flex h-14 w-14 mx-auto items-center justify-center rounded-2xl bg-white text-slate-700 shadow-sm">
                            <span class="material-symbols-outlined text-[28px]">
                                upload_file
                            </span>
                        </div>

                        <div class="mt-4 text-sm font-semibold text-slate-900">
                            Pilih file Excel SPKL
                        </div>

                        <div class="mt-1 max-w-md mx-auto text-xs leading-5 text-slate-500">
                            Format <strong>.xlsx</strong> dengan sheet
                            <strong>HC_SPKL</strong> (induk) dan
                            <strong>HC_SPKL_PERSON</strong> (rincian per karyawan).
                        </div>

                        <div
                            id="selected-overtime-file"
                            class="mt-4 hidden rounded-lg bg-blue-50 px-3 py-2 text-xs font-medium text-blue-800"
                        ></div>

                        <input
                            id="overtime-file"
                            type="file"
                            name="file"
                            accept=".xlsx,.xls"
                            required
                            class="sr-only"
                        >
                    </label>

                    <div class="mt-6 flex flex-col gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-start gap-2 text-xs leading-5 text-slate-500">
                            <span class="material-symbols-outlined mt-0.5 text-[17px]">
                                verified
                            </span>

                            <span>
                                Durasi dihitung lewat kebijakan lembur resmi;
                                pengajuan dengan nomor SPKL sama tidak digandakan.
                            </span>
                        </div>

                        <button
                            id="btn-preview-overtime-import"
                            type="submit"
                            class="inti-btn inti-btn-primary w-full sm:w-auto"
                        >
                            <span class="material-symbols-outlined text-[18px]">
                                fact_check
                            </span>

                            Validasi & Preview
                        </button>
                    </div>
                </form>
            </div>
        </section>

        {{-- RIWAYAT IMPOR (PROVENANCE) --}}
        <section class="inti-card overflow-hidden">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-base font-semibold text-slate-900">
                    Riwayat Impor
                </h2>

                <p class="mt-1 text-xs leading-5 text-slate-500">
                    Jejak file SPKL yang pernah di-commit — nama file asli, operator,
                    waktu, dan jumlah baris. Rujukan saat data lembur dipertanyakan.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm min-w-[760px]">
                    <thead>
                        <tr class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                            <th class="py-3 px-5">File</th>
                            <th class="py-3 px-5">Operator</th>
                            <th class="py-3 px-5">Waktu</th>
                            <th class="py-3 px-5">Periode</th>
                            <th class="py-3 px-5 text-right">Baris Masuk</th>
                            <th class="py-3 px-5 text-right">Gagal</th>
                            <th class="py-3 px-5">Status</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse(($batches ?? collect()) as $batch)
                            <tr>
                                <td class="py-3 px-5 font-medium text-slate-900">
                                    {{ $batch->nama_file }}
                                </td>

                                <td class="py-3 px-5 text-slate-600">
                                    {{ $batch->user?->name ?? '-' }}
                                </td>

                                <td class="py-3 px-5 text-slate-600">
                                    {{ $batch->created_at->format('d M Y H:i') }}
                                </td>

                                <td class="py-3 px-5 text-slate-600">
                                    {{ implode(', ', $batch->periode ?? []) ?: '-' }}
                                </td>

                                <td class="py-3 px-5 text-right">{{ $batch->berhasil }}</td>
                                <td class="py-3 px-5 text-right">{{ $batch->gagal }}</td>

                                <td class="py-3 px-5">
                                    @if ($batch->duplikat)
                                        <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800">
                                            File terulang
                                        </span>
                                    @elseif ($batch->gagal > 0)
                                        <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800">
                                            Sebagian
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-800">
                                            Bersih
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-6 text-center text-sm text-slate-500">
                                    Belum ada impor lembur yang di-commit.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('overtime-file');
    const selectedFile = document.getElementById('selected-overtime-file');
    const form = document.getElementById('overtime-import-form');
    const button = document.getElementById('btn-preview-overtime-import');

    input?.addEventListener('change', function () {
        const file = this.files?.[0];

        if (!file) {
            selectedFile?.classList.add('hidden');
            if (selectedFile) selectedFile.textContent = '';
            return;
        }

        if (selectedFile) {
            selectedFile.textContent = `File dipilih: ${file.name}`;
            selectedFile.classList.remove('hidden');
        }
    });

    form?.addEventListener('submit', function () {
        if (!button) return;

        button.disabled = true;
        button.classList.add('opacity-70', 'cursor-wait');

        button.innerHTML = `
            <span class="material-symbols-outlined text-[18px] animate-spin">
                progress_activity
            </span>
            Memvalidasi...
        `;
    });
});
</script>
@endsection
