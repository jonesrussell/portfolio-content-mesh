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

use App\Repositories\PageRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * ProcessPage
 *
 * @category PubSub
 * @package  Laravel
 * @author   Russell Jones <russell@web.net>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://content-mesh.jonesrussell42.xyz
 */
class ProcessPage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $pageRepo;

    protected $details;

    /**
     * Create a new job instance.
     *
     * @param array $details Page details
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Execute the job.
     *
     * @param PageRepository $pageRepo Page repository
     *
     * @throws \Exception
     * @return null
     */
    public function handle(PageRepository $pageRepo)
    {
        $pageRepo->addItemFromUrl($this->details);
    }
}
