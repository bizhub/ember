<?php

namespace Bizhub\Ember\Embedding;

use Prism\Prism\Enums\Provider;
use Prism\Prism\Prism;

class EmbeddingManager
{
    public function create(string $input): array
    {
        $embedding = Prism::embeddings()
            ->using(Provider::Gemini, 'gemini-embedding-001')
            ->fromInput($input)
            ->asEmbeddings();

        return $embedding->embeddings[0]->embedding;
    }
}
