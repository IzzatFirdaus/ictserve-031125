# AI Agent Output Policy & Memory Retention Guidelines

## 🚨 CRITICAL: AI Agent Output Policy

**MANDATORY DIRECTIVE FOR ALL AI AGENTS**

### File Creation Restrictions

**❌ PROHIBITED: Automatic Markdown File Creation**

AI agents **MUST NOT** automatically create markdown files in the following directories:

- `docs/` - System documentation (D00-D15 canonical documents)
- `.agents/` - Agent memory and instruction files
- `.github/` - GitHub configuration and instructions
- `.kiro/` - Kiro AI specifications and steering
- `.amazonq/` - Amazon Q rules and memory bank
- `.cursor/` - Cursor AI rules
- `.gemini/` - Gemini AI settings
- `.junie/` - Junie AI guidelines
- `.opencode/` - OpenCode AI guidelines

**Prohibited File Types** (unless explicitly requested):

- Reports (`*-report.md`, `*-summary.md`, `*-analysis.md`)
- Summaries (`*-summary.md`, `summary-*.md`)
- Checklists (`*-checklist.md`, `checklist-*.md`)
- Implementation logs (`implementation-*.md`, `*-implementation.md`)
- Analysis documents (`analysis-*.md`, `*-analysis.md`)
- Task status files (`task-*.md`, `*-task.md`)
- Progress reports (`progress-*.md`, `*-progress.md`)
- Completion reports (`completion-*.md`, `*-complete.md`)

### Default Response Mode

**✅ REQUIRED: Inline Chat Responses**

By default, AI agents **MUST**:

1. Provide responses **inline in the chat interface**
2. Use structured markdown formatting in chat
3. Include code blocks, tables, and lists in chat responses
4. Summarize findings and recommendations in chat

**File Creation Only When**:

- User **explicitly requests** file creation
- User uses phrases like "create a file", "save to file", "write to markdown"
- User specifies a filename or path
- User asks to "document this in a file"

### Minimal Code Policy

**✅ REQUIRED: Absolute Minimal Code Implementations**

AI agents **MUST**:

1. Write **ONLY** the code needed to solve the problem
2. Avoid verbose implementations
3. Exclude code that doesn't directly contribute to the solution
4. Prioritize clarity and simplicity over completeness
5. Remove unnecessary abstractions and boilerplate

**Example - Minimal vs Verbose**:

```php
// ❌ VERBOSE (Avoid)
class UserService
{
    private UserRepository $repository;
    private LoggerInterface $logger;
    private EventDispatcher $dispatcher;

    public function __construct(
        UserRepository $repository,
        LoggerInterface $logger,
        EventDispatcher $dispatcher
    ) {
        $this->repository = $repository;
        $this->logger = $logger;
        $this->dispatcher = $dispatcher;
    }

    public function createUser(array $data): User
    {
        $this->logger->info('Creating user', $data);

        try {
            $user = $this->repository->create($data);
            $this->dispatcher->dispatch(new UserCreated($user));
            $this->logger->info('User created', ['id' => $user->id]);
            return $user;
        } catch (\Exception $e) {
            $this->logger->error('User creation failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}

// ✅ MINIMAL (Preferred)
class UserService
{
    public function __construct(private UserRepository $repository) {}

    public function createUser(array $data): User
    {
        return $this->repository->create($data);
    }
}
```

---

## 💾 Memory Retention Recommendations

### Recommended Memory Solutions

AI agents should use **ONE** of the following memory retention methods (in order of preference):

#### 1. MCP Memory Server (RECOMMENDED)

**Repository**: <https://github.com/modelcontextprotocol/servers/tree/main/src/memory>

**Why Use MCP Memory Server**:

- ✅ **Persistent**: Survives session boundaries
- ✅ **Queryable**: Search and retrieve structured knowledge
- ✅ **Evolving**: Update with new information
- ✅ **Graph-based**: Entities, relations, observations
- ✅ **Standardized**: MCP Protocol v2025-06-18 compliant

**Usage Pattern**:

```typescript
// Create entity
mcp_memory_create_entities([{
  name: 'Feature_Implementation_Status',
  entityType: 'technical_implementation',
  observations: [
    'Feature: Staff Dashboard Export',
    'Status: Completed',
    'Files: ExportService.php, ExportJob.php',
    'Lines: 147',
    'Date: 2025-11-10'
  ]
}])

// Query entity
search_nodes('export feature')
open_nodes(['Feature_Implementation_Status'])

// Add observation
add_observations([{
  entityName: 'Feature_Implementation_Status',
  contents: ['Bug fix: Export timeout increased to 5 minutes']
}])
```

**When to Use**:

- Long-term project knowledge
- Cross-session context preservation
- Feature implementation tracking
- Pattern and solution storage
- Requirements traceability

---

#### 2. Memory Instructions File (`.agents/memory.instructions.md`)

**Location**: `.agents/memory.instructions.md`

**Why Use Memory Instructions**:

- ✅ **File-based**: Simple text file storage
- ✅ **Version controlled**: Git tracking
- ✅ **Human-readable**: Markdown format
- ✅ **Searchable**: Text search in IDE
- ✅ **Portable**: Works across all AI agents

**Usage Pattern**:

```markdown
## Feature Implementation: Staff Dashboard Export

**Status**: Completed
**Date**: 2025-11-10
**Files Modified**:
- `app/Services/ExportService.php` (new)
- `app/Jobs/ExportJob.php` (new)
- `app/Livewire/Staff/ExportButton.php` (modified)

**Implementation Details**:
- Used Laravel Maatwebsite Excel library
- Queue-based async exports (1-hour timeout)
- 3-week retention policy for export files
- Supports CSV and Excel formats

**Testing**:
- Unit tests: ExportServiceTest.php (5 tests)
- Feature tests: ExportFeatureTest.php (3 tests)
- All tests passing

**Next Steps**:
- Add email notification when export completes
- Implement export history view
```

**When to Use**:

- Session-specific context
- Quick notes and reminders
- Temporary implementation details
- Work-in-progress tracking

---

#### 3. Memory JSONL File (`.agents/memory.jsonl`)

**Location**: `.agents/memory.jsonl`

**Why Use Memory JSONL**:

- ✅ **Structured**: JSON format for parsing
- ✅ **Append-only**: Easy to add new entries
- ✅ **Queryable**: Can be parsed programmatically
- ✅ **Timestamped**: Track when information was added

**Usage Pattern**:

```jsonl
{"timestamp":"2025-11-10T14:30:00Z","type":"feature","name":"Staff Dashboard Export","status":"completed","files":["ExportService.php","ExportJob.php"],"lines":147}
{"timestamp":"2025-11-10T15:45:00Z","type":"bug_fix","name":"Export Timeout","description":"Increased timeout from 1 hour to 5 minutes","file":"ExportJob.php"}
{"timestamp":"2025-11-10T16:20:00Z","type":"test","name":"Export Feature Tests","status":"passing","count":8}
```

**When to Use**:

- Structured logging of events
- Timeline tracking
- Programmatic memory access
- Integration with external tools

---

### Memory Retention Best Practices

#### DO ✅

1. **Use MCP Memory Server for**:
   - Long-term project knowledge
   - Cross-session context
   - Pattern and solution storage
   - Requirements traceability

2. **Use Memory Instructions File for**:
   - Session-specific notes
   - Work-in-progress tracking
   - Quick reminders

3. **Use Memory JSONL for**:
   - Event logging
   - Timeline tracking
   - Structured data storage

4. **Update Memory When**:
   - Completing a feature
   - Solving a bug
   - Discovering a pattern
   - Learning project conventions

#### DON'T ❌

1. **Don't Create Markdown Files for**:
   - Reports (use inline chat responses)
   - Summaries (use inline chat responses)
   - Analysis (use inline chat responses)
   - Checklists (use MCP Memory observations)

2. **Don't Duplicate Information**:
   - Avoid storing same info in multiple places
   - Use MCP Memory relations to link entities
   - Reference existing documentation (D00-D15)

3. **Don't Store Temporary Information**:
   - Avoid storing session-specific data in MCP Memory
   - Use memory.instructions.md for temporary notes
   - Clean up outdated information regularly

---

## 🔄 Memory Workflow Examples

### Example 1: Feature Implementation

**Scenario**: Implementing staff dashboard export feature

**Workflow**:

```
1. START SESSION
   - Query MCP Memory: search_nodes('export feature')
   - Load context: open_nodes(['Export_Service_Implementation'])

2. DURING IMPLEMENTATION
   - Add notes to .agents/memory.instructions.md
   - Log events to .agents/memory.jsonl (optional)

3. AFTER COMPLETION
   - Update MCP Memory:
     add_observations(['Export_Service_Implementation'], [
       'Status: Completed',
       'Date: 2025-11-10',
       'Files: ExportService.php, ExportJob.php',
       'Tests: 8 passing'
     ])

4. INLINE RESPONSE TO USER
   - Summarize implementation in chat
   - Include code snippets in chat
   - Provide testing results in chat
   - NO markdown file creation
```

---

### Example 2: Bug Fix

**Scenario**: Fixing export timeout issue

**Workflow**:

```
1. START SESSION
   - Query MCP Memory: search_nodes('export timeout')
   - Check existing solutions

2. DURING DEBUGGING
   - Add debugging notes to .agents/memory.instructions.md
   - Log attempts to .agents/memory.jsonl (optional)

3. AFTER FIX
   - Update MCP Memory:
     create_entities([{
       name: 'Export_Timeout_Fix_2025_11_10',
       entityType: 'solved_issue',
       observations: [
         'Issue: Export timeout after 1 hour',
         'Root cause: Queue timeout too short',
         'Solution: Increased timeout to 5 minutes',
         'File: ExportJob.php',
         'Time to resolution: 30 minutes'
       ]
     }])

4. INLINE RESPONSE TO USER
   - Explain fix in chat
   - Show code changes in chat
   - Provide testing verification in chat
   - NO markdown file creation
```

---

### Example 3: Pattern Discovery

**Scenario**: Discovering Livewire component pattern

**Workflow**:

```
1. DURING DEVELOPMENT
   - Notice reusable pattern in Livewire components
   - Add temporary notes to .agents/memory.instructions.md

2. AFTER VERIFICATION
   - Create MCP Memory entity:
     create_entities([{
       name: 'Livewire_Export_Button_Pattern',
       entityType: 'coding_pattern',
       observations: [
         'Pattern: Export button with loading state',
         'Usage: wire:click="export" wire:loading.attr="disabled"',
         'Example: ExportButton.php component',
         'Related: Export_Service_Implementation'
       ]
     }])

   - Create relation:
     create_relations([{
       from: 'Livewire_Export_Button_Pattern',
       relationType: 'related_to',
       to: 'Export_Service_Implementation'
     }])

3. INLINE RESPONSE TO USER
   - Explain pattern in chat
   - Show example code in chat
   - Provide usage guidelines in chat
   - NO markdown file creation
```

---

## 📋 Compliance Checklist

Before responding to user requests, verify:

- [ ] **No automatic markdown file creation** (unless explicitly requested)
- [ ] **Inline chat response** as default output mode
- [ ] **Minimal code implementation** (only necessary code)
- [ ] **Memory retention** using MCP Memory Server (preferred)
- [ ] **Update memory** after completing work
- [ ] **Query existing memory** before starting new work
- [ ] **No duplicate information** across memory systems
- [ ] **Clean up temporary notes** after session

---

## 🔗 References

- **MCP Memory Server**: <https://github.com/modelcontextprotocol/servers/tree/main/src/memory>
- **MCP Protocol Specification**: v2025-06-18
- **Project Memory Instructions**: `.agents/memory.instructions.md`
- **Project Memory JSONL**: `.agents/memory.jsonl`
- **Amazon Q Rules**: `.amazonq/rules/Memory.md`
- **Agent Guidelines**: `AGENTS.md`
- **Claude Guidelines**: `CLAUDE.md`

---

**Status**: ✅ Active for all AI agents
**Version**: 1.0.0
**Last Updated**: 2025-11-10
