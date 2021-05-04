<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class PageResource extends JsonResource
{
    /**
     * A JSON:API Document resource.
     *
     * @var WoohooLabs\Yang\JsonApi\Response\JsonApiResponse
     */
    private $doc;

    /**
     * An array to hold model fields.
     *
     * @var array
     */
    private $attributes;

    /**
     * The key of an image include.
     *
     * @var string
     */
    private $imageKey;

    /**
     * Create a new resource instance.
     *
     * @param mixed $resource
     */
    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->doc = $resource->primaryResource();

        $this->attributes = $this->doc->idAndAttributes();
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            '_id'     => $this->attributes['id'],
            'status'  => $this->attributes['status'],
            'title'   => $this->attributes['title'],
            'promote' => $this->attributes['promote'],
            'sticky'  => $this->attributes['sticky'],
            'metatag' => $this->attributes['metatag'],
            'body'    => $this->attributes['body'],
            // 'link'    => $this->attributes['link'],
        ];
    }
}
