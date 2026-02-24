<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Temporary Directory Demo</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<div class="bg-white shadow-2xl rounded-xl p-10 w-[520px] text-center">

    <h1 class="text-3xl font-bold text-gray-800 mb-2">
         Temporary Directory Demo
    </h1>

    <p class="text-gray-500 mb-6">
        Laravel 12 + Spatie Temporary Directory
    </p>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div id="alertBox"
             class="mb-6 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-4">

        {{-- CREATE --}}
        <a href="{{ route('temp.create') }}"
           class="block bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 rounded-lg">
            📄 Create Temp File
        </a>

        {{-- DOWNLOAD FILE --}}
        <button onclick="downloadAndRefresh('{{ route('temp.download') }}')"
            class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 rounded-lg">
            ⬇️ Download File
        </button>

        {{-- ZIP DOWNLOAD --}}
        <button onclick="downloadAndRefresh('{{ route('temp.zip') }}')"
            class="w-full bg-purple-500 hover:bg-purple-600 text-white font-semibold py-3 rounded-lg">
            🗜️ Download ZIP
        </button>

    </div>

    <div class="mt-8 text-sm text-gray-400">
        Laravel Practice Project
    </div>

</div>

{{-- AUTO HIDE MESSAGE --}}
<script>
setTimeout(() => {
    let alertBox = document.getElementById('alertBox');
    if(alertBox){
        alertBox.style.transition = "0.5s";
        alertBox.style.opacity = "0";
        setTimeout(()=>alertBox.remove(),500);
    }
}, 3000);
</script>

{{-- DOWNLOAD + AUTO REFRESH SCRIPT --}}
<script>
function downloadAndRefresh(url)
{
    // hidden iframe download
    let iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = url;
    document.body.appendChild(iframe);

    // reload page to show flash message
    setTimeout(() => {
        window.location.reload();
    }, 1200);
}
</script>

</body>
</html>