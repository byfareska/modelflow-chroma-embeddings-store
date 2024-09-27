<?php declare(strict_types=1);

namespace Byfareska\Modelflow\ChromaEmbeddingsStore;

use BadMethodCallException;
use ModelflowAi\Embeddings\Model\EmbeddingInterface;

readonly class EmbeddingEnvelope implements EmbeddingInterface
{
    public function __construct(
        public EmbeddingInterface $embedding,
        public ?float $distance,
    )
    {
    }

    public static function fromArray(array $data): EmbeddingInterface
    {
        throw new BadMethodCallException('It\'s not possible to create an EmbeddingEnvelope from an array.');
    }

    public function getIdentifier(): string
    {
        return $this->embedding->getIdentifier();
    }

    public function split(string $content, int $chunkNumber): EmbeddingInterface
    {
        return $this->embedding->split($content, $chunkNumber);
    }

    public function getContent(): string
    {
        return $this->embedding->getContent();
    }

    public function getFormattedContent(): string
    {
        return $this->embedding->getFormattedContent();
    }

    public function setFormattedContent(string $formattedContent): void
    {
        $this->embedding->setFormattedContent($formattedContent);
    }

    public function getVector(): ?array
    {
        return $this->embedding->getVector();
    }

    public function setVector(array $vector): void
    {
        $this->embedding->setVector($vector);
    }

    public function getHash(): string
    {
        return $this->embedding->getHash();
    }

    public function getChunkNumber(): int
    {
        return $this->embedding->getChunkNumber();
    }

    public function toArray(): array
    {
        return $this->embedding->toArray();
    }
}