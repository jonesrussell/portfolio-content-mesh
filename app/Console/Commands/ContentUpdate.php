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

namespace App\Console\Commands;

use App\Jobs\ProcessPage;
use App\Jobs\ProcessPost;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Log;

/**
 * Watch for and process messages sent through Redis.
 *
 * @category PubSub
 * @package  PubSub
 * @author   Russell Jones <russell@web.net>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://content-mesh.jonesrussell42.xyz
 */
class ContentUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'content:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Subscribe to Redis topic looking for content updates";

    private $redis;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        // $this->redis = Redis::connection();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set("default_socket_timeout", -1);

        Redis::psubscribe(
            [config('topic.content')],
            function ($message) {
                $content = json_decode($message, true);

                switch ($content['type']) {
                    case 'page':
                        Log::debug('content is page, dispatch job');
                        ProcessPage::dispatch($content);
                        // ->onConnection('default');
                        break;
                    case 'post':
                        Log::debug('content is post, dispatch job');
                        ProcessPost::dispatch($content)
                            ->onConnection('default');
                        break;
                }
            }
        );
    }
}