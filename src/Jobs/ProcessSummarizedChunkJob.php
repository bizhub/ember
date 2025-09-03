<?php

namespace Domain\Ember\Jobs;

use Bizhub\Ember\Ember;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Sleep;
use Prism\Prism\Exceptions\PrismException;
use Ramsey\Uuid\Uuid;

class ProcessSummarizedChunkJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $summary,
    ) {}

    public function handle(): void
    {
        try {
            $embedding = Ember::embedding()->create($this->summary);
        } catch (PrismException $e) {
            throw $e;
        }

        Ember::memory()->upsert('pdf_chunks', [[
            'id' => Uuid::uuid4()->toString(),
            'vector' => $embedding,
            'payload' => [
                'summary' => $this->summary,
            ],
        ]]);

        Sleep::for(10)->seconds();
    }
}
