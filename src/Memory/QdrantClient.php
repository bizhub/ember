<?php

namespace Bizhub\Ember\Memory;

use Illuminate\Support\Facades\Http;

class QdrantClient
{
    protected string $baseUrl;
    protected ?string $apiKey;

    public function __construct(string $baseUrl = null, string $apiKey = null)
    {
        $this->baseUrl = $baseUrl ?? config('ember.vector_db_url', 'http://localhost:6333');
        $this->apiKey = $apiKey ?? config('ember.vector_db_api_key');
    }

    protected function request(string $method, string $path, array $data = []): array
    {
        $response = Http::withHeaders(
            $this->apiKey ? ['Authorization' => "Bearer {$this->apiKey}"] : []
        )->{$method}("{$this->baseUrl}{$path}", $method === 'get' ? ['query' => $data] : $data);

        return $response->json();
    }

    public function post(string $path, array $data = []): array
    {
        return $this->request('post', $path, $data);
    }

    public function get(string $path, array $query = []): array
    {
        return $this->request('get', $path, $query);
    }
}
