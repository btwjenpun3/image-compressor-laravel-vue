<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class DeleteOriginalImageJob implements ShouldQueue
{
    use Queueable;

    public $imagePath;

    /**
     * Create a new job instance.
     */
    public function __construct($imagePath)
    {
        $this->imagePath = $imagePath;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (Storage::disk('public')->exists($this->imagePath)) {
            Storage::disk('public')->delete($this->imagePath);
        }
    }
}
