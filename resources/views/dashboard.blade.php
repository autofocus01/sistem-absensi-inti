<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center justify-between">

            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Dashboard') }}
                </h2>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Sistem Absensi & Overtime PT INTI
                </p>
            </div>

        </div>

    </x-slot>


    <div class="py-8">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">


            <!-- Welcome -->
            <div class="bg-white dark:bg-gray-800
                        overflow-hidden shadow-sm sm:rounded-lg
                        mb-6">

                <div class="p-6">

                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Selamat datang, {{ auth()->user()->name }}
                    </h3>

                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Anda login sebagai
                        <span class="font-semibold">
                            {{ strtoupper(auth()->user()->role) }}
                        </span>
                    </p>

                </div>

            </div>


            <!-- =========================================================
                 KARYAWAN
                 ========================================================= -->

            @if(auth()->user()->isKaryawan())

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


                    <!-- Absensi -->
                    <a
                        href="{{ route('attendance.my') }}"
                        class="block bg-white dark:bg-gray-800
                               overflow-hidden shadow-sm
                               sm:rounded-lg
                               hover:shadow-md transition">

                        <div class="p-6">

                            <div class="flex items-center">

                                <div class="p-3 rounded-lg bg-indigo-100 dark:bg-indigo-900">

                                    <svg
                                        class="w-6 h-6 text-indigo-600 dark:text-indigo-300"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                        />

                                    </svg>

                                </div>

                                <div class="ms-4">

                                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">
                                        Absensi
                                    </h3>

                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Status, Clock In/Out, dan riwayat presensi Anda.
                                    </p>

                                </div>

                            </div>

                        </div>

                    </a>


                    <!-- Overtime -->
                    <a
                        href="{{ route('overtime.index') }}"
                        class="block bg-white dark:bg-gray-800
                               overflow-hidden shadow-sm
                               sm:rounded-lg
                               hover:shadow-md transition">

                        <div class="p-6">

                            <div class="flex items-center">

                                <div class="p-3 rounded-lg bg-amber-100 dark:bg-amber-900">

                                    <svg
                                        class="w-6 h-6 text-amber-600 dark:text-amber-300"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />

                                    </svg>

                                </div>

                                <div class="ms-4">

                                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">
                                        Overtime
                                    </h3>

                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Ajukan dan lihat status overtime.
                                    </p>

                                </div>

                            </div>

                        </div>

                    </a>

                </div>

            @endif


            <!-- =========================================================
                 VP
                 ========================================================= -->

            @if(auth()->user()->isVp())

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


                    <a
                        href="{{ route('attendance.my') }}"
                        class="block bg-white dark:bg-gray-800
                               overflow-hidden shadow-sm
                               sm:rounded-lg hover:shadow-md transition">

                        <div class="p-6">

                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">
                                Absensi
                            </h3>

                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Monitoring data absensi.
                            </p>

                        </div>

                    </a>


                    <a
                        href="{{ route('overtime.vp') }}"
                        class="block bg-white dark:bg-gray-800
                               overflow-hidden shadow-sm
                               sm:rounded-lg hover:shadow-md transition">

                        <div class="p-6">

                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">
                                Persetujuan Overtime
                            </h3>

                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Review dan proses pengajuan overtime dari divisi Anda.
                            </p>

                        </div>

                    </a>

                    <a href="{{ route('team-recap.index') }}" class="block bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                        <div class="p-6"><h3 class="font-semibold text-gray-900 dark:text-gray-100">Rekap Divisi</h3><p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Monitoring presensi dan lembur resmi divisi Anda.</p></div>
                    </a>

                </div>

            @endif


            <!-- =========================================================
                 HR ADMIN
                 ========================================================= -->

            @if(auth()->user()->isHrAdmin())

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">


                    <a
                        href="{{ route('attendance.index') }}"
                        class="block bg-white dark:bg-gray-800
                               overflow-hidden shadow-sm
                               sm:rounded-lg hover:shadow-md transition">

                        <div class="p-6">

                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">
                                Absensi
                            </h3>

                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Kelola dan monitor absensi.
                            </p>

                        </div>

                    </a>


                    <a
                        href="{{ route('overtime.hr.verification') }}"
                        class="block bg-white dark:bg-gray-800
                               overflow-hidden shadow-sm
                               sm:rounded-lg hover:shadow-md transition">

                        <div class="p-6">

                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">
                                Verifikasi Overtime
                            </h3>

                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Verifikasi overtime yang telah disetujui VP.
                            </p>

                        </div>

                    </a>


                    <a
                        href="{{ route('employees.index') }}"
                        class="block bg-white dark:bg-gray-800
                               overflow-hidden shadow-sm
                               sm:rounded-lg hover:shadow-md transition">

                        <div class="p-6">

                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">
                                Karyawan
                            </h3>

                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Kelola data karyawan.
                            </p>

                        </div>

                    </a>


                    <a
                        href="{{ route('divisions.index') }}"
                        class="block bg-white dark:bg-gray-800
                               overflow-hidden shadow-sm
                               sm:rounded-lg hover:shadow-md transition">

                        <div class="p-6">

                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">
                                Divisi
                            </h3>

                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Kelola struktur divisi.
                            </p>

                        </div>

                    </a>

                    <a href="{{ route('team-recap.index') }}" class="block bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                        <div class="p-6"><h3 class="font-semibold text-gray-900 dark:text-gray-100">Laporan Team</h3><p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Rekap kehadiran tim.</p></div>
                    </a>
                    <a href="{{ route('audit-logs.index') }}" class="block bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                        <div class="p-6"><h3 class="font-semibold text-gray-900 dark:text-gray-100">Audit Trail</h3><p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Telusuri aktivitas penting sistem.</p></div>
                    </a>

                </div>

            @endif


            <!-- =========================================================
                 DIREKTUR UTAMA
                 ========================================================= -->

            @if(auth()->user()->isDirekturUtama())

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <a
                        href="{{ route('direktur.dashboard') }}"
                        class="block bg-white dark:bg-gray-800
                               overflow-hidden shadow-sm
                               sm:rounded-lg hover:shadow-md transition">

                        <div class="p-6">

                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">
                                Absensi
                            </h3>

                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Monitoring absensi.
                            </p>

                        </div>

                    </a>

                </div>

            @endif


        </div>

    </div>

</x-app-layout>