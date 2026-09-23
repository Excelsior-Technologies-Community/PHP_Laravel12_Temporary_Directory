<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Temporary Directory Dashboard</title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-100 min-h-screen">

<div class="max-w-7xl mx-auto px-4 py-8">

    {{-- =========================================================
         HEADER
    ========================================================== --}}

    <div class="mb-8">

        <h1 class="text-4xl font-bold text-gray-800">
            Temporary Directory Dashboard
        </h1>

        <p class="text-gray-500 mt-2">
            Laravel 12 + Spatie Temporary Directory
        </p>

    </div>


    {{-- =========================================================
         SUCCESS MESSAGE
    ========================================================== --}}

    @if(session('success'))

        <div id="successAlert"
             class="mb-6 px-5 py-4 bg-green-100
                    border border-green-400
                    text-green-700 rounded-lg">

            {{ session('success') }}

        </div>

    @endif


    {{-- =========================================================
         ERROR MESSAGE
    ========================================================== --}}

    @if(session('error'))

        <div id="errorAlert"
             class="mb-6 px-5 py-4 bg-red-100
                    border border-red-400
                    text-red-700 rounded-lg">

            {{ session('error') }}

        </div>

    @endif


    {{-- =========================================================
         STATISTICS CARDS
    ========================================================== --}}

    <div class="grid grid-cols-1 md:grid-cols-2
                lg:grid-cols-4 gap-5 mb-8">

        {{-- Total Activities --}}
        <div class="bg-white rounded-xl shadow p-6">

            <div class="text-gray-500 text-sm">
                Total Activities
            </div>

            <div class="text-3xl font-bold text-gray-800 mt-2">
                {{ $totalActivities }}
            </div>

            <div class="text-sm text-gray-400 mt-2">
                All temporary operations
            </div>

        </div>


        {{-- Total Files --}}
        <div class="bg-white rounded-xl shadow p-6">

            <div class="text-gray-500 text-sm">
                TXT Files
            </div>

            <div class="text-3xl font-bold text-blue-600 mt-2">
                {{ $totalFiles }}
            </div>

            <div class="text-sm text-gray-400 mt-2">
                Temporary text files
            </div>

        </div>


        {{-- ZIP Files --}}
        <div class="bg-white rounded-xl shadow p-6">

            <div class="text-gray-500 text-sm">
                ZIP Archives
            </div>

            <div class="text-3xl font-bold text-purple-600 mt-2">
                {{ $totalZipFiles }}
            </div>

            <div class="text-sm text-gray-400 mt-2">
                Generated archives
            </div>

        </div>


        {{-- Downloads --}}
        <div class="bg-white rounded-xl shadow p-6">

            <div class="text-gray-500 text-sm">
                Downloads
            </div>

            <div class="text-3xl font-bold text-green-600 mt-2">
                {{ $totalDownloads }}
            </div>

            <div class="text-sm text-gray-400 mt-2">
                Download operations
            </div>

        </div>

    </div>


    {{-- =========================================================
         OPERATION STATISTICS
    ========================================================== --}}

    <div class="grid grid-cols-1 md:grid-cols-3
                gap-5 mb-8">

        <div class="bg-white rounded-xl shadow p-6">

            <div class="text-gray-500 text-sm">
                Successful Operations
            </div>

            <div class="text-2xl font-bold text-green-600 mt-2">
                {{ $successfulOperations }}
            </div>

        </div>


        <div class="bg-white rounded-xl shadow p-6">

            <div class="text-gray-500 text-sm">
                Failed Operations
            </div>

            <div class="text-2xl font-bold text-red-600 mt-2">
                {{ $failedOperations }}
            </div>

        </div>


        <div class="bg-white rounded-xl shadow p-6">

            <div class="text-gray-500 text-sm">
                Tracked Data
            </div>

            <div class="text-2xl font-bold text-indigo-600 mt-2">

                {{ number_format($totalStorageBytes / 1024, 2) }} KB

            </div>

        </div>

    </div>


    {{-- =========================================================
         EXISTING TEMPORARY DIRECTORY ACTIONS
    ========================================================== --}}

    <div class="bg-white rounded-xl shadow p-6 mb-8">

        <h2 class="text-2xl font-bold text-gray-800 mb-5">
            Temporary Directory Operations
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- CREATE --}}
            <a href="{{ route('temp.create') }}"
               class="text-center bg-blue-500
                      hover:bg-blue-600
                      text-white font-semibold
                      py-3 px-4 rounded-lg
                      transition">

                📄 Create Temp File

            </a>


            {{-- DOWNLOAD --}}
            <button
                onclick="downloadAndRefresh('{{ route('temp.download') }}')"
                class="bg-green-500
                       hover:bg-green-600
                       text-white font-semibold
                       py-3 px-4 rounded-lg
                       transition">

                ⬇️ Download File

            </button>


            {{-- ZIP --}}
            <button
                onclick="downloadAndRefresh('{{ route('temp.zip') }}')"
                class="bg-purple-500
                       hover:bg-purple-600
                       text-white font-semibold
                       py-3 px-4 rounded-lg
                       transition">

                🗜️ Download ZIP

            </button>

        </div>

    </div>


    {{-- =========================================================
         STORAGE MONITORING
    ========================================================== --}}

    <div class="bg-white rounded-xl shadow p-6 mb-8">

        <div class="flex flex-col md:flex-row
                    md:items-center
                    md:justify-between gap-4">

            <div>

                <h2 class="text-2xl font-bold text-gray-800">
                    Temporary Storage Monitor
                </h2>

                <p class="text-gray-500 mt-1">
                    Persistent copies created by the demo application.
                </p>

            </div>

            <div class="text-right">

                <div class="text-sm text-gray-500">
                    Stored Files
                </div>

                <div class="text-2xl font-bold text-indigo-600">
                    {{ $storedFileCount }}
                </div>

                <div class="text-sm text-gray-400">
                    {{ number_format($storedStorageBytes / 1024, 2) }} KB
                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         CLEANUP MANAGER
    ========================================================== --}}

    <div class="bg-white rounded-xl shadow p-6 mb-8">

        <h2 class="text-2xl font-bold text-gray-800">
            🧹 Temporary Storage Cleanup Manager
        </h2>

        <p class="text-gray-500 mt-2 mb-5">

            Remove old persistent temporary-file copies and
            their activity history.

        </p>

        <form action="{{ route('temp.cleanup') }}"
              method="POST"
              class="flex flex-col md:flex-row gap-4">

            @csrf

            <select name="days"
                    class="border border-gray-300
                           rounded-lg px-4 py-3
                           focus:ring-2
                           focus:ring-red-400">

                <option value="1">
                    Older than 1 day
                </option>

                <option value="7" selected>
                    Older than 7 days
                </option>

                <option value="30">
                    Older than 30 days
                </option>

                <option value="90">
                    Older than 90 days
                </option>

            </select>


            <button type="submit"
                    onclick="return confirmCleanup()"
                    class="bg-red-500
                           hover:bg-red-600
                           text-white
                           font-semibold
                           px-6 py-3
                           rounded-lg
                           transition">

                🧹 Cleanup Expired Data

            </button>

        </form>

    </div>


    {{-- =========================================================
         SEARCH AND FILTER
    ========================================================== --}}

    <div class="bg-white rounded-xl shadow p-6 mb-8">

        <div class="flex flex-col
                    lg:flex-row
                    lg:items-center
                    lg:justify-between
                    gap-4 mb-5">

            <div>

                <h2 class="text-2xl font-bold text-gray-800">
                    🔎 Temporary File Activity
                </h2>

                <p class="text-gray-500 mt-1">
                    Search, filter and monitor temporary operations.
                </p>

            </div>

        </div>


        <form method="GET"
              action="{{ route('temp.index') }}"
              class="grid grid-cols-1
                     md:grid-cols-2
                     lg:grid-cols-5
                     gap-3">

            {{-- SEARCH --}}
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Search filename..."
                class="border border-gray-300
                       rounded-lg px-4 py-3
                       focus:ring-2
                       focus:ring-blue-400">


            {{-- TYPE --}}
            <select name="type"
                    class="border border-gray-300
                           rounded-lg px-4 py-3">

                <option value="">
                    All Types
                </option>

                <option value="TXT"
                    {{ $type === 'TXT' ? 'selected' : '' }}>
                    TXT
                </option>

                <option value="ZIP"
                    {{ $type === 'ZIP' ? 'selected' : '' }}>
                    ZIP
                </option>

            </select>


            {{-- OPERATION --}}
            <select name="operation"
                    class="border border-gray-300
                           rounded-lg px-4 py-3">

                <option value="">
                    All Operations
                </option>

                <option value="Create"
                    {{ $operation === 'Create' ? 'selected' : '' }}>
                    Create
                </option>

                <option value="Download"
                    {{ $operation === 'Download' ? 'selected' : '' }}>
                    Download
                </option>

            </select>


            {{-- STATUS --}}
            <select name="status"
                    class="border border-gray-300
                           rounded-lg px-4 py-3">

                <option value="">
                    All Status
                </option>

                <option value="Success"
                    {{ $status === 'Success' ? 'selected' : '' }}>
                    Success
                </option>

                <option value="Failed"
                    {{ $status === 'Failed' ? 'selected' : '' }}>
                    Failed
                </option>

            </select>


            {{-- BUTTON --}}
            <button type="submit"
                    class="bg-gray-800
                           hover:bg-gray-900
                           text-white
                           font-semibold
                           px-4 py-3
                           rounded-lg">

                🔍 Search

            </button>

        </form>


<div class="mt-5 flex flex-col md:flex-row gap-3">

    @if($search || $type || $operation || $status)

        <a href="{{ route('temp.index') }}"
           class="inline-flex
                  items-center
                  justify-center
                  bg-gray-100
                  hover:bg-gray-200
                  text-gray-700
                  font-semibold
                  px-5
                  py-3
                  rounded-lg
                  transition">

            ✕ Clear All Filters

        </a>

    @endif

    <a href="{{ route('temp.export.csv', [
        'search' => $search,
        'type' => $type,
        'operation' => $operation,
        'status' => $status,
    ]) }}"
       class="inline-flex
              items-center
              justify-center
              bg-emerald-600
              hover:bg-emerald-700
              text-white
              font-semibold
              px-5
              py-3
              rounded-lg
              transition">

        📥 Export Activity CSV

    </a>

</div>

    </div>


    {{-- =========================================================
         ACTIVITY TABLE
    ========================================================== --}}

    <div class="bg-white rounded-xl shadow overflow-hidden">

        <div class="p-6 border-b">

            <h2 class="text-2xl font-bold text-gray-800">
                Recent Temporary File Activity
            </h2>

        </div>


        @if($activities->count())

            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead class="bg-gray-50">

                    <tr>

                        <th class="px-6 py-4 text-left
                                   text-sm font-semibold
                                   text-gray-600">

                            #

                        </th>

                        <th class="px-6 py-4 text-left
                                   text-sm font-semibold
                                   text-gray-600">

                            File Name

                        </th>

                        <th class="px-6 py-4 text-left
                                   text-sm font-semibold
                                   text-gray-600">

                            Type

                        </th>

                        <th class="px-6 py-4 text-left
                                   text-sm font-semibold
                                   text-gray-600">

                            Operation

                        </th>

                        <th class="px-6 py-4 text-left
                                   text-sm font-semibold
                                   text-gray-600">

                            Size

                        </th>

                        <th class="px-6 py-4 text-left
                                   text-sm font-semibold
                                   text-gray-600">

                            Status

                        </th>

                        <th class="px-6 py-4 text-left
                                   text-sm font-semibold
                                   text-gray-600">

                            Date

                        </th>

                    </tr>

                    </thead>


                    <tbody class="divide-y">

                    @foreach($activities as $activity)

                        <tr class="hover:bg-gray-50">

                            <td class="px-6 py-4 text-gray-600">
                                {{ $activity->id }}
                            </td>


                            <td class="px-6 py-4">

                                <div class="font-semibold text-gray-800">
                                    {{ $activity->file_name }}
                                </div>

                            </td>


                            <td class="px-6 py-4">

                                @if($activity->file_type === 'ZIP')

                                    <span class="px-3 py-1
                                                 rounded-full
                                                 text-xs
                                                 font-semibold
                                                 bg-purple-100
                                                 text-purple-700">

                                        ZIP

                                    </span>

                                @else

                                    <span class="px-3 py-1
                                                 rounded-full
                                                 text-xs
                                                 font-semibold
                                                 bg-blue-100
                                                 text-blue-700">

                                        TXT

                                    </span>

                                @endif

                            </td>


                            <td class="px-6 py-4 text-gray-600">

                                @if($activity->operation === 'Create')

                                    📄 Create

                                @else

                                    ⬇️ Download

                                @endif

                            </td>


                            <td class="px-6 py-4 text-gray-600">

                                {{ number_format($activity->file_size / 1024, 2) }}
                                KB

                            </td>


                            <td class="px-6 py-4">

                                @if($activity->status === 'Success')

                                    <span class="px-3 py-1
                                                 rounded-full
                                                 text-xs
                                                 font-semibold
                                                 bg-green-100
                                                 text-green-700">

                                        ✓ Success

                                    </span>

                                @else

                                    <span class="px-3 py-1
                                                 rounded-full
                                                 text-xs
                                                 font-semibold
                                                 bg-red-100
                                                 text-red-700">

                                        ✕ Failed

                                    </span>

                                @endif

                            </td>


                            <td class="px-6 py-4 text-gray-500">

                                {{ $activity->created_at->format('d M Y, h:i A') }}

                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>

            </div>


            {{-- PAGINATION --}}

            <div class="p-6 border-t">

                {{ $activities->links() }}

            </div>

        @else

            <div class="p-12 text-center">

                <div class="text-5xl mb-4">
                    📂
                </div>

                <h3 class="text-xl font-semibold text-gray-700">
                    No temporary activity found
                </h3>

                <p class="text-gray-500 mt-2">

                    Create or download a temporary file
                    to generate activity records.

                </p>

            </div>

        @endif

    </div>


    {{-- =========================================================
         FOOTER
    ========================================================== --}}

    <div class="text-center text-sm text-gray-400 mt-8">

        Laravel 12 Practice Project
        • Spatie Temporary Directory

    </div>

</div>


{{-- =========================================================
     AUTO HIDE ALERTS
========================================================== --}}

<script>

setTimeout(() => {

    const alerts = [
        document.getElementById('successAlert'),
        document.getElementById('errorAlert')
    ];

    alerts.forEach(alert => {

        if (alert) {

            alert.style.transition = "0.5s";
            alert.style.opacity = "0";

            setTimeout(() => {
                alert.remove();
            }, 500);

        }

    });

}, 4000);

</script>


{{-- =========================================================
     DOWNLOAD + REFRESH
========================================================== --}}

<script>

function downloadAndRefresh(url)
{
    const iframe = document.createElement('iframe');

    iframe.style.display = 'none';

    iframe.src = url;

    document.body.appendChild(iframe);

    setTimeout(() => {

        window.location.reload();

    }, 1500);
}

</script>


{{-- =========================================================
     CLEANUP CONFIRMATION
========================================================== --}}

<script>

function confirmCleanup()
{
    return confirm(
        "Are you sure you want to remove expired temporary data?"
    );
}

</script>

</body>

</html>