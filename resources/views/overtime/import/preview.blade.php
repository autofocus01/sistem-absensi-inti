@extends('layouts.absensi')

@section('title', 'Preview Import Lembur')

@section('content')
<div class="inti-page">

    <div class="space-y-6">

        <h1 class="text-2xl font-semibold">Preview Import Lembur</h1>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-xl bg-white p-5 shadow-sm">
                <div class="text-xs text-gray-500">Format</div>
                <div class="text-lg font-semibold">{{ $preview['format'] ?? 'tidak dikenali' }}</div>
            </div>

            <div class="rounded-xl bg-white p-5 shadow-sm">
                <div class="text-xs text-gray-500">Total Baris</div>
                <div class="text-lg font-semibold">{{ $preview['total_rows'] }}</div>
            </div>

            <div class="rounded-xl bg-white p-5 shadow-sm">
                <div class="text-xs text-gray-500">Valid</div>
                <div class="text-lg font-semibold">{{ $preview['valid'] }}</div>
            </div>
        </div>

        @if(count($preview['errors']))
            <div class="rounded-xl bg-red-50 p-5">
                <h2 class="font-semibold text-red-800">Baris bermasalah</h2>

                <div class="mt-3 space-y-2 text-sm">
                    @foreach($preview['errors'] as $error)
                        <div>Baris {{ $error['row'] }} — {{ $error['reason'] }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($preview['valid'] > 0)
            <form method="POST" action="{{ route('overtime.import.commit') }}">
                @csrf

                <button class="rounded-lg bg-primary px-5 py-2 text-white">
                    Commit {{ $preview['valid'] }} baris valid
                </button>
            </form>
        @endif

    </div>

</div>
@endsection
