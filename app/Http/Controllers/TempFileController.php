<?php

namespace App\Http\Controllers;

use App\Models\TemporaryFileActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use ZipArchive;

class TempFileController extends Controller
{
    /**
     * Temporary Directory Dashboard
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type');
        $operation = $request->input('operation');
        $status = $request->input('status');

        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $quickFilter = $request->input('quick_filter');

        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');

        $perPage = (int) $request->input('per_page', 5);

        /*
        |--------------------------------------------------------------------------
        | Allowed values
        |--------------------------------------------------------------------------
        */

        $allowedSorts = [
            'id',
            'file_name',
            'file_size',
            'file_type',
            'operation',
            'status',
            'created_at',
        ];

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }

        $allowedPerPage = [5, 8, 15, 25, 50];

        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 5;
        }

        /*
        |--------------------------------------------------------------------------
        | Activity Query
        |--------------------------------------------------------------------------
        */

        $activitiesQuery = TemporaryFileActivity::query();

        /*
        |--------------------------------------------------------------------------
        | File Search
        |--------------------------------------------------------------------------
        */

        if ($search) {
            $activitiesQuery->where(
                'file_name',
                'like',
                '%' . $search . '%'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | File Type
        |--------------------------------------------------------------------------
        */

        if ($type) {
            $activitiesQuery->where(
                'file_type',
                $type
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Operation
        |--------------------------------------------------------------------------
        */

        if ($operation) {
            $activitiesQuery->where(
                'operation',
                $operation
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        if ($status) {
            $activitiesQuery->where(
                'status',
                $status
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Date From
        |--------------------------------------------------------------------------
        */

        if ($dateFrom) {
            $activitiesQuery->whereDate(
                'created_at',
                '>=',
                $dateFrom
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Date To
        |--------------------------------------------------------------------------
        */

        if ($dateTo) {
            $activitiesQuery->whereDate(
                'created_at',
                '<=',
                $dateTo
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Quick Filters
        |--------------------------------------------------------------------------
        */

        if ($quickFilter === 'today') {
            $activitiesQuery->whereDate(
                'created_at',
                today()
            );
        }

        if ($quickFilter === '7days') {
            $activitiesQuery->where(
                'created_at',
                '>=',
                now()->subDays(7)
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $activitiesQuery->orderBy(
            $sort,
            $direction
        );

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $activities = $activitiesQuery
            ->paginate($perPage)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Filtered Result Count
        |--------------------------------------------------------------------------
        */

        $filteredActivities = $activities->total();

        /*
        |--------------------------------------------------------------------------
        | Overall Statistics
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Today's Statistics
        |--------------------------------------------------------------------------
        */

        $todayActivities = TemporaryFileActivity::whereDate(
            'created_at',
            today()
        )->count();

        $todayDownloads = TemporaryFileActivity::whereDate(
            'created_at',
            today()
        )
            ->where(
                'operation',
                'Download'
            )
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Storage Monitoring
        |--------------------------------------------------------------------------
        */

        $storedFiles = Storage::files();

        $storedFileCount = count($storedFiles);

        $storedStorageBytes = 0;

        foreach ($storedFiles as $storedFile) {
            try {
                $storedStorageBytes += Storage::size(
                    $storedFile
                );
            } catch (\Throwable $e) {
                // Ignore unreadable files.
            }
        }

        return view(
            'temp.index',
            compact(
                'activities',
                'search',
                'type',
                'operation',
                'status',
                'dateFrom',
                'dateTo',
                'quickFilter',
                'sort',
                'direction',
                'perPage',
                'filteredActivities',
                'totalActivities',
                'totalFiles',
                'totalZipFiles',
                'totalDownloads',
                'successfulOperations',
                'failedOperations',
                'totalStorageBytes',
                'storedFileCount',
                'storedStorageBytes',
                'todayActivities',
                'todayDownloads'
            )
        );
    }

    /**
     * Create temporary file.
     */
    public function createTemp()
    {
        $temp = TemporaryDirectory::make();

        try {
            $file = $temp->path('demo.txt');

            $content = 'Temp file created at ' . now();

            file_put_contents(
                $file,
                $content
            );

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
                    '📄 Temporary file created successfully!'
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

    /**
     * Download temporary TXT file.
     */
    public function downloadTempFile()
    {
        $temp = TemporaryDirectory::make();

        $file = $temp->path('export.txt');

        try {
            $content = 'Export generated at ' . now();

            file_put_contents(
                $file,
                $content
            );

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

    /**
     * Create ZIP archive.
     */
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
     * Export activity as CSV.
     */
    public function exportCsv(Request $request)
    {
        $activities = $this->filteredQuery(
            $request
        )
            ->latest()
            ->get();

        $fileName =
            'temporary-file-activity-' .
            now()->format('Y-m-d-H-i-s') .
            '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' =>
                'attachment; filename="' .
                $fileName .
                '"',
            'Pragma' => 'no-cache',
            'Cache-Control' =>
                'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->streamDownload(
            function () use ($activities) {

                $handle = fopen(
                    'php://output',
                    'w'
                );

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

    /**
     * Export activity as JSON.
     */
    public function exportJson(Request $request)
    {
        $activities = $this->filteredQuery(
            $request
        )
            ->latest()
            ->get();

        $fileName =
            'temporary-file-activity-' .
            now()->format('Y-m-d-H-i-s') .
            '.json';

        $data = $activities->map(
            function ($activity) {
                return [
                    'id' => $activity->id,
                    'file_name' => $activity->file_name,
                    'file_type' => $activity->file_type,
                    'operation' => $activity->operation,
                    'file_size_bytes' => $activity->file_size,
                    'file_size_kb' =>
                        round(
                            $activity->file_size / 1024,
                            2
                        ),
                    'status' => $activity->status,
                    'created_at' =>
                        $activity->created_at
                            ->format('Y-m-d H:i:s'),
                ];
            }
        );

        return response()->streamDownload(
            function () use ($data) {

                echo json_encode(
                    $data,
                    JSON_PRETTY_PRINT
                );
            },
            $fileName,
            [
                'Content-Type' =>
                    'application/json',
            ]
        );
    }

    /**
     * Delete one activity record.
     */
    public function delete($id)
    {
        $activity = TemporaryFileActivity::findOrFail(
            $id
        );

        $activity->delete();

        return redirect()
            ->route('temp.index')
            ->with(
                'success',
                '🗑️ Activity record deleted successfully.'
            );
    }

    /**
     * Delete all filtered activity records.
     */
    public function deleteFiltered(Request $request)
    {
        $query = $this->filteredQuery(
            $request
        );

        $count = $query->count();

        $query->delete();

        return redirect()
            ->route('temp.index')
            ->with(
                'success',
                "🗑️ {$count} filtered activity record(s) deleted."
            );
    }

    /**
     * Delete all failed operations.
     */
    public function deleteFailed()
    {
        $count = TemporaryFileActivity::where(
            'status',
            'Failed'
        )->count();

        TemporaryFileActivity::where(
            'status',
            'Failed'
        )->delete();

        return redirect()
            ->route('temp.index')
            ->with(
                'success',
                "🧹 {$count} failed activity record(s) deleted."
            );
    }

    /**
     * Cleanup old activity and storage.
     */
    public function cleanup(Request $request)
    {
        $request->validate([
            'days' => [
                'required',
                'integer',
                'min:1',
                'max:365',
            ],
        ]);

        $days = (int) $request->days;

        $cutoff = now()->subDays($days);

        $oldActivities =
            TemporaryFileActivity::where(
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
                        $deletedBytes += Storage::size(
                            $path
                        );
                    } catch (\Throwable $e) {
                        // Ignore.
                    }

                    Storage::delete($path);

                    $deletedFiles++;
                }
            }
        }

        $deletedActivities =
            TemporaryFileActivity::where(
                'created_at',
                '<',
                $cutoff
            )->delete();

        return redirect()
            ->route('temp.index')
            ->with(
                'success',
                "🧹 Cleanup completed! " .
                "{$deletedFiles} stored file(s), " .
                "{$deletedActivities} activity record(s), " .
                $this->formatBytes($deletedBytes) .
                " of storage were removed."
            );
    }

    /**
     * Reusable filtered query.
     */
    private function filteredQuery(
        Request $request
    ) {
        $query = TemporaryFileActivity::query();

        $search = $request->input('search');
        $type = $request->input('type');
        $operation = $request->input('operation');
        $status = $request->input('status');

        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $quickFilter =
            $request->input('quick_filter');

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

        if ($dateFrom) {
            $query->whereDate(
                'created_at',
                '>=',
                $dateFrom
            );
        }

        if ($dateTo) {
            $query->whereDate(
                'created_at',
                '<=',
                $dateTo
            );
        }

        if ($quickFilter === 'today') {
            $query->whereDate(
                'created_at',
                today()
            );
        }

        if ($quickFilter === '7days') {
            $query->where(
                'created_at',
                '>=',
                now()->subDays(7)
            );
        }

        return $query;
    }

    /**
     * Format bytes.
     */
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