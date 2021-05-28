<?php

namespace App\Repositories;

use App\Models\Page;
use App\Models\Traits\RemoteContent;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class PageRepository.
 */
class PageRepository extends BaseRepository
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
     * Get a static Page class instance.
     *
     * @return Model
     */
    public function model()
    {
        logger("PageRepository.php model()");
        return Page::class;
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