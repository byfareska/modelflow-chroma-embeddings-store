# Usage

```php
        $dsn = new \Nyholm\Psr7\Uri::__construct('http://chroma:8000/app');
        $chroma = \Codewithkyrian\ChromaDB\ChromaDB::factory()
            ->withHost($dsn->getHost())
            ->withPort($dsn->getPort())
            ->withDatabase(substr($dsn->getPath(), 1))
            ->connect();
        $store = new \Byfareska\Modelflow\ChromaEmbeddingsStore\ChromaEmbeddingsStore($chroma->getOrCreateCollection('test'));
```