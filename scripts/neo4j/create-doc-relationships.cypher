// PCTX Documentation relationships
MATCH (pctx_setup:MemoryEntity {name: 'PCTX Integration Setup Guide for ICTServe'})
MATCH (pctx_status:MemoryEntity {name: 'PCTX Implementation Status Report'})
MATCH (pctx_examples:MemoryEntity {name: 'PCTX Code Mode Examples for ICTServe'})
MERGE (pctx_setup)-[:IMPLEMENTS]->(pctx_examples)
MERGE (pctx_status)-[:DOCUMENTS]->(pctx_setup)
MERGE (pctx_status)-[:BLOCKS]->(pctx_examples);

// Main documentation index relationships
MATCH (main_index:MemoryEntity {name: 'ICTServe Documentation Index'})
MATCH (system_doc:MemoryEntity {name: 'Dokumentasi Induk Sistem ICTServe (iServe) (ICTServe Master System Documentation)'})
MATCH (glossary:MemoryEntity {name: 'Glosari Istilah Sistem ICTServe (System Glossary)'})
MATCH (deployment:MemoryEntity {name: 'ICTServe Deployment and Maintenance Guide'})
MATCH (deployment_checklist:MemoryEntity {name: 'ICTServe Deployment Checklist'})
MATCH (perf_guide:MemoryEntity {name: 'ICTServe Performance Optimization Guide'})
MATCH (dashboard_perf:MemoryEntity {name: 'Filament Dashboard Performance Optimization'})
MATCH (gap_analysis:MemoryEntity {name: 'IMPROVEMENT_GAP_ANALYSIS'})
MERGE (main_index)-[:DOCUMENTS]->(system_doc)
MERGE (main_index)-[:DOCUMENTS]->(glossary)
MERGE (main_index)-[:DOCUMENTS]->(deployment)
MERGE (main_index)-[:DOCUMENTS]->(deployment_checklist)
MERGE (main_index)-[:DOCUMENTS]->(perf_guide)
MERGE (deployment)-[:USES]->(deployment_checklist)
MERGE (perf_guide)-[:IMPLEMENTS]->(dashboard_perf)
MERGE (gap_analysis)-[:RELATES_TO]->(system_doc);

// MCP Documentation relationships
MATCH (mcp_dev:MemoryEntity {name: 'DEVELOPERS_MCP'})
MATCH (github_mcp:MemoryEntity {name: 'GitHub MCP Server — local Docker setup'})
MATCH (devtools_mcp:MemoryEntity {name: 'devtools-mcp-getting-started'})
MERGE (mcp_dev)-[:DOCUMENTS]->(github_mcp)
MERGE (mcp_dev)-[:DOCUMENTS]->(devtools_mcp);

// Broadcasting and WebSocket documentation
MATCH (broadcasting:MemoryEntity {name: 'Laravel Echo Broadcasting Setup Guide — ICTServe'})
MATCH (reverb:MemoryEntity {name: 'Laravel Reverb WebSocket Setup - Complete ✅'})
MERGE (broadcasting)-[:USES]->(reverb);

// E2E Testing Triage documentation
MATCH (helpdesk_perf:MemoryEntity {name: 'Helpdesk - Performance Triage'})
MATCH (loan_perf:MemoryEntity {name: 'Loan Module - Performance Triage'})
MATCH (loan_a11y:MemoryEntity {name: 'Loan Module - Accessibility Triage'})
MATCH (perf_opt:MemoryEntity {name: 'ICTServe Performance Optimization Guide'})
MERGE (perf_opt)-[:RESOLVES]->(helpdesk_perf)
MERGE (perf_opt)-[:RESOLVES]->(loan_perf)
MERGE (loan_a11y)-[:RELATES_TO]->(loan_perf);

// Reference documentation relationships
MATCH (user_access:MemoryEntity {name: 'User Access Verification & Authorization Summary'})
MATCH (sla_refactor:MemoryEntity {name: 'SLA Threshold Management - UI Refactor'})
MATCH (quick_fix:MemoryEntity {name: 'Quick Fix Reference - ICTServe UI/UX'})
MATCH (future_ai:MemoryEntity {name: 'Pelan Implementasi AI Chatbot Menggunakan Ollama untuk ICTServe (Future AI Chatbot Implementation Plan Using Ollama)'})
MATCH (system_doc:MemoryEntity {name: 'Dokumentasi Induk Sistem ICTServe (iServe) (ICTServe Master System Documentation)'})
MERGE (user_access)-[:DOCUMENTS]->(system_doc)
MERGE (sla_refactor)-[:DOCUMENTS]->(system_doc)
MERGE (quick_fix)-[:DOCUMENTS]->(system_doc)
MERGE (future_ai)-[:EXTENDS]->(system_doc);

// Docker troubleshooting
MATCH (docker_trouble:MemoryEntity {name: 'Docker — Quick DB troubleshooting'})
MATCH (deployment:MemoryEntity {name: 'ICTServe Deployment and Maintenance Guide'})
MERGE (deployment)-[:USES]->(docker_trouble);

// Documentation import completion
MATCH (doc_import:MemoryEntity {name: 'Documentation Import & Repository Cleanup — Complete ✅'})
MATCH (main_index:MemoryEntity {name: 'ICTServe Documentation Index'})
MERGE (doc_import)-[:DOCUMENTS]->(main_index);
