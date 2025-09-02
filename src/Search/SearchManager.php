<?php

namespace Bizhub\Ember\Search;

class SearchManager
{
    public function similar(array $vector): array
    {
        $client = new Client;

        $searchResponse = $client->post('/collections/pdf_chunks/points/search', [
            'vector' => $vector,
            'top' => 5,
            'with_payload' => true,
        ]);

        return $searchResponse->json()['result'];
    }
}
