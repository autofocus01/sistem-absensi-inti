<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Audit Trail</h2>
            <p class="text-sm text-gray-500 mt-1">Riwayat aksi penting pada sistem absensi dan lembur.</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Action</label>
                        <select name="action" class="w-full rounded-md border-gray-300">
                            <option value="">Semua action</option>
                            @foreach ($actions as $action)
                                <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Tanggal</label>
                        <input type="date" name="date" value="{{ request('date') }}" class="w-full rounded-md border-gray-300">
                    </div>
                    <div class="md:col-span-2 flex items-end gap-2">
                        <button class="px-4 py-2 rounded-md bg-gray-900 text-white">Filter</button>
                        <a href="{{ route('audit-logs.index') }}" class="px-4 py-2 rounded-md border">Reset</a>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left">Waktu</th>
                                <th class="px-4 py-3 text-left">User</th>
                                <th class="px-4 py-3 text-left">Action</th>
                                <th class="px-4 py-3 text-left">Subject</th>
                                <th class="px-4 py-3 text-left">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($logs as $log)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">{{ $log->created_at?->format('d/m/Y H:i:s') }}</td>
                                    <td class="px-4 py-3">
                                        {{ $log->user?->name ?? 'System' }}
                                        @if ($log->user?->role)
                                            <div class="text-xs text-gray-500">{{ $log->user->role }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 font-medium">{{ $log->action }}</td>
                                    <td class="px-4 py-3">{{ class_basename($log->subject_type ?? '-') }} #{{ $log->subject_id ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $log->notes ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada audit log.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4">{{ $logs->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
