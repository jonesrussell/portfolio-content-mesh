<?php

namespace App\Models\Traits;

use App\Exceptions\GeneralException;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\PostResource;

/**
 * Trait Uuid.
 */
trait RemoteContent
{
    /**
     * Type of resource/content.
     *
     * @var string|null
     */
    protected $type = null;

    /**
     * Retrieve content from API and
     * add to the database.
     *
     * @param array $details
     *
     * @throws \GeneralException
     *
     * @return Collection|false
     */
    public function addItemFromUrl(array $details)
    {
        logger('addItemFromUrl()');
        $this->type = $details['type'];

        // Get content from API
        try {
            $document = $this->fetchDocument($details);

            /*
             * TODO: catch additional exceptions such as:
             *
             * \GuzzleHttp\Exception\ConnectException
             * \GuzzleHttp\Exception\ClientException
             */
        } catch (GeneralException $e) {
            report($e);

            return false;
        }

        // Exit if item hasn't been fetched from API
        if (!$document || !$document->hasAnyPrimaryResources()) {
            // TODO: determine if this will ever throw.
            // RemoteContent::fetchDocument() throw enough
            throw new GeneralException('Failed to fetch document.');
        }

        // We're only fetching single resources thus far
        if (!$document->isSingleResourceDocument()) {
            throw new GeneralException('$document does not have a single resource (could be a collection or none).');
        }

        // TODO: Why doesn't this have the city names included?
        logger('seemingly successfully fetched a document');
        logger($document->toArray());

        // Transform the document into a Resource
        switch ($this->type) {
            case 'page':
                $item = PostResource::make($document)->resolve();
                break;

            default:
                return false;
        }

        // Save item to database
        $post = $this->makeModel()
            ->updateOrCreate(['_id' => $item['_id']], $item);

        return $post;
    }
}
