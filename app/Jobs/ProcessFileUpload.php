<?php

namespace App\Jobs;

use App\Models\FileUpload;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use League\Csv\Statement;
use Throwable;

class ProcessFileUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 25;
 
    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 3; 
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 600;
    /**
     * Create a new job instance.
     */
    public function __construct(public FileUpload $fileUpload)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->fileUpload->update(['status' => 'processing']);
        $this->processCsv();
        $this->fileUpload->update(['status' => 'completed']);
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception): void
    {
        $this->fileUpload->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage()
        ]);
        report($exception);
    }

    protected function processCsv()
    {
        $path = Storage::path('uploads/' . $this->fileUpload->filename);
        
        // Use League CSV to read and process the CSV
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);
        
        $stmt = Statement::create();
        $records = $stmt->process($csv);
        
        foreach ($records as $record) {
            // Clean non-UTF8 characters
            $record = array_map(function($value) {
                return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            }, $record);
            
            // Convert CSV field names to lowercase for consistency
            $formattedRecord = [
                'unique_key' => $record['UNIQUE_KEY'] ?? '',
                'product_title' => $record['PRODUCT_TITLE'] ?? '',
                'product_description' => $record['PRODUCT_DESCRIPTION'] ?? '',
                'style_number' => $record['STYLE#'] ?? '',
                'sanmar_mainframe_color' => $record['SANMAR_MAINFRAME_COLOR'] ?? '',
                'size' => $record['SIZE'] ?? '',
                'color_name' => $record['COLOR_NAME'] ?? '',
                'piece_price' => $record['PIECE_PRICE'] ?? 0.00,
            ];
            
            // Ensure unique_key is present
            if (empty($formattedRecord['unique_key'])) {
                continue; // Skip records without a unique key
            }
            
            // Upsert: Update if exists, insert if new
            Product::updateOrCreate(
                ['unique_key' => $formattedRecord['unique_key']], 
                $formattedRecord
            );
        }
    }
}
