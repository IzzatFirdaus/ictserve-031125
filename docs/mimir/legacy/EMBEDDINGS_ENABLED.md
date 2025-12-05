# Mimir Embeddings - Enabled

**Date**: 2025-12-04 09:07 MYT  
**Status**: ✅ ENABLED  
**Provider**: GitHub Copilot  
**Model**: text-embedding-3-small

---

## ✅ Configuration Applied

### Embeddings Settings

```env
MIMIR_EMBEDDINGS_ENABLED=true
MIMIR_EMBEDDINGS_PROVIDER=copilot
MIMIR_EMBEDDINGS_MODEL=text-embedding-3-small
MIMIR_EMBEDDINGS_API=http://copilot-api:4141
MIMIR_EMBEDDINGS_API_PATH=/v1/embeddings
MIMIR_EMBEDDINGS_DIMENSIONS=1536
MIMIR_EMBEDDINGS_CHUNK_SIZE=512
MIMIR_EMBEDDINGS_CHUNK_OVERLAP=100
MIMIR_EMBEDDINGS_DELAY_MS=200
MIMIR_EMBEDDINGS_MAX_RETRIES=3
```

### Verification from Logs

```
✅ Embeddings Enabled: true
✅ Embeddings Provider: copilot
✅ Embeddings Model: text-embedding-3-small
✅ Vector embeddings enabled: copilot/text-embedding-3-small
   Base URL: http://copilot-api:4141
   Dimensions: 1536
✅ UnifiedSearchService: Vector search enabled
```

---

## 🔍 What This Enables

### 1. Semantic Search

Search by **meaning** instead of exact keywords:

```javascript
// Example: Find related concepts
vector_search_nodes({
  query: "authentication and security",
  min_similarity: 0.75,
  limit: 10
})
```

### 2. File Indexing with Embeddings

Index files and search by semantic similarity:

```javascript
// Index a folder
index_folder({
  path: "/workspace/app",
  generate_embeddings: true,
  file_patterns: ["*.php", "*.js"]
})

// Search indexed files
vector_search_nodes({
  query: "user authentication logic",
  types: ["file", "file_chunk"]
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
    content: "Implemented JWT-based authentication..."
  }
})

// Find similar memories
vector_search_nodes({
  query: "authentication implementation",
  types: ["memory"]
})
```

---

## 📊 Embedding Model Details

### text-embedding-3-small

| Property | Value |
|----------|-------|
| **Provider** | GitHub Copilot (OpenAI) |
| **Dimensions** | 1536 |
| **Max Input** | 8191 tokens |
| **Performance** | Fast, optimized for code |
| **Cost** | Included with Copilot subscription |

### Chunking Strategy

- **Chunk Size**: 512 tokens
- **Overlap**: 100 tokens (20%)
- **Delay**: 200ms between requests (rate limiting)
- **Retries**: 3 attempts on failure

---

## 🚀 Usage Examples

### Example 1: Semantic Code Search

```javascript
// Find authentication-related code
vector_search_nodes({
  query: "user login and session management",
  types: ["file_chunk"],
  min_similarity: 0.7,
  limit: 5
})
```

### Example 2: Find Similar Tasks

```javascript
// Find tasks related to security
vector_search_nodes({
  query: "implement security features",
  types: ["todo"],
  min_similarity: 0.75
})
```

### Example 3: Knowledge Graph Search

```javascript
// Find related concepts
vector_search_nodes({
  query: "database optimization techniques",
  types: ["memory", "concept"],
  depth: 2  // Include connected nodes
})
```

---

## 🔧 Configuration Files Updated

### 1. Mimir/.env

```env
MIMIR_EMBEDDINGS_ENABLED=true
MIMIR_EMBEDDINGS_PROVIDER=copilot
MIMIR_EMBEDDINGS_MODEL=text-embedding-3-small
```

### 2. Mimir/docker-compose.yml

```yaml
environment:
  - MIMIR_EMBEDDINGS_ENABLED=${MIMIR_EMBEDDINGS_ENABLED:-true}
  - MIMIR_EMBEDDINGS_PROVIDER=${MIMIR_EMBEDDINGS_PROVIDER:-copilot}
  - MIMIR_EMBEDDINGS_MODEL=${MIMIR_EMBEDDINGS_MODEL:-text-embedding-3-small}
  - MIMIR_EMBEDDINGS_API=${MIMIR_EMBEDDINGS_API:-http://copilot-api:4141}
  - MIMIR_EMBEDDINGS_DIMENSIONS=${MIMIR_EMBEDDINGS_DIMENSIONS:-1536}
```

---

## ⚡ Performance Considerations

### Rate Limiting

- **Delay**: 200ms between embedding requests
- **Reason**: Avoid overwhelming Copilot API
- **Impact**: Slower indexing, but more reliable

### Indexing Speed

- **Small files** (<1000 lines): ~1-2 seconds
- **Large files** (>5000 lines): ~5-10 seconds
- **Parallel threads**: 2 (configurable via `MIMIR_INDEXING_THREADS`)

### Memory Usage

- **Per embedding**: ~6KB (1536 dimensions × 4 bytes)
- **1000 chunks**: ~6MB
- **10000 chunks**: ~60MB

---

## 🐛 Troubleshooting

### Embeddings Not Working

**Check Copilot API**:

```powershell
docker logs copilot_api_server --tail 20
```

**Test Embeddings Endpoint**:

```powershell
curl http://localhost:4141/v1/embeddings `
  -Method POST `
  -Headers @{"Content-Type"="application/json"} `
  -Body '{"input":"test","model":"text-embedding-3-small"}'
```

### Slow Indexing

**Increase Threads** (in `Mimir/.env`):

```env
MIMIR_INDEXING_THREADS=4  # Default: 2
```

**Reduce Delay** (faster but may hit rate limits):

```env
MIMIR_EMBEDDINGS_DELAY_MS=100  # Default: 200
```

### High Memory Usage

**Reduce Chunk Size**:

```env
MIMIR_EMBEDDINGS_CHUNK_SIZE=256  # Default: 512
```

---

## 📈 Monitoring

### Check Embedding Stats

```javascript
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
  "dimensions": 1536,
  "provider": "copilot",
  "model": "text-embedding-3-small"
}
```

### View Logs

```powershell
# Watch embedding activity
docker logs mimir_server -f | Select-String "embedding"

# Check for errors
docker logs mimir_server --tail 100 | Select-String "error"
```

---

## 🔄 Restart After Changes

If you modify embeddings configuration:

```powershell
cd Mimir
docker compose restart mimir-server
```

Wait 5-10 seconds for service to be healthy:

```powershell
docker logs mimir_server --tail 20
```

---

## ✅ Verification Checklist

- [x] Embeddings enabled in `.env`
- [x] Docker Compose updated with Copilot config
- [x] Mimir server restarted
- [x] Logs show "Vector embeddings enabled"
- [x] Health check returns 200 OK
- [x] 17 tools available (including `vector_search_nodes`)
- [ ] Test semantic search (after indexing files)
- [ ] Verify embedding stats

---

## 📚 Related Documentation

- `MIMIR_RUNNING_STATUS.md` - Service status
- `MIMIR_ACCESS_URLS.md` - Access endpoints
- `MCP_INTEGRATION.md` - Kiro IDE integration
- `DOCKER.md` - Docker configuration

---

**Status**: ✅ EMBEDDINGS ENABLED  
**Provider**: GitHub Copilot  
**Model**: text-embedding-3-small (1536 dimensions)  
**Vector Search**: Ready for use
