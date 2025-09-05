<?php

namespace Bizhub\Ember\Search;

use Bizhub\Ember\Memory\QdrantClient;

class SearchManager
{
    public function __construct(
        protected QdrantClient $client,
    ) {}

    public function similar(array $vector, int $top = 5): array
    {
        $response = $this->client->post(
            path: '/collections/ember/points/search',
            data: [
                'vector' => $vector,
                'top' => $top,
                'with_payload' => true,
            ],
        );

        return $response['result'] ?? [];
    }
}
