#  PHP_Laravel12_Temporary_Directory

<p align="center">
<a href="#"><img src="https://img.shields.io/badge/Laravel-12-red" alt="Laravel Version"></a>
<a href="#"><img src="https://img.shields.io/badge/PHP-8.2+-blue" alt="PHP Version"></a>
<a href="#"><img src="https://img.shields.io/badge/Spatie-TemporaryDirectory-orange" alt="Package"></a>
<a href="#"><img src="https://img.shields.io/badge/Status-Demo_Project-success" alt="Project Status"></a>
</p>

A Laravel 12 demo project showing how to use **Spatie Temporary Directory** for safely creating temporary files, downloading generated files, creating ZIP archives dynamically, and automatically cleaning temporary storage.

---

## Overview

This project demonstrates how to use **Spatie Temporary Directory** in a Laravel 12 application to:

* Create temporary files
* Download generated files
* Generate ZIP archives dynamically
* Automatically clean up temporary directories safely
* Display success messages in a clean UI

---

## Features

* Create a temporary file
* Download a generated file
* Create and download ZIP files
* Automatic temporary directory cleanup
* Flash success messages
* TailwindCSS UI
* Production-safe download handling

---

##  Folder Structure

```
app/
 └── Http/
     └── Controllers/
         └── TempFileController.php

resources/
 └── views/
     └── temp/
         └── index.blade.php

routes/
 └── web.php
```

---
## Requirements

* PHP 8.2+
* Composer
* Laravel 12

---

## Step 1 — Create Laravel Project

```bash
composer create-project laravel/laravel temp-demo
```

Start server:

```bash
php artisan serve
```

Open:

```
http://127.0.0.1:8000
```

---

## Step 2 — Install Spatie Temporary Directory

```bash
composer require spatie/temporary-directory
```

---

## Step 3 — Create Controller

```bash
php artisan make:controller TempFileController
```

### File Location

```
app/Http/Controllers/TempFileController.php
```

### Controller Code

```php
<?php

namespace App\Http\Controllers;

use Spatie\TemporaryDirectory\TemporaryDirectory;
use ZipArchive;
use Illuminate\Support\Facades\Storage;

class TempFileController extends Controller
{
    /* ================= UI PAGE ================= */
    public function index()
    {
        return view('temp.index');
    }

    /* ================= CREATE FILE ================= */
    public function createTemp()
    {
        $temp = TemporaryDirectory::make();

        $file = $temp->path('demo.txt');

        file_put_contents($file, 'Temp file created at '.now());

        Storage::put('demo.txt', file_get_contents($file));

        $temp->delete();

        return redirect()
            ->route('temp.index')
            ->with('success','Temp file created successfully! File saved at: storage/app/demo.txt');
    }

    /* ================= DOWNLOAD FILE ================= */
    public function downloadTempFile()
    {
        $temp = TemporaryDirectory::make();

        $file = $temp->path('export.txt');

        file_put_contents($file, "Export generated at ".now());

        app()->terminating(fn () => $temp->delete());

        session()->flash('success','✅ File downloaded successfully!');

        return response()
            ->download($file)
            ->deleteFileAfterSend(true);
    }

    /* ================= ZIP DOWNLOAD ================= */
    public function createZip()
    {
        $temp = TemporaryDirectory::make();

        file_put_contents($temp->path('file1.txt'), 'File One');
        file_put_contents($temp->path('file2.txt'), 'File Two');

        $zipPath = $temp->path('files.zip');

        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            $zip->addFile($temp->path('file1.txt'),'file1.txt');
            $zip->addFile($temp->path('file2.txt'),'file2.txt');
            $zip->close();
        }

        app()->terminating(fn () => $temp->delete());

        session()->flash('success','✅ ZIP downloaded successfully!');

        return response()->download($zipPath);
    }
}
```

---

## Step 4 — Routes

### routes/web.php

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TempFileController;

Route::get('/', fn () => redirect('/temp'));

Route::get('/temp', [TempFileController::class,'index'])->name('temp.index');

Route::get('/temp/create', [TempFileController::class,'createTemp'])
    ->name('temp.create');

Route::get('/temp/download', [TempFileController::class,'downloadTempFile'])
    ->name('temp.download');

Route::get('/temp/zip', [TempFileController::class,'createZip'])
    ->name('temp.zip');
```

---

## Step 5 — Blade View

Create file:

```
resources/views/temp/index.blade.php
```

### View Code

```html
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

@if(session('success'))
<div id="alertBox"
class="mb-6 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded-lg">
{{ session('success') }}
</div>
@endif

<div class="space-y-4">

<a href="{{ route('temp.create') }}"
class="block bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 rounded-lg">
📄 Create Temp File
</a>

<button onclick="downloadAndRefresh('{{ route('temp.download') }}')"
class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 rounded-lg">
⬇️ Download File
</button>

<button onclick="downloadAndRefresh('{{ route('temp.zip') }}')"
class="w-full bg-purple-500 hover:bg-purple-600 text-white font-semibold py-3 rounded-lg">
🗜️ Download ZIP
</button>

</div>

<div class="mt-8 text-sm text-gray-400">
Laravel Practice Project
</div>

</div>

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

<script>
function downloadAndRefresh(url)
{
let iframe = document.createElement('iframe');
iframe.style.display = 'none';
iframe.src = url;
document.body.appendChild(iframe);

setTimeout(() => {
window.location.reload();
}, 1200);
}
</script>

</body>
</html>
```

---

## Step 6 — Run Project

```bash
php artisan serve
```

Open:

```
http://127.0.0.1:8000/temp
```
<img width="525" height="400" alt="Screenshot 2026-02-24 123247" src="https://github.com/user-attachments/assets/eaaf623d-9d8c-41bf-b6eb-62c4f7475345" />

---
Create Temp File :

<img width="529" height="499" alt="Screenshot 2026-02-24 123320" src="https://github.com/user-attachments/assets/d6e0e3ad-e0c9-4a6f-9ae4-917c6e96ac6c" />

---
Download File :

<img width="1715" height="768" alt="Screenshot 2026-02-24 124315" src="https://github.com/user-attachments/assets/8288df18-6061-45bc-a37d-e7b2d07e6c0d" />

---
Download Zip : 

<img width="1634" height="743" alt="Screenshot 2026-02-24 124335" src="https://github.com/user-attachments/assets/19c2f7dc-5590-4921-905f-e7b223480b41" />

---

## Result

* Create Temp File → creates file and shows success message
* Download File → downloads generated file safely
* Download ZIP → creates ZIP dynamically and downloads
* Temporary directories cleaned automatically

---

