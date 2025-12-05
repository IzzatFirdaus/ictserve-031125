# Mimir Submodule Management

**Version**: 4.1.0  
**Last Updated**: 2025-12-05  
**Status**: ✅ Current

---

## Overview

Mimir is integrated as a Git submodule in ICTServe, allowing independent version control while maintaining seamless integration. This guide covers all submodule operations.

---

## Initial Setup

### Clone Repository with Submodules

```bash
# Clone with all submodules
git clone --recurse-submodules https://github.com/IzzatFirdaus/ictserve-031125.git

# Or if already cloned without submodules
cd ictserve-031125
git submodule update --init --recursive
```

### Verify Submodule Status

```bash
# Check submodule status
git submodule status

# Expected output:
# <commit-hash> Mimir (heads/main)

# Detailed status
git submodule foreach git status
```

---

## Common Operations

### Update Submodule to Latest

```bash
# Navigate to submodule directory
cd Mimir

# Pull latest changes from main branch
git pull origin main

# Return to parent repository
cd ..

# Stage the submodule update
git add Mimir

# Commit the update
git commit -m "Update Mimir submodule to latest version"

# Push to remote
git push origin main
```

### Update All Submodules

```bash
# Update to latest commit on tracked branch
git submodule update --remote

# Update and merge changes
git submodule update --remote --merge

# Update and rebase changes
git submodule update --remote --rebase
```

### Make Changes in Submodule

```bash
# Navigate to submodule
cd Mimir

# Create feature branch
git checkout -b feature/my-feature

# Make your changes
# ... edit files ...

# Commit changes
git add .
git commit -m "feat: add new feature"

# Push to Mimir repository
git push origin feature/my-feature

# Return to parent repository
cd ..

# Parent now points to your branch commit
git add Mimir
git commit -m "Update Mimir submodule (feature branch)"
```

### Switch Submodule Branch

```bash
# Navigate to submodule
cd Mimir

# Switch to different branch
git checkout develop

# Pull latest changes
git pull origin develop

# Return to parent
cd ..

# Update parent reference
git add Mimir
git commit -m "Switch Mimir to develop branch"
```

---

## Troubleshooting

### Submodule Not Initialized

**Symptom**: `Mimir/` directory is empty

**Solution**:

```bash
# Initialize and update submodule
git submodule update --init --recursive

# Verify
cd Mimir
ls -la
```

### Submodule in Detached HEAD State

**Symptom**: Git shows "HEAD detached at <commit>"

**Solution**:

```bash
# Navigate to submodule
cd Mimir

# Checkout main branch
git checkout main

# Pull latest
git pull origin main

# Return to parent
cd ..

# Update reference
git add Mimir
git commit -m "Update Mimir to main branch HEAD"
```

### Submodule Merge Conflicts

**Symptom**: Conflicts in `.gitmodules` or submodule reference

**Solution**:

```bash
# Accept their version
git checkout --theirs Mimir
git add Mimir

# Or accept our version
git checkout --ours Mimir
git add Mimir

# Continue merge
git merge --continue
```

### Reset Submodule to Parent's Reference

```bash
# Reset to commit referenced by parent
git submodule update --init

# Force reset (discards local changes)
git submodule update --init --force
```

### Remove Submodule (If Needed)

```bash
# Deinitialize submodule
git submodule deinit -f Mimir

# Remove from .git/modules
rm -rf .git/modules/Mimir

# Remove directory and .gitmodules entry
git rm -f Mimir

# Commit removal
git commit -m "Remove Mimir submodule"
```

---

## Best Practices

### 1. Always Commit Submodule Updates

```bash
# After updating submodule
cd Mimir
git pull origin main
cd ..

# Always commit the reference change
git add Mimir
git commit -m "chore: update Mimir submodule to v4.1.0"
```

### 2. Document Submodule Versions

```bash
# Tag parent repo with submodule version
git tag -a v3.5.0 -m "ICTServe v3.5.0 with Mimir v4.1.0"
git push origin v3.5.0
```

### 3. Pin to Specific Commits

```bash
# Pin to specific stable commit (not branch)
cd Mimir
git checkout <commit-hash>
cd ..

git add Mimir
git commit -m "Pin Mimir to stable commit <hash>"
```

### 4. Test Before Updating

```bash
# Test submodule changes locally first
cd Mimir
git pull origin main

# Run tests
docker compose up -d
docker logs mimir_server --tail 20

# If tests pass, commit update
cd ..
git add Mimir
git commit -m "Update Mimir (tests passing)"
```

### 5. Keep Submodule Clean

```bash
# Check for uncommitted changes
cd Mimir
git status

# Stash changes if needed
git stash

# Or commit them
git add .
git commit -m "WIP: local changes"
```

---

## CI/CD Integration

### GitHub Actions

```yaml
# .github/workflows/ci.yml
name: CI

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - name: Checkout with submodules
        uses: actions/checkout@v4
        with:
          submodules: recursive
      
      - name: Update submodules
        run: git submodule update --init --recursive
      
      - name: Test Mimir
        run: |
          cd Mimir
          docker compose up -d
          docker logs mimir_server --tail 20
```

### Docker Build

```dockerfile
# Dockerfile
FROM php:8.2-fpm

# Clone with submodules
RUN git clone --recurse-submodules \
    https://github.com/IzzatFirdaus/ictserve-031125.git /app

WORKDIR /app
```

---

## Submodule Information

### Current Configuration

- **Repository**: https://github.com/IzzatFirdaus/Mimir
- **Branch**: main
- **Version**: 4.1.0
- **Location**: `Mimir/` (project root)

### Submodule Structure

```
ictserve-031125/
├── .gitmodules              # Submodule configuration
├── Mimir/                   # Submodule directory
│   ├── .git                 # Submodule git metadata
│   ├── docker-compose.yml
│   ├── .env
│   └── ...
└── .git/
    └── modules/
        └── Mimir/           # Submodule git repository
```

---

## Quick Reference

### Check Status

```bash
# Submodule status
git submodule status

# Detailed info
git submodule foreach git status
```

### Update

```bash
# Update to latest
cd Mimir && git pull origin main && cd ..
git add Mimir && git commit -m "Update Mimir"
```

### Reset

```bash
# Reset to parent's reference
git submodule update --init --force
```

### Branch Operations

```bash
# Check current branch
cd Mimir && git branch

# Switch branch
cd Mimir && git checkout <branch>
```

---

## Related Documentation

- **[01-SETUP.md](01-SETUP.md)** - Mimir installation
- **[02-DOCKER.md](02-DOCKER.md)** - Docker deployment
- **[10-TROUBLESHOOTING.md](10-TROUBLESHOOTING.md)** - Common issues

---

## External Resources

- [Git Submodules Documentation](https://git-scm.com/book/en/v2/Git-Tools-Submodules)
- [Mimir Repository](https://github.com/IzzatFirdaus/Mimir)
- [ICTServe Repository](https://github.com/IzzatFirdaus/ictserve-031125)

---

**Last Updated**: 2025-12-05  
**Mimir Version**: 4.1.0  
**Status**: ✅ Production Ready
