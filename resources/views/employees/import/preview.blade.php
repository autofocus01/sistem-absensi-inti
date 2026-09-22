<x-app-layout>
    <div class="max-w-7xl mx-auto p-6 space-y-6">
        <div>
            <h1 class="text-2xl font-semibold">Preview Import Karyawan</h1>
            <p class="text-sm opacity-70">Tidak ada perubahan database sampai tombol commit ditekan.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 border rounded">Total: <strong>{{ $preview['total_rows'] }}</strong></div>
            <div class="p-4 border rounded">Valid: <strong>{{ $preview['valid'] }}</strong></div>
            <div class="p-4 border rounded">Error: <strong>{{ count($preview['errors']) }}</strong></div>
        </div>

        @if(count($preview['errors']))
            <div class="p-4 border border-red-200 rounded bg-red-50 text-red-800">
                <h2 class="font-semibold mb-3">Baris yang harus diperbaiki</h2>
                <div class="space-y-2 text-sm">
                    @foreach($preview['errors'] as $error)
                        <div>Baris {{ $error['row'] ?? '-' }}: {{ $error['reason'] }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        @if(!empty($preview['rows']))
            <div class="border rounded overflow-x-auto bg-white">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="px-4 py-3 text-left">Baris</th>
                            <th class="px-4 py-3 text-left">NIPEG</th>
                            <th class="px-4 py-3 text-left">Nama</th>
                            <th class="px-4 py-3 text-left">Divisi</th>
                            <th class="px-4 py-3 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($preview['rows'] as $row)
                            <tr class="border-b last:border-0">
                                <td class="px-4 py-3">{{ $row['row'] }}</td>
                                <td class="px-4 py-3 font-medium">{{ $row['nipeg'] }}</td>
                                <td class="px-4 py-3">{{ $row['nama'] }}</td>
                                <td class="px-4 py-3">{{ $row['divisi'] }}</td>
                                <td class="px-4 py-3 font-semibold">{{ $row['action'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="flex gap-3">
            <a href="{{ route('employees.import') }}" class="px-4 py-2 rounded border">Upload ulang</a>

            @if($preview['valid'] > 0 && count($preview['errors']) === 0)
                <form method="POST" action="{{ route('employees.import.commit') }}">
                    @csrf
                    <button class="px-4 py-2 rounded bg-primary text-white">Commit Import</button>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
