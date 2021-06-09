<?php

namespace App\Repositories;

use App\Exceptions\GeneralException as GeneralException;
use GuzzleHttp\Psr7\Request;
use Http\Adapter\Guzzle6\Client as GuzzleClient;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Moloquent as Model;
use Log;
use WoohooLabs\Yang\JsonApi\Client\JsonApiClient;
use WoohooLabs\Yang\JsonApi\Request\JsonApiRequestBuilder;
use WoohooLabs\Yang\JsonApi\Response\JsonApiResponse;
use WoohooLabs\Yang\JsonApi\Schema\Document;

/**
 * Class BaseRepository.
 */
abstract class BaseRepository implements RepositoryContract
{
    /**
     * URL of resource to fetch.
     *
     * @var string
     */
    protected $content_url;

    /**
     * The repository model.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * The query builder.
     *
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $query;

    /**
     * Alias for the query limit.
     *
     * @var int
     */
    protected $take;

    /**
     * Array of related models to eager load.
     *
     * @var array
     */
    protected $with = [];

    /**
     * Array of one or more where clause parameters.
     *
     * @var array
     */
    protected $wheres = [];

    /**
     * Array of one or more where in clause parameters.
     *
     * @var array
     */
    protected $whereIns = [];

    /**
     * Array of one or more ORDER BY column/value pairs.
     *
     * @var array
     */
    protected $orderBys = [];

    /**
     * Array of scope methods to call on the model.
     *
     * @var array
     */
    protected $scopes = [];

    /**
     * BaseRepository constructor.
     */
    public function __construct()
    {
        $this->makeModel();

        $this->client = $this->createClient();
    }

    /**
     * Specify Model class name.
     *
     * @return mixed
     */
    abstract public function model();

    /**
     * @throws GeneralException
     *
     * @return Model|mixed
     */
    public function makeModel()
    {
        $model = app()->make($this->model());

        logger($model);

        if (!$model instanceof Moloquent) {
            Log::error('!$model instanceof Model');
            logger("Class {$this->model()} must be an instance of " . Model::class);
            throw new GeneralException("Class {$this->model()} must be an instance of " . Model::class);
        }

        return $this->model = $model;
    }

    /**
     * Get all the model records in the database.
     *
     * @param array $columns
     *
     * @return Collection|static[]
     */
    public function all(array $columns = ['*'])
    {
        $this->newQuery()->eagerLoad();

        $models = $this->query->get($columns);

        $this->unsetClauses();

        return $models;
    }

    /**
     * Count the number of specified model records in the database.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->get()->count();
    }

    /**
     * Create a new model record in the database.
     *
     * @param array $data
     *By
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data)
    {
        $this->unsetClauses();

        return $this->model->create($data);
    }

    /**
     * Create one or more new model records in the database.
     *
     * @param array $data
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function createMultiple(array $data)
    {
        $models = new Collection();

        foreach ($data as $d) {
            $models->push($this->create($d));
        }

        return $models;
    }

    /**
     * Delete one or more model records from the database.
     *
     * @return mixed
     */
    public function delete()
    {
        $this->newQuery()->setClauses()->setScopes();

        $result = $this->query->delete();

        $this->unsetClauses();

        return $result;
    }

    /**
     * Delete the specified model record from the database.
     *
     * @param $id
     *
     * @throws \Exception
     *
     * @return bool|null
     */
    public function deleteById($id): bool
    {
        $this->unsetClauses();

        return $this->getById($id)->delete();
    }

    /**
     * Delete multiple records.
     *
     * @param array $ids
     *
     * @return int
     */
    public function deleteMultipleById(array $ids): int
    {
        return $this->model->destroy($ids);
    }

    /**
     * Get the first specified model record from the database.
     *
     * @param array $columns
     *
     * @return Model|static
     */
    public function first(array $columns = ['*'])
    {
        $this->newQuery()->eagerLoad()->setClauses()->setScopes();

        $model = $this->query->firstOrFail($columns);

        $this->unsetClauses();

        return $model;
    }

    /**
     * Get all the specified model records in the database.
     *
     * @param array $columns
     *
     * @return Collection|static[]
     */
    public function get(array $columns = ['*'])
    {
        $this->newQuery()->eagerLoad()->setClauses()->setScopes();

        $models = $this->query->get($columns);

        $this->unsetClauses();

        return $models;
    }

    /**
     * Get the specified model record from the database.
     *
     * @param       $id
     * @param array $columns
     *
     * @return Collection|Model
     */
    public function getById($id, array $columns = ['*'])
    {
        $this->unsetClauses();

        $this->newQuery()->eagerLoad();

        return $this->query->findOrFail($id, $columns);
    }

    /**
     * @param       $item
     * @param       $column
     * @param array $columns
     *
     * @return Model|static|null
     */
    public function getByColumn($item, $column, array $columns = ['*'])
    {
        $this->unsetClauses();

        $this->newQuery()->eagerLoad();

        return $this->query->where($column, $item)->first($columns);
    }

    /**
     * @param int    $limit
     * @param array  $columns
     * @param string $pageName
     * @param null   $page
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($limit = 25, array $columns = ['*'], $pageName = 'page', $page = null)
    {
        $this->newQuery()->eagerLoad()->setClauses()->setScopes();

        $models = $this->query->paginate($limit, $columns, $pageName, $page);

        $this->unsetClauses();

        return $models;
    }

    /**
     * Update the specified model record in the database.
     *
     * @param       $id
     * @param array $data
     * @param array $options
     *
     * @return Collection|Modeld
     */
    public function updateById($id, array $data, array $options = [])
    {
        $this->unsetClauses();

        $model = $this->getById($id);

        $model->update($data, $options);

        return $model;
    }

    /**
     * Set the query limit.
     *
     * @param int $limit
     *
     * @return $this
     */
    public function limit($limit)
    {
        $this->take = $limit;

        return $this;
    }

    /**
     * Set an ORDER BY clause.
     *
     * @param string $column
     * @param string $direction
     *
     * @return $this
     */
    public function orderBy($column, $direction = 'asc')
    {
        $this->orderBys[] = compact('column', 'direction');

        return $this;
    }

    /**
     * Add a simple where clause to the query.
     *
     * @param string $column
     * @param string $value
     * @param string $operator
     *
     * @return $this
     */
    public function where($column, $value, $operator = '=')
    {
        $this->wheres[] = compact('column', 'value', 'operator');

        return $this;
    }

    /**
     * Add a simple where in clause to the query.
     *
     * @param string $column
     * @param mixed  $values
     *
     * @return $this
     */
    public function whereIn($column, $values)
    {
        $values = is_array($values) ? $values : [$values];

        $this->whereIns[] = compact('column', 'values');

        return $this;
    }

    /**
     * Set Eloquent relationships to eager load.
     *
     * @param $relations
     *
     * @return $this
     */
    public function with($relations)
    {
        if (is_string($relations)) {
            $relations = func_get_args();
        }

        $this->with = $relations;

        return $this;
    }

    /**
     * Create a new instance of the model's query builder.
     *
     * @return $this
     */
    protected function newQuery()
    {
        $this->query = $this->model->newQuery();

        return $this;
    }

    /**
     * Add relationships to the query builder to eager load.
     *
     * @return $this
     */
    protected function eagerLoad()
    {
        foreach ($this->with as $relation) {
            $this->query->with($relation);
        }

        return $this;
    }

    /**
     * Set clauses on the query builder.
     *
     * @return $this
     */
    protected function setClauses()
    {
        foreach ($this->wheres as $where) {
            $this->query->where($where['column'], $where['operator'], $where['value']);
        }

        foreach ($this->whereIns as $whereIn) {
            $this->query->whereIn($whereIn['column'], $whereIn['values']);
        }

        foreach ($this->orderBys as $orders) {
            $this->query->orderBy($orders['column'], $orders['direction']);
        }

        if (isset($this->take) and !is_null($this->take)) {
            $this->query->take($this->take);
        }

        return $this;
    }

    /**
     * Set query scopes.
     *
     * @return $this
     */
    protected function setScopes()
    {
        foreach ($this->scopes as $method => $args) {
            $this->query->$method(implode(', ', $args));
        }

        return $this;
    }

    /**
     * Reset the query clause parameter arrays.
     *
     * @return $this
     */
    protected function unsetClauses()
    {
        // Log::info(__FUNCTION__);
        $this->wheres = [];
        $this->whereIns = [];
        $this->scopes = [];
        $this->take = null;

        return $this;
    }

    /**
     * Create and return a guzzle http client.
     *
     * @return JsonApiClient
     */
    protected function createClient(): JsonApiClient
    {
        // Create an HTTP Client
        $guzzleClient = GuzzleClient::createWithConfig([
            // 'base_uri' => config('app.rest_url'),
            // 'timeout' => config('app.rest_timeout'),
            'verify'  => false,
        ]);
        logger('createClient()', ['guzzleClient' => $guzzleClient]);
        return new JsonApiClient($guzzleClient);
    }

    /**
     * Build a PSR-7 request object to our JSONAPI endpoint.
     *
     * @param array $details
     *
     * @return GuzzleHttp\Psr7\Request
     */
    protected function buildRequest(string $url, array $fields, array $includes): Request
    {
        $requestBuilder = new JsonApiRequestBuilder(new Request('GET', ''));

        return $requestBuilder
            ->fetch()
            ->setUri($url)
            ->setJsonApiFields($fields)
            ->setJsonApiIncludes($includes)
            ->getRequest();
    }

    /**
     * Error check response.
     *
     * @param JsonApiResponse $response
     *
     * @return array|null
     */
    private function errorCheck(JsonApiResponse $response)
    {
        // Assume catastrophe, set generic error code and message
        $error = [
            'code'  => 500,
            'msg'   => __('backend.content.jsonapi.fetch_error'),
        ];

        logger($error, ['called from' => 'errorCheck']);

        // Check if HTTP response is bad
        if (!$response->isSuccessful([200, 202])) {
            Log::debug('Bad HTTP, check for errors');
            // Bad HTTP, check for errors
            if ($response->hasDocument() && $response->document()->hasErrors()) {
                $firstError = $response->document()->error(0);
                $error->code = $firstError->status();
                $error->msg = "{$firstError->title()}: {$firstError->detail()}";
            }
            // No errors, all bad
            Log::error($error);

            return $error;
        }

        // Good HTTP response, check for document
        if (!$response->hasDocument()) {
            Log::debug('Good HTTP response, check for document');
            Log::error($error);

            return $error;
        }

        if ($response->document()->hasErrors()) {
            $firstError = $response->document()->error(0);
            $error->code = $firstError->status();
            $error->msg = "{$firstError->title()}: { $firstError->detail()}";

            return $error;
        }

        return null;
    }

    /**
     * Fetch News item from API with GuzzleHttp.
     *
     * @param array $details
     *
     * @throws GeneralException
     *
     * @return WoohooLabs\Yang\JsonApi\Schema\Document
     */
    public function fetchDocument(array $details): Document
    {
        // Build a PSR-7 request
        $request = $this->buildRequest(
            $details['content_url'],
            $details['fields'],
            $details['include']
        );

        // Send request, retrieve response
        $response = $this->getClient()
            ->sendRequest($request);

        /*$errorMessage = $this->errorCheck($response);

        if ($errorMessage) {
            throw new GeneralException($errorMessage->msg, $errorMessage->code);
        }*/

        if (!$response->isSuccessfulDocument()) {
            logger('!isSuccessfulDocument()', ['statusCode' => $response->getStatusCode()]);

            $code = 500;
            $msg = __('backend.content.jsonapi.fetch_error');

            if ($response->document()->hasErrors()) {
                $firstError = $response->document()->error(0);
                $code = $firstError->status();
                // $msg = "{$firstError->title()}: { $firstError->detail()}";
            }

            throw new GeneralException($msg, $code);
        }

        return $response->document();
    }

    /**
     * Get the HTTP client.
     *
     * @return JsonApiClient
     */
    protected function getClient(): JsonApiClient
    {
        return $this->client;
    }
}
