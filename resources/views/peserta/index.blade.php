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

    <section class="relative shadow-md sm:rounded-lg overflow-hidden antialiased">

        <!-- Start coding here -->
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
            <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                <div class="w-full md:w-1/2">
                    <form class="flex items-center" method="GET" action="{{ route('dashboard.peserta') }}">
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
                                placeholder="Search" value="{{ old('searchQuery', $searchQuery) }}" name="searchQuery">
                        </div>
                    </form>


                </div>
                @if (auth()->user()->role == 'Super Admin')
                    <div
                        class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                        <button
                            class="open-modal flex items-center justify-center text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800"
                            type="button">

                            <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewbox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path clip-rule="evenodd" fill-rule="evenodd"
                                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                            </svg>Import Excel
                        </button>
                    </div>
                    <div
                        class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                        <a href="{{ route('export.peserta') }}"
                            class="flex items-center justify-center text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800">
                            <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewbox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path clip-rule="evenodd" fill-rule="evenodd"
                                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                            </svg>
                            <input type="file" name="file" class="hidden" accept=".xlsx,.xls">Export Excel
                        </a>
                    </div>
                    <div
                        class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                        <a href="{{ route('peserta.create') }}"
                            class="flex items-center justify-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">

                            <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewbox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path clip-rule="evenodd" fill-rule="evenodd"
                                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                            </svg>
                            Add Employee

                        </a>
                    </div>
                @endif
            </div>
            @if (session('success'))
                <div class="alert alert-success ml-4">
                    {{ session('success') }}
                </div>
            @endif
            <div class="overflow-x-auto">

                <table class="w-full text-sm text-center text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>

                            <th scope="col" class="px-4 py-4">No</th>
                            <th scope="col" class="px-4 py-4">Badge No</th>
                            <th scope="col" class="px-4 py-3">Employee Name</th>
                            <th scope="col" class="px-4 py-3">Dept</th>
                            <th scope="col" class="px-4 py-3">Position/effective</th>
                            <th scope="col" class="px-4 py-3">Join Date</th>
                            <th scope="col" class="px-4 py-3">Status</th>
                            <th scope="col" class="px-4 py-3">Category Level</th>
                            <th scope="col" class="px-4 py-3">Gender</th>
                            <th scope="col" class="px-4 py-3">Last Modified</th>
                            @if (auth()->user()->role == 'Super Admin')
                                <th scope="col" class="px-4 py-3">Action</th>
                            @endif
                        </tr>
                    </thead>

                    <?php $no = ($peserta->currentPage() - 1) * $peserta->perPage(); ?>
                    <tbody class="text-gray-600 dark:text-gray-200 bg-gray-50 dark:bg-gray-700">
                        @if ($peserta->isEmpty())
                            <tr class="">
                                <td colspan="10" class="px-4 py-3 text-center">
                                    {{ $message }}
                                </td>
                            </tr>
                        @else
                            @foreach ($peserta as $p)
                                <tr class=>
                                    <th scope="row" name="id"
                                        class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ ++$no }}
                                    </th>
                                    <td class="px-4 py-3">{{ $p->badge_no }}</td>
                                    <td class="px-4 py-3">{{ $p->employee_name }}</td>
                                    <td class="px-4 py-3">{{ $p->dept }}</td>
                                    <td class="px-4 py-3">{{ $p->position }}</td>
                                    <td class="px-4 py-3">{{ $p->join_date }}</td>
                                    <td class="px-4 py-3">{{ $p->status }}</td>
                                    <td class="px-4 py-3">{{ $p->category_level }}</td>
                                    <td class="px-4 py-3">{{ $p->gender }}</td>
                                    <td class="px-4 py-3">{{ optional($p->user)->user ?? 'Tidak ada user' }}<br>
                                        {{ optional($p->user)->updated_at ?? '' }}</td>
                                    @if (auth()->user()->role == 'Super Admin')
                                    <td class="relative px-4 py-3 text-center">
                                        <!-- Trigger & Dropdown wrapper -->
                                        <div class="inline-block text-left">
                                            <!-- Button -->
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

                                                <!-- Button Edit -->
                                                @if (auth()->user()->role == 'Super Admin')
                                                    <a href="{{ route('peserta.edit', $p->id) }}"
                                                        class="w-full flex items-center gap-2 text-left px-4 py-2 hover:bg-gray-100">
                                                        <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor"
                                                            stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path d="M12 20h9" />
                                                            <path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4 12.5-12.5z" />
                                                        </svg>
                                                        Edit
                                                    </a>
                                                    <form action="{{ route('peserta.destroy', $p->id) }}" method="POST"
                                                        onsubmit="return confirm('Are you sure you want to delete this Employee?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button
                                                            class="w-full flex items-center gap-2 text-left px-4 py-2 text-red-600 hover:bg-red-100">
                                                            <svg class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                            Delete
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $peserta->links() }}
        </div>
    </section>


    <!-- Modal -->
    <div id="uploadModal"
        class="hidden fixed inset-0 p-4 justify-center items-center w-full h-full z-50 overflow-auto font-[sans-serif]">
        <!-- Overlay (background hitam) -->
        <div class="fixed inset-0 bg-black bg-opacity-50"></div>
        <!-- Konten Modal -->
        <div class="w-full max-w-lg bg-white shadow-lg rounded-lg p-6 relative z-10">
            <div class="flex items-center pb-3 border-b border-gray-200">
                <div class="flex-1">
                    <h3 class="text-gray-800 text-xl font-bold">Upload File</h3>
                    <p class="text-gray-600 text-xs mt-1">Upload file to this project</p>
                </div>

                <!-- Tombol Close -->
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-3 ml-2 cursor-pointer shrink-0 fill-gray-400 hover:fill-red-500 close-modal"
                    viewBox="0 0 320.591 320.591">
                    <path
                        d="M30.391 318.583a30.37 30.37 0 0 1-21.56-7.288c-11.774-11.844-11.774-30.973 0-42.817L266.643 10.665c12.246-11.459 31.462-10.822 42.921 1.424 10.362 11.074 10.966 28.095 1.414 39.875L51.647 311.295a30.366 30.366 0 0 1-21.256 7.288z"
                        data-original="#000000"></path>
                    <path
                        d="M287.9 318.583a30.37 30.37 0 0 1-21.257-8.806L8.83 51.963C-2.078 39.225-.595 20.055 12.143 9.146c11.369-9.736 28.136-9.736 39.504 0l259.331 257.813c12.243 11.462 12.876 30.679 1.414 42.922-.456.487-.927.958-1.414 1.414a30.368 30.368 0 0 1-23.078 7.288z"
                        data-original="#000000"></path>
                </svg>
            </div>

            <!-- Konten Lainnya -->
            <form action="{{ route('import.peserta') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class=" border-gray-200 border-dashed mt-6">
                    <!-- Konten Upload -->
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="file_input">Upload
                        file</label>
                    <input
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                        id="file_input" type="file" name="file" accept=".xlsx,.xls">
                </div>

                <div class="border-t border-gray-200 pt-6 flex justify-between gap-4 mt-6">
                    <button type="button"
                        class="w-full px-4 py-2 rounded-lg text-gray-800 text-sm border-none outline-none tracking-wide bg-gray-200 hover:bg-gray-300 active:bg-gray-200 close-modal">Cancel</button>
                    <button type="submit"
                        class="w-full px-4 py-2 rounded-lg text-white text-sm border-none outline-none tracking-wide bg-blue-600 hover:bg-blue-700 active:bg-blue-600">Import</button>
                </div>
            </form>
        </div>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('uploadModal');
        const openModalButtons = document.querySelectorAll(
            '.open-modal'); // Sesuaikan tombol untuk membuka modal
        const closeModalButtons = document.querySelectorAll('.close-modal');

        // Fungsi untuk membuka modal
        openModalButtons.forEach(button => {
            button.addEventListener('click', () => {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            });
        });

        // Fungsi untuk menutup modal
        closeModalButtons.forEach(button => {
            button.addEventListener('click', () => {
                modal.classList.add('hidden');
            });
        });
    });

    function toggleDropdown(event, btn) {
            event.stopPropagation(); // cegah event bubbling
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