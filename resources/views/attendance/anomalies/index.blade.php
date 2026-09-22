@extends('layouts.absensi')
@section('title','Review Anomali')
@section('content')
<div class="max-w-7xl mx-auto p-6 space-y-6">
<div class="flex items-center justify-between"><div><h1 class="text-2xl font-semibold">Review Anomali</h1><p class="text-sm text-gray-500">Temuan presensi dan lembur yang perlu ditinjau HR.</p></div><a href="{{ route('attendance.anomalies', ['scan'=>1]) }}" class="rounded-lg bg-primary px-4 py-2 text-white">Scan Sekarang</a></div>
<div class="overflow-hidden rounded-xl bg-white shadow-sm"><table class="w-full text-sm"><thead class="bg-gray-50"><tr><th class="p-3 text-left">Tanggal</th><th class="p-3 text-left">Karyawan</th><th class="p-3 text-left">Anomali</th><th class="p-3 text-left">Severity</th><th class="p-3 text-left">Status</th><th class="p-3 text-left">Aksi</th></tr></thead><tbody>@forelse($anomalies as $item)<tr class="border-t"><td class="p-3">{{ optional($item->tanggal)->format('d/m/Y') }}</td><td class="p-3">{{ $item->employee?->nama ?? '-' }}</td><td class="p-3">{{ $item->type }}</td><td class="p-3">{{ $item->severity }}</td><td class="p-3">{{ $item->status }}</td><td class="p-3">@if($item->status==='OPEN')<form method="POST" action="{{ route('attendance.anomalies.resolve',$item) }}" class="flex gap-2">@csrf @method('PATCH')<input type="hidden" name="status" value="RESOLVED"><input name="resolution_notes" placeholder="Catatan" class="rounded border px-2 py-1"><button class="rounded bg-primary px-3 py-1 text-white">Selesaikan</button></form>@else<span class="text-gray-500">{{ $item->resolution_notes ?: '—' }}</span>@endif</td></tr>@empty<tr><td colspan="6" class="p-8 text-center text-gray-500">Belum ada anomaly.</td></tr>@endforelse</tbody></table></div>
{{ $anomalies->links() }}
</div>
@endsection
