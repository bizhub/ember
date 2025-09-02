<?php

namespace Bizhub\Ember\Memory;

use Prism\Prism\Enums\Provider;
use Prism\Prism\Prism;
use Prism\Prism\Schema\ArraySchema;
use Prism\Prism\Schema\ObjectSchema;
use Prism\Prism\Schema\StringSchema;

class MemoryManager
{
    public function consume(string $text): self
    {
        $response = Prism::structured()
            ->using(Provider::Gemini, 'gemini-2.0-flash')
            ->withSchema($this->getSchema())
            ->withSystemPrompt('You are an AI assistant that processes PDF text for use in a retrieval-augmented generation (RAG) AI agent.
Your task is to break the text into coherent, self-contained chunks that preserve meaning and important details.
Aim for approximately 200-500 words per chunk, but prioritize semantic boundaries over exact length.

- Detect natural sections such as headings, subheadings, bullet points, numbered lists, and recipe sections.
- When text contains bullet points or numbered lists, keep the structure in the summary while condensing wording.
- For recipes, keep ingredient amounts and section titles intact.
- Include headings or context indicators in summaries where appropriate.
- Remove redundancy and filler, but do not lose essential information.
- Write summaries in clear, factual, and unambiguous language suitable for AI embeddings.

Each chunk should produce a summary only, suitable for your Prism `summary` schema.')
            ->withPrompt($text)
            ->asStructured();

        foreach ($response->structured as $chunk) {
            // ProcessSummarizedChunkJob::dispatch($chunk['summary']);
        }

        return $this;
    }

    public function summarize(): string
    {
        return 'Summary';
    }

    protected function getSchema(): ArraySchema
    {
        return new ArraySchema(
            name: 'document_chunks',
            description: 'List of document chunks',
            items: new ObjectSchema(
                name: 'document_chunk',
                description: 'A chunk of a document with raw text and summary for AI embedding',
                properties: [
                    new StringSchema('summary', 'A condensed, factual summary suitable for AI retrieval'),
                ],
                requiredFields: ['summary'],
            ),
        );
    }
}
