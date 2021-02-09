<?php

namespace App\Jobs;

use App\Repositories\PostRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var PostRepository */
    protected $postRepo;

    /** @var array */
    protected $details;

    /**
     * Create a new job instance.
     *
     * @param array $details
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Execute the job.
     *
     * @param PostRepository $postRepo
     *
     * @throws \Exception
     */
    public function handle(PostRepository $postRepo)
    {
        $postRepo->addItemFromUrl($this->details);
    }
}
