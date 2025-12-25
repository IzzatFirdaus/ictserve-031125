# Environment Security Guidelines

## 🚨 CRITICAL: Environment File Security

This document outlines security practices for handling environment files in ICTServe to prevent accidental exposure of API keys and secrets.

## Files That Must NEVER Be Committed

The following environment files are **NEVER** committed to version control and are listed in `.gitignore`:

```
.env                    # Main environment file (contains secrets)
.env.bak               # Local backup of .env with API keys
.env.laragon           # Laragon-specific configuration
.env.workspace         # Docker workspace configuration  
.env.docker            # Docker-specific configuration
.env.testing           # Testing environment configuration
.env.staging           # Staging environment configuration
.env.xampp             # XAMPP-specific configuration
.env.production        # Production configuration
.env.local             # Local development overrides
.env.nova              # Laravel Nova configuration
```

## Safe Environment Files (Can Be Committed)

These template files are safe to commit as they contain no secrets:

```
.env.example           # Main template file
.env.generated.example # Generated template
.env.local.example     # Local development template
.env.production.example # Production template
```

## Setup Workflow for Developers

### 1. Initial Setup

```bash
# Copy the example file
cp .env.example .env

# Configure your local environment
# Add your API keys, database credentials, etc.
```

### 2. Create Local Backup

```bash
# Create a backup of your configured .env
cp .env .env.bak

# This backup is automatically ignored by Git
```

### 3. Environment Switching

Use the provided scripts for switching between environments:

```bash
# Switch to Laragon environment
.\scripts\switch-env.ps1 -env laragon

# Switch to Docker environment  
.\scripts\switch-env.ps1 -env workspace
```

## What Happened (Security Incident)

**Issue**: Environment files containing configuration templates were accidentally committed to version control in commit `1b04529a`.

**Resolution**: 
- Removed all environment-specific `.env.*` files from Git tracking
- Added comprehensive `.env.*` patterns to `.gitignore`
- Created this security documentation

**Impact**: No actual secrets were exposed (files contained only templates), but this prevents future incidents.

## Best Practices

### ✅ DO:
- Use `.env.example` as your template
- Keep API keys and secrets in local `.env` files only
- Use `.env.bak` for local backups
- Copy environment-specific templates to `.env` for local use
- Use environment variables for secrets in production

### ❌ DON'T:
- Commit any `.env` file with actual values
- Share `.env` files via chat, email, or other channels
- Store secrets in code or configuration files
- Use real API keys in example files

## Recovery from Secret Exposure

If secrets are accidentally committed:

1. **Immediately rotate all exposed credentials**
2. **Rewrite Git history to remove the secrets**:
   ```bash
   git filter-branch --force --index-filter \
   'git rm --cached --ignore-unmatch .env.laragon .env.workspace' \
   --prune-empty --tag-name-filter cat -- --all
   ```
3. **Force push to all remotes**:
   ```bash
   git push --force --all
   git push --force --tags
   ```
4. **Notify all team members to re-clone the repository**

## Environment File Templates

### Laragon Configuration
Use `.env.laragon` template for local Laragon development with MySQL and Redis.

### Docker Configuration  
Use `.env.workspace` template for Docker-based development with containerized services.

### Production Configuration
Use `.env.production.example` as a template for production deployments.

## Monitoring and Alerts

- All `.env.*` files are now properly ignored
- Git hooks can be added to prevent accidental commits
- CI/CD pipelines should validate no secrets are in commits

## Contact

For security concerns or questions about environment configuration:
- Create an issue in the repository
- Contact the development team lead
- Follow the incident response procedure for security issues

---

**Remember**: When in doubt, don't commit it. Environment files should remain local-only.
