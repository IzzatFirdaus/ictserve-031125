# Mimir Submodule Management

Guide for managing Mimir as a Git submodule in ICTServe.

## Overview

Mimir is integrated as a Git submodule, allowing independent version control while maintaining integration with ICTServe.

## Initial Setup

### Clone with Submodules

```bash
# Clone repository with submodules
git clone --recurse-submodules https://github.com/IzzatFirdaus/ictserve-031125.git

# Or if already cloned
git submodule update --init --recursive
```

### Verify Submodule

```bash
# Check submodule status
git submodule status

# Should show:
# <commit-hash> Mimir (heads/main)
```

## Working with Submodule

### Update Submodule to Latest

```bash
# Navigate to submodule
cd Mimir

# Pull latest changes
git pull origin main

# Return to parent repo
cd ..

# Stage submodule update
git add Mimir

# Commit update
git commit -m "Update Mimir submodule to latest"
```

### Make Changes in Submodule

```bash
# Navigate to submodule
cd Mimir

# Create branch
git checkout -b feature/my-feature

# Make changes
# ... edit files ...

# Commit changes
git add .
git commit -m "Add new feature"

# Push to Mimir repository
git push origin feature/my-feature

# Return to parent repo
cd ..

# Parent repo now points to your branch commit
git add Mimir
git commit -m "Update Mimir submodule (feature branch)"
```

### Switch Submodule Branch

```bash
# Navigate to submodule
cd Mimir

# Switch branch
git checkout develop

# Pull latest
git pull origin develop

# Return to parent
cd ..

# Update parent reference
git add Mimir
git commit -m "Switch Mimir to develop branch"
```

## Common Operations

### Check Submodule Status

```bash
# From parent repo
git submodule status

# Detailed status
git submodule foreach git status
```

### Update All Submodules

```bash
# Update to latest commit on tracked branch
git submodule update --remote

# Update and merge
git submodule update --remote --merge
```

### Reset Submodule

```bash
# Reset to commit referenced by parent
git submodule update --init

# Force reset
git submodule update --init --force
```

## Troubleshooting

### Submodule Not Initialized

**Symptom**: `Mimir/` directory empty

**Solution**:

```bash
git submodule update --init --recursive
```

### Submodule Detached HEAD

**Symptom**: Submodule in detached HEAD state

**Solution**:

```bash
cd Mimir
git checkout main
git pull origin main
cd ..
git add Mimir
git commit -m "Update Mimir to main branch"
```

### Submodule Conflicts

**Symptom**: Merge conflicts in `.gitmodules` or submodule reference

**Solution**:

```bash
# Accept theirs
git checkout --theirs Mimir
git add Mimir

# Or accept ours
git checkout --ours Mimir
git add Mimir

# Then continue merge
git merge --continue
```

### Remove Submodule

```bash
# Remove submodule entry
git submodule deinit -f Mimir

# Remove from .git/modules
rm -rf .git/modules/Mimir

# Remove directory
git rm -f Mimir

# Commit removal
git commit -m "Remove Mimir submodule"
```

## Best Practices

### 1. Always Commit Submodule Updates

```bash
# After updating submodule
cd Mimir
git pull origin main
cd ..
git add Mimir
git commit -m "Update Mimir submodule"
```

### 2. Document Submodule Version

```bash
# Tag parent repo with submodule version
git tag -a v1.0.0 -m "ICTServe v1.0.0 with Mimir v4.1.0"
```

### 3. Use Specific Commits

```bash
# Pin to specific commit (not branch)
cd Mimir
git checkout <commit-hash>
cd ..
git add Mimir
git commit -m "Pin Mimir to stable commit"
```

### 4. Test Before Updating

```bash
# Test submodule changes locally
cd Mimir
git pull origin main
make test
cd ..

# If tests pass, commit update
git add Mimir
git commit -m "Update Mimir (tests passing)"
```

## CI/CD Integration

### GitHub Actions

```yaml
# .github/workflows/ci.yml
jobs:
  test:
    steps:
      - uses: actions/checkout@v4
        with:
          submodules: recursive  # Clone submodules
      
      - name: Update submodules
        run: git submodule update --init --recursive
```

### Docker Build

```dockerfile
# Dockerfile
# Clone with submodules
RUN git clone --recurse-submodules https://github.com/IzzatFirdaus/ictserve-031125.git
```

## References

- [Git Submodules Documentation](https://git-scm.com/book/en/v2/Git-Tools-Submodules)
- [Mimir Repository](https://github.com/IzzatFirdaus/Mimir)
- [ICTServe Repository](https://github.com/IzzatFirdaus/ictserve-031125)

## Next Steps

- [Mimir Setup](SETUP.md) - Install and configure Mimir
- [Docker Deployment](DOCKER.md) - Deploy Mimir with Docker
- [MCP Integration](MCP_INTEGRATION.md) - AI agent integration
