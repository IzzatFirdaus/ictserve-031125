# Mimir Vector Embeddings

**Version**: 4.1.0  
**Last Updated**: 2025-12-05  
**Status**: ✅ Enabled (Ollama nomic-embed-text)

---

## Overview

Mimir supports vector embeddings for semantic search across your knowledge graph. This enables finding content by **meaning** rather than exact keyword matches.

---

## Current Configuration

### Embeddings Provider

- **Provider**: Ollama
- **Model**: nomic-embed-text
- **Dimensions**: 768
- **Status**: ✅ Enabled

### Why Ollama + nomic-embed-text?

- **Lightweight**: 768 dimensions (vs 1536 for OpenAI)
- **Fast**: Local processing, no API rate limits
- **Free**: No API costs
- **Privacy**: All processing stays local
- **Quality**: Optimized for code and technical content

---

## Configuration

### Environment Variables

```env
# Embeddings Configuration (Mimir/.env)
MIMIR_EMBEDDINGS_ENABLED=true
MIMIR_FEATURE_VECTOR_EMBEDDINGS=true
MIMIR_EMBEDDINGS_PROVIDER=ollama
MIMIR_EMBEDDINGS_MODEL=nomic-embed-text
MIMIR_EMBEDDINGS_API=http://ollama:11434
MIMIR_EMBEDDINGS_API_PATH=/v1/embeddings
MIMIR_EMBEDDINGS_API_KEY=dummy-key
MIMIR_EMBEDDINGS_DIMENSIONS=768
MIMIR_EMBEDDINGS_CHUNK_SIZE=768
MIMIR_EMBEDDINGS_CHUNK_OVERLAP=100
MIMIR_EMBEDDINGS_DELAY_MS=200
MIMIR_EMBEDDINGS_MAX_RETRIES=3
```

### Chunking Strategy

| Parameter | Value | Purpose |
|-----------|-------|---------|
| **Chunk Size** | 768 tokens | Matches model's optimal input |
| **Overlap** | 100 tokens (13%) | Preserves context between chunks |
| **Delay** | 200ms | Rate limiting between requests |
| **Max Retries** | 3 | Retry failed embedding requests |

---

## What Embeddings Enable

### 1. Semantic Search

Search by meaning instead of exact keywords:

```javascript
// Find authentication-related content
vector_search_nodes({
  query: "user login and session management",
  types: ["file_chunk", "memory"],
  min_similarity: 0.75,
  limit: 10
})
```

### 2. File Indexing with Semantic Search

Index files and search by semantic similarity:

```javascript
// Index a folder with embeddings
index_folder({
  path: "/workspace/app/Models",
  generate_embeddings: true,
  file_patterns: ["*.php"],
  recursive: true
})

// Search indexed files
vector_search_nodes({
  query: "eloquent model relationships",
  types: ["file", "file_chunk"],
  min_similarity: 0.7
})
```

### 3. Memory Node Similarity

Find similar memories, tasks, or concepts:

```javascript
// Create memory with automatic embedding
memory_node({
  operation: "add",
  type: "memory",
  properties: {
    title: "Laravel authentication implementation",
    content: "Implemented JWT-based authentication with refresh tokens..."
  }
})

// Find similar memories
vector_search_nodes({
  query: "authentication implementation patterns",
  types: ["memory"],
  depth: 2  // Include connected nodes
})
```

---

## Model Comparison

### nomic-embed-text (Current)

| Property | Value |
|----------|-------|
| **Provider** | Ollama (Local) |
| **Dimensions** | 768 |
| **Max Input** | 8192 tokens |
| **Speed** | Fast (local) |
| **Cost** | Free |
| **Privacy** | 100% local |

### Alternative: text-embedding-3-small (GitHub Copilot)

| Property | Value |
|----------|-------|
| **Provider** | GitHub Copilot (OpenAI) |
| **Dimensions** | 1536 |
| **Max Input** | 8191 tokens |
| **Speed** | Fast (API) |
| **Cost** | Included with Copilot |
| **Privacy** | Cloud-based |

---

## Usage Examples

### Example 1: Semantic Code Search

```javascript
// Find authentication-related code
vector_search_nodes({
  query: "user authentication and authorization logic",
  types: ["file_chunk"],
  min_similarity: 0.7,
  limit: 5
})

// Returns:
// - app/Http/Controllers/Auth/LoginController.php (similarity: 0.85)
// - app/Policies/UserPolicy.php (similarity: 0.78)
// - app/Models/User.php (similarity: 0.72)
```

### Example 2: Find Similar Tasks

```javascript
// Find tasks related to security
vector_search_nodes({
  query: "implement security features and authentication",
  types: ["todo"],
  min_similarity: 0.75
})

// Returns:
// - "Add two-factor authentication" (similarity: 0.82)
// - "Implement API rate limiting" (similarity: 0.76)
```

### Example 3: Knowledge Graph Search

```javascript
// Find related concepts with graph traversal
vector_search_nodes({
  query: "database optimization and performance",
  types: ["memory", "concept", "file_chunk"],
  depth: 2,  // Include connected nodes
  min_similarity: 0.7
})

// Returns nodes + their relationships
```

---

## Performance Considerations

### Indexing Speed

| File Size | Indexing Time | Chunks Generated |
|-----------|---------------|------------------|
| Small (<1000 lines) | 1-2 seconds | 5-10 chunks |
| Medium (1000-5000 lines) | 3-5 seconds | 10-30 chunks |
| Large (>5000 lines) | 5-10 seconds | 30+ chunks |

### Memory Usage

| Chunks | Memory Usage | Storage (Neo4j) |
|--------|--------------|-----------------|
| 100 | ~300KB | ~500KB |
| 1,000 | ~3MB | ~5MB |
| 10,000 | ~30MB | ~50MB |

### Rate Limiting

- **Delay**: 200ms between requests
- **Reason**: Prevent overwhelming Ollama
- **Impact**: Slower indexing, more reliable
- **Adjustable**: Set `MIMIR_EMBEDDINGS_DELAY_MS` lower for faster indexing

---

## Switching Providers

### Switch to GitHub Copilot

```env
# Mimir/.env
MIMIR_EMBEDDINGS_PROVIDER=openai
MIMIR_EMBEDDINGS_MODEL=text-embedding-3-small
MIMIR_EMBEDDINGS_API=http://copilot-api:4141
MIMIR_EMBEDDINGS_DIMENSIONS=1536
MIMIR_EMBEDDINGS_CHUNK_SIZE=512
```

```bash
# Restart Mimir
cd Mimir
docker compose restart mimir-server
```

### Switch to OpenAI

```env
# Mimir/.env
MIMIR_EMBEDDINGS_PROVIDER=openai
MIMIR_EMBEDDINGS_MODEL=text-embedding-3-small
MIMIR_EMBEDDINGS_API=https://api.openai.com
MIMIR_EMBEDDINGS_API_KEY=sk-your-api-key
MIMIR_EMBEDDINGS_DIMENSIONS=1536
```

---

## Monitoring

### Check Embedding Stats

```javascript
// Get embedding statistics
get_embedding_stats()
```

**Returns**:

```json
{
  "total_nodes_with_embeddings": 150,
  "by_type": {
    "file_chunk": 120,
    "memory": 20,
    "todo": 10
  },
  "dimensions": 768,
  "provider": "ollama",
  "model": "nomic-embed-text"
}
```

### View Embedding Logs

```powershell
# Watch embedding activity
docker logs mimir_server -f | Select-String "embedding"

# Check for errors
docker logs mimir_server --tail 100 | Select-String "error"

# Check Ollama logs
docker logs ollama_server --tail 50
```

---

## Troubleshooting

### Embeddings Not Working

**Check Ollama Service**:

```powershell
# Check Ollama status
docker ps | Select-String "ollama"

# View Ollama logs
docker logs ollama_server --tail 20

# Test Ollama API
curl http://localhost:11434/v1/embeddings `
  -Method POST `
  -Headers @{"Content-Type"="application/json"} `
  -Body '{"input":"test","model":"nomic-embed-text"}'
```

**Verify Mimir Configuration**:

```powershell
# Check Mimir logs for embedding initialization
docker logs mimir_server --tail 50 | Select-String "embedding"

# Should see:
# ✅ Vector embeddings enabled: ollama/nomic-embed-text
# ✅ Embeddings Dimensions: 768
```

### Slow Indexing

**Increase Parallel Threads**:

```env
# Mimir/.env
MIMIR_INDEXING_THREADS=4  # Default: 2
```

**Reduce Delay** (may hit rate limits):

```env
# Mimir/.env
MIMIR_EMBEDDINGS_DELAY_MS=100  # Default: 200
```

### High Memory Usage

**Reduce Chunk Size**:

```env
# Mimir/.env
MIMIR_EMBEDDINGS_CHUNK_SIZE=512  # Default: 768
MIMIR_EMBEDDINGS_CHUNK_OVERLAP=50  # Default: 100
```

### Model Not Found

**Pull nomic-embed-text Model**:

```bash
# Pull model manually
docker exec -it ollama_server ollama pull nomic-embed-text

# Verify model
docker exec -it ollama_server ollama list
```

---

## Advanced Configuration

### Disable Embeddings

```env
# Mimir/.env
MIMIR_EMBEDDINGS_ENABLED=false
MIMIR_FEATURE_VECTOR_EMBEDDINGS=false
```

```bash
# Restart Mimir
cd Mimir
docker compose restart mimir-server
```

### Image Embeddings (Experimental)

```env
# Mimir/.env
MIMIR_EMBEDDINGS_IMAGES=true
MIMIR_EMBEDDINGS_IMAGES_DESCRIBE_MODE=true
```

**Note**: Requires vision-capable model (e.g., GPT-4 Vision)

---

## Verification Checklist

- [x] Embeddings enabled in `.env`
- [x] Ollama service running
- [x] nomic-embed-text model pulled
- [x] Mimir server restarted
- [x] Logs show "Vector embeddings enabled"
- [x] Health check returns 200 OK
- [x] `vector_search_nodes` tool available
- [ ] Test semantic search (after indexing files)
- [ ] Verify embedding stats

---

## Related Documentation

- **[01-SETUP.md](01-SETUP.md)** - Initial setup
- **[02-DOCKER.md](02-DOCKER.md)** - Docker configuration
- **[06-API-REFERENCE.md](06-API-REFERENCE.md)** - MCP tools reference
- **[07-NEO4J-GUIDE.md](07-NEO4J-GUIDE.md)** - Knowledge graph usage

---

## External Resources

- [Ollama Documentation](https://ollama.ai/docs)
- [nomic-embed-text Model](https://ollama.ai/library/nomic-embed-text)
- [Mimir Embeddings Guide](https://github.com/IzzatFirdaus/Mimir/blob/main/docs/embeddings.md)

---

**Status**: ✅ EMBEDDINGS ENABLED  
**Provider**: Ollama  
**Model**: nomic-embed-text (768 dimensions)  
**Vector Search**: Ready for use
