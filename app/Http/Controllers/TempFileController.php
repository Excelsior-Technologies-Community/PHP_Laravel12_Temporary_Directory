<?php

namespace App\Http\Controllers;

use App\Models\TemporaryFileActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use ZipArchive;

class TempFileController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type');
        $operation = $request->input('operation');
        $status = $request->input('status');

        $activitiesQuery = TemporaryFileActivity::query();

        if ($search) {
            $activitiesQuery->where(
                'file_name',
                'like',
                '%' . $search . '%'
            );
        }

        if ($type) {
            $activitiesQuery->where('file_type', $type);
        }

        if ($operation) {
            $activitiesQuery->where('operation', $operation);
        }

        if ($status) {
            $activitiesQuery->where('status', $status);
        }

        $activities = $activitiesQuery
            ->latest()
            ->paginate(8)
            ->withQueryString();

        $totalActivities = TemporaryFileActivity::count();

        $totalFiles = TemporaryFileActivity::where(
            'file_type',
            'TXT'
        )->count();

        $totalZipFiles = TemporaryFileActivity::where(
            'file_type',
            'ZIP'
        )->count();

        $totalDownloads = TemporaryFileActivity::where(
            'operation',
            'Download'
        )->count();

        $successfulOperations = TemporaryFileActivity::where(
            'status',
            'Success'
        )->count();

        $failedOperations = TemporaryFileActivity::where(
            'status',
            'Failed'
        )->count();

        $totalStorageBytes = TemporaryFileActivity::sum(
            'file_size'
        );

        $storedFiles = Storage::files();

        $storedFileCount = count($storedFiles);

        $storedStorageBytes = 0;

        foreach ($storedFiles as $storedFile) {
            try {
                $storedStorageBytes += Storage::size($storedFile);
            } catch (\Throwable $e) {
                // Ignore files whose size cannot be read.
            }
        }

        return view('temp.index', compact(
            'activities',
            'search',
            'type',
            'operation',
            'status',
            'totalActivities',
            'totalFiles',
            'totalZipFiles',
            'totalDownloads',
            'successfulOperations',
            'failedOperations',
            'totalStorageBytes',
            'storedFileCount',
            'storedStorageBytes'
        ));
    }

    public function createTemp()
    {
        $temp = TemporaryDirectory::make();

        try {
            $file = $temp->path('demo.txt');

            $content = 'Temp file created at ' . now();

            file_put_contents($file, $content);

            Storage::put(
                'demo.txt',
                file_get_contents($file)
            );

            $fileSize = filesize($file);

            TemporaryFileActivity::create([
                'file_name' => 'demo.txt',
                'file_type' => 'TXT',
                'operation' => 'Create',
                'file_size' => $fileSize ?: 0,
                'status' => 'Success',
            ]);

            return redirect()
                ->route('temp.index')
                ->with(
                    'success',
                    '📄 Temporary file created successfully! '
                    . 'A copy was saved to storage/app/demo.txt.'
                );

        } catch (\Throwable $e) {

            TemporaryFileActivity::create([
                'file_name' => 'demo.txt',
                'file_type' => 'TXT',
                'operation' => 'Create',
                'file_size' => 0,
                'status' => 'Failed',
            ]);

            return redirect()
                ->route('temp.index')
                ->with(
                    'error',
                    'Unable to create temporary file.'
                );

        } finally {
            $temp->delete();
        }
    }

    public function downloadTempFile()
    {
        $temp = TemporaryDirectory::make();

        $file = $temp->path('export.txt');

        try {
            $content = 'Export generated at ' . now();

            file_put_contents($file, $content);

            $fileSize = filesize($file);

            TemporaryFileActivity::create([
                'file_name' => 'export.txt',
                'file_type' => 'TXT',
                'operation' => 'Download',
                'file_size' => $fileSize ?: 0,
                'status' => 'Success',
            ]);

            app()->terminating(
                fn () => $temp->delete()
            );

            session()->flash(
                'success',
                '⬇️ Temporary file downloaded successfully!'
            );

            return response()
                ->download(
                    $file,
                    'export.txt'
                )
                ->deleteFileAfterSend(true);

        } catch (\Throwable $e) {

            $temp->delete();

            TemporaryFileActivity::create([
                'file_name' => 'export.txt',
                'file_type' => 'TXT',
                'operation' => 'Download',
                'file_size' => 0,
                'status' => 'Failed',
            ]);

            return redirect()
                ->route('temp.index')
                ->with(
                    'error',
                    'Unable to generate the temporary download.'
                );
        }
    }

    public function createZip()
    {
        $temp = TemporaryDirectory::make();

        try {
            $file1 = $temp->path('file1.txt');
            $file2 = $temp->path('file2.txt');
            $zipPath = $temp->path('files.zip');

            file_put_contents(
                $file1,
                'File One'
            );

            file_put_contents(
                $file2,
                'File Two'
            );

            $zip = new ZipArchive();

            if (
                $zip->open(
                    $zipPath,
                    ZipArchive::CREATE
                ) !== true
            ) {
                throw new \RuntimeException(
                    'Unable to create ZIP archive.'
                );
            }

            $zip->addFile(
                $file1,
                'file1.txt'
            );

            $zip->addFile(
                $file2,
                'file2.txt'
            );

            $zip->close();

            $zipSize = filesize($zipPath);

            TemporaryFileActivity::create([
                'file_name' => 'files.zip',
                'file_type' => 'ZIP',
                'operation' => 'Download',
                'file_size' => $zipSize ?: 0,
                'status' => 'Success',
            ]);

            app()->terminating(
                fn () => $temp->delete()
            );

            session()->flash(
                'success',
                '🗜️ ZIP archive downloaded successfully!'
            );

            return response()->download(
                $zipPath,
                'files.zip'
            );

        } catch (\Throwable $e) {

            $temp->delete();

            TemporaryFileActivity::create([
                'file_name' => 'files.zip',
                'file_type' => 'ZIP',
                'operation' => 'Download',
                'file_size' => 0,
                'status' => 'Failed',
            ]);

            return redirect()
                ->route('temp.index')
                ->with(
                    'error',
                    'Unable to create the ZIP archive.'
                );
        }
    }

    /**
     * Export temporary file activity as CSV.
     *
     * The current search and filters are preserved.
     */
    public function exportCsv(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type');
        $operation = $request->input('operation');
        $status = $request->input('status');

        $query = TemporaryFileActivity::query();

        if ($search) {
            $query->where(
                'file_name',
                'like',
                '%' . $search . '%'
            );
        }

        if ($type) {
            $query->where(
                'file_type',
                $type
            );
        }

        if ($operation) {
            $query->where(
                'operation',
                $operation
            );
        }

        if ($status) {
            $query->where(
                'status',
                $status
            );
        }

        $activities = $query
            ->latest()
            ->get();

        $fileName = 'temporary-file-activity-'
            . now()->format('Y-m-d-H-i-s')
            . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' =>
                'attachment; filename="' . $fileName . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->streamDownload(
            function () use ($activities) {

                $handle = fopen(
                    'php://output',
                    'w'
                );

                /*
                 * CSV header row.
                 */
                fputcsv($handle, [
                    'ID',
                    'File Name',
                    'File Type',
                    'Operation',
                    'File Size (Bytes)',
                    'File Size (KB)',
                    'Status',
                    'Created At',
                ]);

                /*
                 * CSV data rows.
                 */
                foreach ($activities as $activity) {

                    fputcsv($handle, [
                        $activity->id,
                        $activity->file_name,
                        $activity->file_type,
                        $activity->operation,
                        $activity->file_size,
                        number_format(
                            $activity->file_size / 1024,
                            2,
                            '.',
                            ''
                        ),
                        $activity->status,
                        $activity->created_at
                            ->format('Y-m-d H:i:s'),
                    ]);
                }

                fclose($handle);
            },
            $fileName,
            $headers
        );
    }

    public function cleanup(Request $request)
    {
        $request->validate([
            'days' => [
                'required',
                'integer',
                'min:1',
                'max:365'
            ],
        ]);

        $days = (int) $request->days;

        $cutoff = now()->subDays($days);

        $oldActivities = TemporaryFileActivity::where(
            'created_at',
            '<',
            $cutoff
        )->get();

        $deletedFiles = 0;
        $deletedBytes = 0;

        foreach ($oldActivities as $activity) {

            if ($activity->operation === 'Create') {

                $path = $activity->file_name;

                if (Storage::exists($path)) {

                    try {
                        $deletedBytes += Storage::size($path);
                    } catch (\Throwable $e) {
                        // Ignore size calculation errors.
                    }

                    Storage::delete($path);

                    $deletedFiles++;
                }
            }
        }

        $deletedActivities = TemporaryFileActivity::where(
            'created_at',
            '<',
            $cutoff
        )->delete();

        return redirect()
            ->route('temp.index')
            ->with(
                'success',
                "🧹 Cleanup completed! "
                . "{$deletedFiles} stored file(s), "
                . "{$deletedActivities} activity record(s), and "
                . $this->formatBytes($deletedBytes)
                . " of storage were removed."
            );
    }

    private function formatBytes(
        int|float $bytes
    ): string {
        if ($bytes <= 0) {
            return '0 Bytes';
        }

        $units = [
            'Bytes',
            'KB',
            'MB',
            'GB',
            'TB',
        ];

        $power = floor(
            log($bytes, 1024)
        );

        $power = min(
            $power,
            count($units) - 1
        );

        return number_format(
            $bytes / pow(1024, $power),
            2
        ) . ' ' . $units[$power];
    }
}