# Mimir Memory System Setup Guide

## Overview
Mimir is an optional memory system that provides persistent knowledge graph storage using Neo4j. It's useful for AI agents to maintain context across sessions but is **NOT required** for the main ICTServe application.

## Current Status

- ❌ **Disabled in main docker-compose.yml** (by default)
- ✅ Mimir project available in `./Mimir/` subdirectory
- ⚠️ Requires properly configured Ollama models OR GitHub Copilot API access

## Issues Identified (2025-11-22)

### 1. Model Loading Error (llama_server)

```
gguf_init_from_file: failed to open GGUF file
'/models/models/blobs/sha256-819c2adf5ce6df2b6bd2ae4ca90d2a69f060afeb438d0c171db57daa02e39c3d'
```

**Cause**: The llama_server container expects Ollama models to be present at specific paths, but:

- You have Ollama installed on Windows host (not accessible to container)
- Model files are not mounted correctly to the container
- No API access to hosted Ollama service

### 2. Laravel Boost Error (Fixed)

```
Class "Laravel\Boost\BoostServiceProvider" not found
```

**Solution**: ✅ Fixed by running `composer require laravel/boost --dev`

## Solution Options

### Option 1: Disable Mimir (Recommended for Now)
**Status**: ✅ **DONE** - Already disabled in main docker-compose.yml

**Benefits**:

- ICTServe application runs without dependencies on Mimir
- No need for Ollama models or GPU configuration
- Simpler development environment

**To use ICTServe without Mimir**:

```powershell
# Just start the essential services
docker compose up -d app db

# Or run Laravel locally with XAMPP
php artisan serve
```

### Option 2: Run Mimir Separately (Advanced Users)
**Status**: ⚠️ Requires additional configuration

**Prerequisites**:

1. **Either** properly configured Ollama with models
   - Install models: `ollama pull mxbai-embed-large`
   - OR use hosted Ollama service with API key
2. **Or** GitHub Copilot API access (requires subscription)

**Steps to Enable**:

1. **Navigate to Mimir directory**:

   ```powershell
   cd Mimir
   ```

2. **Configure environment**:
   - Copy `env.example` to `.env`
   - Set your Ollama or Copilot API credentials
   - Configure Neo4j password

3. **Choose deployment mode**:

   **Option A - With Copilot API** (Recommended if you have GitHub Copilot):

   ```powershell
   # Use copilot-api for both LLM and embeddings
   docker compose up -d
   ```

   **Option B - With local Ollama**:

   ```powershell
   # Requires Ollama installed and running on host
   docker compose -f docker-compose.ollama.yml up -d
   ```

4. **Verify services**:

   ```powershell
   # Check all services are healthy
   docker compose ps
   
   # Check Neo4j Browser
   start http://localhost:7474
   
   # Check Mimir API
   curl http://localhost:9042/health
   ```

### Option 3: Use Copilot API Only (No Ollama)
**Status**: ⚠️ Requires GitHub Copilot subscription

To use Mimir with only Copilot API (no Ollama/llama_server):

1. **Edit `Mimir/docker-compose.yml`**:
   - Comment out the `llama-server` service entirely
   - Update mimir-server dependencies to remove llama-server

2. **Configure embeddings to use Copilot API**:

   ```yaml
   # In mimir-server environment:
   - MIMIR_EMBEDDINGS_API=http://copilot-api:4141
   - MIMIR_EMBEDDINGS_MODEL=text-embedding-ada-002  # Use OpenAI model
   ```

3. **Start services**:

   ```powershell
   cd Mimir
   docker compose up -d neo4j copilot-api mimir-server
   ```

## Configuration Reference

### Environment Variables for Mimir

If you enable Mimir, configure these in `Mimir/.env`:

```ini
# Neo4j Database
NEO4J_PASSWORD=your-secure-password-here
NEO4J_URI=bolt://neo4j:7687

# LLM Provider (choose one)
MIMIR_DEFAULT_PROVIDER=copilot  # or 'ollama'
MIMIR_LLM_API=http://copilot-api:4141  # or http://host.docker.internal:11434
MIMIR_DEFAULT_MODEL=gpt-4.1  # or your Ollama model name

# Embeddings
MIMIR_EMBEDDINGS_ENABLED=true
MIMIR_EMBEDDINGS_API=http://copilot-api:4141  # or llama-server
MIMIR_EMBEDDINGS_MODEL=text-embedding-ada-002  # or mxbai-embed-large

# Workspace
HOST_WORKSPACE_ROOT=/c/XAMPP/htdocs/ictserve-031125
```

### Ollama Model Setup (If Using Ollama)

If you choose to use Ollama:

1. **Install Ollama on Windows host**:

   ```powershell
   # Download from https://ollama.ai/download
   # Or use existing installation
   ```

2. **Pull required models**:

   ```powershell
   ollama pull mxbai-embed-large  # Embedding model (required)
   ollama pull deepseek-r1:7b     # Chat model (optional)
   ```

3. **Verify models are downloaded**:

   ```powershell
   ollama list
   ```

4. **Mount models to container**:
   - On Windows, Ollama stores models in `%USERPROFILE%\.ollama\models`
   - Update `docker-compose.yml` volume mount:

     ```yaml
     volumes:
       - C:/Users/YourUsername/.ollama/models:/models:ro
     ```

## Troubleshooting

### Issue: "failed to open GGUF file"
**Cause**: Model file not found or incorrect path

**Solution**:

1. Check if model is actually downloaded: `ollama list`
2. Verify volume mount path in docker-compose.yml
3. Check model file exists at expected location
4. Or disable llama-server entirely and use Copilot API

### Issue: "Class Laravel\Boost\BoostServiceProvider not found"
**Cause**: Laravel Boost package not installed

**Solution**: ✅ Already fixed

```powershell
composer require laravel/boost --dev
```

### Issue: Mimir service keeps restarting
**Cause**: Missing dependencies or incorrect configuration

**Solution**:

1. Check logs: `docker compose logs mimir-server`
2. Verify Neo4j is healthy: `docker compose ps`
3. Verify Copilot API is running: `curl http://localhost:4141`
4. Check all environment variables are set

## Recommended Path Forward

**For ICTServe development**:

1. ✅ Keep Mimir disabled (already done)
2. ✅ Use Laravel locally with XAMPP
3. ⚠️ Only enable Mimir if you need persistent memory across AI agent sessions

**If you need Mimir**:

1. Get GitHub Copilot subscription (easiest)
2. Use Copilot API only (no Ollama/llama_server)
3. Follow "Option 3" above

## Documentation Updates Needed

The following files reference Mimir but don't require it for core functionality:

- `mimir.md` - Migration guide (informational only)
- `.env.mimir` - Configuration template (not loaded unless explicitly used)
- `AGENTS.md` - Agent instructions (mentions Mimir but works without it)

**These do NOT need to be changed** - they're just guides for optional advanced features.

## Summary

✅ **Current State**: ICTServe works perfectly WITHOUT Mimir
❌ **Mimir Issues**: Model loading failures, configuration complexity
⚠️ **Recommendation**: Keep Mimir disabled unless you specifically need persistent memory graphs

**To run ICTServe now**:

```powershell
# Use XAMPP (easiest)
php artisan serve

# Or use Docker (without Mimir)
docker compose up -d app db
php artisan migrate
```

---
Last Updated: 2025-11-22
Status: Mimir optional, ICTServe functional without it
