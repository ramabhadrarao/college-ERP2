// modules/academic-structure/fields.js - Creates fields for academic structure collections
const utils = require('../../utils');

async function createFields() {
  // Departments Fields
  const departmentsFields = [
    { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
    { field: 'name', type: 'string', schema: { length: 255, is_nullable: false }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Department Name' } } },
    { field: 'code', type: 'string', schema: { length: 50, is_nullable: false, is_unique: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Department Code' } } },
    { field: 'college_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'hod_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'logo', type: 'string', schema: { length: 255, is_nullable: true }, meta: { interface: 'file-image', width: 'half' } },
    { field: 'description', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-multiline', width: 'full', options: { placeholder: 'Description' } } },
    { field: 'email', type: 'string', schema: { length: 100, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Email' } } },
    { field: 'phone', type: 'string', schema: { length: 20, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Phone' } } },
    { field: 'established_date', type: 'date', schema: { is_nullable: true }, meta: { interface: 'datetime', width: 'half' } },
    { field: 'status', type: 'string', schema: { length: 20, default_value: 'active', is_nullable: true }, meta: { interface: 'select-dropdown', width: 'half', options: { choices: [{ text: 'Active', value: 'active' }, { text: 'Inactive', value: 'inactive' }] } } },
    { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
    { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
  ];

  for (const field of departmentsFields) {
    await utils.createField('departments', field);
  }

  // Programs Fields
  const programsFields = [
    { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
    { field: 'name', type: 'string', schema: { length: 255, is_nullable: false }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Program Name' } } },
    { field: 'code', type: 'string', schema: { length: 50, is_nullable: false, is_unique: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Program Code' } } },
    { field: 'department_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'coordinator_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'duration', type: 'string', schema: { length: 50, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Duration' } } },
    { field: 'degree_type', type: 'string', schema: { length: 50, is_nullable: true }, meta: { interface: 'select-dropdown', width: 'half', options: { choices: [{ text: "Bachelor's", value: "Bachelor's" }, { text: "Master's", value: "Master's" }, { text: "Doctoral", value: "Doctoral" }] } } },
    { field: 'description', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-multiline', width: 'full', options: { placeholder: 'Description' } } },
    { field: 'status', type: 'string', schema: { length: 20, default_value: 'active', is_nullable: true }, meta: { interface: 'select-dropdown', width: 'half', options: { choices: [{ text: 'Active', value: 'active' }, { text: 'Inactive', value: 'inactive' }] } } },
    { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
    { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
  ];

  for (const field of programsFields) {
    await utils.createField('programs', field);
  }

  // Branches Fields
  const branchesFields = [
    { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
    { field: 'name', type: 'string', schema: { length: 255, is_nullable: false }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Branch Name' } } },
    { field: 'code', type: 'string', schema: { length: 50, is_nullable: false, is_unique: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Branch Code' } } },
    { field: 'program_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'coordinator_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'description', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-multiline', width: 'full', options: { placeholder: 'Description' } } },
    { field: 'status', type: 'string', schema: { length: 20, default_value: 'active', is_nullable: true }, meta: { interface: 'select-dropdown', width: 'half', options: { choices: [{ text: 'Active', value: 'active' }, { text: 'Inactive', value: 'inactive' }] } } },
    { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
    { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
  ];

  for (const field of branchesFields) {
    await utils.createField('branches', field);
  }

  // Regulations Fields
  const regulationsFields = [
    { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
    { field: 'name', type: 'string', schema: { length: 255, is_nullable: false }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Regulation Name' } } },
    { field: 'code', type: 'string', schema: { length: 50, is_nullable: false, is_unique: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Regulation Code' } } },
    { field: 'program_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'branch_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'effective_from_year', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Effective From Year' } } },
    { field: 'effective_to_year', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Effective To Year' } } },
    { field: 'description', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-multiline', width: 'full', options: { placeholder: 'Description' } } },
    { field: 'status', type: 'string', schema: { length: 20, default_value: 'active', is_nullable: true }, meta: { interface: 'select-dropdown', width: 'half', options: { choices: [{ text: 'Active', value: 'active' }, { text: 'Inactive', value: 'inactive' }] } } },
    { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
    { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
  ];

  for (const field of regulationsFields) {
    await utils.createField('regulations', field);
  }

  // Semesters Fields
  const semestersFields = [
    { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
    { field: 'name', type: 'string', schema: { length: 255, is_nullable: false }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Semester Name' } } },
    { field: 'academic_year_id', type: 'integer', schema: { is_nullable: false }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'regulation_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'start_date', type: 'date', schema: { is_nullable: true }, meta: { interface: 'datetime', width: 'half' } },
    { field: 'end_date', type: 'date', schema: { is_nullable: true }, meta: { interface: 'datetime', width: 'half' } },
    { field: 'status', type: 'string', schema: { length: 20, default_value: 'upcoming', is_nullable: true }, meta: { interface: 'select-dropdown', width: 'half', options: { choices: [{ text: 'Upcoming', value: 'upcoming' }, { text: 'Active', value: 'active' }, { text: 'Completed', value: 'completed' }] } } },
    { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
    { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
  ];

  for (const field of semestersFields) {
    await utils.createField('semesters', field);
  }

  // Batches Fields
  const batchesFields = [
    { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
    { field: 'name', type: 'string', schema: { length: 50, is_nullable: false }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Batch Name' } } },
    { field: 'program_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'branch_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'start_year', type: 'integer', schema: { is_nullable: false }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Start Year' } } },
    { field: 'end_year', type: 'integer', schema: { is_nullable: false }, meta: { interface: 'input', width: 'half', options: { placeholder: 'End Year' } } },
    { field: 'mentor_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'status', type: 'string', schema: { length: 20, default_value: 'active', is_nullable: true }, meta: { interface: 'select-dropdown', width: 'half', options: { choices: [{ text: 'Active', value: 'active' }, { text: 'Inactive', value: 'inactive' }, { text: 'Graduated', value: 'graduated' }] } } },
    { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
    { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
  ];

  for (const field of batchesFields) {
    await utils.createField('batches', field);
  }

  // Student Types Fields
  const studentTypesFields = [
    { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
    { field: 'name', type: 'string', schema: { length: 50, is_nullable: false, is_unique: true }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Student Type Name' } } },
    { field: 'description', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-multiline', width: 'full', options: { placeholder: 'Description' } } },
    { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
    { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
  ];

  for (const field of studentTypesFields) {
    await utils.createField('student_types', field);
  }
}

module.exports = {
  createFields
};