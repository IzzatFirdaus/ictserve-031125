# Configuration Guide - ICTServe Automation Scripts

## Overview

This guide provides detailed instructions for configuring the ICTServe Comprehensive Automation Suite for different environments, credentials, browsers, and demonstration modes.

## Configuration File Structure

```
config/
├── environments.json           # Environment-specific settings
├── credentials.json           # Test user credentials (encrypted)
├── settings.json             # General script execution settings
├── browser-settings.json     # Browser automation configurations
├── demo-settings.json        # Visual demonstration configurations
└── ai-settings.json          # AI service configurations
```

## Environment Configuration

### environments.json

Configure different deployment environments for testing:

```json
{
  "development": {
    "name": "Development Environment",
    "baseUrl": "http://localhost:8000",
    "apiUrl": "http://localhost:8000/api",
    "database": {
      "host": "localhost",
      "port": 3306,
      "database": "ictserve_dev",
      "username": "dev_user",
      "password": "dev_password"
    },
    "redis": {
      "host": "localhost",
      "port": 6379,
      "database": 0
    },
    "email": {
      "driver": "log",
      "host": null,
      "port": null
    },
    "ai": {
      "ollama": {
        "enabled": true,
        "baseUrl": "http://localhost:11434",
        "models": ["llama2", "codellama"]
      },
      "bedrock": {
        "enabled": false,
        "region": "us-east-1",
        "models": []
      }
    },
    "debug": true,
    "logLevel": "debug",
    "timeout": 60000,
    "retryAttempts": 3
  },
  "testing": {
    "name": "Testing Environment",
    "baseUrl": "https://test.ictserve.motac.gov.my",
    "apiUrl": "https://test.ictserve.motac.gov.my/api",
    "database": {
      "host": "test-db.motac.gov.my",
      "port": 3306,
      "database": "ictserve_test",
      "username": "test_user",
      "password": "encrypted:test_password_hash"
    },
    "redis": {
      "host": "test-redis.motac.gov.my",
      "port": 6379,
      "database": 1
    },
    "email": {
      "driver": "smtp",
      "host": "smtp.motac.gov.my",
      "port": 587,
      "encryption": "tls"
    },
    "ai": {
      "ollama": {
        "enabled": true,
        "baseUrl": "https://test-ai.motac.gov.my:11434",
        "models": ["llama2", "codellama", "mistral"]
      },
      "bedrock": {
        "enabled": true,
        "region": "ap-southeast-1",
        "models": ["claude-3-sonnet", "claude-3-haiku"]
      }
    },
    "debug": false,
    "logLevel": "info",
    "timeout": 30000,
    "retryAttempts": 2
  },
  "staging": {
    "name": "Staging Environment",
    "baseUrl": "https://staging.ictserve.motac.gov.my",
    "apiUrl": "https://staging.ictserve.motac.gov.my/api",
    "database": {
      "host": "staging-db.motac.gov.my",
      "port": 3306,
      "database": "ictserve_staging",
      "username": "staging_user",
      "password": "encrypted:staging_password_hash"
    },
    "redis": {
      "host": "staging-redis.motac.gov.my",
      "port": 6379,
      "database": 2
    },
    "email": {
      "driver": "smtp",
      "host": "smtp.motac.gov.my",
      "port": 587,
      "encryption": "tls"
    },
    "ai": {
      "ollama": {
        "enabled": true,
        "baseUrl": "https://staging-ai.motac.gov.my:11434",
        "models": ["llama2", "codellama", "mistral", "neural-chat"]
      },
      "bedrock": {
        "enabled": true,
        "region": "ap-southeast-1",
        "models": ["claude-3-opus", "claude-3-sonnet", "claude-3-haiku"]
      }
    },
    "debug": false,
    "logLevel": "warning",
    "timeout": 30000,
    "retryAttempts": 2
  },
  "production": {
    "name": "Production Environment",
    "baseUrl": "https://ictserve.motac.gov.my",
    "apiUrl": "https://ictserve.motac.gov.my/api",
    "database": {
      "host": "prod-db.motac.gov.my",
      "port": 3306,
      "database": "ictserve_prod",
      "username": "prod_user",
      "password": "encrypted:prod_password_hash"
    },
    "redis": {
      "host": "prod-redis.motac.gov.my",
      "port": 6379,
      "database": 3
    },
    "email": {
      "driver": "smtp",
      "host": "smtp.motac.gov.my",
      "port": 587,
      "encryption": "tls"
    },
    "ai": {
      "ollama": {
        "enabled": true,
        "baseUrl": "https://ai.motac.gov.my:11434",
        "models": ["llama2", "codellama", "mistral", "neural-chat", "phi"]
      },
      "bedrock": {
        "enabled": true,
        "region": "ap-southeast-1",
        "models": ["claude-3-opus", "claude-3-sonnet", "claude-3-haiku", "claude-3-5-sonnet"]
      }
    },
    "debug": false,
    "logLevel": "error",
    "timeout": 20000,
    "retryAttempts": 1
  }
}
```

### Environment Selection

Set the active environment using:

```powershell
# PowerShell
$env:ICTSERVE_ENVIRONMENT = "testing"

# Command Prompt
set ICTSERVE_ENVIRONMENT=testing

# Or specify in script execution
.\Main-Menu.ps1 -Environment testing
```

## Credential Configuration

### credentials.json (Encrypted)

Store test user credentials securely:

```json
{
  "encryption": {
    "algorithm": "AES-256-GCM",
    "keyDerivation": "PBKDF2",
    "iterations": 100000
  },
  "credentials": {
    "guest": {
      "name": "Ahmad bin Abdullah",
      "email": "ahmad.test@motac.gov.my",
      "phone": "03-1234-5678",
      "department": "Bahagian Pengurusan Maklumat",
      "position": "Pegawai Teknologi Maklumat",
      "grade": "41",
      "address": {
        "line1": "Tingkat 5, Blok C",
        "line2": "Kompleks Kementerian Pelancongan",
        "city": "Putrajaya",
        "state": "Wilayah Persekutuan",
        "postcode": "62200"
      }
    },
    "authenticated": {
      "email": "demo.user@motac.gov.my",
      "password": "encrypted:demo_password_hash",
      "username": "demo.user",
      "name": "Siti Nurhaliza binti Ahmad",
      "department": "Bahagian Pengurusan Maklumat",
      "position": "Penolong Pegawai Teknologi Maklumat",
      "grade": "29",
      "phone": "03-2345-6789",
      "alternateEmail": "siti.demo@gmail.com"
    },
    "admin": {
      "email": "admin@motac.gov.my",
      "password": "encrypted:admin_password_hash",
      "username": "admin.user",
      "name": "Muhammad Farid bin Hassan",
      "role": "administrator",
      "department": "Bahagian Pengurusan Maklumat",
      "position": "Ketua Unit Teknologi Maklumat",
      "grade": "48",
      "phone": "03-3456-7890",
      "permissions": [
        "manage_users",
        "manage_tickets",
        "manage_assets",
        "view_reports",
        "system_configuration"
      ]
    },
    "approver": {
      "email": "approver@motac.gov.my",
      "password": "encrypted:approver_password_hash",
      "username": "approver.user",
      "name": "Dato' Rashid bin Abdullah",
      "role": "approver",
      "department": "Bahagian Pengurusan Maklumat",
      "position": "Pengarah Bahagian Pengurusan Maklumat",
      "grade": "54",
      "phone": "03-4567-8901",
      "approvalLimits": {
        "assetValue": 50000,
        "loanDuration": 365,
        "emergencyApproval": true
      }
    },
    "superuser": {
      "email": "superuser@motac.gov.my",
      "password": "encrypted:superuser_password_hash",
      "username": "superuser",
      "name": "Dr. Aminah binti Ismail",
      "role": "superuser",
      "department": "Bahagian Pengurusan Maklumat",
      "position": "Ketua Pengarah Teknologi Maklumat",
      "grade": "JUSA C",
      "phone": "03-5678-9012",
      "permissions": [
        "all_permissions",
        "system_administration",
        "telescope_access",
        "horizon_access",
        "pulse_access"
      ]
    }
  },
  "apiKeys": {
    "hrmis": {
      "clientId": "encrypted:hrmis_client_id",
      "clientSecret": "encrypted:hrmis_client_secret",
      "baseUrl": "https://hrmis.motac.gov.my/api"
    },
    "email": {
      "smtpUsername": "encrypted:smtp_username",
      "smtpPassword": "encrypted:smtp_password"
    },
    "ai": {
      "bedrock": {
        "accessKeyId": "encrypted:aws_access_key",
        "secretAccessKey": "encrypted:aws_secret_key",
        "region": "ap-southeast-1"
      },
      "duckduckgo": {
        "apiKey": "encrypted:duckduckgo_api_key"
      }
    }
  }
}
```

### Credential Encryption

Encrypt credentials using the provided utility:

```powershell
# Encrypt new credentials
.\utilities\config-loader.ps1 -EncryptCredentials -InputFile "credentials-plain.json" -OutputFile "credentials.json"

# Decrypt for viewing (admin only)
.\utilities\config-loader.ps1 -DecryptCredentials -InputFile "credentials.json" -OutputFile "credentials-plain.json"

# Rotate encryption keys
.\utilities\config-loader.ps1 -RotateKeys -BackupPath ".\backups\"
```

## General Settings Configuration

### settings.json

Configure general script execution settings:

```json
{
  "execution": {
    "defaultTimeout": 30000,
    "implicitWait": 10000,
    "pageLoadTimeout": 60000,
    "scriptTimeout": 300000,
    "retryAttempts": 3,
    "retryDelay": 2000,
    "parallelExecution": true,
    "maxParallelScripts": 4,
    "failFast": false,
    "continueOnError": true
  },
  "logging": {
    "level": "info",
    "format": "json",
    "outputPath": "./logs",
    "maxFileSize": "10MB",
    "maxFiles": 30,
    "includeStackTrace": true,
    "logToConsole": true,
    "logToFile": true,
    "timestampFormat": "ISO8601"
  },
  "reporting": {
    "generateReports": true,
    "reportFormat": ["html", "json", "junit"],
    "outputPath": "./reports",
    "includeScreenshots": true,
    "includePerformanceMetrics": true,
    "includeLogs": false,
    "emailReports": false,
    "emailRecipients": ["admin@motac.gov.my"],
    "reportRetention": 90
  },
  "notifications": {
    "enabled": true,
    "channels": ["email", "webhook"],
    "email": {
      "smtp": {
        "host": "smtp.motac.gov.my",
        "port": 587,
        "encryption": "tls",
        "username": "automation@motac.gov.my",
        "password": "encrypted:notification_password"
      },
      "recipients": {
        "success": ["team@motac.gov.my"],
        "failure": ["admin@motac.gov.my", "team@motac.gov.my"],
        "critical": ["admin@motac.gov.my", "manager@motac.gov.my"]
      }
    },
    "webhook": {
      "url": "https://hooks.slack.com/services/YOUR/SLACK/WEBHOOK",
      "method": "POST",
      "headers": {
        "Content-Type": "application/json"
      }
    }
  },
  "performance": {
    "monitoring": true,
    "metricsCollection": true,
    "performanceThresholds": {
      "pageLoadTime": 5000,
      "apiResponseTime": 2000,
      "scriptExecutionTime": 300000
    },
    "resourceMonitoring": {
      "cpu": true,
      "memory": true,
      "disk": false,
      "network": true
    }
  },
  "security": {
    "encryptLogs": false,
    "maskSensitiveData": true,
    "auditTrail": true,
    "accessControl": {
      "requireAuthentication": false,
      "allowedUsers": ["admin@motac.gov.my"],
      "sessionTimeout": 3600
    }
  }
}
```

## Browser Configuration

### browser-settings.json

Configure browser automation settings:

```json
{
  "defaultBrowser": "chrome",
  "browsers": {
    "chrome": {
      "enabled": true,
      "driverPath": "./drivers/chromedriver.exe",
      "binaryPath": null,
      "version": "latest",
      "options": {
        "headless": false,
        "windowSize": {
          "width": 1920,
          "height": 1080
        },
        "position": {
          "x": 0,
          "y": 0
        },
        "maximized": false,
        "incognito": false,
        "disableExtensions": true,
        "disablePlugins": true,
        "disableImages": false,
        "disableJavaScript": false,
        "disableCSS": false,
        "userAgent": "ICTServe-Automation-Suite/1.0 (Chrome)",
        "downloadPath": "./downloads",
        "preferences": {
          "download.default_directory": "./downloads",
          "download.prompt_for_download": false,
          "download.directory_upgrade": true,
          "safebrowsing.enabled": false,
          "profile.default_content_setting_values.notifications": 2
        },
        "arguments": [
          "--no-sandbox",
          "--disable-dev-shm-usage",
          "--disable-gpu",
          "--disable-web-security",
          "--allow-running-insecure-content",
          "--ignore-certificate-errors",
          "--ignore-ssl-errors",
          "--ignore-certificate-errors-spki-list"
        ]
      }
    },
    "firefox": {
      "enabled": true,
      "driverPath": "./drivers/geckodriver.exe",
      "binaryPath": null,
      "version": "latest",
      "options": {
        "headless": false,
        "windowSize": {
          "width": 1920,
          "height": 1080
        },
        "position": {
          "x": 0,
          "y": 0
        },
        "maximized": false,
        "private": false,
        "downloadPath": "./downloads",
        "preferences": {
          "browser.download.folderList": 2,
          "browser.download.dir": "./downloads",
          "browser.download.useDownloadDir": true,
          "browser.helperApps.neverAsk.saveToDisk": "application/pdf,application/octet-stream",
          "pdfjs.disabled": true,
          "plugin.scan.plid.all": false,
          "plugin.scan.Acrobat": "99.0"
        },
        "arguments": [
          "--no-sandbox",
          "--disable-dev-shm-usage"
        ]
      }
    },
    "edge": {
      "enabled": true,
      "driverPath": "./drivers/msedgedriver.exe",
      "binaryPath": null,
      "version": "latest",
      "options": {
        "headless": false,
        "windowSize": {
          "width": 1920,
          "height": 1080
        },
        "position": {
          "x": 0,
          "y": 0
        },
        "maximized": false,
        "inPrivate": false,
        "downloadPath": "./downloads",
        "userAgent": "ICTServe-Automation-Suite/1.0 (Edge)",
        "arguments": [
          "--no-sandbox",
          "--disable-dev-shm-usage",
          "--disable-gpu"
        ]
      }
    },
    "safari": {
      "enabled": false,
      "driverPath": "/usr/bin/safaridriver",
      "options": {
        "windowSize": {
          "width": 1920,
          "height": 1080
        }
      }
    }
  },
  "mobile": {
    "enabled": true,
    "devices": {
      "iPhone12": {
        "userAgent": "Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1",
        "viewport": {
          "width": 390,
          "height": 844,
          "deviceScaleFactor": 3,
          "isMobile": true,
          "hasTouch": true
        }
      },
      "iPadPro": {
        "userAgent": "Mozilla/5.0 (iPad; CPU OS 14_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1",
        "viewport": {
          "width": 1024,
          "height": 1366,
          "deviceScaleFactor": 2,
          "isMobile": true,
          "hasTouch": true
        }
      },
      "SamsungGalaxyS21": {
        "userAgent": "Mozilla/5.0 (Linux; Android 11; SM-G991B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.120 Mobile Safari/537.36",
        "viewport": {
          "width": 360,
          "height": 800,
          "deviceScaleFactor": 3,
          "isMobile": true,
          "hasTouch": true
        }
      }
    }
  },
  "timeouts": {
    "implicit": 10000,
    "pageLoad": 60000,
    "script": 30000,
    "elementWait": 15000
  },
  "capabilities": {
    "acceptInsecureCerts": true,
    "unhandledPromptBehavior": "dismiss",
    "pageLoadStrategy": "normal"
  }
}
```

## Visual Demonstration Configuration

### demo-settings.json

Configure visual demonstration and training modes:

```json
{
  "visualMode": {
    "enabled": true,
    "defaultMode": "demo",
    "modes": {
      "visual": {
        "description": "Live browser automation with visible interactions",
        "browserVisible": true,
        "executionSpeed": "normal",
        "highlightElements": true,
        "showMouseCursor": true,
        "addAnnotations": false,
        "takeScreenshots": false,
        "recordVideo": false,
        "pauseAtSteps": [],
        "showNetworkActivity": false
      },
      "demo": {
        "description": "Slower execution with highlights and annotations",
        "browserVisible": true,
        "executionSpeed": "slow",
        "highlightElements": true,
        "showMouseCursor": true,
        "addAnnotations": true,
        "takeScreenshots": true,
        "recordVideo": false,
        "pauseAtSteps": ["Login", "FormSubmit", "Results"],
        "showNetworkActivity": true,
        "annotationDelay": 2000,
        "stepDelay": 1500
      },
      "interactive": {
        "description": "Pauses at key steps for explanation",
        "browserVisible": true,
        "executionSpeed": "variable",
        "highlightElements": true,
        "showMouseCursor": true,
        "addAnnotations": true,
        "takeScreenshots": true,
        "recordVideo": false,
        "pauseAtSteps": ["Navigation", "Login", "FormFill", "FormSubmit", "Validation", "Results", "Completion"],
        "showNetworkActivity": true,
        "interactivePauses": true,
        "pauseMessage": "Press SPACE to continue, ESC to exit...",
        "pauseTimeout": 300000
      },
      "recording": {
        "description": "Captures video while running automation",
        "browserVisible": true,
        "executionSpeed": "normal",
        "highlightElements": true,
        "showMouseCursor": true,
        "addAnnotations": true,
        "takeScreenshots": true,
        "recordVideo": true,
        "pauseAtSteps": [],
        "showNetworkActivity": false,
        "recordingQuality": "high"
      },
      "headless": {
        "description": "Fast execution without browser window",
        "browserVisible": false,
        "executionSpeed": "fast",
        "highlightElements": false,
        "showMouseCursor": false,
        "addAnnotations": false,
        "takeScreenshots": false,
        "recordVideo": false,
        "pauseAtSteps": [],
        "showNetworkActivity": false
      }
    }
  },
  "highlighting": {
    "enabled": true,
    "style": {
      "border": "3px solid #007bff",
      "backgroundColor": "rgba(0, 123, 255, 0.1)",
      "borderRadius": "4px",
      "boxShadow": "0 0 10px rgba(0, 123, 255, 0.5)"
    },
    "duration": 2000,
    "fadeIn": 300,
    "fadeOut": 300,
    "elements": {
      "input": {
        "border": "3px solid #28a745",
        "backgroundColor": "rgba(40, 167, 69, 0.1)"
      },
      "button": {
        "border": "3px solid #dc3545",
        "backgroundColor": "rgba(220, 53, 69, 0.1)"
      },
      "link": {
        "border": "3px solid #17a2b8",
        "backgroundColor": "rgba(23, 162, 184, 0.1)"
      }
    }
  },
  "annotations": {
    "enabled": true,
    "style": {
      "fontFamily": "Arial, sans-serif",
      "fontSize": "14px",
      "fontWeight": "bold",
      "color": "#ffffff",
      "backgroundColor": "rgba(0, 0, 0, 0.8)",
      "padding": "8px 12px",
      "borderRadius": "4px",
      "boxShadow": "0 2px 8px rgba(0, 0, 0, 0.3)",
      "zIndex": 10000
    },
    "position": "top-right",
    "duration": 3000,
    "fadeIn": 500,
    "fadeOut": 500,
    "maxWidth": 300,
    "arrow": true
  },
  "mouseCursor": {
    "enabled": true,
    "style": {
      "size": 20,
      "color": "#ff6b6b",
      "strokeWidth": 2,
      "strokeColor": "#ffffff"
    },
    "animation": {
      "clickDuration": 200,
      "moveDuration": 800,
      "easing": "ease-in-out"
    },
    "trail": {
      "enabled": true,
      "length": 10,
      "opacity": 0.3
    }
  },
  "screenshots": {
    "enabled": true,
    "format": "png",
    "quality": 100,
    "outputPath": "./screenshots",
    "naming": {
      "pattern": "{timestamp}_{script}_{step}_{description}",
      "timestampFormat": "YYYY-MM-DD_HH-mm-ss"
    },
    "automaticCapture": {
      "onError": true,
      "onSuccess": true,
      "onPause": true,
      "beforeAction": false,
      "afterAction": true
    },
    "thumbnails": {
      "generate": true,
      "width": 300,
      "height": 200
    }
  },
  "videoRecording": {
    "enabled": false,
    "format": "mp4",
    "quality": "high",
    "frameRate": 30,
    "outputPath": "./videos",
    "naming": {
      "pattern": "{timestamp}_{script}_{description}",
      "timestampFormat": "YYYY-MM-DD_HH-mm-ss"
    },
    "codec": "h264",
    "bitrate": "2000k",
    "resolution": {
      "width": 1920,
      "height": 1080
    },
    "audio": {
      "enabled": false,
      "codec": "aac",
      "bitrate": "128k"
    }
  },
  "networkMonitoring": {
    "enabled": true,
    "displayInConsole": true,
    "logRequests": true,
    "logResponses": false,
    "filterTypes": ["xhr", "fetch", "websocket"],
    "showTiming": true,
    "showHeaders": false,
    "showPayload": false
  }
}
```

## AI Services Configuration

### ai-settings.json

Configure AI integration settings:

```json
{
  "ollama": {
    "enabled": true,
    "baseUrl": "http://localhost:11434",
    "timeout": 60000,
    "retryAttempts": 3,
    "retryDelay": 2000,
    "models": {
      "default": "llama2",
      "available": [
        {
          "name": "llama2",
          "size": "7B",
          "description": "General purpose conversational AI",
          "useCase": "general",
          "maxTokens": 4096
        },
        {
          "name": "codellama",
          "size": "7B",
          "description": "Code generation and analysis",
          "useCase": "code",
          "maxTokens": 4096
        },
        {
          "name": "mistral",
          "size": "7B",
          "description": "Fast and efficient responses",
          "useCase": "quick",
          "maxTokens": 8192
        },
        {
          "name": "neural-chat",
          "size": "7B",
          "description": "Conversational AI with enhanced context",
          "useCase": "conversation",
          "maxTokens": 4096
        }
      ]
    },
    "parameters": {
      "temperature": 0.7,
      "topP": 0.9,
      "topK": 40,
      "repeatPenalty": 1.1,
      "seed": -1,
      "numCtx": 2048,
      "numPredict": 512
    },
    "dataClassification": {
      "sensitiveKeywords": [
        "password", "secret", "confidential", "classified",
        "personal", "private", "internal", "restricted"
      ],
      "publicKeywords": [
        "public", "general", "common", "standard",
        "documentation", "help", "faq", "guide"
      ]
    }
  },
  "bedrock": {
    "enabled": true,
    "region": "ap-southeast-1",
    "timeout": 30000,
    "retryAttempts": 2,
    "retryDelay": 1000,
    "models": {
      "default": "claude-3-sonnet",
      "available": [
        {
          "name": "claude-3-opus",
          "modelId": "anthropic.claude-3-opus-20240229-v1:0",
          "description": "Most capable model for complex tasks",
          "useCase": "complex",
          "maxTokens": 200000,
          "costPerToken": 0.000015
        },
        {
          "name": "claude-3-sonnet",
          "modelId": "anthropic.claude-3-sonnet-20240229-v1:0",
          "description": "Balanced performance and speed",
          "useCase": "balanced",
          "maxTokens": 200000,
          "costPerToken": 0.000003
        },
        {
          "name": "claude-3-haiku",
          "modelId": "anthropic.claude-3-haiku-20240307-v1:0",
          "description": "Fastest responses for simple tasks",
          "useCase": "simple",
          "maxTokens": 200000,
          "costPerToken": 0.00000025
        },
        {
          "name": "claude-3-5-sonnet",
          "modelId": "anthropic.claude-3-5-sonnet-20241022-v2:0",
          "description": "Latest model with enhanced capabilities",
          "useCase": "latest",
          "maxTokens": 200000,
          "costPerToken": 0.000003
        }
      ]
    },
    "parameters": {
      "temperature": 0.7,
      "topP": 0.9,
      "maxTokens": 4096,
      "stopSequences": []
    },
    "dlpFiltering": {
      "enabled": true,
      "sensitivityLevels": ["high", "medium", "low"],
      "defaultLevel": "medium",
      "filters": {
        "personalData": true,
        "financialData": true,
        "healthData": true,
        "governmentData": true,
        "intellectualProperty": true
      }
    },
    "costManagement": {
      "budgetLimit": 1000.00,
      "dailyLimit": 50.00,
      "alertThresholds": [0.5, 0.8, 0.9],
      "autoStop": true
    }
  },
  "modelRouting": {
    "enabled": true,
    "rules": [
      {
        "condition": "dataSensitivity == 'high'",
        "target": "ollama",
        "model": "llama2",
        "reason": "PKS 4.2 compliance - sensitive data must be processed locally"
      },
      {
        "condition": "dataSensitivity == 'medium' && complexity == 'high'",
        "target": "bedrock",
        "model": "claude-3-opus",
        "reason": "Complex analysis requires advanced cloud model"
      },
      {
        "condition": "dataSensitivity == 'low' && complexity == 'medium'",
        "target": "bedrock",
        "model": "claude-3-sonnet",
        "reason": "Balanced performance for standard tasks"
      },
      {
        "condition": "dataSensitivity == 'low' && complexity == 'low'",
        "target": "bedrock",
        "model": "claude-3-haiku",
        "reason": "Fast responses for simple queries"
      },
      {
        "condition": "default",
        "target": "ollama",
        "model": "llama2",
        "reason": "Default to local processing for data sovereignty"
      }
    ],
    "fallback": {
      "enabled": true,
      "order": ["ollama", "bedrock"],
      "timeout": 10000
    }
  },
  "webAugmentation": {
    "enabled": true,
    "provider": "duckduckgo",
    "timeout": 15000,
    "maxResults": 5,
    "safeSearch": "moderate",
    "region": "my-en",
    "filters": {
      "contentTypes": ["text", "news"],
      "excludeDomains": ["example.com"],
      "includeDomains": ["gov.my", "edu.my"]
    },
    "caching": {
      "enabled": true,
      "ttl": 3600,
      "maxSize": 1000
    }
  },
  "conversationManagement": {
    "enabled": true,
    "maxConversations": 1000,
    "maxMessagesPerConversation": 100,
    "retentionPeriod": 90,
    "autoCleanup": true,
    "export": {
      "formats": ["json", "txt", "pdf"],
      "includeMetadata": true,
      "compression": true
    }
  },
  "mcpIntegration": {
    "enabled": true,
    "servers": [
      {
        "name": "document-analyzer",
        "url": "http://localhost:8001/mcp",
        "description": "Document analysis and processing",
        "tools": ["analyze_document", "extract_text", "summarize"]
      },
      {
        "name": "data-processor",
        "url": "http://localhost:8002/mcp",
        "description": "Data processing and transformation",
        "tools": ["process_data", "transform_format", "validate_data"]
      },
      {
        "name": "knowledge-base",
        "url": "http://localhost:8003/mcp",
        "description": "Knowledge base search and retrieval",
        "tools": ["search_kb", "retrieve_document", "update_kb"]
      }
    ],
    "timeout": 30000,
    "retryAttempts": 2
  }
}
```

## Configuration Management Commands

### Loading Configuration

```powershell
# Load configuration for specific environment
.\utilities\config-loader.ps1 -Environment testing -LoadAll

# Load specific configuration file
.\utilities\config-loader.ps1 -LoadConfig browser-settings

# Validate configuration
.\utilities\config-loader.ps1 -ValidateConfig -Environment testing
```

### Updating Configuration

```powershell
# Update environment settings
.\utilities\config-loader.ps1 -UpdateEnvironment testing -Setting "baseUrl" -Value "https://new-test.motac.gov.my"

# Update browser settings
.\utilities\config-loader.ps1 -UpdateBrowser chrome -Setting "headless" -Value $true

# Update AI settings
.\utilities\config-loader.ps1 -UpdateAI ollama -Setting "baseUrl" -Value "http://new-ai-server:11434"
```

### Configuration Backup and Restore

```powershell
# Backup all configurations
.\utilities\config-loader.ps1 -BackupConfig -OutputPath ".\backups\config-backup-$(Get-Date -Format 'yyyy-MM-dd')"

# Restore configuration from backup
.\utilities\config-loader.ps1 -RestoreConfig -BackupPath ".\backups\config-backup-2024-01-15"

# Export configuration for sharing
.\utilities\config-loader.ps1 -ExportConfig -Environment testing -OutputPath ".\exports\testing-config.json"
```

## Environment-Specific Configuration

### Development Environment Setup

```powershell
# Set up development environment
.\utilities\config-loader.ps1 -SetupEnvironment development -Features @(
    "debug-logging",
    "local-ai",
    "mock-email",
    "test-data-generation"
)
```

### Testing Environment Setup

```powershell
# Set up testing environment
.\utilities\config-loader.ps1 -SetupEnvironment testing -Features @(
    "comprehensive-logging",
    "ai-integration",
    "email-integration",
    "performance-monitoring"
)
```

### Production Environment Setup

```powershell
# Set up production environment (read-only testing)
.\utilities\config-loader.ps1 -SetupEnvironment production -Features @(
    "minimal-logging",
    "full-ai-integration",
    "production-monitoring",
    "security-compliance"
) -ReadOnly
```

## Security Considerations

### Credential Security

1. **Encryption**: All credentials are encrypted using AES-256-GCM
2. **Key Management**: Encryption keys are stored separately and rotated regularly
3. **Access Control**: Credential access is logged and audited
4. **Environment Separation**: Different credentials for each environment

### Network Security

1. **HTTPS Only**: All production environments use HTTPS
2. **Certificate Validation**: SSL certificates are validated by default
3. **Network Isolation**: Test environments are isolated from production
4. **VPN Access**: Production access requires VPN connection

### Data Protection

1. **Data Masking**: Sensitive data is masked in logs and reports
2. **Audit Trails**: All configuration changes are logged
3. **Retention Policies**: Logs and reports are retained according to policy
4. **PDPA Compliance**: Personal data handling follows PDPA requirements

## Troubleshooting Configuration Issues

### Common Configuration Problems

1. **Invalid JSON Format**

   ```powershell
   .\utilities\config-loader.ps1 -ValidateJSON -FilePath "config/environments.json"
   ```

2. **Missing Required Settings**

   ```powershell
   .\utilities\config-loader.ps1 -CheckRequired -Environment testing
   ```

3. **Credential Decryption Errors**

   ```powershell
   .\utilities\config-loader.ps1 -TestCredentials -Environment testing
   ```

4. **Network Connectivity Issues**

   ```powershell
   .\utilities\config-loader.ps1 -TestConnectivity -Environment testing
   ```

### Configuration Validation

```powershell
# Validate all configurations
.\utilities\config-loader.ps1 -ValidateAll -Environment testing

# Test configuration with actual services
.\utilities\config-loader.ps1 -TestConfiguration -Environment testing -IncludeServices

# Generate configuration report
.\utilities\config-loader.ps1 -GenerateReport -Environment testing -OutputPath ".\reports\config-report.html"
```

## Best Practices

### Configuration Management

1. **Version Control**: Store configurations in Git with proper branching
2. **Environment Parity**: Keep configurations consistent across environments
3. **Documentation**: Document all configuration changes and their purposes
4. **Testing**: Test configuration changes in development before deployment
5. **Backup**: Regular backups of all configuration files

### Security Best Practices

1. **Least Privilege**: Grant minimum required permissions
2. **Regular Rotation**: Rotate credentials and encryption keys regularly
3. **Monitoring**: Monitor configuration access and changes
4. **Separation**: Keep production configurations separate from development
5. **Encryption**: Encrypt all sensitive configuration data

### Performance Optimization

1. **Caching**: Enable caching for frequently accessed configurations
2. **Lazy Loading**: Load configurations only when needed
3. **Compression**: Compress large configuration files
4. **Indexing**: Index configuration data for fast retrieval
5. **Monitoring**: Monitor configuration loading performance

---

*This configuration guide provides comprehensive instructions for setting up and managing the ICTServe Comprehensive Automation Suite across different environments. For additional support or questions about specific configuration scenarios, please contact the development team.*
