<?php declare(strict_types=1);

namespace Byfareska\Modelflow\ChromaEmbeddingsStore;

use Codewithkyrian\ChromaDB\Resources\CollectionResource;
use Generator;
use ModelflowAi\Embeddings\Model\EmbeddingInterface;
use ModelflowAi\Embeddings\Store\EmbeddingsStoreInterface;
use RuntimeException;

readonly class ChromaEmbeddingsStore implements EmbeddingsStoreInterface
{
    public function __construct(
        private CollectionResource $collection,
    )
    {
    }

    public function addDocument(EmbeddingInterface $embedding): void
    {
        $this->addDocuments([$embedding]);
    }

    /**
     * @param EmbeddingInterface[] $embeddings
     */
    public function addDocuments(array $embeddings): void
    {
        $this->collection->add(
            array_map(static fn(EmbeddingInterface $e) => $e->getIdentifier(), $embeddings),
            array_map(static fn(EmbeddingInterface $e) => $e->getVector(), $embeddings),
            array_map(static fn(EmbeddingInterface $e) => $e->toArray() + ['@type' => $e::class], $embeddings),
        );
    }

    public function similaritySearch(array $vector, int $k = 4, array $additionalArguments = []): array
    {
        return [...$this->similaritySearchGenerator($vector, $k, $additionalArguments)];
    }

    /**
     * @param float[] $vector
     * @param array<string, scalar> $additionalArguments
     * @return Generator<EmbeddingInterface>
     */
    public function similaritySearchGenerator(array $vector, int $k = 4, array $additionalArguments = []): Generator
    {
        $items = $this->collection->query(
            queryEmbeddings: [$vector],
            queryTexts: $additionalArguments['queryTexts'] ?? null,
            queryImages: $additionalArguments['queryImages'] ?? null,
            nResults: $k,
            where: $additionalArguments['where'] ?? null,
            whereDocument: $additionalArguments['whereDocument'] ?? null,
            include: $additionalArguments['include'] ?? null,
        );

        $mapper = static function (string $id, ?array $vector, ?array $metadatas, ?array $data, ?float $distance, ?array $documents, ?array $uris) {
            $type = $metadatas['@type'] ?? null;
            if (empty($type)) {
                throw new RuntimeException('Missing @type in metadata');
            }

            return new EmbeddingEnvelope(
                $type::fromArray($metadatas + compact('vector')),
                $distance
            );
        };

        foreach ($items->ids[0] as $i => $id) {
            yield $mapper(
                $id,
                $items->embeddings[0][$i] ?? null,
                $items->metadatas[0][$i] ?? null,
                $items->data[0][$i] ?? null,
                $items->distances[0][$i] ?? null,
                $items->documents[0][$i] ?? null,
                $items->uris[0][$i] ?? null,
            );
        }
    }
}