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

        // save copy so user can verify creation
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

        // delete temp after response
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