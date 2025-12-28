# Reports Directory

This directory contains generated reports, logs, and media from automation script executions.

## Directory Structure

```
reports/
├── execution-logs/     # Script execution logs (JSON format)
├── screenshots/        # Visual demonstration screenshots
├── videos/             # Recorded demonstration videos
└── analytics/          # Performance and coverage analytics
```

## Report Types

### Execution Logs

- JSON format with test results, timing, and error details
- Named: `{category}-{timestamp}.json`

### Screenshots

- PNG format captured at key workflow steps
- Named: `{test-name}-{step}-{timestamp}.png`

### Videos

- MP4 format for training and documentation
- Named: `{workflow-name}-{timestamp}.mp4`

### Analytics

- HTML dashboards with test coverage and performance metrics
- CSV exports for data analysis

## Cleanup

Old reports are automatically cleaned up based on retention settings in `config/settings.json`.
