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

    <!-- Start block -->
    <section class="relative shadow-md sm:rounded-lg overflow-hidden antialiased">


        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-lg dark:bg-gray-800 dark:shadow-none">
            <form method="GET" action="{{ route('dashboard.summary') }}">
                <div class="relative mb-10 w-full flex  items-center justify-between rounded-md">
                    <svg class="absolute left-2 block h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8" class=""></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65" class=""></line>
                    </svg>
                    <input type="text" name="search" id="search"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Training Name" value="{{ request('search') }}" />
                </div>

                <div
                    class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 p-6 bg-white rounded-lg shadow-md dark:bg-gray-800">
                    <div class="flex flex-col">
                        <label for="training_category" class="text-sm font-medium dark:text-gray-200 text-black">Station
                        </label>
                        <select id="station" name="station"
                            class="mt-2 block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white text-black dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value=""
                                class="dark:bg-gray-700  dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                All Station</option>
                            @if(isset($station))
                                @foreach ($station as $nama_stasiun)
                                    <option value="{{ $nama_stasiun }}" {{ (isset($station) && $station == $nama_stasiun) ? 'selected' : '' }}>{{ $nama_stasiun }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="flex flex-col">
                        <label for="training_date" class="text-sm font-medium dark:text-gray-200 text-black">Training
                            Date</label>
                        <input type="date" id="date_start" name="date_start" value="{{ request('date_start') }}"
                            class="mt-2 block w-full cursor-pointer rounded-md border border-gray-300 bg-gray-50 px-3 py-2 shadow-sm outline-none focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 text-black dark:focus:border-blue-500" />
                    </div>
                    <div class="flex flex-col">
                        <label for="training_category" class="text-sm font-medium dark:text-gray-200 text-black">Training
                            Category</label>
                        <select id="category" name="category"
                            class="mt-2 block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white text-black dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value=""
                                class="dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500  dark:focus:border-blue-500">
                                All Categories</option>
                            @foreach ($training_categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit"
                        class="mt-4 mx-3 inline-flex h-10 w-50 items-center justify-center rounded-lg bg-blue-600 py-2 px-4 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300">
                        Filter
                    </button>
                    <a href="{{ route('dashboard.summary') }}"
                        class="mt-4 mx-3 inline-flex h-10 w-50 items-center justify-center rounded-lg bg-red-600 py-2 px-4 text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-300">
                        Clear
                    </a>
                </div>

            </form>

        </div>

        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden mt-5">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-center text-gray-500 dark:text-gray-400">
                    <thead
                        class="text-xs text-center text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-4">No</th>
                            <th scope="col" class="px-4 py-4">Doc. Ref</th>
                            <th scope="col" class="px-4 py-3">Training Category</th>
                            <th scope="col" class="px-4 py-3">Training Name</th>
                            <th scope="col" class="px-4 py-3">Rev</th>
                            <th scope="col" class="px-4 py-3">Station</th>
                            <th scope="col" class="px-4 py-3">Trainer Name</th>
                            <th scope="col" class="px-4 py-3">Training Date</th>
                            <th scope="col" class="px-4 py-3">Event Number</th>
                            <th scope="col" class="px-4 py-3">Action</th>

                        </tr>
                    </thead>
                    @php
                        $uniqueRecords = $trainingRecords->unique('doc_ref');
                    @endphp

                    <?php $no = ($trainingRecords->currentPage() - 1) * $trainingRecords->perPage(); ?>
                    @if ($trainingRecords->isNotEmpty())
                        @foreach ($trainingRecords as $rc)
                            <tbody class="text-gray-600 dark:text-gray-200 bg-gray-50 dark:bg-gray-700">
                                <tr class=>
                                    <td scope="row" class="px-4 py-3 ">
                                        {{ ++$no }}
                                    </td>
                                    <td class="px-4 py-3">{{ $rc->doc_ref }}</td>
                                    <td class="px-4 py-3">{{ $rc->trainingCategory->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3">{{ $rc->training_name }}</td>
                                    <td class="px-4 py-3">{{ $rc->rev }}</td>
                                    <td class="px-4 py-3">{{ $rc->station }}</td>
                                    <td class="px-4 py-3">{{ $rc->trainer_name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3">{{ $rc->formatted_date_range ?? 'N/A' }}</td>
                                    <td class="px-4 py-3">TR-{{ $rc->id }}</td>
                                    <td class="relative px-4 py-3 text-center">
                                        <div class="inline-block text-left">
                                            <button onclick="toggleDropdown(event, this)"
                                                class="hover:bg-gray-100 dark:hover:bg-gray-600 rounded-full p-2">
                                                <!-- SVG icon -->
                                                <svg class="h-6 w-6 text-gray-500" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="1" />
                                                    <circle cx="12" cy="5" r="1" />
                                                    <circle cx="12" cy="19" r="1" />
                                                </svg>
                                            </button>

                                            <div
                                                class="dropdown-menu hidden absolute top-0 right-full ml-2 bg-white border rounded shadow-md z-50 w-32">

                                                @if (in_array(Auth::user()->role, ['Super Admin', 'Admin']))
                                                    <a href="{{ route('download.summary', $rc->id) }}">
                                                        <button type="button" data-modal-target="updateProductModal"
                                                            data-modal-toggle="updateProductModal"
                                                            class="w-full flex items-center gap-2 text-left px-4 py-2 hover:bg-gray-100">
                                                            <svg class="w-4 h-4 flex-shrink-0" xmlns="http://www.w3.org/2000/svg"
                                                                viewbox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                                <path
                                                                    d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
                                                            </svg>
                                                            Download
                                                        </button>
                                                    </a>
                                                @endif
                                                <button type="button" data-modal-target="readProductModal"
                                                    data-modal-toggle="readProductModal" onclick="openModal({{ $rc->id }})"
                                                    class="w-full flex items-center gap-2 text-left px-4 py-2 hover:bg-gray-100">
                                                    <svg class="w-4 h-4 flex-shrink-0" xmlns="http://www.w3.org/2000/svg"
                                                        viewbox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" />
                                                    </svg>
                                                    View
                                                </button>
                                            </div>
                                        </div>
                                    </td>

                                </tr>
                            </tbody>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="10" class="px-4 py-3 text-center">Tidak ada training records ditemukan.
                            </td>
                        </tr>
                    @endif
                </table>
            </div>
            <div class="mt-4">
                {{ $trainingRecords->appends(['date_start' => request('date_start'), 'category' => request('category'), 'station' => request('station'), 'search' => request('search')])->links() }}
            </div>
        </div>
    </section>

    <!-- Read modal -->
    <div id="readProductModal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black bg-opacity-50 z-50">
        <div class="relative p-4 w-full max-w-6xl max-h-full">
            <!-- Modal content -->
            <div class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5">
                <!-- Modal header -->
                <div class="flex items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600">
                    <h3 class="flex-grow text-lg font-semibold text-gray-900 text-center dark:text-white">Summary Training
                        Record
                    </h3>
                    <button type="button"
                        class="justify-end text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-toggle="readProductModal" onclick="closeModal()">
                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div id="modalBody">
                    <!-- Konten akan diisi secara dinamis melalui JavaScript -->
                </div>
            </div>
        </div>
    </div>
    <!-- Delete modal -->
    <script>
        let abortController;

        document.getElementById('filterForm').addEventListener('submit', function (e) {
            // Ambil semua input dan select dalam form
            const inputs = this.querySelectorAll('input, select');
        });

        inputs.forEach(input => {
            // Jika value kosong atau default, hapus attribute name sehingga tidak akan dikirim
            if (!input.value || input.value === 'N/A') {
                input.removeAttribute('name');
            }
        });


        function openModal(id) {
            // Abort any ongoing requests
            if (abortController) {
                abortController.abort();
            }

            // Create a new AbortController for the new request
            abortController = new AbortController();

            function hideModal() {
                document.getElementById('modalBody').style.display = 'none';
            }

            // Hide the modal before opening a new one
            hideModal();

            fetch(`Training-record/public/summary/${id}`, {
                    signal: abortController.signal,
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }

                    return response.json();
                })
                .then(data => {
                    const trainingList = data.map(record => `
                            <div class="grid grid-cols-2 text-left px-9 m-5 text-black dark:text-white">
                                <h3>Training Name: ${record.training_name}</h3>
                                <p>Doc Ref: ${record.doc_ref}</p>
                                <p>Trainer Name: ${record.trainer_name}</p>
                                <p>Rev: ${record.rev}</p>
                                <p>Station: ${record.station}</p>
                                <p>Training Date: ${record.date_formatted ?? '-'}</p>
                                 <p>Skill Code: <br> ${record.skills.map(skill => skill.skill_code).join('<br> ') || '-'}</p>
                                  <p>Job Skill: <br> ${record.skills.map(skill => skill.job_skill).join('<br> ') || '-'}</p>
                            </div>

                                <table class="w-full text-sm text-center text-gray-500 dark:text-gray-400">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th scope="col" class="px-4 py-3">Employee Name</th>
                                            <th scope="col" class="px-4 py-3">Badge No</th>
                                            <th scope="col" class="px-4 py-3">Dept</th>
                                            <th scope="col" class="px-4 py-3">Position</th>
                                            <th scope="col" class="px-4 py-3">Theory Result</th>
                                            <th scope="col" class="px-4 py-3">Practical Result</th>
                                            <th scope="col" class="px-4 py-3">Level</th>
                                            <th scope="col" class="px-4 py-3">Final Judgement</th>
                                        </tr>
                                    </thead>
                                    ${record.peserta.map(peserta => `                                                                                                                                                                    <tbody class="text-center">
                                                                                                                                                                                                                <td scope="col" class="px-4 py-3">${peserta.employee_name}</td>
                                                                                                                                                                                                                <td scope="col" class="px-4 py-3">${peserta.badge_no}</td>
                                                                                                                                                                                                                <td scope="col" class="px-4 py-3">${peserta.dept}</td>
                                                                                                                                                                                                                <td scope="col" class="px-4 py-3">${peserta.position}</td>
                                                                                                                                                                                                                <td scope="col" class="px-4 py-3">${peserta.pivot.theory_result || 'N/A'}</td>
                                                                                                                                                                                                                <td scope="col" class="px-4 py-3">${peserta.pivot.practical_result || 'N/A'}</td>
                                                                                                                                                                                                                <td scope="col" class="px-4 py-3">${peserta.pivot.level || 'N/A'}</td>
                                                                                                                                                                                                                <td scope="col" class="px-4 py-3">${peserta.pivot.final_judgement || 'N/A'}</td>                                                                                                                                                                                                   </tbody>                                                                                     `).join('')}
                        `).join('');

                    document.getElementById('modalBody').innerHTML = trainingList;
                    setTimeout(showModal, 100);
                })
                .catch(error => console.error('Error fetching data:', error));
        }

        function showModal() {
            document.getElementById('modalBody').style.display = 'block';
        }

        function hideModal() {
            document.getElementById('modalBody').style.display = 'none';
        }

        // Toggle untuk membuka/menutup dropdown
        function toggleDropdown() {
            var dropdown = document.getElementById("filterDropdown");
            dropdown.classList.toggle("hidden");
        }

        // Tutup dropdown saat mengklik di luar elemen
        window.onclick = function (event) {
            var dropdown = document.getElementById("filterDropdown");
            var button = document.getElementById("filterDropdownButton");

            if (!button.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add("hidden");
            }
        }

        function toggleDropdown(event, btn) {
            event.stopPropagation();
            const dropdown = btn.nextElementSibling;
            const allDropdowns = document.querySelectorAll('.dropdown-menu');

            allDropdowns.forEach(d => {
                if (d !== dropdown) d.classList.add('hidden');
            });

            dropdown.classList.toggle('hidden');
        }

        // Tutup dropdown saat klik di luar
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.dropdown-menu') && !e.target.closest('button')) {
                document.querySelectorAll('.dropdown-menu').forEach(d => d.classList.add('hidden'));
            }
        });

    </script>

@endsection