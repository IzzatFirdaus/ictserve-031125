MERGE (m:Memory {name: 'DASHBOARD_PERFORMANCE_OPTIMIZATION'})
SET m.title = 'DASHBOARD_PERFORMANCE_OPTIMIZATION', m.project_id='ictserve-031125', m.source='docs', m.type='document'
MERGE (f:File {path: '/workspace/docs/DASHBOARD_PERFORMANCE_OPTIMIZATION.md'})
SET f.name='DASHBOARD_PERFORMANCE_OPTIMIZATION', f.project_id='ictserve-031125'
MERGE (m)-[:DOCUMENTS]->(f)
MERGE (proj:Memory {name:'ictserve-project-overview'})
MERGE (m)-[:PART_OF]->(proj)

MERGE (m:Memory {name: 'DEPLOYMENT_GUIDE'})
SET m.title = 'DEPLOYMENT_GUIDE', m.project_id='ictserve-031125', m.source='docs', m.type='document'
MERGE (f:File {path: '/workspace/docs/DEPLOYMENT_GUIDE.md'})
SET f.name='DEPLOYMENT_GUIDE', f.project_id='ictserve-031125'
MERGE (m)-[:DOCUMENTS]->(f)
MERGE (proj:Memory {name:'ictserve-project-overview'})
MERGE (m)-[:PART_OF]->(proj)

MERGE (m:Memory {name: 'DEVELOPERS_MCP'})
SET m.title = 'DEVELOPERS_MCP', m.project_id='ictserve-031125', m.source='docs', m.type='document'
MERGE (f:File {path: '/workspace/docs/DEVELOPERS_MCP.md'})
SET f.name='DEVELOPERS_MCP', f.project_id='ictserve-031125'
MERGE (m)-[:DOCUMENTS]->(f)
MERGE (proj:Memory {name:'ictserve-project-overview'})
MERGE (m)-[:PART_OF]->(proj)

MERGE (m:Memory {name: 'GITHUB_MCP_SETUP'})
SET m.title = 'GITHUB_MCP_SETUP', m.project_id='ictserve-031125', m.source='docs', m.type='document'
MERGE (f:File {path: '/workspace/docs/GITHUB_MCP_SETUP.md'})
SET f.name='GITHUB_MCP_SETUP', f.project_id='ictserve-031125'
MERGE (m)-[:DOCUMENTS]->(f)
MERGE (proj:Memory {name:'ictserve-project-overview'})
MERGE (m)-[:PART_OF]->(proj)

MERGE (m:Memory {name: 'ICTServe_System_Documentation'})
SET m.title = 'ICTServe_System_Documentation', m.project_id='ictserve-031125', m.source='docs', m.type='document'
MERGE (f:File {path: '/workspace/docs/ICTServe_System_Documentation.md'})
SET f.name='ICTServe_System_Documentation', f.project_id='ictserve-031125'
MERGE (m)-[:DOCUMENTS]->(f)
MERGE (proj:Memory {name:'ictserve-project-overview'})
MERGE (m)-[:PART_OF]->(proj)

MERGE (m:Memory {name: 'IMPROVEMENT_GAP_ANALYSIS'})
SET m.title = 'IMPROVEMENT_GAP_ANALYSIS', m.project_id='ictserve-031125', m.source='docs', m.type='document'
MERGE (f:File {path: '/workspace/docs/IMPROVEMENT_GAP_ANALYSIS.md'})
SET f.name='IMPROVEMENT_GAP_ANALYSIS', f.project_id='ictserve-031125'
MERGE (m)-[:DOCUMENTS]->(f)
MERGE (proj:Memory {name:'ictserve-project-overview'})
MERGE (m)-[:PART_OF]->(proj)

MERGE (m:Memory {name: 'INDEX'})
SET m.title = 'INDEX', m.project_id='ictserve-031125', m.source='docs', m.type='document'
MERGE (f:File {path: '/workspace/docs/INDEX.md'})
SET f.name='INDEX', f.project_id='ictserve-031125'
MERGE (m)-[:DOCUMENTS]->(f)
MERGE (proj:Memory {name:'ictserve-project-overview'})
MERGE (m)-[:PART_OF]->(proj)

MERGE (m:Memory {name: 'PCTX_CODE_MODE_EXAMPLES'})
SET m.title = 'PCTX_CODE_MODE_EXAMPLES', m.project_id='ictserve-031125', m.source='docs', m.type='document'
MERGE (f:File {path: '/workspace/docs/PCTX_CODE_MODE_EXAMPLES.md'})
SET f.name='PCTX_CODE_MODE_EXAMPLES', f.project_id='ictserve-031125'
MERGE (m)-[:DOCUMENTS]->(f)
MERGE (proj:Memory {name:'ictserve-project-overview'})
MERGE (m)-[:PART_OF]->(proj)

MERGE (m:Memory {name: 'PCTX_IMPLEMENTATION_STATUS'})
SET m.title = 'PCTX_IMPLEMENTATION_STATUS', m.project_id='ictserve-031125', m.source='docs', m.type='document'
MERGE (f:File {path: '/workspace/docs/PCTX_IMPLEMENTATION_STATUS.md'})
SET f.name='PCTX_IMPLEMENTATION_STATUS', f.project_id='ictserve-031125'
MERGE (m)-[:DOCUMENTS]->(f)
MERGE (proj:Memory {name:'ictserve-project-overview'})
MERGE (m)-[:PART_OF]->(proj)

MERGE (m:Memory {name: 'PCTX_INTEGRATION_SETUP'})
SET m.title = 'PCTX_INTEGRATION_SETUP', m.project_id='ictserve-031125', m.source='docs', m.type='document'
MERGE (f:File {path: '/workspace/docs/PCTX_INTEGRATION_SETUP.md'})
SET f.name='PCTX_INTEGRATION_SETUP', f.project_id='ictserve-031125'
MERGE (m)-[:DOCUMENTS]->(f)
MERGE (proj:Memory {name:'ictserve-project-overview'})
MERGE (m)-[:PART_OF]->(proj)

MERGE (m:Memory {name: 'PCTX_INTEGRATION_SUMMARY'})
SET m.title = 'PCTX_INTEGRATION_SUMMARY', m.project_id='ictserve-031125', m.source='docs', m.type='document'
MERGE (f:File {path: '/workspace/docs/PCTX_INTEGRATION_SUMMARY.md'})
SET f.name='PCTX_INTEGRATION_SUMMARY', f.project_id='ictserve-031125'
MERGE (m)-[:DOCUMENTS]->(f)
MERGE (proj:Memory {name:'ictserve-project-overview'})
MERGE (m)-[:PART_OF]->(proj)

MERGE (m:Memory {name: 'PCTX_QUICK_REFERENCE'})
SET m.title = 'PCTX_QUICK_REFERENCE', m.project_id='ictserve-031125', m.source='docs', m.type='document'
MERGE (f:File {path: '/workspace/docs/PCTX_QUICK_REFERENCE.md'})
SET f.name='PCTX_QUICK_REFERENCE', f.project_id='ictserve-031125'
MERGE (m)-[:DOCUMENTS]->(f)
MERGE (proj:Memory {name:'ictserve-project-overview'})
MERGE (m)-[:PART_OF]->(proj)

MERGE (m:Memory {name: 'PCTX_ROLLOUT_CHECKLIST'})
SET m.title = 'PCTX_ROLLOUT_CHECKLIST', m.project_id='ictserve-031125', m.source='docs', m.type='document'
MERGE (f:File {path: '/workspace/docs/PCTX_ROLLOUT_CHECKLIST.md'})
SET f.name='PCTX_ROLLOUT_CHECKLIST', f.project_id='ictserve-031125'
MERGE (m)-[:DOCUMENTS]->(f)
MERGE (proj:Memory {name:'ictserve-project-overview'})
MERGE (m)-[:PART_OF]->(proj)

MERGE (m:Memory {name: 'PCTX_WINDOWS_STATUS'})
SET m.title = 'PCTX_WINDOWS_STATUS', m.project_id='ictserve-031125', m.source='docs', m.type='document'
MERGE (f:File {path: '/workspace/docs/PCTX_WINDOWS_STATUS.md'})
SET f.name='PCTX_WINDOWS_STATUS', f.project_id='ictserve-031125'
MERGE (m)-[:DOCUMENTS]->(f)
MERGE (proj:Memory {name:'ictserve-project-overview'})
MERGE (m)-[:PART_OF]->(proj)

MERGE (m:Memory {name: 'README'})
SET m.title = 'README', m.project_id='ictserve-031125', m.source='docs', m.type='document'
MERGE (f:File {path: '/workspace/docs/README.md'})
SET f.name='README', f.project_id='ictserve-031125'
MERGE (m)-[:DOCUMENTS]->(f)
MERGE (proj:Memory {name:'ictserve-project-overview'})
MERGE (m)-[:PART_OF]->(proj)

MERGE (m:Memory {name: 'deployment-checklist'})
SET m.title = 'deployment-checklist', m.project_id='ictserve-031125', m.source='docs', m.type='document'
MERGE (f:File {path: '/workspace/docs/deployment-checklist.md'})
SET f.name='deployment-checklist', f.project_id='ictserve-031125'
MERGE (m)-[:DOCUMENTS]->(f)
MERGE (proj:Memory {name:'ictserve-project-overview'})
MERGE (m)-[:PART_OF]->(proj)

MERGE (m:Memory {name: 'devtools-mcp-getting-started'})
SET m.title = 'devtools-mcp-getting-started', m.project_id='ictserve-031125', m.source='docs', m.type='document'
MERGE (f:File {path: '/workspace/docs/technical/devtools-mcp-getting-started.md'})
SET f.name='devtools-mcp-getting-started', f.project_id='ictserve-031125'
MERGE (m)-[:DOCUMENTS]->(f)
MERGE (proj:Memory {name:'ictserve-project-overview'})
MERGE (m)-[:PART_OF]->(proj)

MERGE (m:Memory {name: 'docker-troubleshooting'})
SET m.title = 'docker-troubleshooting', m.project_id='ictserve-031125', m.source='docs', m.type='document'
MERGE (f:File {path: '/workspace/docs/docker-troubleshooting.md'})
SET f.name='docker-troubleshooting', f.project_id='ictserve-031125'
MERGE (m)-[:DOCUMENTS]->(f)
MERGE (proj:Memory {name:'ictserve-project-overview'})
MERGE (m)-[:PART_OF]->(proj)

MERGE (m:Memory {name: 'helpdesk-performance-triage'})
SET m.title = 'helpdesk-performance-triage', m.project_id='ictserve-031125', m.source='docs', m.type='document'
MERGE (f:File {path: '/workspace/docs/e2e-triage/helpdesk-performance-triage.md'})
SET f.name='helpdesk-performance-triage', f.project_id='ictserve-031125'
MERGE (m)-[:DOCUMENTS]->(f)
MERGE (proj:Memory {name:'ictserve-project-overview'})
MERGE (m)-[:PART_OF]->(proj)

MERGE (m:Memory {name: 'loan-accessibility-triage'})
SET m.title = 'loan-accessibility-triage', m.project_id='ictserve-031125', m.source='docs', m.type='document'
MERGE (f:File {path: '/workspace/docs/e2e-triage/loan-accessibility-triage.md'})
SET f.name='loan-accessibility-triage', f.project_id='ictserve-031125'
MERGE (m)-[:DOCUMENTS]->(f)
MERGE (proj:Memory {name:'ictserve-project-overview'})
MERGE (m)-[:PART_OF]->(proj)

MERGE (m:Memory {name: 'loan-performance-triage'})
SET m.title = 'loan-performance-triage', m.project_id='ictserve-031125', m.source='docs', m.type='document'
MERGE (f:File {path: '/workspace/docs/e2e-triage/loan-performance-triage.md'})
SET f.name='loan-performance-triage', f.project_id='ictserve-031125'
MERGE (m)-[:DOCUMENTS]->(f)
MERGE (proj:Memory {name:'ictserve-project-overview'})
MERGE (m)-[:PART_OF]->(proj)

MERGE (m:Memory {name: 'performance-optimization-guide'})
SET m.title = 'performance-optimization-guide', m.project_id='ictserve-031125', m.source='docs', m.type='document'
MERGE (f:File {path: '/workspace/docs/performance-optimization-guide.md'})
SET f.name='performance-optimization-guide', f.project_id='ictserve-031125'
MERGE (m)-[:DOCUMENTS]->(f)
MERGE (proj:Memory {name:'ictserve-project-overview'})
MERGE (m)-[:PART_OF]->(proj)

