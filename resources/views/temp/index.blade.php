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

        <div class="flex flex-col md:flex-row
                    md:items-center
                    md:justify-between
                    gap-4">

            <div>

                <h1 class="text-4xl font-bold text-gray-800">
                    Temporary Directory Dashboard
                </h1>

                <p class="text-gray-500 mt-2">
                    Laravel 12 + Spatie Temporary Directory
                </p>

            </div>

            <div>

                <a href="{{ route('temp.index') }}"
                   class="inline-flex
                          items-center
                          bg-gray-800
                          hover:bg-gray-900
                          text-white
                          font-semibold
                          px-5
                          py-3
                          rounded-lg">

                    🔄 Refresh Dashboard

                </a>

            </div>

        </div>

    </div>


    {{-- =========================================================
         ALERTS
    ========================================================== --}}

    @if(session('success'))

        <div id="successAlert"
             class="mb-6 px-5 py-4
                    bg-green-100
                    border border-green-400
                    text-green-700
                    rounded-lg">

            {{ session('success') }}

        </div>

    @endif


    @if(session('error'))

        <div id="errorAlert"
             class="mb-6 px-5 py-4
                    bg-red-100
                    border border-red-400
                    text-red-700
                    rounded-lg">

            {{ session('error') }}

        </div>

    @endif


    {{-- =========================================================
         MAIN STATISTICS
    ========================================================== --}}

    <div class="grid grid-cols-1
                md:grid-cols-2
                lg:grid-cols-4
                gap-5
                mb-8">

        <div class="bg-white rounded-xl shadow p-6">

            <div class="text-gray-500 text-sm">
                Total Activities
            </div>

            <div class="text-3xl font-bold text-gray-800 mt-2">
                {{ $totalActivities }}
            </div>

            <div class="text-sm text-gray-400 mt-2">
                All operations
            </div>

        </div>


        <div class="bg-white rounded-xl shadow p-6">

            <div class="text-gray-500 text-sm">
                TXT Files
            </div>

            <div class="text-3xl font-bold text-blue-600 mt-2">
                {{ $totalFiles }}
            </div>

            <div class="text-sm text-gray-400 mt-2">
                Text file operations
            </div>

        </div>


        <div class="bg-white rounded-xl shadow p-6">

            <div class="text-gray-500 text-sm">
                ZIP Archives
            </div>

            <div class="text-3xl font-bold text-purple-600 mt-2">
                {{ $totalZipFiles }}
            </div>

            <div class="text-sm text-gray-400 mt-2">
                ZIP operations
            </div>

        </div>


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
         SECONDARY STATISTICS
    ========================================================== --}}

    <div class="grid grid-cols-1
                md:grid-cols-2
                lg:grid-cols-5
                gap-5
                mb-8">

        <div class="bg-white rounded-xl shadow p-5">

            <div class="text-sm text-gray-500">
                Successful
            </div>

            <div class="text-2xl font-bold text-green-600 mt-2">
                {{ $successfulOperations }}
            </div>

        </div>


        <div class="bg-white rounded-xl shadow p-5">

            <div class="text-sm text-gray-500">
                Failed
            </div>

            <div class="text-2xl font-bold text-red-600 mt-2">
                {{ $failedOperations }}
            </div>

        </div>


        <div class="bg-white rounded-xl shadow p-5">

            <div class="text-sm text-gray-500">
                Today's Activities
            </div>

            <div class="text-2xl font-bold text-indigo-600 mt-2">
                {{ $todayActivities }}
            </div>

        </div>


        <div class="bg-white rounded-xl shadow p-5">

            <div class="text-sm text-gray-500">
                Today's Downloads
            </div>

            <div class="text-2xl font-bold text-purple-600 mt-2">
                {{ $todayDownloads }}
            </div>

        </div>


        <div class="bg-white rounded-xl shadow p-5">

            <div class="text-sm text-gray-500">
                Tracked Storage
            </div>

            <div class="text-2xl font-bold text-orange-600 mt-2">
                {{ number_format($totalStorageBytes / 1024, 2) }} KB
            </div>

        </div>

    </div>


    {{-- =========================================================
         TEMPORARY DIRECTORY ACTIONS
    ========================================================== --}}

    <div class="bg-white rounded-xl shadow p-6 mb-8">

        <h2 class="text-2xl font-bold text-gray-800 mb-5">
            Temporary Directory Operations
        </h2>

        <div class="grid grid-cols-1
                    md:grid-cols-3
                    gap-4">

            <a href="{{ route('temp.create') }}"
               class="text-center
                      bg-blue-500
                      hover:bg-blue-600
                      text-white
                      font-semibold
                      py-3
                      px-4
                      rounded-lg
                      transition">

                📄 Create Temp File

            </a>


            <button
                onclick="downloadAndRefresh('{{ route('temp.download') }}')"
                class="bg-green-500
                       hover:bg-green-600
                       text-white
                       font-semibold
                       py-3
                       px-4
                       rounded-lg
                       transition">

                ⬇️ Download File

            </button>


            <button
                onclick="downloadAndRefresh('{{ route('temp.zip') }}')"
                class="bg-purple-500
                       hover:bg-purple-600
                       text-white
                       font-semibold
                       py-3
                       px-4
                       rounded-lg
                       transition">

                🗜️ Download ZIP

            </button>

        </div>

    </div>


    {{-- =========================================================
         STORAGE MONITOR
    ========================================================== --}}

    <div class="bg-white rounded-xl shadow p-6 mb-8">

        <div class="flex flex-col
                    md:flex-row
                    md:items-center
                    md:justify-between
                    gap-4">

            <div>

                <h2 class="text-2xl font-bold text-gray-800">
                    Temporary Storage Monitor
                </h2>

                <p class="text-gray-500 mt-1">
                    Persistent files created by the application.
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

            Remove old temporary-file copies and
            activity history.

        </p>

        <form action="{{ route('temp.cleanup') }}"
              method="POST"
              class="flex flex-col md:flex-row gap-4">

            @csrf

            <select name="days"
                    class="border border-gray-300
                           rounded-lg
                           px-4
                           py-3">

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
                           px-6
                           py-3
                           rounded-lg">

                🧹 Cleanup Expired Data

            </button>

        </form>

    </div>


    {{-- =========================================================
         SEARCH / FILTER
    ========================================================== --}}

    <div class="bg-white rounded-xl shadow p-6 mb-8">

        <div class="mb-6">

            <h2 class="text-2xl font-bold text-gray-800">
                🔎 Temporary File Activity
            </h2>

            <p class="text-gray-500 mt-1">
                Search, filter, sort and manage activity.
            </p>

        </div>


        <form method="GET"
              action="{{ route('temp.index') }}">

            <div class="grid grid-cols-1
                        md:grid-cols-2
                        lg:grid-cols-4
                        gap-4">

                {{-- SEARCH --}}

                <div>

                    <label class="block
                                  text-sm
                                  font-semibold
                                  text-gray-600
                                  mb-2">

                        File Name

                    </label>

                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Search filename..."
                        class="w-full
                               border
                               border-gray-300
                               rounded-lg
                               px-4
                               py-3">

                </div>


                {{-- TYPE --}}

                <div>

                    <label class="block
                                  text-sm
                                  font-semibold
                                  text-gray-600
                                  mb-2">

                        File Type

                    </label>

                    <select name="type"
                            class="w-full
                                   border
                                   border-gray-300
                                   rounded-lg
                                   px-4
                                   py-3">

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

                </div>


                {{-- OPERATION --}}

                <div>

                    <label class="block
                                  text-sm
                                  font-semibold
                                  text-gray-600
                                  mb-2">

                        Operation

                    </label>

                    <select name="operation"
                            class="w-full
                                   border
                                   border-gray-300
                                   rounded-lg
                                   px-4
                                   py-3">

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

                </div>


                {{-- STATUS --}}

                <div>

                    <label class="block
                                  text-sm
                                  font-semibold
                                  text-gray-600
                                  mb-2">

                        Status

                    </label>

                    <select name="status"
                            class="w-full
                                   border
                                   border-gray-300
                                   rounded-lg
                                   px-4
                                   py-3">

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

                </div>


                {{-- DATE FROM --}}

                <div>

                    <label class="block
                                  text-sm
                                  font-semibold
                                  text-gray-600
                                  mb-2">

                        Date From

                    </label>

                    <input
                        type="date"
                        name="date_from"
                        value="{{ $dateFrom }}"
                        class="w-full
                               border
                               border-gray-300
                               rounded-lg
                               px-4
                               py-3">

                </div>


                {{-- DATE TO --}}

                <div>

                    <label class="block
                                  text-sm
                                  font-semibold
                                  text-gray-600
                                  mb-2">

                        Date To

                    </label>

                    <input
                        type="date"
                        name="date_to"
                        value="{{ $dateTo }}"
                        class="w-full
                               border
                               border-gray-300
                               rounded-lg
                               px-4
                               py-3">

                </div>


                {{-- SORT --}}

                <div>

                    <label class="block
                                  text-sm
                                  font-semibold
                                  text-gray-600
                                  mb-2">

                        Sort By

                    </label>

                    <select name="sort"
                            class="w-full
                                   border
                                   border-gray-300
                                   rounded-lg
                                   px-4
                                   py-3">

                        <option value="created_at"
                            {{ $sort === 'created_at' ? 'selected' : '' }}>
                            Date
                        </option>

                        <option value="file_name"
                            {{ $sort === 'file_name' ? 'selected' : '' }}>
                            File Name
                        </option>

                        <option value="file_size"
                            {{ $sort === 'file_size' ? 'selected' : '' }}>
                            File Size
                        </option>

                        <option value="file_type"
                            {{ $sort === 'file_type' ? 'selected' : '' }}>
                            File Type
                        </option>

                        <option value="operation"
                            {{ $sort === 'operation' ? 'selected' : '' }}>
                            Operation
                        </option>

                        <option value="status"
                            {{ $sort === 'status' ? 'selected' : '' }}>
                            Status
                        </option>

                    </select>

                </div>


                {{-- DIRECTION --}}

                <div>

                    <label class="block
                                  text-sm
                                  font-semibold
                                  text-gray-600
                                  mb-2">

                        Direction

                    </label>

                    <select name="direction"
                            class="w-full
                                   border
                                   border-gray-300
                                   rounded-lg
                                   px-4
                                   py-3">

                        <option value="desc"
                            {{ $direction === 'desc' ? 'selected' : '' }}>
                            Descending
                        </option>

                        <option value="asc"
                            {{ $direction === 'asc' ? 'selected' : '' }}>
                            Ascending
                        </option>

                    </select>

                </div>

            </div>


            {{-- PER PAGE --}}

            <div class="mt-5 flex flex-col
                        md:flex-row
                        md:items-center
                        gap-3">

                <label class="font-semibold text-gray-600">

                    Records Per Page

                </label>

                <select name="per_page"
                        onchange="this.form.submit()"
                        class="border
                               border-gray-300
                               rounded-lg
                               px-4
                               py-2">

                    @foreach([5, 8, 15, 25, 50] as $number)

                        <option value="{{ $number }}"
                            {{ $perPage == $number ? 'selected' : '' }}>

                            {{ $number }}

                        </option>

                    @endforeach

                </select>

            </div>


            {{-- BUTTONS --}}

            <div class="mt-5 flex flex-wrap gap-3">

                <button type="submit"
                        class="bg-gray-800
                               hover:bg-gray-900
                               text-white
                               font-semibold
                               px-6
                               py-3
                               rounded-lg">

                    🔍 Apply Filters

                </button>


                <a href="{{ route('temp.index') }}"
                   class="bg-gray-100
                          hover:bg-gray-200
                          text-gray-700
                          font-semibold
                          px-6
                          py-3
                          rounded-lg">

                    ✕ Clear

                </a>


                <a href="{{ route('temp.export.csv', request()->query()) }}"
                   class="bg-emerald-600
                          hover:bg-emerald-700
                          text-white
                          font-semibold
                          px-6
                          py-3
                          rounded-lg">

                    📥 CSV

                </a>


                <a href="{{ route('temp.export.json', request()->query()) }}"
                   class="bg-indigo-600
                          hover:bg-indigo-700
                          text-white
                          font-semibold
                          px-6
                          py-3
                          rounded-lg">

                    📥 JSON

                </a>

            </div>

        </form>


        {{-- =====================================================
             QUICK FILTERS
        ====================================================== --}}

        <div class="mt-6">

            <div class="text-sm
                        font-semibold
                        text-gray-600
                        mb-3">

                Quick Filters

            </div>

            <div class="flex flex-wrap gap-3">

                <a href="{{ route('temp.index', [
                    'quick_filter' => 'today'
                ]) }}"
                   class="bg-blue-100
                          hover:bg-blue-200
                          text-blue-700
                          font-semibold
                          px-5
                          py-2
                          rounded-lg">

                    📅 Today

                </a>


                <a href="{{ route('temp.index', [
                    'quick_filter' => '7days'
                ]) }}"
                   class="bg-purple-100
                          hover:bg-purple-200
                          text-purple-700
                          font-semibold
                          px-5
                          py-2
                          rounded-lg">

                    📅 Last 7 Days

                </a>

            </div>

        </div>


        {{-- =====================================================
             FILTERED COUNT
        ====================================================== --}}

        <div class="mt-6
                    bg-blue-50
                    border
                    border-blue-200
                    rounded-lg
                    px-5
                    py-4">

            <span class="text-blue-700 font-semibold">

                📊 Filtered Results:

            </span>

            <span class="text-blue-900 font-bold">

                {{ $filteredActivities }}

            </span>

            <span class="text-blue-700">

                record(s)

            </span>

        </div>

    </div>


    {{-- =========================================================
         BULK MANAGEMENT
    ========================================================== --}}

    <div class="bg-white rounded-xl shadow p-6 mb-8">

        <div class="flex flex-col
                    md:flex-row
                    md:items-center
                    md:justify-between
                    gap-4">

            <div>

                <h2 class="text-xl font-bold text-gray-800">

                    🗑️ Activity Management

                </h2>

                <p class="text-gray-500 mt-1">

                    Delete filtered or failed activity records.

                </p>

            </div>


            <div class="flex flex-wrap gap-3">

                <form
                    action="{{ route('temp.activity.delete.filtered') }}"
                    method="POST"
                    onsubmit="return confirmDeleteFiltered()">

                    @csrf

                    @method('DELETE')

                    @foreach(request()->except('page') as $key => $value)

                        @if(is_array($value))

                            @foreach($value as $item)

                                <input type="hidden"
                                       name="{{ $key }}[]"
                                       value="{{ $item }}">

                            @endforeach

                        @else

                            <input type="hidden"
                                   name="{{ $key }}"
                                   value="{{ $value }}">

                        @endif

                    @endforeach

                    <button type="submit"
                            class="bg-orange-500
                                   hover:bg-orange-600
                                   text-white
                                   font-semibold
                                   px-5
                                   py-3
                                   rounded-lg">

                        🗑️ Delete Filtered

                    </button>

                </form>


                <form
                    action="{{ route('temp.activity.delete.failed') }}"
                    method="POST"
                    onsubmit="return confirmDeleteFailed()">

                    @csrf

                    @method('DELETE')

                    <button type="submit"
                            class="bg-red-500
                                   hover:bg-red-600
                                   text-white
                                   font-semibold
                                   px-5
                                   py-3
                                   rounded-lg">

                        🧹 Delete Failed

                    </button>

                </form>

            </div>

        </div>

    </div>


    {{-- =========================================================
         ACTIVITY TABLE
    ========================================================== --}}

    <div class="bg-white rounded-xl shadow overflow-hidden">

        <div class="p-6 border-b">

            <div class="flex flex-col
                        md:flex-row
                        md:items-center
                        md:justify-between
                        gap-3">

                <h2 class="text-2xl font-bold text-gray-800">

                    Recent Temporary File Activity

                </h2>

                <div class="text-sm text-gray-500">

                    Showing
                    <strong>
                        {{ $activities->firstItem() ?? 0 }}
                    </strong>
                    -
                    <strong>
                        {{ $activities->lastItem() ?? 0 }}
                    </strong>
                    of
                    <strong>
                        {{ $activities->total() }}
                    </strong>

                </div>

            </div>

        </div>


        @if($activities->count())

            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead class="bg-gray-50">

                    <tr>

                        <th class="px-6 py-4
                                   text-left
                                   text-sm
                                   font-semibold
                                   text-gray-600">

                            #

                        </th>


                        <th class="px-6 py-4
                                   text-left
                                   text-sm
                                   font-semibold
                                   text-gray-600">

                            File Name

                        </th>


                        <th class="px-6 py-4
                                   text-left
                                   text-sm
                                   font-semibold
                                   text-gray-600">

                            Type

                        </th>


                        <th class="px-6 py-4
                                   text-left
                                   text-sm
                                   font-semibold
                                   text-gray-600">

                            Operation

                        </th>


                        <th class="px-6 py-4
                                   text-left
                                   text-sm
                                   font-semibold
                                   text-gray-600">

                            Size

                        </th>


                        <th class="px-6 py-4
                                   text-left
                                   text-sm
                                   font-semibold
                                   text-gray-600">

                            Status

                        </th>


                        <th class="px-6 py-4
                                   text-left
                                   text-sm
                                   font-semibold
                                   text-gray-600">

                            Date

                        </th>


                        <th class="px-6 py-4
                                   text-left
                                   text-sm
                                   font-semibold
                                   text-gray-600">

                            Action

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

                                <div class="font-semibold
                                            text-gray-800">

                                    {{ $activity->file_name }}

                                </div>

                            </td>


                            <td class="px-6 py-4">

                                @if($activity->file_type === 'ZIP')

                                    <span
                                        class="px-3
                                               py-1
                                               rounded-full
                                               text-xs
                                               font-semibold
                                               bg-purple-100
                                               text-purple-700">

                                        ZIP

                                    </span>

                                @else

                                    <span
                                        class="px-3
                                               py-1
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

                                {{ number_format(
                                    $activity->file_size / 1024,
                                    2
                                ) }}

                                KB

                            </td>


                            <td class="px-6 py-4">

                                @if($activity->status === 'Success')

                                    <span
                                        class="px-3
                                               py-1
                                               rounded-full
                                               text-xs
                                               font-semibold
                                               bg-green-100
                                               text-green-700">

                                        ✓ Success

                                    </span>

                                @else

                                    <span
                                        class="px-3
                                               py-1
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

                                {{ $activity->created_at
                                    ->format('d M Y, h:i A') }}

                            </td>


                            <td class="px-6 py-4">

                                <form
                                    action="{{ route(
                                        'temp.activity.delete',
                                        $activity->id
                                    ) }}"
                                    method="POST"
                                    onsubmit="return confirmDelete()">

                                    @csrf

                                    @method('DELETE')

                                    <button type="submit"
                                            class="bg-red-100
                                                   hover:bg-red-200
                                                   text-red-700
                                                   font-semibold
                                                   px-3
                                                   py-2
                                                   rounded-lg">

                                        🗑️ Delete

                                    </button>

                                </form>

                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>

            </div>


            {{-- =================================================
                 NUMBER ONLY PAGINATION
            ================================================== --}}

            <div class="p-6 border-t">

                <div class="flex flex-wrap
                            justify-center
                            gap-2">

                    @if($activities->onFirstPage())

                        <span
                            class="px-4
                                   py-2
                                   rounded-lg
                                   bg-gray-100
                                   text-gray-400">

                            ‹

                        </span>

                    @else

                        <a href="{{ $activities->previousPageUrl() }}"
                           class="px-4
                                  py-2
                                  rounded-lg
                                  bg-gray-800
                                  text-white">

                            ‹

                        </a>

                    @endif


                    @foreach($activities->getUrlRange(
                        max(1, $activities->currentPage() - 2),
                        min(
                            $activities->lastPage(),
                            $activities->currentPage() + 2
                        )
                    ) as $page => $url)

                        @if($page == $activities->currentPage())

                            <span
                                class="px-4
                                       py-2
                                       rounded-lg
                                       bg-blue-600
                                       text-white
                                       font-bold">

                                {{ $page }}

                            </span>

                        @else

                            <a href="{{ $url }}"
                               class="px-4
                                      py-2
                                      rounded-lg
                                      bg-gray-100
                                      hover:bg-gray-200
                                      text-gray-700">

                                {{ $page }}

                            </a>

                        @endif

                    @endforeach


                    @if($activities->hasMorePages())

                        <a href="{{ $activities->nextPageUrl() }}"
                           class="px-4
                                  py-2
                                  rounded-lg
                                  bg-gray-800
                                  text-white">

                            ›

                        </a>

                    @else

                        <span
                            class="px-4
                                   py-2
                                   rounded-lg
                                   bg-gray-100
                                   text-gray-400">

                            ›

                        </span>

                    @endif

                </div>

            </div>

        @else

            <div class="p-12 text-center">

                <div class="text-5xl mb-4">
                    📂
                </div>

                <h3 class="text-xl
                           font-semibold
                           text-gray-700">

                    No temporary activity found

                </h3>

                <p class="text-gray-500 mt-2">

                    Try changing your filters or create
                    a temporary file.

                </p>

            </div>

        @endif

    </div>


    {{-- =========================================================
         FOOTER
    ========================================================== --}}

    <div class="text-center
                text-sm
                text-gray-400
                mt-8">

        Laravel 12 Practice Project
        • Spatie Temporary Directory

    </div>

</div>


{{-- =============================================================
     JAVASCRIPT
============================================================= --}}

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


function confirmCleanup()
{
    return confirm(
        "Are you sure you want to remove expired temporary data?"
    );
}


function confirmDelete()
{
    return confirm(
        "Are you sure you want to delete this activity record?"
    );
}


function confirmDeleteFiltered()
{
    return confirm(
        "Are you sure you want to delete all currently filtered activity records?"
    );
}


function confirmDeleteFailed()
{
    return confirm(
        "Are you sure you want to delete all failed activity records?"
    );
}

</script>

</body>

</html>