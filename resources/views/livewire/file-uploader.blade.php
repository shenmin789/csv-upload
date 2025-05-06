<div class="py-6 sm:py-12 flex flex-col space-y-6 justify-center">
    <div class="max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">CSV File Upload</h1>
        </div>
    </div>

    <div class="max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <!-- File Upload Form -->
                 
                <div 
                    class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:bg-gray-50 transition-colors duration-150 ease-in-out"
                    x-data="{ dragover: false }"
                    x-on:dragover.prevent="dragover = true"
                    x-on:dragleave.prevent="dragover = false"
                    x-on:drop.prevent="dragover = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                    x-bind:class="{ 'border-blue-400 bg-blue-50': dragover }"
                    onclick="document.getElementById('file-upload').click()"
                >
                    <div class="space-y-2">
                        <div class="flex text-sm text-gray-600">
                            <label class="relative  bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                <span>Select file/Drag and drop</span>
                                <input id="file-upload" name="file-upload" type="file" class="sr-only" wire:model="file" x-ref="fileInput">
                            </label>
                        </div>
                    </div>
                    
                    <!-- Upload Button -->
                    <div class="mt-4 flex justify-center">
                        <button 
                            type="button" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Upload File
                        </button>
                    </div>
                </div>
                
                <!-- Upload Progress -->
                @if($isUploading)
                <div class="mt-4">
                    <div class="relative pt-1">
                        <div class="flex mb-2 items-center justify-between">
                            <div>
                                <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-blue-600 bg-blue-200">
                                    Uploading...
                                </span>
                            </div>
                        </div>
                        <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-blue-200">
                            <div style="width:100%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500 animate-pulse"></div>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Error Message -->
                @if($uploadErrorMessage)
                <div class="mt-4 rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                {{ $uploadErrorMessage }}
                            </h3>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- poll upload history-->
    <div wire:poll class="max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Upload History</h2>
                
                <div class="flex flex-col mt-2">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Time
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                File Name
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse ($uploads as $upload)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <div>{{ $this->getFormattedTime($upload->created_at) }}</div>
                                                    <div class="text-xs text-gray-400">({{ $this->getHumanReadableTime($upload->created_at) }})</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $upload->original_filename }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $this->getStatusBadgeClass($upload->status) }}">
                                                        {{ $upload->status }}
                                                    </span>
                                                    @if ($upload->error_message)
                                                        <div class="text-xs text-red-600 mt-1">{{ $upload->error_message }}</div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                    No uploads found
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for notifications -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        window.addEventListener('notify', event => {
            const type = event.detail.type;
            const message = event.detail.message;
            
            // You could use a notification library here
            // For now, just use alert
            alert(`${type}: ${message}`);
        });
    });
</script>