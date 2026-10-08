@extends('layouts.absensi')

@section('title', $division->exists ? 'Edit Divisi' : 'Tambah Divisi')

@section('content')
<div class="inti-page">

    <div class="max-w-3xl mx-auto space-y-6">

        {{-- HEADER --}}
        <section>

            <div class="flex flex-col sm:flex-row
                        sm:items-start sm:justify-between gap-4">

                <div>

                    <div class="flex items-center gap-2 mb-2">

                        <span class="inline-flex items-center gap-1.5
                                     px-3 py-1 rounded-full
                                     bg-primary-container text-on-primary
                                     font-label-sm text-label-sm
                                     uppercase tracking-wider">

                            <span class="material-symbols-outlined text-[15px]">
                                account_tree
                            </span>

                            Struktur Organisasi

                        </span>

                    </div>

                    <h1 class="font-headline-lg text-headline-lg
                               text-primary tracking-tight">

                        {{ $division->exists
                            ? 'Edit Divisi'
                            : 'Tambah Divisi' }}

                    </h1>

                    <p class="font-body-md text-body-md
                              text-on-surface-variant mt-2">

                        {{ $division->exists
                            ? 'Perbarui informasi struktur divisi.'
                            : 'Tambahkan divisi baru ke struktur organisasi.' }}

                    </p>

                </div>


                <a
                    href="{{ route('divisions.index') }}"
                    class="inline-flex items-center justify-center gap-2
                           min-h-10 px-4 rounded-lg
                           bg-surface-container
                           text-on-surface
                           font-title-sm text-title-sm
                           hover:bg-surface-container-high
                           focus:outline-none
                           focus-visible:ring-2
                           focus-visible:ring-primary
                           shrink-0"
                >
                    <span class="material-symbols-outlined text-[18px]">
                        arrow_back
                    </span>

                    Kembali
                </a>

            </div>

        </section>


        {{-- VALIDATION --}}
        @if ($errors->any())

            <section>

                <div class="rounded-xl border border-error/20
                            bg-error/10 p-4 sm:p-5">

                    <div class="flex items-start gap-3">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center
                                    rounded-lg bg-error-container text-error">

                            <span class="material-symbols-outlined">
                                error
                            </span>

                        </div>

                        <div>

                            <div class="font-title-sm text-title-sm text-error">
                                Periksa kembali data
                            </div>

                            <ul class="mt-2 space-y-1 list-disc list-inside
                                       font-body-sm text-body-sm text-error">

                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach

                            </ul>

                        </div>

                    </div>

                </div>

            </section>

        @endif


        {{-- FORM --}}
        <section class="rounded-xl border border-outline-variant/70
                        bg-surface-container-lowest
                        shadow-sm overflow-hidden">

            <div class="p-5 sm:p-6
                        border-b border-outline-variant/60">

                <div class="flex items-start gap-3">

                    <div class="flex h-11 w-11 shrink-0 items-center justify-center
                                rounded-xl bg-primary-container
                                text-on-primary">

                        <span class="material-symbols-outlined">
                            {{ $division->exists ? 'edit' : 'add_business' }}
                        </span>

                    </div>

                    <div>

                        <h2 class="font-headline-sm text-headline-sm text-primary">
                            Informasi Divisi
                        </h2>

                        <p class="font-body-sm text-body-sm
                                  text-on-surface-variant mt-1">
                            Field bertanda <strong>*</strong> wajib diisi.
                        </p>

                    </div>

                </div>

            </div>


            <form
                method="POST"
                action="{{ $division->exists
                    ? route('divisions.update', $division)
                    : route('divisions.store') }}"
                class="p-5 sm:p-6 space-y-6"
            >

                @csrf

                @if ($division->exists)
                    @method('PUT')
                @endif


                {{-- NAMA --}}
                <div>

                    <label
                        for="division-nama"
                        class="block font-title-sm text-title-sm
                               text-on-surface mb-2"
                    >
                        Nama Divisi <span class="text-error">*</span>
                    </label>

                    <input
                        id="division-nama"
                        type="text"
                        name="nama"
                        value="{{ old('nama', $division->nama) }}"
                        maxlength="255"
                        autocomplete="organization"
                        required
                        autofocus
                        class="w-full min-h-11 rounded-lg
                               border border-outline-variant
                               bg-surface-container-low
                               text-on-surface
                               px-4
                               font-body-md text-body-md
                               focus:outline-none
                               focus-visible:ring-2
                               focus-visible:ring-primary
                               focus-visible:border-primary
                               @error('nama')
                                   border-error
                               @enderror"
                        placeholder="Contoh: Divisi Bisnis & Teknologi"
                        aria-describedby="division-nama-help"
                    >

                    <p
                        id="division-nama-help"
                        class="font-body-sm text-body-sm
                               text-on-surface-variant mt-2"
                    >
                        Gunakan nama resmi divisi sesuai struktur organisasi.
                    </p>

                    @error('nama')
                        <p class="font-body-sm text-body-sm text-error mt-2">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- LINI --}}
                <div>

                    <label
                        for="division-lini"
                        class="block font-title-sm text-title-sm
                               text-on-surface mb-2"
                    >
                        Lini
                        <span class="font-body-sm text-body-sm
                                     text-on-surface-variant">
                            (opsional)
                        </span>
                    </label>

                    <input
                        id="division-lini"
                        type="text"
                        name="lini"
                        value="{{ old('lini', $division->lini) }}"
                        maxlength="255"
                        autocomplete="organization-title"
                        class="w-full min-h-11 rounded-lg
                               border border-outline-variant
                               bg-surface-container-low
                               text-on-surface
                               px-4
                               font-body-md text-body-md
                               focus:outline-none
                               focus-visible:ring-2
                               focus-visible:ring-primary
                               focus-visible:border-primary
                               @error('lini')
                                   border-error
                               @enderror"
                        placeholder="Contoh: SEVP, Direktur Langsung"
                        aria-describedby="division-lini-help"
                    >

                    <p
                        id="division-lini-help"
                        class="font-body-sm text-body-sm
                               text-on-surface-variant mt-2"
                    >
                        Isi lini organisasi jika memang digunakan.
                    </p>

                    @error('lini')
                        <p class="font-body-sm text-body-sm text-error mt-2">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- INFO --}}
                <div class="rounded-xl border border-outline-variant
                            bg-surface-container-low p-4">

                    <div class="flex items-start gap-3">

                        <span class="material-symbols-outlined
                                     text-primary text-[20px] shrink-0">
                            info
                        </span>

                        <div>

                            <div class="font-title-sm text-title-sm text-primary">
                                Catatan struktur organisasi
                            </div>

                            <p class="font-body-sm text-body-sm
                                      text-on-surface-variant mt-1 leading-5">
                                Perubahan nama atau lini divisi dapat memengaruhi
                                tampilan struktur organisasi pada modul HR.
                                Pastikan informasi yang dimasukkan sesuai
                                struktur resmi perusahaan.
                            </p>

                        </div>

                    </div>

                </div>


                {{-- ACTIONS --}}
                <div class="flex flex-col-reverse sm:flex-row
                            sm:items-center sm:justify-end gap-3 pt-2">

                    <a
                        href="{{ route('divisions.index') }}"
                        class="inline-flex items-center justify-center gap-2
                               min-h-11 px-5 rounded-lg
                               bg-surface-container
                               text-on-surface
                               font-title-sm text-title-sm
                               hover:bg-surface-container-high
                               focus:outline-none
                               focus-visible:ring-2
                               focus-visible:ring-primary"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2
                               min-h-11 px-5 rounded-lg
                               inti-btn inti-btn-primary
                               font-title-sm text-title-sm
                               focus:outline-none
                               focus-visible:ring-2
                               focus-visible:ring-primary"
                    >

                        <span class="material-symbols-outlined text-[18px]">
                            save
                        </span>

                        {{ $division->exists
                            ? 'Simpan Perubahan'
                            : 'Simpan Divisi' }}

                    </button>

                </div>

            </form>

        </section>

    </div>
</div>
@endsection