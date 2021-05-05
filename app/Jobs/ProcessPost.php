<?php

/**
 * ContentUpdate.php
 * PHP Version 7
 *
 * @category PubSub
 * @package  Laravel
 * @author   Russell Jones <russell@web.net>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://content-mesh.jonesrussell42.xyz
 */

namespace App\Jobs;

use App\Repositories\PostRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * ProcessPost
 *
 * @category PubSub
 * @package  Laravel
 * @author   Russell Jones <russell@web.net>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://content-mesh.jonesrussell42.xyz
 */
class ProcessPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $postRepo;

    protected $details;

    /**
     * Create a new job instance.
     *
     * @param array $details Post details
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Execute the job.
     *
     * @param PostRepository $postRepo Post repository
     *
     * @throws \Exception
     * @return null
     */
    public function handle(PostRepository $postRepo)
    {
        $postRepo->addItemFromUrl($this->details);
    }
}
