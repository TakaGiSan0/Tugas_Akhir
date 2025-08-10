@extends('layouts.app')

@section('title', 'Dashboard')

@section('sidebar')
    @if (auth()->user()->role == 'Super Admin')
        @include('sidebar.superadmin.sidebar')
    @elseif(auth()->user()->role == 'Admin')
        @include('sidebar.admin.sidebar')
    @elseif(auth()->user()->role == 'User')
        @include('sidebar.user.sidebar')
    @endif
@endsection

@section('content')

    <!-- Dashboard Header -->
    <section class="relative shadow-md sm:rounded-lg overflow-hidden antialiased">

        <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-xl border border-gray-200">
            <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                <div class="w-full md:w-1/2">
                    <form class="flex items-center" method="GET" action="{{ url()->current() }}">
                        <label for="simple-search" class="sr-only">Search</label>
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor"
                                    viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" id="simple-search"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Badge No/Employee Name" value="{{ $searchQuery }}" name="searchQuery">
                        </div>
                    </form>

                </div>
                @php
                    $pesertaCount = $pesertas->total();
                @endphp
                <p class="dark:text-gray-200 text-black">Demand : {{ $pesertaCount }}</p>
                @if (auth()->user()->role == 'Super Admin')
                    <div
                        class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                        <a href="{{ route('download.pdf', request()->query()) }}"
                            class="flex items-center justify-center text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800">
                            <svg class="h-4 w-4 mr-2 text-white-500" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" />
                                <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                <polyline points="7 11 12 16 17 11" />
                                <line x1="12" y1="4" x2="12" y2="16" />
                            </svg>
                            Download
                        </a>
                    </div>

                    <div
                        class="w-full relative md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">

                        <div class="relative inline-block text-left">
                            <div>
                                <button id="filterDropdownButton" type="button"
                                    class="w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                                    aria-haspopup="true" aria-expanded="true">
                                    Dept
                                    <svg class="-mr-1 ml-1.5 w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path clip-rule="evenodd" fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                    </svg>
                                </button>
                            </div>

                            <div id="filterDropdown"
                                class="absolute right-0 mt-2 w-56 origin-top-right rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 overflow-auto max-h-96 hidden"
                                style="z-index: 9999;">
                                <form method="GET" action="{{ url()->current() }}">
                                    <div class="p-4">
                                        <div>
                                            @foreach($departments as $department)
                                                <div class="flex items-center">
                                                    <input type="checkbox" name="dept[]" value="{{ $department }}"
                                                        class="w-4 h-4 border-gray-300 rounded text-primary-600" {{ in_array($department, $deptFilters ?? []) ? 'checked' : '' }}>
                                                    <label class="ml-2 text-sm font-medium">{{ $department }}</label>
                                                </div>
                                            @endforeach
                                        </div>

                                        <button type="submit"
                                            class="mt-4 w-full inline-flex items-center justify-center py-2 px-4 text-sm font-medium text-white bg-blue-600 bg-primary-600 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-4 focus:ring-primary-300">Filter</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse sm:rounded-xl">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th rowspan="2" class="px-4 py-4 border border-gray-300">No</th>
                            <th rowspan="2" class="px-4 py-4 border border-gray-300">Badge No</th>
                            <th rowspan="2" class="px-4 py-4 border border-gray-300">Emp Name</th>
                            <th rowspan="2" class="px-4 py-4 border border-gray-300">Date of Join</th>
                            <th rowspan="2" class="px-4 py-4 border border-gray-300">Dept</th>
                            <th colspan="{{ count($allStations) }}" class="px-4 py-2 text-center border border-gray-300">
                                Station</th>
                            <th colspan="{{ count($masterSkills) }}" class="px-4 py-2 text-center border border-gray-300">
                                Skill Code</th>
                        </tr>
                        <tr>
                            @foreach($allStations as $station)
                                <th class="px-4 py-2 text-center border border-gray-300">{{ $station }}</th>
                            @endforeach
                            @foreach ($masterSkills as $skills)
                                <th class="px-4 py-2 text-center border border-gray-300">{{ $skills->skill_code }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 dark:text-gray-200 bg-gray-50 dark:bg-gray-700">
                        @php $no = ($pesertas->currentPage() - 1) * $pesertas->perPage(); @endphp
                        @foreach ($pesertas as $peserta)
                            <tr class="border-t">
                                <td class="px-4 py-3 border border-gray-300">{{ ++$no }}</td>
                                <td class="px-4 py-3 border border-gray-300">{{ $peserta->badge_no }}</td>
                                <td class="px-4 py-3 border border-gray-300">{{ $peserta->employee_name }}</td>
                                <td class="px-4 py-3 border border-gray-300">{{ $peserta->join_date }}</td>
                                <td class="px-4 py-3 border border-gray-300">{{ $peserta->dept }}</td>

                                @foreach ($allStations as $station)
                                    <td class="px-4 py-2 text-center border border-gray-300">
                                        {{ $peserta->processed_stations[$station] ?? '-' }}
                                    </td>
                                @endforeach

                                @foreach ($masterSkills as $skillCode => $skillModel)
                                    <td class="px-4 py-2 text-center border border-gray-300">
                                        {{ isset($peserta->processed_skills_lookup[$skillCode]) ? '✔️' : '-' }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                        <tr>
                            <td class="px-4 py-3 text-center border border-gray-300" colspan="5">Supply</td>

                            @foreach ($allStations as $station)
                                <td class="px-4 py-2 text-center border border-gray-300">
                                    {{ $stationsWithLevels[$station] ?? '-' }}
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-center border border-gray-300" colspan="5">GAP</td>

                            @foreach ($allStations as $station)
                                <td class="px-4 py-2 text-center border border-gray-300">
                                    {{ $stationsWithGaps[$station] ?? '-' }}
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>

            </div>
            <div class="mt-4">
                {{ $pesertas->appends(['dept' => request('dept'), 'searchQuery' => request('searchQuery')])->links()  }}
            </div>


    </section>

    <footer class="w-full bg-neutral text-neutral-content py-10 bg-gray-100 dark:bg-gray-800 dark:text-[#f1f1f1]">
        <div class="w-full p-4 py-6 lg:py-8">
            <div class="flex items-start space-x-6 mb-10">
                <!-- Legend Product Code -->
                <div class="flex items-center">
                    <p class="text-xs  text-gray-500 uppercase dark:text-white">
                        Legend Product Code :
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-6 sm:gap-8 sm:grid-cols-6 text-xs ">
                    <div class="dark:text-white">
                        <ul class="text-gray-500 dark:text-white ">
                            <li>
                                <p class="text-xs   text-gray-500 uppercase dark:text-white">
                                    404 : Not Found
                                </p>

                            </li>
                            <li class="">
                                <p>403 : Forbidden</p>
                            </li>
                            <li>
                                <p>400 : Bad Request</p>
                            </li>
                        </ul>
                    </div>
                    <div class="dark:text-white">
                        <ul class="text-gray-500 dark:text-white ">
                            <li>
                                <p class="text-xs  text-gray-500 uppercase dark:text-white">
                                    500 : Internal Server Error
                                </p>

                            </li>

                            <li class="">
                                <p>503 : Service Unavailable</p>
                            </li>
                            <li>
                                <p>502 : Bad Gateway</p>
                            </li>
                        </ul>
                    </div>

                </div>

                <div
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6 text-xs text-gray-500 dark:text-white">

                </div>



            </div>
            <div class="flex items-start space-x-6 mb-10">
                <div class="flex items-center">
                    <p class="text-sm  text-gray-500 uppercase dark:text-white">
                        Legend Skill Code :
                    </p>
                </div>


                @php
                    $chunks = $masterSkills->chunk(5);
                @endphp

                <div
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6 text-xs text-gray-500 dark:text-white">
                    @foreach ($chunks as $chunk)
                        <div>
                            <ul>
                                @foreach ($chunk as $skill)
                                    <li>
                                        <p class="text-xs text-gray-500 uppercase dark:text-white">
                                            {{ $skill->skill_code }} : {{ $skill->job_skill }}
                                        </p>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>

            </div>
            <div class="flex items-start space-x-6">
                <!-- Legend Product Code -->
                <div class="flex items-center pr-10">
                    <p class="text-sm  text-gray-500 uppercase dark:text-white">
                        Skill Level:
                    </p>
                </div>

                <!-- Resources Section -->
                <div class="grid grid-cols-2 gap-6 sm:gap-8 sm:grid-cols-6 text-xs">
                    <div>
                        <ul class="text-gray-500 dark:text-white">
                            <li>
                                <p class="text-xs   text-gray-500 uppercase dark:text-white">
                                    1 = LEVEL 1 (work under supervision)
                                </p>

                            </li>
                            <li class="">
                                <p>2 = LEVEL 2 (work according to
                                    standards)</p>
                            </li>
                            <li>
                                <p>3 = Level 3 (expert)</p>
                            </li>
                            <li>
                                <p>4 = Level 4 (expert & trainer)
                                </p>
                            </li>

                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('filterDropdownButton').addEventListener('click', function () {
            const dropdown = document.getElementById('filterDropdown');
            dropdown.classList.toggle('hidden'); // Toggle visibility
        });

        // Optional: Close the dropdown when clicking outside
        document.addEventListener('click', function (event) {
            const dropdown = document.getElementById('filterDropdown');
            const button = document.getElementById('filterDropdownButton');
            if (!button.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
@endsection