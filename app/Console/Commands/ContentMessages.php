<?php

namespace App\Console\Commands;

use App\Jobs\ProcessPost;
use App\Jobs\ProcessProject;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Log;

/**
 * Watch for and process messages sent through Redis.
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
    protected $description = 'Subscribe to a Redis topic looking for content updates from CMS.';

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
        $redis->psubscribe([config('topic.content')], function ($message) {
            $content = json_decode($message, true);
            logger($content, ["config('topic.content')" => config('topic.content')]);

            switch ($content['type']) {
                case 'page':
                    Log::debug('content is page, dispatch job');
                    ProcessPost::dispatch($content)
                        ->onConnection('default');
                    break;

                case 'project':
                    Log::debug('content is project, dispatch job');
                    ProcessProject::dispatch($content)
                        ->onConnection('default');
                    break;
            }
        });
    }
}
