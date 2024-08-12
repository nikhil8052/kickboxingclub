<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchApiResults implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

     protected $page;
     protected $url;
    public function __construct($url, $page = 1)
    {
        $this->url = $url;
        $this->page = $page;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
    }
}
