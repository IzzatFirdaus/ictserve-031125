# KRISA PPSM PKS Compliance Validation Framework

This framework provides comprehensive validation tools for ensuring KRISA documentation compliance with MOTAC's Cyber Security Policy (PKS) and Strategic Digitalization Plan (PSPM).

## Overview

The compliance validation framework addresses critical compliance gaps across the entire KRISA ICTServe documentation suite (D01-D17) for MOTAC's intranet-only deployment, as specified in the requirements and design documents.

## Framework Components

### 1. PKS Reference Validator (`validate-pks-references.php`)

Validates that KRISA documents contain proper PKS policy references per sections:

- **PKS 5.2.1**: Accountability and Non-repudiation principles
- **PKS 9.2.1**: Data transfer procedures and confidentiality protection  
- **PKS 4.2**: Data sovereignty and jurisdiction requirements
- **PKS 5.4.3**: Password policy requirements

**Key Validations:**

- Eliminates "Guest Mode" and anonymous access references
- Ensures proper SSO authentication specifications
- Validates data sovereignty compliance for cloud AI integration
- Checks for mandatory LDAP/Active Directory integration

### 2. KRISA Template Validator (`validate-krisa-templates.php`)

Validates compliance with official KRISA template structure and formatting:

- Required sections (i-viii) per official templates
- Proper document headers (NAMA AGENSI, NAMA AGENSI INDUK, etc.)
- Methodology references ([F1.3], [F2.2], [F2.1], [F2.3])
- CRUD indicators in database documentation
- Proper notation format (BF-ICT-XX-YY)

### 3. Cross-Reference Validator (`validate-cross-references.php`)

Validates document interconnections and version consistency:

- Document cross-references and traceability
- Version consistency across references
- Expected reference patterns (D01↔D02-D17, D02↔D03, etc.)
- Orphaned document detection

### 4. Version Control Tracker (`version-control-tracker.php`)

Manages version control following KRISA guidelines:

- Version format validation (X.Y format)
- Change log management in section iii. Kawalan Dokumen
- Target version tracking for compliance updates
- Automated version updates with change descriptions

### 5. Master Validator (`run-all-validations.php`)

Comprehensive validation runner that:

- Executes all validation checks
- Generates executive summary
- Creates compliance action plan
- Provides prioritized remediation guidance

## Usage

### Run All Validations

```bash
php scripts/compliance/run-all-validations.php
```

### Individual Validators

```bash
# PKS compliance validation
php scripts/compliance/validate-pks-references.php

# KRISA template validation  
php scripts/compliance/validate-krisa-templates.php

# Cross-reference validation
php scripts/compliance/validate-cross-references.php

# Version control validation
php scripts/compliance/version-control-tracker.php report
```

### Version Management

```bash
# Update document version
php scripts/compliance/version-control-tracker.php update D02 4.0 "BPM Team" "PKS compliance update"

# View change log
php scripts/compliance/version-control-tracker.php changelog D02
```

## Configuration

The framework is configured via `config/compliance.php` which defines:

- PKS policy sections and requirements
- KRISA template structure requirements  
- Document paths and target versions
- Validation thresholds and criteria
- Agency information and standards

## Authority Sources

### Primary Policy Sources

- `_reference/Polisi_PKS.md` - MOTAC Cyber Security Policy
- `_reference/Ringkasan_Eksekutif_PSPM.md` - MOTAC Strategic Digitalization Plan

### KRISA Template Standards

- `docs/KRISA/OFFICIAL-TEMPLATE-AND-SAMPLES/` - Official KRISA templates

### Specification Documents

- `.kiro/specs/krisa-ppsm-pks-compliance-update/requirements.md` - 11 requirements with 55 acceptance criteria
- `.kiro/specs/krisa-ppsm-pks-compliance-update/design.md` - 8 correctness properties and architectural specifications

## Compliance Criteria

### PKS Policy Compliance (Critical)

- ✅ Zero guest access references (PKS 5.2.1)
- ✅ Proper authentication specifications
- ✅ Data sovereignty controls (PKS 9.2.1 & 4.2)
- ✅ Password policy documentation (PKS 5.4.3)

### KRISA Template Compliance (High)

- ✅ All required sections (i-viii)
- ✅ Proper document headers
- ✅ Methodology references
- ✅ CRUD indicators (D09)
- ✅ Notation format compliance

### Cross-Reference Consistency (Medium)

- ✅ Version consistency across documents
- ✅ Proper document interconnections
- ✅ Traceability statements
- ✅ No orphaned references

### Version Control (Low)

- ✅ KRISA version format (X.Y)
- ✅ Complete change logs
- ✅ Target version compliance

## Output Reports

All validations generate detailed reports saved to `storage/compliance/`:

- `comprehensive-compliance-report-TIMESTAMP.txt` - Master report
- `pks-validation-TIMESTAMP.txt` - PKS compliance details
- `krisa-template-validation-TIMESTAMP.txt` - Template compliance details
- `cross-reference-validation-TIMESTAMP.txt` - Cross-reference analysis
- `version-control-validation-TIMESTAMP.txt` - Version control status

## Integration with Task Implementation

This framework supports Task 1 of the KRISA PPSM PKS Compliance Update:

**Task 1: Set up compliance validation framework and reference materials**

- ✅ PKS reference validation per sections 5.2.1, 9.2.1, 4.2, 5.4.3
- ✅ KRISA template compliance verification using official templates
- ✅ Cross-reference verification tools for document interconnections
- ✅ Version control tracking system following KRISA guidelines

## Requirements Traceability

This framework addresses:

- **Requirements 10.1-10.5**: Version control and change tracking
- **Design Properties 3, 5**: KRISA template structure and cross-document consistency
- **PKS Authority**: Sections 5.2.1, 9.2.1 compliance validation
- **KRISA Authority**: All official template compliance verification

## Exit Codes

- `0`: All validations passed, fully compliant
- `1`: Violations found, remediation required

## Support

For issues or questions regarding the compliance framework, refer to:

- Task specification: `.kiro/specs/krisa-ppsm-pks-compliance-update/tasks.md`
- Requirements document: `.kiro/specs/krisa-ppsm-pks-compliance-update/requirements.md`
- Design document: `.kiro/specs/krisa-ppsm-pks-compliance-update/design.md`
