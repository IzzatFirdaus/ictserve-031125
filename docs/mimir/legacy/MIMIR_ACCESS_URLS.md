# Mimir Access URLs - Quick Reference

**Port Mapping**: Container port 3000 → Host port **9042**

---

## ✅ Correct URLs (Port 9042)

### Mimir Interfaces

| Interface | URL | Purpose |
|-----------|-----|---------|
| **Portal UI** | <http://localhost:9042/portal> | Web interface for memory management |
| **Orchestration Studio** | <http://localhost:9042/studio> | Workflow orchestration interface |
| **MCP Endpoint** | <http://localhost:9042/mcp> | MCP protocol endpoint (for Kiro IDE) ⚠️ Requires SSE headers |
| **Health Check** | <http://localhost:9042/health> | Service health status |

### Neo4j Database

| Interface | URL | Purpose |
|-----------|-----|---------|
| **Neo4j Browser** | <http://localhost:7474> | Graph database web interface |
| **Bolt Protocol** | bolt://localhost:7687 | Direct database connection |

**Credentials**: neo4j / MxXhTKH3qntipYLa1e0QOluJ

### Copilot API

| Interface | URL | Purpose |
|-----------|-----|---------|
| **Chat Completions** | <http://localhost:4141/v1/chat/completions> | OpenAI-compatible chat API |
| **Embeddings** | <http://localhost:4141/v1/embeddings> | Text embeddings API |

---

## ❌ Incorrect URLs (Port 3000)

These URLs will **NOT work** because port 3000 is internal to the container:

- ~~<http://localhost:3000/portal>~~ ❌
- ~~<http://localhost:3000/studio>~~ ❌
- ~~<http://localhost:3000/mcp>~~ ❌
- ~~<http://localhost:3000/health>~~ ❌

---

## 🔍 Port Mapping Explanation

Docker maps the container's internal port to a different external port:

```
Container (internal) → Host (external)
     3000           →      9042
```

**Why?** Port 3000 might be used by other services (like Laravel Vite dev server), so Mimir uses 9042 to avoid conflicts.

---

## 🌐 Open in Browser

Click these links to access Mimir interfaces:

1. **[Mimir Portal](http://localhost:9042/portal)** - Main web interface
2. **[Orchestration Studio](http://localhost:9042/studio)** - Workflow management
3. **[Neo4j Browser](http://localhost:7474)** - Database interface
4. **[Health Check](http://localhost:9042/health)** - Service status

---

## 🔧 Verify Access

### PowerShell Commands

```powershell
# Test Mimir Portal
curl http://localhost:9042/portal -Method HEAD

# Test Health Endpoint
curl http://localhost:9042/health

# Test Neo4j Browser
curl http://localhost:7474 -Method HEAD

# Test MCP Endpoint
curl http://localhost:9042/mcp -Method POST -Headers @{"Content-Type"="application/json"} -Body '{"jsonrpc":"2.0","method":"initialize","params":{},"id":1}'
```

### Expected Results

All should return **StatusCode: 200**

---

## 🐛 Troubleshooting

### "Connection Refused" Error

**Problem**: Trying to access port 3000 instead of 9042

**Solution**: Use port **9042** for all Mimir URLs

### "Site Can't Be Reached"

**Check Docker Status**:

```powershell
docker ps --filter "name=mimir"
```

**Expected Output**:

```
NAMES          PORTS
mimir_server   0.0.0.0:9042->3000/tcp
```

**If Not Running**:

```powershell
cd Mimir
docker compose up -d
```

### Port Already in Use

**Check What's Using Port 9042**:

```powershell
netstat -ano | findstr :9042
```

**If Conflict Exists**:

1. Stop the conflicting service
2. Or change Mimir port in `docker-compose.yml`:

   ```yaml
   ports:
     - "9043:3000"  # Use different port
   ```

---

## 📱 Mobile/Remote Access

To access from other devices on your network:

1. Find your computer's IP address:

   ```powershell
   ipconfig | findstr IPv4
   ```

2. Use your IP instead of localhost:

   ```
   http://192.168.x.x:9042/portal
   http://192.168.x.x:7474  (Neo4j)
   ```

**Note**: Ensure Windows Firewall allows incoming connections on ports 9042 and 7474.

---

## 🔐 Security Note

These services are running **without authentication** in development mode:

- Mimir Portal: No login required
- Neo4j Browser: Requires credentials (neo4j / MxXhTKH3qntipYLa1e0QOluJ)
- Copilot API: No authentication

**For Production**: Enable authentication via environment variables in `Mimir/.env`:

```env
MIMIR_AUTH_PROVIDER=oauth
MIMIR_JWT_SECRET=your-secret-key
```

---

## 📚 Related Documentation

- `MIMIR_RUNNING_STATUS.md` - Service status and management
- `DOCKER.md` - Docker configuration details
- `MCP_INTEGRATION.md` - Kiro IDE integration

---

**Quick Reference Card**  
**Mimir Portal**: <http://localhost:9042/portal>  
**Neo4j Browser**: <http://localhost:7474>  
**MCP Endpoint**: <http://localhost:9042/mcp>  
**Health Check**: <http://localhost:9042/health>

---

## ⚠️ Important: MCP Endpoint Behavior

### Browser Access

If you access <http://localhost:9042/mcp> in a browser, you'll see this error:

```json
{"jsonrpc":"2.0","error":{"code":-32000,"message":"Not Acceptable: Client must accept text/event-stream"},"id":null}
```

**This is CORRECT and EXPECTED!** ✅

The MCP endpoint uses **Server-Sent Events (SSE)** protocol, which requires the `Accept: text/event-stream` header. Browsers don't send this header by default, so you get the error.

### Kiro IDE Connection

Kiro IDE will connect properly because it sends the correct headers:

```
Accept: text/event-stream
Content-Type: application/json
```

### Manual Testing

To test the MCP endpoint manually, use PowerShell with proper headers:

```powershell
curl http://localhost:9042/mcp `
  -Method POST `
  -Headers @{
    "Content-Type"="application/json"
    "Accept"="text/event-stream"
  } `
  -Body '{"jsonrpc":"2.0","method":"initialize","params":{"protocolVersion":"2024-11-05","capabilities":{},"clientInfo":{"name":"test","version":"1.0"}},"id":1}'
```

**Expected Response** (200 OK):

```json
{
  "jsonrpc":"2.0",
  "id":1,
  "result":{
    "protocolVersion":"2024-11-05",
    "capabilities":{"tools":{}},
    "serverInfo":{
      "name":"Mimir-RAG-TODO-MCP",
      "version":"4.0.0",
      "sessionId":"shared-global-session"
    }
  }
}
```

---

## ✅ Connection Status Summary

| Endpoint | Browser Access | Kiro IDE Access | Status |
|----------|---------------|-----------------|--------|
| Portal (9042/portal) | ✅ Works | ✅ Works | Ready |
| Health (9042/health) | ✅ Works | ✅ Works | Ready |
| MCP (9042/mcp) | ❌ Shows error | ✅ Works | Ready |
| Neo4j (7474) | ✅ Works | ✅ Works | Ready |

**Conclusion**: All services are working correctly. The MCP endpoint error in browser is expected behavior.
