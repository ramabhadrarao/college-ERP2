// modules/faculty-management/fields.js - Creates fields for faculty management collections
const utils = require('../../utils');

async function createFields() {
  // Faculty Fields
  const facultyFields = [
    { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
    { field: 'user_id', type: 'uuid', schema: { is_nullable: false, is_unique: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'regdno', type: 'string', schema: { length: 20, is_nullable: false, is_unique: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Registration Number' } } },
    { field: 'first_name', type: 'string', schema: { length: 50, is_nullable: false }, meta: { interface: 'input', width: 'half', options: { placeholder: 'First Name' } } },
    { field: 'last_name', type: 'string', schema: { length: 50, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Last Name' } } },
    { field: 'gender_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'dob', type: 'date', schema: { is_nullable: true }, meta: { interface: 'datetime', width: 'half' } },
    { field: 'contact_no', type: 'string', schema: { length: 15, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Contact Number' } } },
    { field: 'email', type: 'string', schema: { length: 100, is_nullable: false, is_unique: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Email' } } },
    { field: 'department_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'designation', type: 'string', schema: { length: 100, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Designation' } } },
    { field: 'qualification', type: 'string', schema: { length: 255, is_nullable: true }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Qualification' } } },
    { field: 'specialization', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-multiline', width: 'full', options: { placeholder: 'Specialization' } } },
    { field: 'join_date', type: 'date', schema: { is_nullable: false }, meta: { interface: 'datetime', width: 'half' } },
    { field: 'address', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-multiline', width: 'full', options: { placeholder: 'Address' } } },
    { field: 'blood_group_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'is_active', type: 'boolean', schema: { default_value: true, is_nullable: true }, meta: { interface: 'boolean', width: 'half' } },
    { field: 'edit_enabled', type: 'boolean', schema: { default_value: true, is_nullable: true }, meta: { interface: 'boolean', width: 'half' } },
    { field: 'aadhar_attachment_id', type: 'uuid', schema: { is_nullable: true }, meta: { interface: 'file', width: 'half' } },
    { field: 'pan_attachment_id', type: 'uuid', schema: { is_nullable: true }, meta: { interface: 'file', width: 'half' } },
    { field: 'photo_attachment_id', type: 'uuid', schema: { is_nullable: true }, meta: { interface: 'file-image', width: 'half' } },
    { field: 'visibility', type: 'string', schema: { length: 10, default_value: 'show', is_nullable: true }, meta: { interface: 'select-dropdown', width: 'half', options: { choices: [{ text: 'Show', value: 'show' }, { text: 'Hide', value: 'hide' }] } } },
    { field: 'status', type: 'string', schema: { length: 20, default_value: 'active', is_nullable: true }, meta: { interface: 'select-dropdown', width: 'half', options: { choices: [{ text: 'Active', value: 'active' }, { text: 'Inactive', value: 'inactive' }, { text: 'On Leave', value: 'on_leave' }] } } },
    { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
    { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
  ];

  for (const field of facultyFields) {
    await utils.createField('faculty', field);
  }

  // Faculty Additional Details Fields
  const additionalDetailsFields = [
    { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
    { field: 'faculty_id', type: 'integer', schema: { is_nullable: false }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'father_name', type: 'string', schema: { length: 255, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Father Name' } } },
    { field: 'father_occupation', type: 'string', schema: { length: 255, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Father Occupation' } } },
    { field: 'mother_name', type: 'string', schema: { length: 255, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Mother Name' } } },
    { field: 'mother_occupation', type: 'string', schema: { length: 255, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Mother Occupation' } } },
    { field: 'marital_status', type: 'string', schema: { length: 20, is_nullable: true }, meta: { interface: 'select-dropdown', width: 'half', options: { choices: [{ text: 'Single', value: 'Single' }, { text: 'Married', value: 'Married' }, { text: 'Divorced', value: 'Divorced' }, { text: 'Widowed', value: 'Widowed' }] } } },
    { field: 'spouse_name', type: 'string', schema: { length: 255, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Spouse Name' } } },
    { field: 'spouse_occupation', type: 'string', schema: { length: 255, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Spouse Occupation' } } },
    { field: 'nationality_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'religion_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'caste_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'sub_caste_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'aadhar_no', type: 'string', schema: { length: 20, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Aadhar Number' } } },
    { field: 'pan_no', type: 'string', schema: { length: 20, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'PAN Number' } } },
    { field: 'contact_no2', type: 'string', schema: { length: 20, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Alternate Contact Number' } } },
    { field: 'permanent_address', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-multiline', width: 'full', options: { placeholder: 'Permanent Address' } } },
    { field: 'correspondence_address', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-multiline', width: 'full', options: { placeholder: 'Correspondence Address' } } },
    { field: 'scopus_author_id', type: 'string', schema: { length: 255, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Scopus Author ID' } } },
    { field: 'orcid_id', type: 'string', schema: { length: 255, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'ORCID ID' } } },
    { field: 'google_scholar_id_link', type: 'string', schema: { length: 255, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Google Scholar ID/Link' } } },
    { field: 'aicte_id', type: 'string', schema: { length: 255, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'AICTE ID' } } },
    { field: 'visibility', type: 'string', schema: { length: 10, default_value: 'show', is_nullable: true }, meta: { interface: 'select-dropdown', width: 'half', options: { choices: [{ text: 'Show', value: 'show' }, { text: 'Hide', value: 'hide' }] } } },
    { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
    { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
  ];

  for (const field of additionalDetailsFields) {
    await utils.createField('faculty_additional_details', field);
  }

  // Work Experiences Fields
  const workExperiencesFields = [
    { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
    { field: 'faculty_id', type: 'integer', schema: { is_nullable: false }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'institution_name', type: 'string', schema: { length: 255, is_nullable: false }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Institution Name' } } },
    { field: 'experience_type', type: 'string', schema: { length: 20, is_nullable: false }, meta: { interface: 'select-dropdown', width: 'half', options: { choices: [{ text: 'Teaching', value: 'Teaching' }, { text: 'Research', value: 'Research' }, { text: 'Industry', value: 'Industry' }, { text: 'Administrative', value: 'Administrative' }, { text: 'Other', value: 'Other' }] } } },
    { field: 'designation', type: 'string', schema: { length: 255, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Designation' } } },
    { field: 'from_date', type: 'date', schema: { is_nullable: true }, meta: { interface: 'datetime', width: 'half' } },
    { field: 'to_date', type: 'date', schema: { is_nullable: true }, meta: { interface: 'datetime', width: 'half' } },
    { field: 'number_of_years', type: 'decimal', schema: { precision: 5, scale: 2, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Number of Years' } } },
    { field: 'responsibilities', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-multiline', width: 'full', options: { placeholder: 'Responsibilities' } } },
    { field: 'service_certificate_attachment_id', type: 'uuid', schema: { is_nullable: true }, meta: { interface: 'file', width: 'half' } },
    { field: 'visibility', type: 'string', schema: { length: 10, default_value: 'show', is_nullable: true }, meta: { interface: 'select-dropdown', width: 'half', options: { choices: [{ text: 'Show', value: 'show' }, { text: 'Hide', value: 'hide' }] } } },
    { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
    { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
  ];

  for (const field of workExperiencesFields) {
    await utils.createField('work_experiences', field);
  }

  // Faculty Qualifications Fields
  const qualificationsFields = [
    { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
    { field: 'faculty_id', type: 'integer', schema: { is_nullable: false }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'degree', type: 'string', schema: { length: 100, is_nullable: false }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Degree' } } },
    { field: 'specialization', type: 'string', schema: { length: 100, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Specialization' } } },
    { field: 'institution', type: 'string', schema: { length: 200, is_nullable: false }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Institution' } } },
    { field: 'board_university', type: 'string', schema: { length: 200, is_nullable: true }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Board/University' } } },
    { field: 'passing_year', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Passing Year' } } },
    { field: 'percentage_cgpa', type: 'string', schema: { length: 20, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Percentage/CGPA' } } },
    { field: 'certificate_attachment_id', type: 'uuid', schema: { is_nullable: true }, meta: { interface: 'file', width: 'half' } },
    { field: 'visibility', type: 'string', schema: { length: 10, default_value: 'show', is_nullable: true }, meta: { interface: 'select-dropdown', width: 'half', options: { choices: [{ text: 'Show', value: 'show' }, { text: 'Hide', value: 'hide' }] } } },
    { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
    { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
  ];

  for (const field of qualificationsFields) {
    await utils.createField('faculty_qualifications', field);
  }

  // For the rest of the tables, we'll create basic fields patterns and apply them
  // This demonstrates how to handle similar tables in a more efficient way
  
  // Generic type tables (publication_type, intellectual_property_status, etc.)
  const typeTablesList = [
    'publication_type', 
    'intellectual_property_status', 
    'workshop_type', 
    'mdp_fdp_type', 
    'award_category', 
    'conference_role'
  ];
  
  for (const table of typeTablesList) {
    const typeFields = [
      { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
      { field: 'name', type: 'string', schema: { length: 50, is_nullable: false, is_unique: true }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Name' } } },
      { field: 'description', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-multiline', width: 'full', options: { placeholder: 'Description' } } },
      { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
      { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
    ];
    
    for (const field of typeFields) {
      await utils.createField(table, field);
    }
  }
  
  // Funding Agency has a few specific fields
  const fundingAgencyFields = [
    { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
    { field: 'name', type: 'string', schema: { length: 100, is_nullable: false, is_unique: true }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Name' } } },
    { field: 'agency_type', type: 'string', schema: { length: 50, is_nullable: true }, meta: { interface: 'select-dropdown', width: 'half', options: { choices: [
          { text: 'Government', value: 'Government' }, 
          { text: 'Private', value: 'Private' }, 
          { text: 'International', value: 'International' }, 
          { text: 'Non-Profit', value: 'Non-Profit' }, 
          { text: 'Other', value: 'Other' }
        ] } } },
    { field: 'website', type: 'string', schema: { length: 100, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Website' } } },
    { field: 'contact_info', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-multiline', width: 'full', options: { placeholder: 'Contact Information' } } },
    { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
    { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
  ];
  
  for (const field of fundingAgencyFields) {
    await utils.createField('funding_agency', field);
  }
  
  // Create fields for faculty achievement tables
  // These are common fields across many faculty achievement tables
  const createCommonAchievementFields = async (tableName, specificFields = []) => {
    const commonFields = [
      { field: 'id', type: 'integer', schema: { is_primary_key: true, has_auto_increment: true }, meta: { interface: 'input', readonly: true } },
      { field: 'faculty_id', type: 'integer', schema: { is_nullable: false }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
      { field: 'visibility', type: 'string', schema: { length: 10, default_value: 'show', is_nullable: true }, meta: { interface: 'select-dropdown', width: 'half', options: { choices: [{ text: 'Show', value: 'show' }, { text: 'Hide', value: 'hide' }] } } },
      { field: 'date_created', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } },
      { field: 'date_updated', type: 'timestamp', schema: { default_value: 'CURRENT_TIMESTAMP', is_nullable: true }, meta: { interface: 'datetime', readonly: true, width: 'half', display: 'datetime' } }
    ];
    
    const allFields = [...commonFields, ...specificFields];
    
    for (const field of allFields) {
      await utils.createField(tableName, field);
    }
  };
  
  // Teaching Activities
  await createCommonAchievementFields('teaching_activities', [
    { field: 'course_name', type: 'string', schema: { length: 200, is_nullable: false }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Course Name' } } },
    { field: 'semester_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'academic_year_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'course_code', type: 'string', schema: { length: 20, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Course Code' } } },
    { field: 'description', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-multiline', width: 'full', options: { placeholder: 'Description' } } },
    { field: 'attachment_id', type: 'uuid', schema: { is_nullable: true }, meta: { interface: 'file', width: 'half' } }
  ]);
  
  // Research Publications
  await createCommonAchievementFields('research_publications', [
    { field: 'title', type: 'string', schema: { length: 200, is_nullable: false }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Publication Title' } } },
    { field: 'journal_name', type: 'string', schema: { length: 200, is_nullable: true }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Journal Name' } } },
    { field: 'type_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'publication_date', type: 'date', schema: { is_nullable: true }, meta: { interface: 'datetime', width: 'half' } },
    { field: 'doi', type: 'string', schema: { length: 50, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'DOI' } } },
    { field: 'description', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-multiline', width: 'full', options: { placeholder: 'Description' } } },
    { field: 'attachment_id', type: 'uuid', schema: { is_nullable: true }, meta: { interface: 'file', width: 'half' } },
    { field: 'citations', type: 'integer', schema: { default_value: 0, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Citations' } } },
    { field: 'impact_factor', type: 'decimal', schema: { precision: 5, scale: 2, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Impact Factor' } } }
  ]);
  
  // Books and Chapters
  await createCommonAchievementFields('books_and_chapters', [
    { field: 'title', type: 'string', schema: { length: 200, is_nullable: false }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Book Title' } } },
    { field: 'chapter_title', type: 'string', schema: { length: 200, is_nullable: true }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Chapter Title' } } },
    { field: 'publisher', type: 'string', schema: { length: 100, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Publisher' } } },
    { field: 'publication_year', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Publication Year' } } },
    { field: 'isbn', type: 'string', schema: { length: 20, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'ISBN' } } },
    { field: 'description', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-multiline', width: 'full', options: { placeholder: 'Description' } } },
    { field: 'attachment_id', type: 'uuid', schema: { is_nullable: true }, meta: { interface: 'file', width: 'half' } }
  ]);
  
  // Conference Proceedings
  await createCommonAchievementFields('conference_proceedings', [
    { field: 'conference_title', type: 'string', schema: { length: 200, is_nullable: false }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Conference Title' } } },
    { field: 'location', type: 'string', schema: { length: 100, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Location' } } },
    { field: 'conference_date', type: 'date', schema: { is_nullable: true }, meta: { interface: 'datetime', width: 'half' } },
    { field: 'paper_title', type: 'string', schema: { length: 200, is_nullable: true }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Paper Title' } } },
    { field: 'role_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'description', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-multiline', width: 'full', options: { placeholder: 'Description' } } },
    { field: 'attachment_id', type: 'uuid', schema: { is_nullable: true }, meta: { interface: 'file', width: 'half' } }
  ]);
  
  // Honours and Awards
  await createCommonAchievementFields('honours_awards', [
    { field: 'award_title', type: 'string', schema: { length: 200, is_nullable: false }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Award Title' } } },
    { field: 'awarded_by', type: 'string', schema: { length: 200, is_nullable: true }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Awarded By' } } },
    { field: 'award_date', type: 'date', schema: { is_nullable: true }, meta: { interface: 'datetime', width: 'half' } },
    { field: 'category_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'description', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-multiline', width: 'full', options: { placeholder: 'Description' } } },
    { field: 'attachment_id', type: 'uuid', schema: { is_nullable: true }, meta: { interface: 'file', width: 'half' } }
  ]);
  
  // Intellectual Property
  await createCommonAchievementFields('intellectual_property', [
    { field: 'title', type: 'string', schema: { length: 200, is_nullable: false }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Title' } } },
    { field: 'type', type: 'string', schema: { length: 20, is_nullable: true }, meta: { interface: 'select-dropdown', width: 'half', options: { choices: [{ text: 'Patent', value: 'Patent' }, { text: 'Copyright', value: 'Copyright' }, { text: 'Trademark', value: 'Trademark' }, { text: 'Other', value: 'Other' }] } } },
    { field: 'patent_app_no', type: 'string', schema: { length: 50, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Patent/Application Number' } } },
    { field: 'filing_date', type: 'date', schema: { is_nullable: true }, meta: { interface: 'datetime', width: 'half' } },
    { field: 'grant_date', type: 'date', schema: { is_nullable: true }, meta: { interface: 'datetime', width: 'half' } },
    { field: 'status_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'description', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-multiline', width: 'full', options: { placeholder: 'Description' } } },
    { field: 'attachment_id', type: 'uuid', schema: { is_nullable: true }, meta: { interface: 'file', width: 'half' } }
  ]);
  
  // Research and Consultancy Projects
  await createCommonAchievementFields('research_consultancy', [
    { field: 'project_title', type: 'string', schema: { length: 200, is_nullable: false }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Project Title' } } },
    { field: 'project_type', type: 'string', schema: { length: 20, is_nullable: true }, meta: { interface: 'select-dropdown', width: 'half', options: { choices: [{ text: 'Research', value: 'Research' }, { text: 'Consultancy', value: 'Consultancy' }, { text: 'Development', value: 'Development' }, { text: 'Other', value: 'Other' }] } } },
    { field: 'agency_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'grant_amount', type: 'decimal', schema: { precision: 12, scale: 2, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Grant Amount' } } },
    { field: 'start_date', type: 'date', schema: { is_nullable: true }, meta: { interface: 'datetime', width: 'half' } },
    { field: 'end_date', type: 'date', schema: { is_nullable: true }, meta: { interface: 'datetime', width: 'half' } },
    { field: 'status', type: 'string', schema: { length: 20, is_nullable: true }, meta: { interface: 'select-dropdown', width: 'half', options: { choices: [{ text: 'Ongoing', value: 'Ongoing' }, { text: 'Completed', value: 'Completed' }, { text: 'Submitted', value: 'Submitted' }, { text: 'Approved', value: 'Approved' }, { text: 'Rejected', value: 'Rejected' }] } } },
    { field: 'description', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-multiline', width: 'full', options: { placeholder: 'Description' } } },
    { field: 'attachment_id', type: 'uuid', schema: { is_nullable: true }, meta: { interface: 'file', width: 'half' } }
  ]);
  
  // Workshops and Seminars
  await createCommonAchievementFields('workshops_seminars', [
    { field: 'title', type: 'string', schema: { length: 200, is_nullable: false }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Title' } } },
    { field: 'type_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'location', type: 'string', schema: { length: 100, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Location' } } },
    { field: 'organized_by', type: 'string', schema: { length: 200, is_nullable: true }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Organized By' } } },
    { field: 'start_date', type: 'date', schema: { is_nullable: true }, meta: { interface: 'datetime', width: 'half' } },
    { field: 'end_date', type: 'date', schema: { is_nullable: true }, meta: { interface: 'datetime', width: 'half' } },
    { field: 'description', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-multiline', width: 'full', options: { placeholder: 'Description' } } },
    { field: 'attachment_id', type: 'uuid', schema: { is_nullable: true }, meta: { interface: 'file', width: 'half' } }
  ]);
  
  // MDP/FDP
  await createCommonAchievementFields('mdp_fdp', [
    { field: 'title', type: 'string', schema: { length: 200, is_nullable: false }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Title' } } },
    { field: 'type_id', type: 'integer', schema: { is_nullable: true }, meta: { interface: 'select-dropdown-m2o', width: 'half' } },
    { field: 'location', type: 'string', schema: { length: 100, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Location' } } },
    { field: 'organized_by', type: 'string', schema: { length: 200, is_nullable: true }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Organized By' } } },
    { field: 'start_date', type: 'date', schema: { is_nullable: true }, meta: { interface: 'datetime', width: 'half' } },
    { field: 'end_date', type: 'date', schema: { is_nullable: true }, meta: { interface: 'datetime', width: 'half' } },
    { field: 'description', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-multiline', width: 'full', options: { placeholder: 'Description' } } },
    { field: 'attachment_id', type: 'uuid', schema: { is_nullable: true }, meta: { interface: 'file', width: 'half' } }
  ]);
  
  // Professional Activities
  await createCommonAchievementFields('professional_activities', [
    { field: 'activity_title', type: 'string', schema: { length: 200, is_nullable: false }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Activity Title' } } },
    { field: 'activity_type', type: 'string', schema: { length: 100, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Activity Type' } } },
    { field: 'role', type: 'string', schema: { length: 100, is_nullable: true }, meta: { interface: 'input', width: 'half', options: { placeholder: 'Role' } } },
    { field: 'organization', type: 'string', schema: { length: 200, is_nullable: true }, meta: { interface: 'input', width: 'full', options: { placeholder: 'Organization' } } },
    { field: 'activity_date', type: 'date', schema: { is_nullable: true }, meta: { interface: 'datetime', width: 'half' } },
    { field: 'description', type: 'text', schema: { is_nullable: true }, meta: { interface: 'input-multiline', width: 'full', options: { placeholder: 'Description' } } },
    { field: 'attachment_id', type: 'uuid', schema: { is_nullable: true }, meta: { interface: 'file', width: 'half' } }
  ]);
}

module.exports = {
  createFields
};