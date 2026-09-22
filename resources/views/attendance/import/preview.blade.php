@extends('layouts.absensi')
@section('title','Preview Import Absensi')
@section('content')
<div class="max-w-6xl mx-auto p-6 space-y-6">
<h1 class="text-2xl font-semibold">Preview Import</h1>
<div class="grid md:grid-cols-3 gap-4"><div class="rounded-xl bg-white p-5 shadow-sm"><div class="text-xs text-gray-500">Format</div><div class="text-lg font-semibold">{{ $preview['format'] }}</div></div><div class="rounded-xl bg-white p-5 shadow-sm"><div class="text-xs text-gray-500">Total Baris</div><div class="text-lg font-semibold">{{ $preview['total_rows'] }}</div></div><div class="rounded-xl bg-white p-5 shadow-sm"><div class="text-xs text-gray-500">Valid</div><div class="text-lg font-semibold">{{ $preview['valid'] }}</div></div></div>
@if(count($preview['errors']))<div class="rounded-xl bg-red-50 p-5"><h2 class="font-semibold text-red-800">Baris bermasalah</h2><div class="mt-3 space-y-2 text-sm">@foreach($preview['errors'] as $error)<div>Baris {{ $error['row'] }} — {{ $error['reason'] }}</div>@endforeach</div></div>@endif
@if($preview['valid'] > 0)<form method="POST" action="{{ route('attendance.import.production.commit') }}">@csrf<button class="rounded-lg bg-primary px-5 py-2 text-white">Commit {{ $preview['valid'] }} baris valid</button></form>@endif
</div>
@endsection
