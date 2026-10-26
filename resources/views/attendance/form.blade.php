@extends('layouts.absensi')

@section('title', $recap->exists ? 'Edit Rekap Absensi' : 'Input Rekap Absensi')

@section('content')
<div class="inti-page">

    {{-- HEADER --}}
    <div class="inti-page-header">
        <div class="min-w-0">
            <div class="inti-eyebrow">
                REKAP KEHADIRAN BULANAN
            </div>

            <h1 class="inti-title">
                {{ $recap->exists ? 'Edit Rekap Absensi' : 'Input Rekap Absensi' }}
            </h1>

            <p class="inti-subtitle max-w-3xl">
                {{ $recap->exists
                    ? 'Perbarui data rekap kehadiran karyawan sesuai hasil verifikasi.'
                    : 'Masukkan rekap kehadiran bulanan berdasarkan data presensi yang telah diverifikasi.' }}
            </p>
        </div>

        <a
            href="{{ route('attendance.index') }}"
            class="inti-btn inti-btn-ghost shrink-0"
        >
            <span class="material-symbols-outlined text-[18px]">
                arrow_back
            </span>
            <span>Kembali</span>
        </a>
    </div>

    {{-- INFO ATTENDANCE RULES --}}
    <div class="inti-card overflow-hidden mb-6">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="text-base font-semibold text-slate-900">
                Aturan Kehadiran
            </h2>

            <p class="mt-1 text-xs leading-5 text-slate-500">
                Total hari kerja = Hadir + Perdin + Cuti + Sakit + Ijin + Alpha
            </p>
        </div>

        <div class="p-5 sm:p-6 grid grid-cols-2 gap-4">

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <h3 class="font-title-sm text-title-sm text-primary mb-2">Hadir</h3>
                <p class="font-body-sm text-body-sm text-on-surface-variant">
                    Hari hadir tanpa alisan
                </p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <h3 class="font-title-sm text-title-sm text-primary mb-2">Alfa / Mangkir</h3>
                <p class="font-body-sm text-body-sm text-on-surface-variant">
                    Kehadiran tanpa izin
                </p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <h3 class="font-title-sm text-title-sm text-primary mb-2">Izin</h3>
                <p class="font-body-sm text-body-sm text-on-surface-variant">
                    Kehadiran dengan izin resmi
                </p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <h3 class="font-title-sm text-title-sm text-primary mb-2">Sakit</h3>
                <p class="font-body-sm text-body-sm text-on-surface-variant">
                    Kehadiran karena sakit
                </p>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <h3 class="font-title-sm text-title-sm text-primary mb-2">Sisa Status</h3>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <strong>Perdin</strong> - Perdin
                </div>
                <div>
                    <strong>Alpha</strong> - Tidak hadir
                </div>
            </div>
        </div>
    </div>

    {{-- VALIDATION SUMMARY --}}
    @if ($errors->any())
        <div
            class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800"
            role="alert"
        >
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined mt-0.5 shrink-0">
                    error
                </span>

                <div class="min-w-0">
                    <div class="text-sm font-semibold">
                        Data belum dapat disimpan
                    </div>

                    <div class="mt-1 text-xs leading-5">
                        Periksa field yang ditandai di bawah sebelum menyimpan kembali.
                    </div>

                    <ul class="mt-3 list-disc space-y-1 pl-5 text-xs">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form
        method="POST"
        action="{{ $recap->exists ? route('attendance.update', $recap) : route('attendance.store') }}"
        class="space-y-5"
    >
        @csrf

        @if ($recap->exists)
            @method('PUT')
        @endif

        @if (!empty($resolveErrorId))
            <input
                type="hidden"
                name="resolve_error_id"
                value="{{ $resolveErrorId }}"
            >

            <div class="inti-card overflow-hidden border border-amber-200">
                <div class="flex items-start gap-3 bg-amber-50 p-4 sm:p-5">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                        <span class="material-symbols-outlined">
                            fact_check
                        </span>
                    </div>

                    <div class="min-w-0">
                        <div class="text-sm font-semibold text-amber-900">
                            Penyelesaian Data Anomali
                        </div>

                        <p class="mt-1 text-xs leading-5 text-amber-800">
                            Form ini berasal dari data anomali yang ditolak saat proses import.
                            Koreksi angka sesuai hasil pemeriksaan sebenarnya, kemudian simpan.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- PERIODE & KARYAWAN --}}
        <section class="inti-card overflow-hidden">

            <div class="border-b border-slate-200 px-5 py-5 sm:px-6">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-800">
                        <span class="material-symbols-outlined text-[21px]">
                            calendar_month
                        </span>
                    </div>

                    <div class="min-w-0">
                        <h2 class="text-base font-semibold text-slate-900">
                            Identitas Rekap
                        </h2>

                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            Tentukan karyawan dan periode rekap yang sedang dicatat.
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 sm:p-6 md:grid-cols-2">

                {{-- KARYAWAN --}}
                <div class="md:col-span-2">
                    <label
                        for="employee_id"
                        class="mb-1.5 block text-sm font-semibold text-slate-800"
                    >
                        Karyawan
                        <span class="text-red-600">*</span>
                    </label>

                    <select
                        id="employee_id"
                        name="employee_id"
                        class="inti-input w-full @error('employee_id') border-red-400 ring-2 ring-red-100 @enderror"
                        required
                    >
                        <option value="">
                            -- Pilih Karyawan --
                        </option>

                        @foreach ($employees as $employee)
                            <option
                                value="{{ $employee->id }}"
                                @selected(old('employee_id', $recap->employee_id) == $employee->id)
                            >
                                {{ $employee->nipeg }} — {{ $employee->nama }}
                            </option>
                        @endforeach
                    </select>

                    @error('employee_id')
                        <p class="mt-1.5 text-xs leading-5 text-red-600">
                            {{ $message }}
                        </p>
                    @else
                        <p class="mt-1.5 text-xs leading-5 text-slate-500">
                            Pilih karyawan sesuai data master perusahaan.
                        </p>
                    @enderror
                </div>

                {{-- TAHUN --}}
                <div>
                    <label
                        for="tahun"
                        class="mb-1.5 block text-sm font-semibold text-slate-800"
                    >
                        Tahun
                        <span class="text-red-600">*</span>
                    </label>

                    <input
                        id="tahun"
                        type="number"
                        name="tahun"
                        value="{{ old('tahun', $recap->tahun ?? now()->year) }}"
                        min="2000"
                        max="2100"
                        inputmode="numeric"
                        class="inti-input w-full @error('tahun') border-red-400 ring-2 ring-red-100 @enderror"
                        required
                    >

                    @error('tahun')
                        <p class="mt-1.5 text-xs leading-5 text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- BULAN --}}
                <div>
                    <label
                        for="bulan"
                        class="mb-1.5 block text-sm font-semibold text-slate-800"
                    >
                        Bulan
                        <span class="text-red-600">*</span>
                    </label>

                    <select
                        id="bulan"
                        name="bulan"
                        class="inti-input w-full @error('bulan') border-red-400 ring-2 ring-red-100 @enderror"
                        required
                    >
                        @foreach (range(1, 12) as $m)
                            <option
                                value="{{ $m }}"
                                @selected(old('bulan', $recap->bulan) == $m)
                            >
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>

                    @error('bulan')
                        <p class="mt-1.5 text-xs leading-5 text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>
        </section>

        {{-- KEHADIRAN --}}
        <section class="inti-card overflow-hidden">

            <div class="border-b border-slate-200 px-5 py-5 sm:px-6">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-800">
                        <span class="material-symbols-outlined text-[21px]">
                            event_available
                        </span>
                    </div>

                    <div class="min-w-0">
                        <h2 class="text-base font-semibold text-slate-900">
                            Komposisi Kehadiran
                        </h2>

                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            Masukkan jumlah hari untuk setiap status kehadiran pada periode tersebut.
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-5 sm:p-6">

                {{-- INFO RULE --}}
                <div class="mb-5 rounded-xl border border-blue-100 bg-blue-50 p-4">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined mt-0.5 shrink-0 text-blue-700">
                            info
                        </span>

                        <div class="text-xs leading-5 text-blue-900">
                            <strong class="font-semibold">
                                Perhatian:
                            </strong>

                            total
                            <strong>Hadir + Perdin + Cuti + Sakit + Ijin + Alpha</strong>
                            wajib sama dengan jumlah
                            <strong>Hari Kerja</strong>.
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">

                    {{-- HARI KERJA --}}
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <label
                            for="hari_kerja"
                            class="mb-1.5 block text-sm font-semibold text-slate-800"
                        >
                            Hari Kerja
                            <span class="text-red-600">*</span>
                        </label>

                        <input
                            id="hari_kerja"
                            type="number"
                            name="hari_kerja"
                            value="{{ old('hari_kerja', $recap->hari_kerja) }}"
                            min="0"
                            inputmode="numeric"
                            class="inti-input w-full bg-white @error('hari_kerja') border-red-400 ring-2 ring-red-100 @enderror"
                            required
                        >

                        @error('hari_kerja')
                            <p class="mt-1.5 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- HADIR --}}
                    <div class="rounded-xl border border-emerald-100 bg-emerald-50/50 p-4">
                        <label
                            for="hadir"
                            class="mb-1.5 block text-sm font-semibold text-slate-800"
                        >
                            Hadir
                            <span class="text-red-600">*</span>
                        </label>

                        <input
                            id="hadir"
                            type="number"
                            name="hadir"
                            value="{{ old('hadir', $recap->hadir) }}"
                            min="0"
                            inputmode="numeric"
                            class="inti-input w-full bg-white @error('hadir') border-red-400 ring-2 ring-red-100 @enderror"
                            required
                        >

                        @error('hadir')
                            <p class="mt-1.5 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- PERDIN --}}
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <label
                            for="perdin"
                            class="mb-1.5 block text-sm font-semibold text-slate-800"
                        >
                            Perdin
                            <span class="text-red-600">*</span>
                        </label>

                        <input
                            id="perdin"
                            type="number"
                            name="perdin"
                            value="{{ old('perdin', $recap->perdin) }}"
                            min="0"
                            inputmode="numeric"
                            class="inti-input w-full bg-white @error('perdin') border-red-400 ring-2 ring-red-100 @enderror"
                            required
                        >

                        @error('perdin')
                            <p class="mt-1.5 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- CUTI --}}
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <label
                            for="cuti"
                            class="mb-1.5 block text-sm font-semibold text-slate-800"
                        >
                            Cuti
                            <span class="text-red-600">*</span>
                        </label>

                        <input
                            id="cuti"
                            type="number"
                            name="cuti"
                            value="{{ old('cuti', $recap->cuti) }}"
                            min="0"
                            inputmode="numeric"
                            class="inti-input w-full bg-white @error('cuti') border-red-400 ring-2 ring-red-100 @enderror"
                            required
                        >

                        @error('cuti')
                            <p class="mt-1.5 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- SAKIT --}}
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <label
                            for="sakit"
                            class="mb-1.5 block text-sm font-semibold text-slate-800"
                        >
                            Sakit
                            <span class="text-red-600">*</span>
                        </label>

                        <input
                            id="sakit"
                            type="number"
                            name="sakit"
                            value="{{ old('sakit', $recap->sakit) }}"
                            min="0"
                            inputmode="numeric"
                            class="inti-input w-full bg-white @error('sakit') border-red-400 ring-2 ring-red-100 @enderror"
                            required
                        >

                        @error('sakit')
                            <p class="mt-1.5 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- IJIN --}}
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <label
                            for="ijin"
                            class="mb-1.5 block text-sm font-semibold text-slate-800"
                        >
                            Ijin
                            <span class="text-red-600">*</span>
                        </label>

                        <input
                            id="ijin"
                            type="number"
                            name="ijin"
                            value="{{ old('ijin', $recap->ijin) }}"
                            min="0"
                            inputmode="numeric"
                            class="inti-input w-full bg-white @error('ijin') border-red-400 ring-2 ring-red-100 @enderror"
                            required
                        >

                        @error('ijin')
                            <p class="mt-1.5 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- ALPHA --}}
                    <div class="rounded-xl border border-red-100 bg-red-50/40 p-4">
                        <label
                            for="alpha"
                            class="mb-1.5 block text-sm font-semibold text-slate-800"
                        >
                            Alpha / Mangkir
                            <span class="text-red-600">*</span>
                        </label>

                        <input
                            id="alpha"
                            type="number"
                            name="alpha"
                            value="{{ old('alpha', $recap->alpha) }}"
                            min="0"
                            inputmode="numeric"
                            class="inti-input w-full bg-white @error('alpha') border-red-400 ring-2 ring-red-100 @enderror"
                            required
                        >

                        @error('alpha')
                            <p class="mt-1.5 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>
            </div>
        </section>

        {{-- KETERLAMBATAN --}}
        <section class="inti-card overflow-hidden">

            <div class="border-b border-slate-200 px-5 py-5 sm:px-6">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-800">
                        <span class="material-symbols-outlined text-[21px]">
                            schedule
                        </span>
                    </div>

                    <div class="min-w-0">
                        <h2 class="text-base font-semibold text-slate-900">
                            Keterlambatan
                        </h2>

                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            Catat frekuensi hari terlambat dan total menit keterlambatan pada periode tersebut.
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 sm:p-6 md:grid-cols-2">

                {{-- TELAT HARI --}}
                <div>
                    <label
                        for="telat_hari"
                        class="mb-1.5 block text-sm font-semibold text-slate-800"
                    >
                        Telat (hari)
                        <span class="text-red-600">*</span>
                    </label>

                    <input
                        id="telat_hari"
                        type="number"
                        name="telat_hari"
                        value="{{ old('telat_hari', $recap->telat_hari) }}"
                        min="0"
                        inputmode="numeric"
                        class="inti-input w-full @error('telat_hari') border-red-400 ring-2 ring-red-100 @enderror"
                        required
                    >

                    @error('telat_hari')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- MENIT TELAT --}}
                <div>
                    <label
                        for="menit_telat"
                        class="mb-1.5 block text-sm font-semibold text-slate-800"
                    >
                        Menit Telat
                        <span class="text-red-600">*</span>
                    </label>

                    <input
                        id="menit_telat"
                        type="number"
                        name="menit_telat"
                        value="{{ old('menit_telat', $recap->menit_telat) }}"
                        min="0"
                        inputmode="numeric"
                        class="inti-input w-full @error('menit_telat') border-red-400 ring-2 ring-red-100 @enderror"
                        required
                    >

                    @error('menit_telat')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>
        </section>

        {{-- LOKASI --}}
        <section class="inti-card overflow-hidden">

            <div class="border-b border-slate-200 px-5 py-5 sm:px-6">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-800">
                        <span class="material-symbols-outlined text-[21px]">
                            location_on
                        </span>
                    </div>

                    <div class="min-w-0">
                        <h2 class="text-base font-semibold text-slate-900">
                            Lokasi Presensi
                        </h2>

                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            Jumlah hari presensi berdasarkan lokasi kerja.
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 sm:p-6 md:grid-cols-2">

                {{-- BANDUNG --}}
                <div>
                    <label
                        for="lokasi_bandung"
                        class="mb-1.5 block text-sm font-semibold text-slate-800"
                    >
                        Lokasi Bandung (hari)
                    </label>

                    <input
                        id="lokasi_bandung"
                        type="number"
                        name="lokasi_bandung"
                        value="{{ old('lokasi_bandung', $recap->lokasi_bandung) }}"
                        min="0"
                        inputmode="numeric"
                        class="inti-input w-full @error('lokasi_bandung') border-red-400 ring-2 ring-red-100 @enderror"
                    >

                    @error('lokasi_bandung')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- JAKARTA --}}
                <div>
                    <label
                        for="lokasi_jakarta"
                        class="mb-1.5 block text-sm font-semibold text-slate-800"
                    >
                        Lokasi Jakarta (hari)
                    </label>

                    <input
                        id="lokasi_jakarta"
                        type="number"
                        name="lokasi_jakarta"
                        value="{{ old('lokasi_jakarta', $recap->lokasi_jakarta) }}"
                        min="0"
                        inputmode="numeric"
                        class="inti-input w-full @error('lokasi_jakarta') border-red-400 ring-2 ring-red-100 @enderror"
                    >

                    @error('lokasi_jakarta')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>
        </section>

        {{-- SYSTEM RULE --}}
        <section class="inti-card overflow-hidden">
            <div class="flex items-start gap-3 bg-slate-50 p-5 sm:p-6">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-slate-700 shadow-sm">
                    <span class="material-symbols-outlined">
                        calculate
                    </span>
                </div>

                <div class="min-w-0">
                    <h2 class="text-sm font-semibold text-slate-900">
                        Perhitungan Kehadiran
                    </h2>

                    <p class="mt-1 text-xs leading-5 text-slate-600">
                        Persentase kehadiran dihitung otomatis oleh sistem dari
                        <strong>Hadir ÷ Hari Kerja</strong>.
                        Nilai persentase tidak diinput secara manual pada form ini.
                    </p>
                </div>
            </div>
        </section>

        {{-- ACTION --}}
        <section class="inti-card p-4 sm:p-5">
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">

                <a
                    href="{{ route('attendance.index') }}"
                    class="inti-btn inti-btn-ghost w-full sm:w-auto"
                >
                    <span class="material-symbols-outlined text-[18px]">
                        close
                    </span>
                    Batal
                </a>

                <button
                    type="submit"
                    class="inti-btn inti-btn-primary w-full sm:w-auto"
                >
                    <span class="material-symbols-outlined text-[18px]">
                        {{ $recap->exists ? 'save' : 'add_task' }}
                    </span>

                    {{ $recap->exists ? 'Simpan Perubahan' : 'Simpan Rekap' }}
                </button>

            </div>
        </section>

    </form>

</div>
@endsection