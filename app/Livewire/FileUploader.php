<?php

namespace App\Livewire;

use App\Jobs\ProcessFileUpload;
use App\Models\FileUpload;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

#[Layout('layout.app')]
class FileUploader extends Component
{
    use WithFileUploads;

    public $file;
    public $uploads = [];
    public $products = [];
    public $p_count = 0;
    public $isUploading = false;
    public $uploadErrorMessage = '';
    
    protected $rules = [
        'file' => 'required|file|mimes:csv,txt|max:102400', // 100MB max
    ];

    public function mount()
    {
        $this->loadUploads();
    }
    
    public function render()
    {
        return view('livewire.file-uploader');
    }
    
    public function updatedFile()
    {
        $this->validate();
        $this->uploadFile();
    }
    
    public function uploadFile()
    {
        $this->isUploading = true;
        $this->uploadErrorMessage = '';
        
        try {
            // Validate file
            $this->validate();
            
            // Get and store the uploaded file
            $originalFilename = $this->file->getClientOriginalName();
            $filename = Str::random(40) . '.csv';
            
            // Store the file
            $this->file->storeAs('uploads', $filename);
            
            // Create upload record
            $fileUpload = FileUpload::create([
                'filename' => $filename,
                'original_filename' => $originalFilename,
                'status' => 'pending',
            ]);
            
            // Dispatch job to process the file
            ProcessFileUpload::dispatch($fileUpload);
            
            // Clear the file input
            $this->file = null;
            
            // Refresh the uploads list
            $this->loadUploads();
            
        } catch (\Exception $e) {
            $this->uploadErrorMessage = $e->getMessage();
        }
        
        $this->isUploading = false;
    }
    
    public function loadUploads()
    {
        $this->uploads = FileUpload::latest()->get();
        $this->products = Product::latest()->get();
    }

    public function deleteAll(){
        Product::truncate();
        FileUpload::truncate();
    }

    public function pCount(){
        $this->p_count = Product::count();
    }
    
    public function getStatusBadgeClass($status)
    {
       
        return [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'processing' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
        ][$status] ?? 'bg-gray-100 text-gray-800';
    }
    
    public function getHumanReadableTime($datetime)
    {
        return \Carbon\Carbon::parse($datetime)->diffForHumans();
    }
    
    public function getFormattedTime($datetime)
    {
        return \Carbon\Carbon::parse($datetime)->format('m-d-y g:ia');
    }
}
