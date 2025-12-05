# Neo4j Knowledge Graph Guide

**Version**: 4.1.0  
**Last Updated**: 2025-12-05  
**Status**: ✅ Operational

---

## Overview

Mimir uses Neo4j as its knowledge graph database, storing entities, relationships, and observations. This guide covers querying, maintaining, and optimizing your knowledge graph.

---

## Access Points

### Web Interfaces

- **Mimir Portal**: <http://localhost:9042/portal>
- **Neo4j Browser**: <http://localhost:7474>
- **Health Check**: <http://localhost:9042/health>

### Credentials

```env
# From Mimir/.env
NEO4J_USER=neo4j
NEO4J_PASSWORD=MxXhTKH3qntipYLa1e0QOluJ
```

---

## Knowledge Graph Structure

### Entity Types

Common entity types in Mimir:

| Type | Description | Example |
|------|-------------|---------|
| `user_request` | User task requests | "user_request_2025_12_05_fix_bug" |
| `memory` | Knowledge entries | "Laravel_Authentication_Pattern" |
| `todo` | Task items | "Implement email notifications" |
| `todoList` | Task collections | "Sprint_1_Tasks" |
| `file` | Indexed files | "app/Models/User.php" |
| `file_chunk` | File segments | "User.php:lines_1-50" |
| `concept` | Abstract concepts | "Authentication_Strategy" |
| `project` | Project entities | "ICTServe_v3.5.0" |

### Relationship Types

Common relationship types:

| Type | Description | Example |
|------|-------------|---------|
| `contains` | Containment | Project → TodoList |
| `depends_on` | Dependencies | Feature → Library |
| `relates_to` | General relation | Memory → Concept |
| `implements` | Implementation | Code → Requirement |
| `calls` | Function calls | Controller → Service |
| `imports` | Module imports | File → Dependency |
| `assigned_to` | Task assignment | Todo → User |
| `parent_of` | Hierarchy | TodoList → Todo |
| `blocks` | Blocking relation | Task → Task |
| `references` | References | Doc → Code |

---

## Querying with Cypher

### Basic Queries

```cypher
// Find all entities of a type
MATCH (n {entityType: 'memory'})
RETURN n.name, n.observations
LIMIT 10;

// Search by name pattern
MATCH (n)
WHERE n.name CONTAINS 'Authentication'
RETURN n.name, n.entityType, n.observations;

// Count entities by type
MATCH (n)
RETURN n.entityType, count(*) as count
ORDER BY count DESC;

// Find recent entities
MATCH (n)
WHERE n.created_at IS NOT NULL
RETURN n.name, n.entityType, n.created_at
ORDER BY n.created_at DESC
LIMIT 20;
```

### Relationship Queries

```cypher
// Find all relationships for an entity
MATCH (n {name: 'ICTServe_System'})-[r]-(related)
RETURN n, r, related;

// Find entities with specific relationship
MATCH (n)-[:implements]->(req)
WHERE req.entityType = 'requirement'
RETURN n.name, req.name;

// Find shortest path between entities
MATCH path = shortestPath(
  (a {name: 'User_Authentication'})-[*]-(b {name: 'Security_Policy'})
)
RETURN path;

// Find all connected entities (depth 2)
MATCH (n {name: 'ICTServe_System'})-[*1..2]-(related)
RETURN DISTINCT related.name, related.entityType;
```

### Advanced Queries

```cypher
// Find entities with most relationships
MATCH (n)-[r]-()
RETURN n.name, n.entityType, count(r) as rel_count
ORDER BY rel_count DESC
LIMIT 10;

// Find orphaned entities (no relationships)
MATCH (n)
WHERE NOT (n)-[]-()
RETURN n.name, n.entityType;

// Find circular dependencies
MATCH path = (n)-[:depends_on*]->(n)
RETURN path;

// Find entities by observation content
MATCH (n)
WHERE any(obs IN n.observations WHERE obs CONTAINS 'Laravel')
RETURN n.name, n.entityType, n.observations;
```

---

## Mimir MCP Tools

### Query Entities

```javascript
// Search nodes by query
memory_node({
  operation: "search",
  query: "authentication implementation",
  options: {
    limit: 10,
    types: ["memory", "file_chunk"]
  }
})

// Query by filters
memory_node({
  operation: "query",
  type: "todo",
  filters: {
    status: "pending",
    priority: "high"
  }
})

// Get specific entity
memory_node({
  operation: "get",
  id: "memory-123"
})
```

### Manage Relationships

```javascript
// Get entity relationships
memory_edge({
  operation: "get",
  node_id: "memory-123",
  direction: "both"
})

// Find neighbors
memory_edge({
  operation: "neighbors",
  node_id: "memory-123",
  edge_type: "relates_to",
  depth: 2
})

// Get subgraph
memory_edge({
  operation: "subgraph",
  node_id: "project-1",
  depth: 2
})
```

---

## Maintenance Operations

### Add Observations

```javascript
// Add observations to existing entity
memory_node({
  operation: "update",
  id: "memory-123",
  properties: {
    observations: [
      ...existing_observations,
      "New observation: Updated authentication flow"
    ]
  }
})
```

### Create Entities

```javascript
// Create new entity
memory_node({
  operation: "add",
  type: "memory",
  properties: {
    name: "Email_Notification_Pattern",
    title: "Email Notification Implementation",
    content: "Pattern for sending email notifications...",
    tags: ["email", "notifications", "laravel"]
  }
})
```

### Create Relationships

```javascript
// Link entities
memory_edge({
  operation: "add",
  source: "memory-123",
  target: "concept-456",
  type: "relates_to",
  properties: {
    strength: "strong",
    context: "implementation pattern"
  }
})
```

### Delete Operations

```javascript
// Delete entity (requires confirmation)
memory_node({
  operation: "delete",
  id: "memory-123"
})
// Returns confirmationId

// Confirm deletion
memory_node({
  operation: "delete",
  id: "memory-123",
  confirm: true,
  confirmationId: "conf-xyz"
})
```

---

## Backup & Recovery

### Export Knowledge Graph

```bash
# Export via Neo4j Browser
# Run in Neo4j Browser (http://localhost:7474):
CALL apoc.export.json.all("backup.json", {})

# Or use Neo4j dump
docker exec mimir_neo4j_db neo4j-admin database dump neo4j \
  --to-path=/backups
```

### Import Knowledge Graph

```bash
# Import via Neo4j Browser
CALL apoc.import.json("backup.json")

# Or restore from dump
docker exec mimir_neo4j_db neo4j-admin database load neo4j \
  --from-path=/backups/neo4j.dump
```

### Backup Neo4j Data Directory

```powershell
# Stop Neo4j
cd Mimir
docker compose stop neo4j_db

# Backup data directory
Copy-Item -Recurse neo4j-data "neo4j-data-backup-$(Get-Date -Format 'yyyyMMdd')"

# Restart Neo4j
docker compose start neo4j_db
```

---

## Performance Optimization

### Indexing

```cypher
// Create index on name property
CREATE INDEX entity_name_index FOR (n:Entity) ON (n.name);

// Create index on entityType
CREATE INDEX entity_type_index FOR (n:Entity) ON (n.entityType);

// Create composite index
CREATE INDEX entity_name_type_index FOR (n:Entity) ON (n.name, n.entityType);

// List all indexes
SHOW INDEXES;
```

### Query Optimization

```cypher
// Use EXPLAIN to see query plan
EXPLAIN
MATCH (n {name: 'ICTServe_System'})-[r]-(related)
RETURN n, r, related;

// Use PROFILE to see execution stats
PROFILE
MATCH (n {name: 'ICTServe_System'})-[r]-(related)
RETURN n, r, related;

// Limit results
MATCH (n)
RETURN n
LIMIT 100;

// Use indexes
MATCH (n:Entity {name: 'ICTServe_System'})
RETURN n;
```

### Monitoring

```powershell
# Check Neo4j memory usage
docker stats neo4j_db

# Check database size
docker exec neo4j_db du -sh /data

# View slow queries (in Neo4j Browser)
CALL dbms.listQueries()
```

---

## Troubleshooting

### Neo4j Not Responding

```powershell
# Check container status
docker ps | Select-String "neo4j"

# View logs
docker logs neo4j_db --tail 50

# Restart Neo4j
cd Mimir
docker compose restart neo4j_db

# Wait for health check
Start-Sleep -Seconds 10
docker ps | Select-String "neo4j"
```

### Connection Refused

**Check Neo4j is running**:

```powershell
docker ps | Select-String "neo4j"
```

**Verify ports**:

```powershell
netstat -an | Select-String "7474|7687"
```

**Check firewall**:

```powershell
# Allow ports in Windows Firewall
New-NetFirewallRule -DisplayName "Neo4j HTTP" -Direction Inbound -LocalPort 7474 -Protocol TCP -Action Allow
New-NetFirewallRule -DisplayName "Neo4j Bolt" -Direction Inbound -LocalPort 7687 -Protocol TCP -Action Allow
```

### Entity Not Found

```javascript
// List all entities
memory_node({
  operation: "query",
  type: "memory"
})

// Search for entity
memory_node({
  operation: "search",
  query: "partial_name"
})
```

### Slow Queries

**Add indexes**:

```cypher
CREATE INDEX entity_name_index FOR (n:Entity) ON (n.name);
```

**Limit result sets**:

```cypher
MATCH (n)
RETURN n
LIMIT 100;
```

**Use specific queries**:

```cypher
// ❌ Slow
MATCH (n)
WHERE n.name CONTAINS 'Auth'
RETURN n;

// ✅ Fast
MATCH (n {name: 'Authentication_Pattern'})
RETURN n;
```

---

## Security Best Practices

### Access Control

- Neo4j password stored in `Mimir/.env` (not committed to git)
- Mimir API accessible only on localhost by default
- Production: Use authentication middleware

### Password Management

```powershell
# Change Neo4j password
docker exec -it neo4j_db cypher-shell -u neo4j -p old_password
ALTER CURRENT USER SET PASSWORD FROM 'old_password' TO 'new_password';

# Update Mimir/.env
# NEO4J_PASSWORD=new_password

# Restart Mimir
cd Mimir
docker compose restart mimir-server
```

### Backup Strategy

- Backup knowledge graph weekly
- Store backups in secure location
- Test restore procedure regularly
- Version control backup scripts

---

## Entity Naming Conventions

### Format

`{Domain}_{Purpose}_{Type}`

### Examples

✅ **Good**:

- `ICTServe_Authentication_Implementation`
- `Helpdesk_Performance_Optimization`
- `User_Request_2025_12_05_Fix_Bug`

❌ **Bad**:

- `auth1` (too vague)
- `implementation` (no context)
- `fix` (not descriptive)

### Best Practices

- Use descriptive, searchable names
- Include domain/module prefix
- Use underscores for word separation
- Avoid abbreviations unless widely known
- Keep names under 60 characters
- Include dates for time-sensitive entities

---

## Related Documentation

- **[01-SETUP.md](01-SETUP.md)** - Mimir installation
- **[02-DOCKER.md](02-DOCKER.md)** - Docker configuration
- **[06-API-REFERENCE.md](06-API-REFERENCE.md)** - MCP tools reference
- **[08-EMBEDDINGS.md](08-EMBEDDINGS.md)** - Vector embeddings

---

## External Resources

- [Neo4j Documentation](https://neo4j.com/docs/)
- [Cypher Query Language](https://neo4j.com/docs/cypher-manual/)
- [Neo4j Browser Guide](https://neo4j.com/docs/browser-manual/)
- [APOC Procedures](https://neo4j.com/labs/apoc/)

---

**Last Updated**: 2025-12-05  
**Neo4j Version**: 5.15-community  
**Status**: ✅ Operational
