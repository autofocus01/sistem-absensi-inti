@extends('layouts.absensi')

@section('title', 'Riwayat Keputusan VP - PT. INTI')

@section('content')
<div class="flex flex-col gap-space-lg w-full">
    <div class="relative overflow-hidden rounded-xl bg-surface-container-lowest shadow-[0_1px_8px_rgba(0,0,0,0.04)] p-space-xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full bg-secondary-container text-on-secondary-container font-label-sm text-label-sm font-semibold">Riwayat Keputusan</span>
                    <span class="text-on-surface-variant text-xs">•</span>
                    <span class="font-body-sm text-body-sm text-on-surface-variant">VP Divisi</span>
                </div>
                <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">Riwayat Keputusan VP</h1>
                <p class="font-body-md text-body-md text-on-surface-variant">
                    Daftar keputusan lembur yang dibuat oleh Anda untuk anggota divisi Anda.
                </p>
            </div>
            <a href="{{ route('overtime.vp') }}" class="inline-flex items-center justify-center gap-2 px-space-lg py-2.5 rounded-lg bg-primary text-on-primary font-title-sm hover:opacity-90">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Antrean Approval
            </a>
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)] p-space-xl">
        <div class="flex items-center justify-between mb-space-md">
            <div>
                <h2 class="font-headline-sm text-headline-sm text-primary">Keputusan Saya</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Hanya keputusan yang dibuat oleh akun VP yang sedang login.</p>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-surface-container text-primary font-label-sm font-semibold">{{ $submissions->total() }} Keputusan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[1050px]">
                <thead>
                    <tr class="bg-surface-container-low text-on-surface-variant font-label-sm uppercase">
                        <th class="py-3 px-space-md rounded-l-lg">No. Tiket</th>
                        <th class="py-3 px-space-md">Karyawan</th>
                        <th class="py-3 px-space-md">Tanggal &amp; Jam</th>
                        <th class="py-3 px-space-md">Durasi</th>
                        <th class="py-3 px-space-md">Keputusan VP</th>
                        <th class="py-3 px-space-md">Status Saat Ini</th>
                        <th class="py-3 px-space-md rounded-r-lg">Waktu Keputusan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container-low">
                @forelse($submissions as $submission)
                    @php
                        $rejected = $submission->status === 'REJECTED_VP';
                        $decisionLabel = $rejected ? 'Ditolak' : 'Disetujui';
                        $decisionClass = $rejected ? 'bg-red-50 text-red-800' : 'bg-emerald-50 text-emerald-800';
                        $currentMeta = match ($submission->status) {
                            'APPROVED_VP' => ['label' => 'Menunggu HR', 'class' => 'bg-blue-50 text-blue-800'],
                            'VERIFIED_HR' => ['label' => 'Diverifikasi HR', 'class' => 'bg-emerald-50 text-emerald-800'],
                            'REJECTED_HR' => ['label' => 'Ditolak HR', 'class' => 'bg-red-50 text-red-800'],
                            'REJECTED_VP' => ['label' => 'Ditolak VP', 'class' => 'bg-red-50 text-red-800'],
                            default => [$submission->status, 'bg-surface-container text-on-surface-variant'],
                        };
                    @endphp
                    <tr class="hover:bg-surface-container-low/40 align-top">
                        <td class="py-4 px-space-md">
                            <div class="font-mono text-xs font-semibold text-primary">{{ $submission->ticket_number }}</div>
                            <div class="text-xs text-on-surface-variant mt-1">{{ $submission->division?->nama }}</div>
                        </td>
                        <td class="py-4 px-space-md">
                            <div class="font-title-sm text-title-sm text-primary">{{ $submission->user?->name ?? 'Karyawan' }}</div>
                            <div class="text-xs text-on-surface-variant">{{ $submission->user?->employee?->nipeg ?? $submission->user?->email }}</div>
                        </td>
                        <td class="py-4 px-space-md">
                            <div class="font-title-sm text-title-sm">{{ $submission->overtime_date?->format('d/m/Y') }}</div>
                            <div class="text-xs text-on-surface-variant">{{ substr($submission->start_time, 0, 5) }} - {{ substr($submission->end_time, 0, 5) }} WIB</div>
                        </td>
                        <td class="py-4 px-space-md font-title-sm text-secondary font-bold">{{ number_format((float) $submission->estimated_hours, 2) }} Jam</td>
                        <td class="py-4 px-space-md">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $decisionClass }}">{{ $decisionLabel }}</span>
                            @if($submission->vp_notes)
                                <div class="mt-2 rounded-lg bg-surface-container-low p-2.5 text-xs text-on-surface-variant max-w-xs">{{ $submission->vp_notes }}</div>
                            @endif
                        </td>
                        <td class="py-4 px-space-md">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $currentMeta['class'] }}">{{ $currentMeta['label'] }}</span>
                            @if($submission->status === 'REJECTED_HR' && $submission->hr_notes)
                                <div class="mt-2 rounded-lg bg-surface-container-low p-2.5 text-xs text-on-surface-variant max-w-xs">Catatan HR: {{ $submission->hr_notes }}</div>
                            @endif
                        </td>
                        <td class="py-4 px-space-md text-xs text-on-surface-variant">{{ $submission->vp_approved_at?->format('d/m/Y H:i') }} WIB</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-4xl block mb-2">history</span>
                            Belum ada keputusan lembur yang Anda proses.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($submissions->hasPages())
            <div class="mt-space-lg">{{ $submissions->links() }}</div>
        @endif
    </div>
</div>
@endsection
