# Mimir Workflow Orchestration

**Version**: 4.1.0  
**Last Updated**: 2025-12-05  
**Status**: ✅ Available

---

## Overview

Mimir supports multi-agent workflow orchestration, allowing you to break complex tasks into parallel and sequential steps executed by specialized AI agents.

---

## Workflow Concepts

### Agents

Mimir workflows use three agent types:

| Agent Type | Role | Context | Use Case |
|------------|------|---------|----------|
| **PM** | Project Manager | Full (100%) | Planning, coordination, review |
| **Worker** | Implementation | Minimal (<10%) | Code generation, specific tasks |
| **QC** | Quality Control | Requirements + Output | Verification, testing |

### Task Dependencies

Tasks can depend on other tasks:

```javascript
{
  id: "task-2",
  dependencies: ["task-1"]  // task-2 runs after task-1 completes
}
```

### Parallel Execution

Tasks with the same `parallelGroup` run in parallel:

```javascript
{
  id: "task-1",
  parallelGroup: 1  // Runs in group 1
},
{
  id: "task-2",
  parallelGroup: 1  // Runs in parallel with task-1
},
{
  id: "task-3",
  parallelGroup: 2  // Runs after group 1 completes
}
```

---

## Creating Workflows

### Basic Workflow

```javascript
execute_workflow({
  tasks: [
    {
      id: "task-1",
      title: "Generate User Model",
      prompt: "Generate Laravel User model with authentication traits",
      agentRoleDescription: "Laravel code generator",
      recommendedModel: "gpt-4o",
      parallelGroup: 1,
      dependencies: [],
      maxRetries: 2,
      successCriteria: [
        "Model file created",
        "Authentication traits included",
        "PHPDoc comments present"
      ]
    }
  ]
})
```

### Multi-Step Workflow

```javascript
execute_workflow({
  tasks: [
    // Step 1: Planning (PM Agent)
    {
      id: "plan",
      title: "Plan Implementation",
      prompt: "Analyze requirements and create implementation plan",
      agentRoleDescription: "Project Manager",
      recommendedModel: "gpt-4o",
      parallelGroup: 1,
      dependencies: []
    },
    
    // Step 2: Parallel Implementation (Worker Agents)
    {
      id: "generate-model",
      title: "Generate Model",
      prompt: "Generate User model based on plan",
      agentRoleDescription: "Laravel model generator",
      recommendedModel: "gpt-4o",
      parallelGroup: 2,
      dependencies: ["plan"]
    },
    {
      id: "generate-migration",
      title: "Generate Migration",
      prompt: "Generate migration for User model",
      agentRoleDescription: "Laravel migration generator",
      recommendedModel: "gpt-4o",
      parallelGroup: 2,
      dependencies: ["plan"]
    },
    {
      id: "generate-tests",
      title: "Generate Tests",
      prompt: "Generate tests for User model",
      agentRoleDescription: "Test generator",
      recommendedModel: "gpt-4o",
      parallelGroup: 2,
      dependencies: ["plan"]
    },
    
    // Step 3: Quality Control (QC Agent)
    {
      id: "verify",
      title: "Verify Implementation",
      prompt: "Verify all generated code meets requirements",
      agentRoleDescription: "Quality Control",
      recommendedModel: "gpt-4o",
      parallelGroup: 3,
      dependencies: ["generate-model", "generate-migration", "generate-tests"]
    }
  ]
})
```

---

## Workflow Execution

### Start Workflow

```javascript
const result = execute_workflow({
  tasks: [/* task definitions */]
})

// Returns:
// { execution_id: "exec-123" }
```

### Monitor Progress

```javascript
get_execution_status({
  execution_id: "exec-123"
})

// Returns:
// {
//   status: "running",
//   progress: {
//     completed: 2,
//     total: 5,
//     current: "generate-tests"
//   },
//   tasks: {
//     "plan": { status: "completed", duration: 5000 },
//     "generate-model": { status: "completed", duration: 8000 },
//     "generate-migration": { status: "running", duration: 3000 },
//     "generate-tests": { status: "pending" },
//     "verify": { status: "pending" }
//   }
// }
```

### Get Results

```javascript
get_execution_results({
  execution_id: "exec-123"
})

// Returns:
// {
//   status: "completed",
//   tasks: {
//     "plan": {
//       status: "completed",
//       output: "Implementation plan...",
//       deliverables: ["plan.md"]
//     },
//     "generate-model": {
//       status: "completed",
//       output: "<?php\nnamespace App\\Models;...",
//       deliverables: ["app/Models/User.php"]
//     },
//     // ... other tasks
//   }
// }
```

### Cancel Workflow

```javascript
cancel_execution({
  execution_id: "exec-123"
})
```

---

## Workflow Patterns

### Pattern 1: Sequential Pipeline

```javascript
// Task 1 → Task 2 → Task 3
execute_workflow({
  tasks: [
    {
      id: "task-1",
      parallelGroup: 1,
      dependencies: []
    },
    {
      id: "task-2",
      parallelGroup: 2,
      dependencies: ["task-1"]
    },
    {
      id: "task-3",
      parallelGroup: 3,
      dependencies: ["task-2"]
    }
  ]
})
```

### Pattern 2: Fan-Out / Fan-In

```javascript
// Task 1 → [Task 2, Task 3, Task 4] → Task 5
execute_workflow({
  tasks: [
    {
      id: "task-1",
      parallelGroup: 1,
      dependencies: []
    },
    {
      id: "task-2",
      parallelGroup: 2,
      dependencies: ["task-1"]
    },
    {
      id: "task-3",
      parallelGroup: 2,
      dependencies: ["task-1"]
    },
    {
      id: "task-4",
      parallelGroup: 2,
      dependencies: ["task-1"]
    },
    {
      id: "task-5",
      parallelGroup: 3,
      dependencies: ["task-2", "task-3", "task-4"]
    }
  ]
})
```

### Pattern 3: Diamond Dependency

```javascript
//     Task 1
//    /      \
// Task 2  Task 3
//    \      /
//     Task 4
execute_workflow({
  tasks: [
    {
      id: "task-1",
      parallelGroup: 1,
      dependencies: []
    },
    {
      id: "task-2",
      parallelGroup: 2,
      dependencies: ["task-1"]
    },
    {
      id: "task-3",
      parallelGroup: 2,
      dependencies: ["task-1"]
    },
    {
      id: "task-4",
      parallelGroup: 3,
      dependencies: ["task-2", "task-3"]
    }
  ]
})
```

---

## Real-World Examples

### Example 1: Feature Implementation

```javascript
execute_workflow({
  tasks: [
    {
      id: "analyze",
      title: "Analyze Requirements",
      prompt: "Analyze D03 requirements for email notification feature",
      agentRoleDescription: "Requirements Analyst",
      recommendedModel: "gpt-4o",
      parallelGroup: 1
    },
    {
      id: "design",
      title: "Design Architecture",
      prompt: "Design email notification architecture",
      agentRoleDescription: "System Architect",
      recommendedModel: "gpt-4o",
      parallelGroup: 2,
      dependencies: ["analyze"]
    },
    {
      id: "implement-mail",
      title: "Implement Mail Class",
      prompt: "Generate Laravel Mail class for notifications",
      agentRoleDescription: "Laravel Developer",
      recommendedModel: "gpt-4o",
      parallelGroup: 3,
      dependencies: ["design"]
    },
    {
      id: "implement-queue",
      title: "Implement Queue Job",
      prompt: "Generate queue job for email sending",
      agentRoleDescription: "Laravel Developer",
      recommendedModel: "gpt-4o",
      parallelGroup: 3,
      dependencies: ["design"]
    },
    {
      id: "implement-tests",
      title: "Generate Tests",
      prompt: "Generate tests for email notification system",
      agentRoleDescription: "Test Engineer",
      recommendedModel: "gpt-4o",
      parallelGroup: 3,
      dependencies: ["design"]
    },
    {
      id: "verify",
      title: "Verify Implementation",
      prompt: "Verify email notification system meets requirements",
      agentRoleDescription: "QA Engineer",
      recommendedModel: "gpt-4o",
      parallelGroup: 4,
      dependencies: ["implement-mail", "implement-queue", "implement-tests"]
    }
  ]
})
```

### Example 2: Code Refactoring

```javascript
execute_workflow({
  tasks: [
    {
      id: "analyze-code",
      title: "Analyze Current Code",
      prompt: "Analyze UserController for refactoring opportunities",
      agentRoleDescription: "Code Analyst",
      recommendedModel: "gpt-4o",
      parallelGroup: 1
    },
    {
      id: "extract-service",
      title: "Extract Service Layer",
      prompt: "Extract business logic to UserService",
      agentRoleDescription: "Refactoring Specialist",
      recommendedModel: "gpt-4o",
      parallelGroup: 2,
      dependencies: ["analyze-code"]
    },
    {
      id: "update-controller",
      title: "Update Controller",
      prompt: "Update UserController to use UserService",
      agentRoleDescription: "Refactoring Specialist",
      recommendedModel: "gpt-4o",
      parallelGroup: 2,
      dependencies: ["analyze-code"]
    },
    {
      id: "update-tests",
      title: "Update Tests",
      prompt: "Update tests for refactored code",
      agentRoleDescription: "Test Engineer",
      recommendedModel: "gpt-4o",
      parallelGroup: 3,
      dependencies: ["extract-service", "update-controller"]
    },
    {
      id: "verify-refactoring",
      title: "Verify Refactoring",
      prompt: "Verify refactored code maintains functionality",
      agentRoleDescription: "QA Engineer",
      recommendedModel: "gpt-4o",
      parallelGroup: 4,
      dependencies: ["update-tests"]
    }
  ]
})
```

---

## Best Practices

### 1. Task Granularity

✅ **Good**: Small, focused tasks

```javascript
{
  id: "generate-model",
  title: "Generate User Model",
  prompt: "Generate User model with authentication traits"
}
```

❌ **Bad**: Large, vague tasks

```javascript
{
  id: "implement-feature",
  title: "Implement Everything",
  prompt: "Implement entire authentication system"
}
```

### 2. Clear Dependencies

✅ **Good**: Explicit dependencies

```javascript
{
  id: "test",
  dependencies: ["generate-model", "generate-migration"]
}
```

❌ **Bad**: Implicit dependencies

```javascript
{
  id: "test",
  dependencies: []  // Assumes model and migration exist
}
```

### 3. Success Criteria

✅ **Good**: Specific criteria

```javascript
{
  successCriteria: [
    "Model file created at app/Models/User.php",
    "Authentication traits included",
    "PHPDoc comments present",
    "PSR-12 compliant"
  ]
}
```

❌ **Bad**: Vague criteria

```javascript
{
  successCriteria: ["Code works"]
}
```

### 4. Error Handling

```javascript
{
  maxRetries: 2,  // Retry failed tasks
  successCriteria: [/* specific criteria */]
}
```

---

## Monitoring & Debugging

### View Workflow Logs

```powershell
# Watch workflow execution
docker logs mimir_server -f | Select-String "workflow"

# Check for errors
docker logs mimir_server --tail 100 | Select-String "error"
```

### Workflow Status

```javascript
// Check status periodically
setInterval(() => {
  const status = get_execution_status({ execution_id: "exec-123" })
  console.log(status)
}, 5000)
```

---

## Limitations

- **Max Parallel Tasks**: 10 per parallel group
- **Max Workflow Duration**: 1 hour
- **Max Task Retries**: 3
- **Max Dependencies**: 10 per task

---

## Related Documentation

- **[06-API-REFERENCE.md](06-API-REFERENCE.md)** - Workflow API reference
- **[04-MCP-INTEGRATION.md](04-MCP-INTEGRATION.md)** - Kiro IDE integration
- **[07-NEO4J-GUIDE.md](07-NEO4J-GUIDE.md)** - Knowledge graph

---

**Last Updated**: 2025-12-05  
**Mimir Version**: 4.1.0  
**Status**: ✅ Production Ready
