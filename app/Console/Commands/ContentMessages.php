<?php

/**
 * ContentMessages.php
 * PHP Version 7
 *
 * @category PubSub
 * @package  Laravel
 * @author   Russell Jones <russell@web.net>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://content-mesh.jonesrussell42.xyz
 */

namespace App\Console\Commands;

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
class ContentMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'content:messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Subscribe to Redis topic looking for content updates";

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $redis = Redis::connection('content');

        $redis->psubscribe(
            [config('topic.content')],
            function ($message) {
                $content = json_decode($message, true);
                logger(
                    $content,
                    ["config('topic.content')" => config('topic.content')]
                );

                switch ($content['type']) {
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
