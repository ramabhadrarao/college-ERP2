// modules/curriculum/fields.js - Creates fields for curriculum collections
const utils = require('../../utils');

async function createFields() {
  // Fields for Course Types
  const courseTypesFields = [
    { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
    { field: 'name', type: 'string', schema: { length: 50, is_nullable: false }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Course Type Name' } } },
    { field: 'description', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-multiline', width: 'full', options: { placeholder: 'Description' } } },
    { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
    { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
  ];

  for (const field of courseTypesFields) {
    await utils.createField('course_types', field);
  }

  // Fields for Courses
  const coursesFields = [
    { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
    { field: 'course_code', type: 'string', schema: { length: 20, is_nullable: false, is_unique: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Course Code' } } },
    { field: 'name', type: 'string', schema: { length: 255, is_nullable: false }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Course Name' } } },
    { field: 'semester_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'branch_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'regulation_id', type: 'integer', schema: { is_nullable: false }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'course_type_id', type: 'integer', schema: { is_nullable: false }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'credits', type: 'integer', schema: { is_nullable: false }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Credits' } } },
    { field: 'syllabus', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-rich-text-md', width: 'full', options: { placeholder: 'Syllabus' } } },
    { field: 'description', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-multiline', width: 'full', options: { placeholder: 'Description' } } },
    { field: 'objectives', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-rich-text-md', width: 'full', options: { placeholder: 'Objectives' } } },
    { field: 'outcomes', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-rich-text-md', width: 'full', options: { placeholder: 'Outcomes' } } },
    { field: 'prerequisites', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-multiline', width: 'full', options: { placeholder: 'Prerequisites' } } },
    { field: 'status', type: 'string', schema: { length: 20, default_value: 'active', is_nullable: true }, meta: { interface: 'select-dropdown', width: 'half', options: { choices: [{ text: 'Active', value: 'active' }, { text: 'Inactive', value: 'inactive' }] } } },
    { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
    { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
  ];

  for (const field of coursesFields) {
    await utils.createField('courses', field);
  }

  // Fields for Course Coordinators
  const courseCoordinatorsFields = [
    { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
    { field: 'course_id', type: 'integer', schema: { is_nullable: false }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'faculty_id', type: 'integer', schema: { is_nullable: false }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'semester_id', type: 'integer', schema: { is_nullable: false }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'is_primary', type: 'boolean', schema: { default_value: false, is_nullable: true }, meta: { interface: 'boolean', width: 'half' } },
    { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
    { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
  ];

  for (const field of courseCoordinatorsFields) {
    await utils.createField('course_coordinators', field);
  }

  // Similar pattern for other collections
  // For brevity, I'm not including all fields for all collections
  // In a real implementation, you would define fields for each collection following the pattern above

  // Fields for Module Types
  const moduleTypesFields = [
    { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
    { field: 'name', type: 'string', schema: { length: 50, is_nullable: false, is_unique: true }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Module Type Name' } } },
    { field: 'description', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-multiline', width: 'full', options: { placeholder: 'Description' } } },
    { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
    { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
  ];

  for (const field of moduleTypesFields) {
    await utils.createField('module_types', field);
  }

  // Fields for Course Modules
  const courseModulesFields = [
    { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
    { field: 'course_id', type: 'integer', schema: { is_nullable: false }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'faculty_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'title', type: 'string', schema: { length: 255, is_nullable: false }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Module Title' } } },
    { field: 'description', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-multiline', width: 'full', options: { placeholder: 'Description' } } },
    { field: 'module_type_id', type: 'integer', schema: { is_nullable: false }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'order_index', type: 'integer', schema: { default_value: 0, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Order' } } },
    { field: 'status', type: 'string', schema: { length: 20, default_value: 'draft', is_nullable: true }, meta: { interface: 'select-dropdown', width: 'half', options: { choices: [{ text: 'Draft', value: 'draft' }, { text: 'Published', value: 'published' }, { text: 'Archived', value: 'archived' }] } } },
    { field: 'start_date', type: 'timestamp', schema: { is_nullable: true }, meta: { interface: 'datetime', width: 'half' } },
    { field: 'end_date', type: 'timestamp', schema: { is_nullable: true }, meta: { interface: 'datetime', width: 'half' } },
    { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
    { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
  ];

  for (const field of courseModulesFields) {
    await utils.createField('course_modules', field);
  }

  // Fields for Assignments
  const assignmentsFields = [
    { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
    { field: 'module_id', type: 'integer', schema: { is_nullable: false }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'title', type: 'string', schema: { length: 255, is_nullable: false }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Assignment Title' } } },
    { field: 'description', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-rich-text-md', width: 'full', options: { placeholder: 'Description' } } },
    { field: 'instructions', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-rich-text-md', width: 'full', options: { placeholder: 'Instructions' } } },
    { field: 'start_date', type: 'timestamp', schema: { is_nullable: true }, meta: { interface: 'datetime', width: 'half' } },
    { field: 'due_date', type: 'timestamp', schema: { is_nullable: true }, meta: { interface: 'datetime', width: 'half' } },
    { field: 'max_marks', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Maximum Marks' } } },
    { field: 'attachment_id', type: 'uuid', schema: { is_nullable: true }, meta: { interface: 'file', width: 'half' } },
    { field: 'allow_late_submission', type: 'boolean', schema: { default_value: false, is_nullable: true }, meta: { interface: 'boolean', width: 'half' } },
    { field: 'late_submission_penalty', type: 'integer', schema: { default_value: 0, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Late Submission Penalty (%)' } } },
    { field: 'status', type: 'string', schema: { length: 20, default_value: 'draft', is_nullable: true }, meta: { interface: 'select-dropdown', width: 'half', options: { choices: [{ text: 'Draft', value: 'draft' }, { text: 'Published', value: 'published' }, { text: 'Closed', value: 'closed' }] } } },
    { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
    { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
  ];

  for (const field of assignmentsFields) {
    await utils.createField('assignments', field);
  }
}

module.exports = {
  createFields
};