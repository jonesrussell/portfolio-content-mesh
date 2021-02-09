<?php

namespace App\Repositories;

use App\Models\Form;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/**
 * Class FormRepository.
 */
class FormRepository implements FormInterface
{
    protected $form;
    protected $client;

    /**
     * @param Form $form
     */
    public function __construct(Form $form)
    {
        $this->form = $form;

        /*
         * Configure the Guzzle client
         */
        $this->client = new Client([
            'base_uri' => config('app.rest_url'),
            'timeout'  => 5.0,    // HTTP request timeout in seconds
        ]);
    }

    public function firstOrNew($title)
    {
        $form = $this->form->firstOrNew(['title' => $title]);

        if (! $form->exists) {
            Log::info(config('app.form_contact_endpoint'));
            try {
                $res = $this->client->get(config('app.form_contact_endpoint'));
            } catch (Exception $ex) {
                report($ex);
                \Log::debug('error');
                \Log::debug((string) $ex->getResponse()->getBody());
            }
            $webform = json_decode($res->getBody()->getContents());
            $form->title = $title;
            // TODO: check for valid fields, null coalesce?
            $form->fields = $webform;
            $form->save();
        }

        return $form;
    }
}
