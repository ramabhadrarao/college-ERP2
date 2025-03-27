// modules/curriculum/relations.js - Creates relations between curriculum collections
const utils = require('../../utils');

async function createRelations() {
  // Define relationships between collections
  const relations = [
    // Courses to Semester
    {
      collection: 'courses',
      field: 'semester_id',
      related_collection: 'semesters',
      meta: {
        junction_field: null,
        many_collection: 'courses',
        many_field: 'semester_id',
        one_collection: 'semesters',
        one_field: 'id',
        one_collection_field: null,
        one_allowed_collections: null,
        junction_field_data: null,
        sort_field: null,
      },
      schema: {
        on_delete: 'SET NULL',
      }
    },
    
    // Courses to Branch
    {
      collection: 'courses',
      field: 'branch_id',
      related_collection: 'branches',
      meta: {
        junction_field: null,
        many_collection: 'courses',
        many_field: 'branch_id',
        one_collection: 'branches',
        one_field: 'id',
        one_collection_field: null,
        one_allowed_collections: null,
        junction_field_data: null,
        sort_field: null,
      },
      schema: {
        on_delete: 'CASCADE',
      }
    },
    
    // Courses to Regulation
    {
      collection: 'courses',
      field: 'regulation_id',
      related_collection: 'regulations',
      meta: {
        junction_field: null,
        many_collection: 'courses',
        many_field: 'regulation_id',
        one_collection: 'regulations',
        one_field: 'id',
        one_collection_field: null,
        one_allowed_collections: null,
        junction_field_data: null,
        sort_field: null,
      },
      schema: {
        on_delete: 'CASCADE',
      }
    },
    
    // Courses to Course Type
    {
      collection: 'courses',
      field: 'course_type_id',
      related_collection: 'course_types',
      meta: {
        junction_field: null,
        many_collection: 'courses',
        many_field: 'course_type_id',
        one_collection: 'course_types',
        one_field: 'id',
        one_collection_field: null,
        one_allowed_collections: null,
        junction_field_data: null,
        sort_field: null,
      },
      schema: {
        on_delete: 'CASCADE',
      }
    },
    
    // Course Coordinators to Course
    {
      collection: 'course_coordinators',
      field: 'course_id',
      related_collection: 'courses',
      meta: {
        junction_field: null,
        many_collection: 'course_coordinators',
        many_field: 'course_id',
        one_collection: 'courses',
        one_field: 'id',
        one_collection_field: null,
        one_allowed_collections: null,
        junction_field_data: null,
        sort_field: null,
      },
      schema: {
        on_delete: 'CASCADE',
      }
    },
    
    // Course Coordinators to Semester
    {
      collection: 'course_coordinators',
      field: 'semester_id',
      related_collection: 'semesters',
      meta: {
        junction_field: null,
        many_collection: 'course_coordinators',
        many_field: 'semester_id',
        one_collection: 'semesters',
        one_field: 'id',
        one_collection_field: null,
        one_allowed_collections: null,
        junction_field_data: null,
        sort_field: null,
      },
      schema: {
        on_delete: 'CASCADE',
      }
    },
    
    // Marks Distribution to Course
    {
      collection: 'marks_distribution',
      field: 'course_id',
      related_collection: 'courses',
      meta: {
        junction_field: null,
        many_collection: 'marks_distribution',
        many_field: 'course_id',
        one_collection: 'courses',
        one_field: 'id',
        one_collection_field: null,
        one_allowed_collections: null,
        junction_field_data: null,
        sort_field: null,
      },
      schema: {
        on_delete: 'CASCADE',
      }
    },
    
    // Marks Distribution to Mark Type
    {
      collection: 'marks_distribution',
      field: 'mark_type_id',
      related_collection: 'mark_types',
      meta: {
        junction_field: null,
        many_collection: 'marks_distribution',
        many_field: 'mark_type_id',
        one_collection: 'mark_types',
        one_field: 'id',
        one_collection_field: null,
        one_allowed_collections: null,
        junction_field_data: null,
        sort_field: null,
      },
      schema: {
        on_delete: 'CASCADE',
      }
    },
    
    // Elective Groups to Semester
    {
      collection: 'elective_groups',
      field: 'semester_id',
      related_collection: 'semesters',
      meta: {
        junction_field: null,
        many_collection: 'elective_groups',
        many_field: 'semester_id',
        one_collection: 'semesters',
        one_field: 'id',
        one_collection_field: null,
        one_allowed_collections: null,
        junction_field_data: null,
        sort_field: null,
      },
      schema: {
        on_delete: 'CASCADE',
      }
    },
    
    // Elective Group Courses to Elective Group
    {
      collection: 'elective_group_courses',
      field: 'elective_group_id',
      related_collection: 'elective_groups',
      meta: {
        junction_field: null,
        many_collection: 'elective_group_courses',
        many_field: 'elective_group_id',
        one_collection: 'elective_groups',
        one_field: 'id',
        one_collection_field: null,
        one_allowed_collections: null,
        junction_field_data: null,
        sort_field: null,
      },
      schema: {
        on_delete: 'CASCADE',
      }
    },
    
    // Elective Group Courses to Course
    {
      collection: 'elective_group_courses',
      field: 'course_id',
      related_collection: 'courses',
      meta: {
        junction_field: null,
        many_collection: 'elective_group_courses',
        many_field: 'course_id',
        one_collection: 'courses',
        one_field: 'id',
        one_collection_field: null,
        one_allowed_collections: null,
        junction_field_data: null,
        sort_field: null,
      },
      schema: {
        on_delete: 'CASCADE',
      }
    },
    
    // Elective Eligibility to Elective Group
    {
      collection: 'elective_eligibility',
      field: 'elective_group_id',
      related_collection: 'elective_groups',
      meta: {
        junction_field: null,
        many_collection: 'elective_eligibility',
        many_field: 'elective_group_id',
        one_collection: 'elective_groups',
        one_field: 'id',
        one_collection_field: null,
        one_allowed_collections: null,
        junction_field_data: null,
        sort_field: null,
      },
      schema: {
        on_delete: 'CASCADE',
      }
    },
    
    // Elective Eligibility to Program
    {
      collection: 'elective_eligibility',
      field: 'program_id',
      related_collection: 'programs',
      meta: {
        junction_field: null,
        many_collection: 'elective_eligibility',
        many_field: 'program_id',
        one_collection: 'programs',
        one_field: 'id',
        one_collection_field: null,
        one_allowed_collections: null,
        junction_field_data: null,
        sort_field: null,
      },
      schema: {
        on_delete: 'CASCADE',
      }
    },
    
    // Course Modules to Course
    {
      collection: 'course_modules',
      field: 'course_id',
      related_collection: 'courses',
      meta: {
        junction_field: null,
        many_collection: 'course_modules',
        many_field: 'course_id',
        one_collection: 'courses',
        one_field: 'id',
        one_collection_field: null,
        one_allowed_collections: null,
        junction_field_data: null,
        sort_field: null,
      },
      schema: {
        on_delete: 'CASCADE',
      }
    },
    
    // Course Modules to Module Type
    {
      collection: 'course_modules',
      field: 'module_type_id',
      related_collection: 'module_types',
      meta: {
        junction_field: null,
        many_collection: 'course_modules',
        many_field: 'module_type_id',
        one_collection: 'module_types',
        one_field: 'id',
        one_collection_field: null,
        one_allowed_collections: null,
        junction_field_data: null,
        sort_field: null,
      },
      schema: {
        on_delete: 'CASCADE',
      }
    },
    
    // Course Materials to Module
    {
      collection: 'course_materials',
      field: 'module_id',
      related_collection: 'course_modules',
      meta: {
        junction_field: null,
        many_collection: 'course_materials',
        many_field: 'module_id',
        one_collection: 'course_modules',
        one_field: 'id',
        one_collection_field: null,
        one_allowed_collections: null,
        junction_field_data: null,
        sort_field: null,
      },
      schema: {
        on_delete: 'CASCADE',
      }
    },
    
    // Course Materials to Material Type
    {
      collection: 'course_materials',
      field: 'material_type_id',
      related_collection: 'material_types',
      meta: {
        junction_field: null,
        many_collection: 'course_materials',
        many_field: 'material_type_id',
        one_collection: 'material_types',
        one_field: 'id',
        one_collection_field: null,
        one_allowed_collections: null,
        junction_field_data: null,
        sort_field: null,
      },
      schema: {
        on_delete: 'CASCADE',
      }
    },
    
    // Assignments to Module
    {
      collection: 'assignments',
      field: 'module_id',
      related_collection: 'course_modules',
      meta: {
        junction_field: null,
        many_collection: 'assignments',
        many_field: 'module_id',
        one_collection: 'course_modules',
        one_field: 'id',
        one_collection_field: null,
        one_allowed_collections: null,
        junction_field_data: null,
        sort_field: null,
      },
      schema: {
        on_delete: 'CASCADE',
      }
    },
    
    // Assignment Submissions to Assignment
    {
      collection: 'assignment_submissions',
      field: 'assignment_id',
      related_collection: 'assignments',
      meta: {
        junction_field: null,
        many_collection: 'assignment_submissions',
        many_field: 'assignment_id',
        one_collection: 'assignments',
        one_field: 'id',
        one_collection_field: null,
        one_allowed_collections: null,
        junction_field_data: null,
        sort_field: null,
      },
      schema: {
        on_delete: 'CASCADE',
      }
    },
    
    // Course Coordinators to Faculty
    {
      collection: 'course_coordinators',
      field: 'faculty_id',
      related_collection: 'faculty',
      meta: {
        junction_field: null,
        many_collection: 'course_coordinators',
        many_field: 'faculty_id',
        one_collection: 'faculty',
        one_field: 'id',
        one_collection_field: null,
        one_allowed_collections: null,
        junction_field_data: null,
        sort_field: null,
      },
      schema: {
        on_delete: 'CASCADE',
      }
    },
    
    // Course Modules to Faculty
    {
      collection: 'course_modules',
      field: 'faculty_id',
      related_collection: 'faculty',
      meta: {
        junction_field: null,
        many_collection: 'course_modules',
        many_field: 'faculty_id',
        one_collection: 'faculty',
        one_field: 'id',
        one_collection_field: null,
        one_allowed_collections: null,
        junction_field_data: null,
        sort_field: null,
      },
      schema: {
        on_delete: 'SET NULL',
      }
    },
    
    // Assignment Submissions to Student
    {
      collection: 'assignment_submissions',
      field: 'student_id',
      related_collection: 'students',
      meta: {
        junction_field: null,
        many_collection: 'assignment_submissions',
        many_field: 'student_id',
        one_collection: 'students',
        one_field: 'id',
        one_collection_field: null,
        one_allowed_collections: null,
        junction_field_data: null,
        sort_field: null,
      },
      schema: {
        on_delete: 'CASCADE',
      }
    },
    
    // Assignment Submissions graded by Faculty
    {
      collection: 'assignment_submissions',
      field: 'graded_by',
      related_collection: 'faculty',
      meta: {
        junction_field: null,
        many_collection: 'assignment_submissions',
        many_field: 'graded_by',
        one_collection: 'faculty',
        one_field: 'id',
        one_collection_field: null,
        one_allowed_collections: null,
        junction_field_data: null,
        sort_field: null,
      },
      schema: {
        on_delete: 'SET NULL',
      }
    }
  ];

  for (const relation of relations) {
    await utils.createRelation(relation);
  }
}

module.exports = {
  createRelations
};