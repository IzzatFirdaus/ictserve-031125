# Neo4j Knowledge Graph Guide

**Primary Knowledge Source for ICTServe Project**

This guide explains how to query and maintain the Neo4j knowledge graph, which serves as the single source of truth for 93% of operational documentation.

---

## 🎯 Quick Start

### Access Points

- **Mimir Portal**: <http://localhost:9042/portal>
- **Neo4j Browser**: <http://localhost:7474> (user: neo4j, password: see Mimir/.env)
- **Mimir API**: <http://localhost:9042/api/memory/>*
- **Health Check**: <http://localhost:9042/health>

### Start Services

```powershell
cd Mimir
docker compose up -d

# Verify services
docker ps --filter "name=mimir" --filter "name=neo4j"
```

---

## 📊 Knowledge Graph Structure

### Entity Types (55 total)

| Type | Count | Description |
|------|-------|-------------|
| canonical_document | 5 | D00-D15 system documentation |
| work_session | 16 | Development session records |
| analysis_work | 7 | Analysis and gap studies |
| technical_implementation | 7 | Feature implementations |
| documentation_guide | 2 | Setup and configuration guides |
| troubleshooting_guide | 4 | E2E triage and debugging |
| Other types | 14 | Various knowledge domains |

### Relation Types (99+ total)

| Type | Description | Example |
|------|-------------|---------|
| documents | Links to canonical docs | MCP_Developer_Guide → D11_Technical_Design |
| implements | Links to requirements | Dashboard_Performance → D03_Software_Requirements |
| uses | Component dependencies | Broadcasting_Setup → Laravel_Echo |
| related_to | General relationships | Performance_Guide → Deployment_Checklist |

---

## 🔍 Common Queries

### Mimir API (curl)

```bash
# Search by keyword
curl "http://localhost:9042/api/memory/search?q=performance"
curl "http://localhost:9042/api/memory/search?q=accessibility"
curl "http://localhost:9042/api/memory/search?q=MCP"

# Get specific entity
curl "http://localhost:9042/api/memory/entity/MCP_Developer_Guide"

# List all entities
curl "http://localhost:9042/api/memory/entities"

# Get statistics
curl "http://localhost:9042/api/memory/stats"

# Get entity relations
curl "http://localhost:9042/api/memory/relations/Performance_Optimization_Guide"
```

### Neo4j Browser (Cypher)

```cypher
// Find all troubleshooting guides
MATCH (n {entityType: 'troubleshooting_guide'})
RETURN n.name, n.observations
LIMIT 10;

// Find entities documenting D03 requirements
MATCH (n)-[:documents]->(d {name: 'D03_Software_Requirements'})
RETURN n.name, n.entityType, n.observations;

// Find all performance-related entities
MATCH (n)
WHERE n.name CONTAINS 'Performance'
RETURN n.name, n.entityType, size(n.observations) as obs_count;

// Get entity with all relations
MATCH (n {name: 'MCP_Developer_Guide'})-[r]-(related)
RETURN n, r, related;

// Find shortest path between two entities
MATCH path = shortestPath(
  (a {name: 'Performance_Optimization_Guide'})-[*]-(b {name: 'D11_Technical_Design'})
)
RETURN path;
```

---

## 📝 Entity Discovery Patterns

### By Topic

**Performance**:

- Performance_Optimization_Guide
- Helpdesk_Performance_Triage_Guide
- Loan_Performance_Triage_Guide
- Dashboard_Performance_Optimization_Implementation

**Accessibility**:

- Loan_Accessibility_Triage_Guide
- D12_UI_UX_Design_Guide
- D14_UI_UX_Style_Guide

**MCP Setup**:

- MCP_Developer_Guide
- GitHub_MCP_Server_Setup
- DevTools_MCP_Getting_Started_Guide

**Deployment**:

- Production_Deployment_Guide
- Production_Deployment_Checklist
- Docker_Database_Troubleshooting

### By Requirement

**D03 (Software Requirements)**:

- Helpdesk_Performance_Triage_Guide
- Loan_Accessibility_Triage_Guide
- Loan_Performance_Triage_Guide

**D11 (Technical Design)**:

- MCP_Developer_Guide
- Performance_Optimization_Guide
- Helpdesk_Performance_Triage_Guide

**D12 (UI/UX Accessibility)**:

- Loan_Accessibility_Triage_Guide
- D14_UI_UX_Style_Guide

---

## 🔧 Maintenance Operations

### Add Observation to Existing Entity

```bash
curl -X POST http://localhost:9042/api/memory/add-observation \
  -H "Content-Type: application/json" \
  -d '{
    "entityName": "MCP_Developer_Guide",
    "observation": "New setup step: Configure VS Code workspace settings"
  }'
```

### Create New Entity

```bash
curl -X POST http://localhost:9042/api/memory/create-entity \
  -H "Content-Type: application/json" \
  -d '{
    "name": "New_Feature_Implementation",
    "entityType": "technical_implementation",
    "observations": [
      "Feature: Real-time notifications",
      "Technology: Laravel Echo + Pusher",
      "Status: In Progress"
    ]
  }'
```

### Create Relation

```bash
curl -X POST http://localhost:9042/api/memory/create-relation \
  -H "Content-Type: application/json" \
  -d '{
    "from": "New_Feature_Implementation",
    "relationType": "documents",
    "to": "D03_Software_Requirements"
  }'
```

### Delete Entity

```bash
curl -X DELETE http://localhost:9042/api/memory/entity/Entity_Name
```

---

## 📚 Entity Naming Conventions

### Format
`{Domain}_{Purpose}_{Type}` (e.g., MCP_Developer_Guide, Helpdesk_Performance_Triage_Guide)

### Best Practices

- Use descriptive, searchable names
- Include domain/module prefix (MCP, Helpdesk, Loan, etc.)
- Use underscores for word separation
- Avoid abbreviations unless widely known
- Keep names under 50 characters

### Examples
✅ **Good**: MCP_Developer_Guide, Loan_Accessibility_Triage_Guide  
❌ **Bad**: guide1, mcp_dev, loan_acc

---

## 🔄 Backup & Recovery

### Export Knowledge Graph

```bash
# Export all entities to JSON
curl "http://localhost:9042/api/memory/export" > backup-$(date +%Y%m%d).json

# Export specific entity
curl "http://localhost:9042/api/memory/entity/MCP_Developer_Guide" > mcp-guide-backup.json
```

### Import Knowledge Graph

```bash
# Import from JSON file
curl -X POST http://localhost:9042/api/memory/import \
  -H "Content-Type: application/json" \
  -d @backup-20251123.json
```

### Neo4j Database Backup

```bash
# Stop Neo4j
docker compose stop neo4j_db

# Backup data directory
cp -r Mimir/neo4j-data Mimir/neo4j-data-backup-$(date +%Y%m%d)

# Restart Neo4j
docker compose start neo4j_db
```

---

## 🐛 Troubleshooting

### Neo4j Not Responding

```bash
# Check container status
docker ps --filter "name=neo4j"

# Check logs
docker logs mimir_neo4j_db --tail=50

# Restart Neo4j
docker compose restart neo4j_db
```

### Mimir API Errors

```bash
# Check Mimir logs
docker logs mimir_server --tail=50

# Verify health
curl http://localhost:9042/health

# Restart Mimir
docker compose restart mimir_server
```

### Entity Not Found

```bash
# List all entities
curl "http://localhost:9042/api/memory/entities" | jq '.[] | .name'

# Search for entity
curl "http://localhost:9042/api/memory/search?q=partial_name"

# Check Neo4j directly
# Open http://localhost:7474 and run:
# MATCH (n) WHERE n.name CONTAINS 'partial_name' RETURN n;
```

---

## 📈 Performance Tips

### Query Optimization

- Use specific entity names instead of broad searches
- Limit result sets with `LIMIT` in Cypher queries
- Index frequently queried properties in Neo4j

### Caching

- Mimir caches frequently accessed entities
- Clear cache: `docker compose restart mimir_server`

### Monitoring

```bash
# Check memory usage
docker stats mimir_neo4j_db mimir_server

# Check entity count
curl "http://localhost:9042/api/memory/stats"
```

---

## 🔐 Security

### Access Control

- Neo4j password stored in `Mimir/.env` (not committed)
- Mimir API accessible only on localhost by default
- Production: Use authentication middleware

### Best Practices

- Rotate Neo4j password regularly
- Backup knowledge graph weekly
- Validate entity data before import
- Use read-only queries for exploration

---

## 📖 Additional Resources

### Documentation

- **Mimir Setup**: docs/mimir.md
- **MCP Servers**: docs/DEVELOPERS_MCP.md (migrated to Neo4j)
- **Consolidation Report**: CONSOLIDATION_COMPLETE_2025-11-23.md

### External Links

- **Neo4j Documentation**: <https://neo4j.com/docs/>
- **Cypher Query Language**: <https://neo4j.com/docs/cypher-manual/>
- **Mimir GitHub**: <https://github.com/mimir-org/mimir>

---

**Knowledge Graph Status**: 55 entities, 209 observations, 99+ relations  
**Consolidation**: 93% complete (13 of 14 operational docs)  
**Last Updated**: November 23, 2025
