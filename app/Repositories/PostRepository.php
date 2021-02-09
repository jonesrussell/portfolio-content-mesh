<?php

namespace App\Repositories;

use App\Models\Post;
use App\Models\Traits\RemoteContent;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class PostRepository.
 */
class PostRepository extends BaseRepository
{
    use RemoteContent;

    /**
     * Class initialization
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get a static Post class instance.
     *
     * @return Model
     */
    public function model()
    {
        return Post::class;
    }

    /**
     * Get all the model records in the database.
     * TODO: Somehow use parent:all() with orderBy
     *
     * @param array $columns
     *
     * @return Collection|static[]
     */
    public function all(array $columns = ['*'])
    {
        return $this->model->get();
    }
}
