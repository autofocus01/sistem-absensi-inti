@extends('layouts.absensi')

@section('title', $employee->exists ? 'Edit Karyawan' : 'Tambah Karyawan')

@section('content')
<div class="inti-page">

    {{-- PAGE HEADER --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div class="min-w-0">
            <div class="inti-eyebrow">
                DATA MASTER • SDM
            </div>

            <h1 class="inti-title">
                {{ $employee->exists ? 'Edit Karyawan' : 'Tambah Karyawan' }}
            </h1>

            <p class="inti-subtitle">
                {{ $employee->exists
                    ? 'Perbarui informasi karyawan sesuai data resmi perusahaan.'
                    : 'Masukkan data karyawan untuk menambahkan personel baru ke sistem.' }}
            </p>
        </div>

        <a
            href="{{ route('employees.index') }}"
            class="inti-btn inti-btn-ghost shrink-0"
        >
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            <span>Kembali</span>
        </a>
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
                    <p class="font-semibold text-sm">
                        Data belum dapat disimpan.
                    </p>

                    <p class="mt-1 text-xs leading-5">
                        Periksa kembali field yang ditandai sebelum mengirim formulir.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <form
        method="POST"
        action="{{ $employee->exists ? route('employees.update', $employee) : route('employees.store') }}"
        class="space-y-5"
    >
        @csrf

        @if ($employee->exists)
            @method('PUT')
        @endif

        {{-- IDENTITAS --}}
        <section class="inti-card overflow-hidden">
            <div class="border-b border-slate-200 px-5 py-5 sm:px-6">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-800">
                        <span class="material-symbols-outlined text-[21px]">
                            badge
                        </span>
                    </div>

                    <div class="min-w-0">
                        <h2 class="text-base font-semibold text-slate-900">
                            Identitas Karyawan
                        </h2>

                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            Informasi dasar dan identitas resmi karyawan.
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 sm:p-6 md:grid-cols-2">

                {{-- NIPEG --}}
                <div>
                    <label
                        for="nipeg"
                        class="mb-1.5 block text-sm font-semibold text-slate-800"
                    >
                        NIPEG
                        <span class="text-red-600">*</span>
                    </label>

                    <input
                        id="nipeg"
                        type="text"
                        name="nipeg"
                        value="{{ old('nipeg', $employee->nipeg) }}"
                        class="inti-input w-full @error('nipeg') border-red-400 ring-2 ring-red-100 @enderror"
                        autocomplete="off"
                        required
                    >

                    @error('nipeg')
                        <p class="mt-1.5 text-xs leading-5 text-red-600">
                            {{ $message }}
                        </p>
                    @else
                        <p class="mt-1.5 text-xs leading-5 text-slate-500">
                            Gunakan NIPEG sesuai data resmi perusahaan.
                        </p>
                    @enderror
                </div>

                {{-- NAMA --}}
                <div>
                    <label
                        for="nama"
                        class="mb-1.5 block text-sm font-semibold text-slate-800"
                    >
                        Nama Lengkap
                        <span class="text-red-600">*</span>
                    </label>

                    <input
                        id="nama"
                        type="text"
                        name="nama"
                        value="{{ old('nama', $employee->nama) }}"
                        class="inti-input w-full @error('nama') border-red-400 ring-2 ring-red-100 @enderror"
                        autocomplete="name"
                        required
                    >

                    @error('nama')
                        <p class="mt-1.5 text-xs leading-5 text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- JENIS KELAMIN --}}
                <div>
                    <label
                        for="jenis_kelamin"
                        class="mb-1.5 block text-sm font-semibold text-slate-800"
                    >
                        Jenis Kelamin
                    </label>

                    <select
                        id="jenis_kelamin"
                        name="jenis_kelamin"
                        class="inti-input w-full @error('jenis_kelamin') border-red-400 ring-2 ring-red-100 @enderror"
                    >
                        <option value="">Pilih jenis kelamin</option>

                        <option
                            value="L"
                            @selected(old('jenis_kelamin', $employee->jenis_kelamin) === 'L')
                        >
                            Laki-laki
                        </option>

                        <option
                            value="P"
                            @selected(old('jenis_kelamin', $employee->jenis_kelamin) === 'P')
                        >
                            Perempuan
                        </option>
                    </select>

                    @error('jenis_kelamin')
                        <p class="mt-1.5 text-xs leading-5 text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- JABATAN --}}
                <div>
                    <label
                        for="jabatan"
                        class="mb-1.5 block text-sm font-semibold text-slate-800"
                    >
                        Jabatan
                    </label>

                    <input
                        id="jabatan"
                        type="text"
                        name="jabatan"
                        value="{{ old('jabatan', $employee->jabatan) }}"
                        class="inti-input w-full @error('jabatan') border-red-400 ring-2 ring-red-100 @enderror"
                        autocomplete="organization-title"
                    >

                    @error('jabatan')
                        <p class="mt-1.5 text-xs leading-5 text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>
        </section>

        {{-- KONTAK & ALAMAT --}}
        <section class="inti-card overflow-hidden">
            <div class="border-b border-slate-200 px-5 py-5 sm:px-6">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-800">
                        <span class="material-symbols-outlined text-[21px]">
                            contact_phone
                        </span>
                    </div>

                    <div class="min-w-0">
                        <h2 class="text-base font-semibold text-slate-900">
                            Kontak & Alamat
                        </h2>

                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            Informasi kontak yang dapat digunakan untuk kebutuhan administrasi.
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 sm:p-6">

                {{-- NO HP --}}
                <div>
                    <label
                        for="no_hp"
                        class="mb-1.5 block text-sm font-semibold text-slate-800"
                    >
                        Nomor HP
                    </label>

                    <input
                        id="no_hp"
                        type="text"
                        name="no_hp"
                        value="{{ old('no_hp', $employee->no_hp) }}"
                        placeholder="08xxxxxxxxxx"
                        inputmode="tel"
                        autocomplete="tel"
                        class="inti-input w-full @error('no_hp') border-red-400 ring-2 ring-red-100 @enderror"
                    >

                    @error('no_hp')
                        <p class="mt-1.5 text-xs leading-5 text-red-600">
                            {{ $message }}
                        </p>
                    @else
                        <p class="mt-1.5 text-xs leading-5 text-slate-500">
                            Contoh format: 08xxxxxxxxxx
                        </p>
                    @enderror
                </div>

                {{-- ALAMAT --}}
                <div>
                    <label
                        for="alamat"
                        class="mb-1.5 block text-sm font-semibold text-slate-800"
                    >
                        Alamat
                    </label>

                    <textarea
                        id="alamat"
                        name="alamat"
                        rows="4"
                        class="inti-input w-full resize-y @error('alamat') border-red-400 ring-2 ring-red-100 @enderror"
                        placeholder="Masukkan alamat karyawan..."
                    >{{ old('alamat', $employee->alamat) }}</textarea>

                    @error('alamat')
                        <p class="mt-1.5 text-xs leading-5 text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>
        </section>

        {{-- ORGANISASI --}}
        <section class="inti-card overflow-hidden">
            <div class="border-b border-slate-200 px-5 py-5 sm:px-6">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-800">
                        <span class="material-symbols-outlined text-[21px]">
                            account_tree
                        </span>
                    </div>

                    <div class="min-w-0">
                        <h2 class="text-base font-semibold text-slate-900">
                            Struktur Organisasi
                        </h2>

                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            Tentukan divisi tempat karyawan terdaftar.
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-5 sm:p-6">
                <div class="max-w-xl">
                    <label
                        for="division_id"
                        class="mb-1.5 block text-sm font-semibold text-slate-800"
                    >
                        Divisi
                        <span class="text-red-600">*</span>
                    </label>

                    <select
                        id="division_id"
                        name="division_id"
                        class="inti-input w-full @error('division_id') border-red-400 ring-2 ring-red-100 @enderror"
                        required
                    >
                        <option value="">Pilih divisi</option>

                        @foreach ($divisions as $division)
                            <option
                                value="{{ $division->id }}"
                                @selected(old('division_id', $employee->division_id) == $division->id)
                            >
                                {{ $division->nama }}
                            </option>
                        @endforeach
                    </select>

                    @error('division_id')
                        <p class="mt-1.5 text-xs leading-5 text-red-600">
                            {{ $message }}
                        </p>
                    @else
                        <p class="mt-1.5 text-xs leading-5 text-slate-500">
                            Divisi menentukan struktur organisasi karyawan dalam sistem.
                        </p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- AKUN LOGIN --}}
        <section class="inti-card overflow-hidden">
            <div class="border-b border-slate-200 px-5 py-5 sm:px-6">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-800">
                        <span class="material-symbols-outlined text-[21px]">
                            manage_accounts
                        </span>
                    </div>

                    <div class="min-w-0">
                        <h2 class="text-base font-semibold text-slate-900">
                            Akun Login
                        </h2>

                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            Status akun akses sistem yang terhubung dengan data karyawan.
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-5 sm:p-6">

                @if ($employee->user)

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

                            <div class="flex min-w-0 items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-slate-800 shadow-sm">
                                    <span class="material-symbols-outlined">
                                        account_circle
                                    </span>
                                </div>

                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-900">
                                        Akun login tersedia
                                    </p>

                                    <p class="mt-1 break-all text-xs leading-5 text-slate-600">
                                        {{ $employee->user->email }}
                                    </p>
                                </div>
                            </div>

                            <span
                                class="inline-flex w-fit shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold
                                {{ $employee->user->is_active
                                    ? 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200'
                                    : 'bg-slate-100 text-slate-600 ring-1 ring-inset ring-slate-200' }}"
                            >
                                <span class="h-1.5 w-1.5 rounded-full {{ $employee->user->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>

                                {{ $employee->user->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>

                        </div>

                        <div class="mt-4 border-t border-slate-200 pt-3 text-xs leading-5 text-slate-500">
                            Role:
                            <strong class="text-slate-700">Karyawan</strong>
                            <span class="mx-1">•</span>
                            Pengelolaan akun dilakukan melalui aksi akun di daftar Data Karyawan.
                        </div>
                    </div>

                @else

                    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined mt-0.5 shrink-0 text-slate-600">
                                person_add
                            </span>

                            <div>
                                <p class="text-sm font-semibold text-slate-900">
                                    Belum memiliki akun login
                                </p>

                                <p class="mt-1 text-xs leading-5 text-slate-600">
                                    Setelah data karyawan disimpan, HR dapat membuat akun login
                                    melalui menu Data Karyawan.
                                </p>
                            </div>
                        </div>
                    </div>

                @endif

            </div>
        </section>

        {{-- FORM ACTION --}}
        <section class="inti-card p-4 sm:p-5">
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">

                <a
                    href="{{ route('employees.index') }}"
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
                        {{ $employee->exists ? 'save' : 'person_add' }}
                    </span>

                    {{ $employee->exists ? 'Simpan Perubahan' : 'Simpan Karyawan' }}
                </button>

            </div>
        </section>

    </form>

</div>
@endsection