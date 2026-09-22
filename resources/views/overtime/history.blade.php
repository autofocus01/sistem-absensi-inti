@extends('layouts.absensi')

@section('title', 'Riwayat Pengajuan Lembur - PT. INTI')

@section('content')
<div class="flex flex-col gap-space-lg w-full">
    <div class="relative overflow-hidden rounded-xl bg-surface-container-lowest shadow-[0_1px_8px_rgba(0,0,0,0.04)] p-space-xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full bg-secondary-container text-on-secondary-container font-label-sm text-label-sm font-semibold">
                        Riwayat Lembur
                    </span>
                    <span class="text-on-surface-variant text-xs">•</span>
                    <span class="font-body-sm text-body-sm text-on-surface-variant">Karyawan</span>
                </div>
                <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">Riwayat Pengajuan Lembur</h1>
                <p class="font-body-md text-body-md text-on-surface-variant">
                    Lihat status, keputusan VP, dan hasil verifikasi HR atas seluruh pengajuan lembur Anda.
                </p>
            </div>
            <a href="{{ route('overtime.index') }}" class="inline-flex items-center justify-center gap-2 px-space-lg py-2.5 rounded-lg bg-primary text-on-primary font-title-sm hover:opacity-90">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Pengajuan Baru
            </a>
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)] p-space-xl">
        <div class="flex items-center justify-between mb-space-md">
            <div>
                <h2 class="font-headline-sm text-headline-sm text-primary">Daftar Pengajuan</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Pengajuan terbaru ditampilkan lebih dahulu.</p>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-surface-container text-primary font-label-sm font-semibold">
                {{ $submissions->total() }} Pengajuan
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[980px]">
                <thead>
                    <tr class="bg-surface-container-low text-on-surface-variant font-label-sm uppercase">
                        <th class="py-3 px-space-md rounded-l-lg">No. Tiket</th>
                        <th class="py-3 px-space-md">Tanggal &amp; Jam</th>
                        <th class="py-3 px-space-md">Durasi</th>
                        <th class="py-3 px-space-md">Status</th>
                        <th class="py-3 px-space-md">Keputusan / Catatan</th>
                        <th class="py-3 px-space-md rounded-r-lg">Diperbarui</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container-low">
                @forelse($submissions as $submission)
                    @php
                        $statusMeta = match ($submission->status) {
                            'PENDING_VP' => ['label' => 'Menunggu VP', 'class' => 'bg-amber-50 text-amber-800'],
                            'APPROVED_VP' => ['label' => 'Disetujui VP • Menunggu HR', 'class' => 'bg-blue-50 text-blue-800'],
                            'REJECTED_VP' => ['label' => 'Ditolak VP', 'class' => 'bg-red-50 text-red-800'],
                            'VERIFIED_HR' => ['label' => 'Diverifikasi HR', 'class' => 'bg-emerald-50 text-emerald-800'],
                            'REJECTED_HR' => ['label' => 'Ditolak HR', 'class' => 'bg-red-50 text-red-800'],
                            default => [$submission->status, 'bg-surface-container text-on-surface-variant'],
                        };
                        $note = in_array($submission->status, ['REJECTED_VP', 'APPROVED_VP'], true)
                            ? $submission->vp_notes
                            : ($submission->hr_notes ?: $submission->vp_notes);
                    @endphp
                    <tr class="hover:bg-surface-container-low/40 align-top">
                        <td class="py-4 px-space-md">
                            <div class="font-mono text-xs font-semibold text-primary">{{ $submission->ticket_number }}</div>
                            <div class="text-xs text-on-surface-variant mt-1">{{ strtoupper(str_replace('_', ' ', $submission->submission_mode)) }}</div>
                        </td>
                        <td class="py-4 px-space-md">
                            <div class="font-title-sm text-title-sm">{{ $submission->overtime_date?->format('d/m/Y') }}</div>
                            <div class="font-body-sm text-body-sm text-on-surface-variant">{{ substr($submission->start_time, 0, 5) }} - {{ substr($submission->end_time, 0, 5) }} WIB</div>
                        </td>
                        <td class="py-4 px-space-md">
                            <div class="font-title-sm text-title-sm">{{ number_format((float) $submission->estimated_hours, 2) }} Jam</div>
                            @if($submission->recognized_minutes !== null)
                                <div class="text-xs text-on-surface-variant mt-1">Diakui: {{ $submission->recognized_minutes }} menit</div>
                            @endif
                        </td>
                        <td class="py-4 px-space-md">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusMeta['class'] }}">{{ $statusMeta['label'] }}</span>
                            @if($submission->eligibility_status && $submission->eligibility_status !== 'ELIGIBLE')
                                <div class="text-xs text-red-700 mt-2">Kelayakan: {{ $submission->eligibility_status }}</div>
                            @endif
                        </td>
                        <td class="py-4 px-space-md max-w-sm">
                            @if($submission->status === 'REJECTED_VP')
                                <div class="text-sm font-semibold text-red-800">Ditolak oleh VP</div>
                                <div class="text-xs text-on-surface-variant mt-1">{{ $submission->vpApprover?->name ?? 'VP Divisi' }}</div>
                            @elseif(in_array($submission->status, ['APPROVED_VP', 'VERIFIED_HR', 'REJECTED_HR'], true) && $submission->vp_approved_at)
                                <div class="text-sm font-semibold text-on-surface">VP: Disetujui</div>
                                <div class="text-xs text-on-surface-variant mt-1">{{ $submission->vpApprover?->name ?? 'VP Divisi' }}</div>
                            @else
                                <div class="text-sm text-on-surface-variant">Belum ada keputusan</div>
                            @endif
                            @if($note)
                                <div class="mt-2 rounded-lg bg-surface-container-low p-2.5 text-xs text-on-surface-variant">{{ $note }}</div>
                            @endif
                        </td>
                        <td class="py-4 px-space-md text-xs text-on-surface-variant">
                            {{ $submission->updated_at?->format('d/m/Y H:i') }} WIB
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-4xl block mb-2">history</span>
                            Belum ada pengajuan lembur.
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
