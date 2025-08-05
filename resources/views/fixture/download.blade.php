@vite(['resources/css/app.css', 'resources/js/app.js'])

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex flex-col items-center justify-center mb-6">
        <img class="w-36 mb-6" src="{{ asset('images/logo.png') }}" alt="Huddersfield Pool Logo" />
        <h1 class="text-4xl font-bold tracking-tight text-gray-900 font-serif mb-1">{{ $section->name }}</h1>
        <p class="text-sm uppercase tracking-widest">{{ $section->season->name }}</p>
    </div>
    <div class="bg-white shadow overflow-hidden -mx-4 sm:mx-0">
        <table class="min-w-full divide-y divide-gray-300 table-fixed">
            <thead>
                <tr class="bg-green-700 text-white">
                    <th class="py-1 px-2 text-left text-sm font-semibold text-gray-900"></th>
                    <th class="py-1 px-2 text-left text-sm font-semibold text-gray-900"></th>
                    @foreach($dates as $date)
                    <th class="py-1 px-1 text-center text-xs font-semibold text-white">
                        {{ $date }}
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @foreach($grid as $key => $row)
                <tr class="divide-x divide-gray-200">
                    <td class="py-1 px-2 pl-2 text-xs font-medium whitespace-nowrap text-gray-900 text-center">{{ $loop->iteration == 10 ? 0 : $loop->iteration }}</td>
                    <td class="py-1 px-2 pl-2 text-xs font-medium whitespace-nowrap text-gray-900">
                        {{ $key }}
                    </td>
                    @foreach($row as $cell)
                    <td class="py-1 px-1 text-xs whitespace-nowrap text-center">
                        {{ $cell ?? '' }}
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>