// modules/curriculum/collections.js - Creates collections for curriculum module
const utils = require('../../utils');

async function createCollections() {
  // Course Types Collection
  await utils.createCollection({
    collection: 'course_types',
    meta: {
      icon: 'category',
      display_template: '{{name}}',
      sort_field: 'id',
      note: 'Types of courses'
    },
    schema: {
      name: 'course_types'
    }
  });

  // Courses Collection
  await utils.createCollection({
    collection: 'courses',
    meta: {
      icon: 'book',
      display_template: '{{course_code}}: {{name}}',
      sort_field: 'id',
      note: 'Course information'
    },
    schema: {
      name: 'courses'
    }
  });

  // Course Coordinators Collection
  await utils.createCollection({
    collection: 'course_coordinators',
    meta: {
      icon: 'supervisor_account',
      display_template: 'Course: {{course_id}} - Faculty: {{faculty_id}}',
      sort_field: 'id',
      note: 'Faculty assigned to courses'
    },
    schema: {
      name: 'course_coordinators'
    }
  });

  // Mark Types Collection
  await utils.createCollection({
    collection: 'mark_types',
    meta: {
      icon: 'assessment',
      display_template: '{{name}}',
      sort_field: 'id',
      note: 'Types of marks'
    },
    schema: {
      name: 'mark_types'
    }
  });

  // Marks Distribution Collection
  await utils.createCollection({
    collection: 'marks_distribution',
    meta: {
      icon: 'pie_chart',
      display_template: 'Course: {{course_id}} - Mark Type: {{mark_type_id}}',
      sort_field: 'id',
      note: 'Distribution of marks for courses'
    },
    schema: {
      name: 'marks_distribution'
    }
  });

  // Elective Groups Collection
  await utils.createCollection({
    collection: 'elective_groups',
    meta: {
      icon: 'group_work',
      display_template: '{{name}}',
      sort_field: 'id',
      note: 'Groups of elective courses'
    },
    schema: {
      name: 'elective_groups'
    }
  });

  // Elective Group Courses Collection
  await utils.createCollection({
    collection: 'elective_group_courses',
    meta: {
      icon: 'list',
      display_template: 'Group: {{elective_group_id}} - Course: {{course_id}}',
      sort_field: 'id',
      note: 'Courses in elective groups'
    },
    schema: {
      name: 'elective_group_courses'
    }
  });

  // Elective Eligibility Collection
  await utils.createCollection({
    collection: 'elective_eligibility',
    meta: {
      icon: 'check_circle',
      display_template: 'Group: {{elective_group_id}} - Program: {{program_id}}',
      sort_field: 'id',
      note: 'Program eligibility for electives'
    },
    schema: {
      name: 'elective_eligibility'
    }
  });

  // Module Types Collection
  await utils.createCollection({
    collection: 'module_types',
    meta: {
      icon: 'view_module',
      display_template: '{{name}}',
      sort_field: 'id',
      note: 'Types of course modules'
    },
    schema: {
      name: 'module_types'
    }
  });

  // Course Modules Collection
  await utils.createCollection({
    collection: 'course_modules',
    meta: {
      icon: 'folder',
      display_template: '{{title}}',
      sort_field: 'order_index',
      note: 'Modules within courses'
    },
    schema: {
      name: 'course_modules'
    }
  });

  // Material Types Collection
  await utils.createCollection({
    collection: 'material_types',
    meta: {
      icon: 'attachment',
      display_template: '{{name}}',
      sort_field: 'id',
      note: 'Types of course materials'
    },
    schema: {
      name: 'material_types'
    }
  });

  // Course Materials Collection
  await utils.createCollection({
    collection: 'course_materials',
    meta: {
      icon: 'insert_drive_file',
      display_template: '{{title}}',
      sort_field: 'order_index',
      note: 'Materials within course modules'
    },
    schema: {
      name: 'course_materials'
    }
  });

  // Assignments Collection
  await utils.createCollection({
    collection: 'assignments',
    meta: {
      icon: 'assignment',
      display_template: '{{title}}',
      sort_field: 'id',
      note: 'Course assignments'
    },
    schema: {
      name: 'assignments'
    }
  });

  // Assignment Submissions Collection
  await utils.createCollection({
    collection: 'assignment_submissions',
    meta: {
      icon: 'upload_file',
      display_template: 'Assignment: {{assignment_id}} - Student: {{student_id}}',
      sort_field: 'id',
      note: 'Student submissions for assignments'
    },
    schema: {
      name: 'assignment_submissions'
    }
  });
}

module.exports = {
  createCollections
};