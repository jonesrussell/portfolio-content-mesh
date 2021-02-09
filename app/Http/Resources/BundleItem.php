<?php

namespace App\Http\Resources;

use App\Exceptions\GeneralException;
use Illuminate\Http\Resources\Json\Resource;
use WoohooLabs\Yang\JsonApi\Hydrator\ClassHydrator;

class BundleItem extends Resource
{
    /**
     * Create an Internet Package instance.
     *
     * @var WoohooLabs\Yang\JsonApi\Response\JsonApiResponse
     */
    public function __construct($document)
    {
        // Verify only 1 valid Internet Package was passed in
        if (! $document->hasAnyPrimaryResources() && ! $document->isSingleResourceDocument()) {
            throw new GeneralException('No primary resource is in $document or there is more than 1.');

            return false;
        }

        // Map $document to new stdClass()
        $hydrator = new ClassHydrator();
        $item = $hydrator->hydrate($document);

        // Pass stdClass to Resource::__construct()
        parent::__construct($item);
    }

    /**
     * Convert multidimensional array into array of slugs.
     */
    protected function getNameSlugs($arr)
    {
        return array_map(function ($item) {
            return str_slug($item->name, '-');
        }, $arr);
    }

    protected function getNames($arr)
    {
        return array_map(function ($item) {
            return $item->name;
        }, $arr);
    }

    /**
     * Return the Internet Package as an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $item = [
            '_id'               => $this->id,
            'status'            => $this->status,
            'title'             => $this->title,
            'promote'           => $this->promote,
            'sticky'            => $this->sticky,
            'bundled'           => $this->bundled,
            'bundled_price'     => $this->bundled_price,
            'download'          => $this->download,
            'upload'            => $this->upload,
            'price'             => $this->price,
            'cities'            => $this->getNameSlugs($this->city),
            'customer'          => $this->getNames($this->customer_type),
            'service'           => $this->getNameSlugs($this->service_type),
            'features'          => $this->getNames($this->package_features),
        ];

        if (isset($this->bundled_features)) {
            $item['bundled_features'] = $this->getNames($this->bundled_features);
        }

        return $item;
    }
}
