// modules/core-schema/fields.js - Creates fields for core schema collections
const utils = require('../../utils');

async function createFields() {
  // College Fields
  const collegeFields = [
    { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
    { field: 'name', type: 'string', schema: { length: 255, is_nullable: false }, meta: { interface: 'input', width: 'full', options: { placeholder: 'College Name' } } },
    { field: 'code', type: 'string', schema: { length: 50, is_nullable: false, is_unique: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'College Code' } } },
    { field: 'logo', type: 'string', schema: { length: 255, is_nullable: true }, meta: { interface: 'file-image', width: 'half' } },
    { field: 'website', type: 'string', schema: { length: 255, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Website URL' } } },
    { field: 'address', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-multiline', width: 'full', options: { placeholder: 'College Address' } } },
    { field: 'phone', type: 'string', schema: { length: 20, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Phone Number' } } },
    { field: 'email', type: 'string', schema: { length: 100, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Email Address' } } },
    { field: 'status', type: 'string', schema: { length: 20, default_value: 'active', is_nullable: true }, meta: { interface: 'select-dropdown', width: 'half', options: { choices: [{ text: 'Active', value: 'active' }, { text: 'Inactive', value: 'inactive' }] } } },
    { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
    { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
  ];

  for (const field of collegeFields) {
    await utils.createField('college', field);
  }

  // System Settings Fields
  const systemSettingsFields = [
    { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
    { field: 'setting_key', type: 'string', schema: { length: 50, is_nullable: false, is_unique: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Setting Key' } } },
    { field: 'setting_value', type: 'text', schema: { is_nullable: false }, meta: { interface: 'input-multiline', width: 'full', options: { placeholder: 'Setting Value' } } },
    { field: 'setting_group', type: 'string', schema: { length: 50, is_nullable: true }, meta: { interface: 'select-dropdown', width: 'half', options: { choices: [{ text: 'General', value: 'general' }, { text: 'Academic', value: 'academic' }, { text: 'Features', value: 'features' }, { text: 'Other', value: 'other' }] } } },
    { field: 'is_public', type: 'boolean', schema: { default_value: false, is_nullable: true }, meta: { interface: 'boolean', width: 'half' } },
    { field: 'description', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-multiline', width: 'full', options: { placeholder: 'Description' } } },
    { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
    { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
  ];

  for (const field of systemSettingsFields) {
    await utils.createField('system_settings', field);
  }

  // Blood Groups Fields
  const bloodGroupsFields = [
    { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
    { field: 'blood_group', type: 'string', schema: { length: 5, is_nullable: false, is_unique: true }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Blood Group' } } },
    { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
    { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
  ];

  for (const field of bloodGroupsFields) {
    await utils.createField('blood_groups', field);
  }

  // Gender Fields
  const genderFields = [
    { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
    { field: 'name', type: 'string', schema: { length: 20, is_nullable: false, is_unique: true }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Gender' } } },
    { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
    { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
  ];

  for (const field of genderFields) {
    await utils.createField('gender', field);
  }

  // Nationality Fields
  const nationalityFields = [
    { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
    { field: 'name', type: 'string', schema: { length: 50, is_nullable: false, is_unique: true }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Nationality' } } },
    { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
    { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
  ];

  for (const field of nationalityFields) {
    await utils.createField('nationality', field);
  }

  // Religion Fields
  const religionFields = [
    { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
    { field: 'name', type: 'string', schema: { length: 50, is_nullable: false, is_unique: true }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Religion' } } },
    { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
    { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
  ];

  for (const field of religionFields) {
    await utils.createField('religion', field);
  }

  // Caste Fields
  const casteFields = [
    { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
    { field: 'name', type: 'string', schema: { length: 50, is_nullable: false, is_unique: true }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Caste' } } },
    { field: 'category', type: 'string', schema: { length: 50, is_nullable: true }, meta: { interface: 'select-dropdown', width: 'half', options: { choices: [{ text: 'General', value: 'General' }, { text: 'OBC', value: 'OBC' }, { text: 'SC', value: 'SC' }, { text: 'ST', value: 'ST' }, { text: 'Other', value: 'Other' }] } } },
    { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
    { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
  ];

  for (const field of casteFields) {
    await utils.createField('caste', field);
  }

  // Sub Caste Fields
  const subCasteFields = [
    { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
    { field: 'name', type: 'string', schema: { length: 50, is_nullable: false }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Sub Caste' } } },
    { field: 'caste_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
    { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
  ];

  for (const field of subCasteFields) {
    await utils.createField('sub_caste', field);
  }

  // States Fields
  const statesFields = [
    { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
    { field: 'name', type: 'string', schema: { length: 50, is_nullable: false, is_unique: true }, meta: { interface: 'input', width: 'full', options: { placeholder: 'State Name' } } },
    { field: 'country', type: 'string', schema: { length: 50, default_value: 'India', is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Country' } } },
    { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
    { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
  ];

  for (const field of statesFields) {
    await utils.createField('states', field);
  }

  // Districts Fields
  const districtsFields = [
    { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
    { field: 'name', type: 'string', schema: { length: 50, is_nullable: false }, meta: { interface: 'input', width: 'full', options: { placeholder: 'District Name' } } },
    { field: 'state_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
    { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
  ];

  for (const field of districtsFields) {
    await utils.createField('districts', field);
  }

  // Academic Years Fields
  const academicYearsFields = [
    { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
    { field: 'year_name', type: 'string', schema: { length: 20, is_nullable: false }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Academic Year (e.g., 2023-2024)' } } },
    { field: 'start_date', type: 'date', schema: { is_nullable: false }, meta: { interface: 'datetime', width: 'half' } },
    { field: 'end_date', type: 'date', schema: { is_nullable: false }, meta: { interface: 'datetime', width: 'half' } },
    { field: 'status', type: 'string', schema: { length: 20, default_value: 'upcoming', is_nullable: true }, meta: { interface: 'select-dropdown', width: 'half', options: { choices: [{ text: 'Upcoming', value: 'upcoming' }, { text: 'Active', value: 'active' }, { text: 'Completed', value: 'completed' }] } } },
    { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
    { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
  ];

  for (const field of academicYearsFields) {
    await utils.createField('academic_years', field);
  }

  // Rooms Fields
  const roomsFields = [
    { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
    { field: 'room_number', type: 'string', schema: { length: 20, is_nullable: false }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Room Number' } } },
    { field: 'building', type: 'string', schema: { length: 100, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Building' } } },
    { field: 'floor', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Floor' } } },
    { field: 'room_type', type: 'string', schema: { length: 20, default_value: 'Classroom', is_nullable: true }, meta: { interface: 'select-dropdown', width: 'half', options: { choices: [{ text: 'Classroom', value: 'Classroom' }, { text: 'Laboratory', value: 'Laboratory' }, { text: 'Conference Room', value: 'Conference Room' }, { text: 'Office', value: 'Office' }, { text: 'Other', value: 'Other' }] } } },
    { field: 'capacity', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Capacity' } } },
    { field: 'has_projector', type: 'boolean', schema: { default_value: false, is_nullable: true }, meta: { interface: 'boolean', width: 'half' } },
    { field: 'has_computer', type: 'boolean', schema: { default_value: false, is_nullable: true }, meta: { interface: 'boolean', width: 'half' } },
    { field: 'has_ac', type: 'boolean', schema: { default_value: false, is_nullable: true }, meta: { interface: 'boolean', width: 'half' } },
    { field: 'status', type: 'string', schema: { length: 20, default_value: 'active', is_nullable: true }, meta: { interface: 'select-dropdown', width: 'half', options: { choices: [{ text: 'Active', value: 'active' }, { text: 'Inactive', value: 'inactive' }, { text: 'Under Maintenance', value: 'maintenance' }] } } },
    { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
    { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
  ];

  for (const field of roomsFields) {
    await utils.createField('rooms', field);
  }
}

module.exports = {
  createFields
};