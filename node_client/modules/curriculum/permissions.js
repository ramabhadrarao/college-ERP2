// modules/curriculum/permissions.js - Creates permissions for curriculum collections
const utils = require('../../utils');

async function createPermissions(roleIds) {
  // Define all curriculum collections
  const collections = [
    'course_types', 'courses', 'course_coordinators', 'mark_types', 
    'marks_distribution', 'elective_groups', 'elective_group_courses', 
    'elective_eligibility', 'module_types', 'course_modules', 
    'material_types', 'course_materials', 'assignments', 'assignment_submissions'
  ];
  
  // Define permissions for Administrator role - full access
  if (roleIds.Administrator) {
    for (const collection of collections) {
      await utils.createPermission({
        role: roleIds.Administrator,
        collection: collection,
        action: 'create',
        permissions: {},
        validation: {}
      });
      
      await utils.createPermission({
        role: roleIds.Administrator,
        collection: collection,
        action: 'read',
        permissions: {},
        validation: {}
      });
      
      await utils.createPermission({
        role: roleIds.Administrator,
        collection: collection,
        action: 'update',
        permissions: {},
        validation: {}
      });
      
      await utils.createPermission({
        role: roleIds.Administrator,
        collection: collection,
        action: 'delete',
        permissions: {},
        validation: {}
      });
    }
  }
  
  // Define permissions for Faculty role
  if (roleIds.Faculty) {
    // Faculty can read all curriculum collections
    for (const collection of collections) {
      await utils.createPermission({
        role: roleIds.Faculty,
        collection: collection,
        action: 'read',
        permissions: {},
        validation: {}
      });
    }
    
    // Faculty can manage course modules, materials, and assignments they are responsible for
    const facultyManageableCollections = [
      'course_modules', 'course_materials', 'assignments'
    ];
    
    for (const collection of facultyManageableCollections) {
      await utils.createPermission({
        role: roleIds.Faculty,
        collection: collection,
        action: 'create',
        permissions: {},
        validation: { faculty_id: { _eq: "$CURRENT_USER.id" } }
      });
      
      await utils.createPermission({
        role: roleIds.Faculty,
        collection: collection,
        action: 'update',
        permissions: { _and: [{ faculty_id: { _eq: "$CURRENT_USER.id" } }] },
        validation: {}
      });
      
      await utils.createPermission({
        role: roleIds.Faculty,
        collection: collection,
        action: 'delete',
        permissions: { _and: [{ faculty_id: { _eq: "$CURRENT_USER.id" } }] },
        validation: {}
      });
    }
    
    // Faculty can grade assignment submissions
    await utils.createPermission({
      role: roleIds.Faculty,
      collection: 'assignment_submissions',
      action: 'update',
      permissions: {},
      validation: {
        // Limit to assignments where faculty is the course coordinator or module creator
        _or: [
          { assignment: { module: { faculty_id: { _eq: "$CURRENT_USER.id" } } } },
          { assignment: { module: { course: { course_coordinators: { faculty_id: { _eq: "$CURRENT_USER.id" } } } } } }
        ]
      }
    });
  }
  
  // Define permissions for Department Head role
  if (roleIds['Department Head']) {
    // Department Heads can read all curriculum collections
    for (const collection of collections) {
      await utils.createPermission({
        role: roleIds['Department Head'],
        collection: collection,
        action: 'read',
        permissions: {},
        validation: {}
      });
    }
    
    // Department Heads can manage courses for their department
    await utils.createPermission({
      role: roleIds['Department Head'],
      collection: 'courses',
      action: 'create',
      permissions: {},
      validation: { branch: { program: { department_id: { _eq: "$CURRENT_USER.department_id" } } } }
    });
    
    await utils.createPermission({
      role: roleIds['Department Head'],
      collection: 'courses',
      action: 'update',
      permissions: { _and: [{ branch: { program: { department_id: { _eq: "$CURRENT_USER.department_id" } } } }] },
      validation: {}
    });
    
    await utils.createPermission({
      role: roleIds['Department Head'],
      collection: 'courses',
      action: 'delete',
      permissions: { _and: [{ branch: { program: { department_id: { _eq: "$CURRENT_USER.department_id" } } } }] },
      validation: {}
    });
    
    // Department Heads can manage course coordinators
    await utils.createPermission({
      role: roleIds['Department Head'],
      collection: 'course_coordinators',
      action: 'create',
      permissions: {},
      validation: { course: { branch: { program: { department_id: { _eq: "$CURRENT_USER.department_id" } } } } }
    });
    
    await utils.createPermission({
      role: roleIds['Department Head'],
      collection: 'course_coordinators',
      action: 'update',
      permissions: { _and: [{ course: { branch: { program: { department_id: { _eq: "$CURRENT_USER.department_id" } } } } }] },
      validation: {}
    });
    
    await utils.createPermission({
      role: roleIds['Department Head'],
      collection: 'course_coordinators',
      action: 'delete',
      permissions: { _and: [{ course: { branch: { program: { department_id: { _eq: "$CURRENT_USER.department_id" } } } } }] },
      validation: {}
    });
  }
  
  // Define permissions for Student role
  if (roleIds.Student) {
    // Students can only read most curriculum collections
    const studentReadableCollections = [
      'course_types', 'courses', 'mark_types', 'elective_groups', 
      'elective_group_courses', 'module_types', 'course_modules', 
      'material_types', 'course_materials', 'assignments'
    ];
    
    for (const collection of studentReadableCollections) {
      await utils.createPermission({
        role: roleIds.Student,
        collection: collection,
        action: 'read',
        permissions: {},
        validation: {}
      });
    }
    
    // Students can manage their own assignment submissions
    await utils.createPermission({
      role: roleIds.Student,
      collection: 'assignment_submissions',
      action: 'create',
      permissions: {},
      validation: { student_id: { _eq: "$CURRENT_USER.id" } }
    });
    
    await utils.createPermission({
      role: roleIds.Student,
      collection: 'assignment_submissions',
      action: 'read',
      permissions: { _and: [{ student_id: { _eq: "$CURRENT_USER.id" } }] },
      validation: {}
    });
    
    await utils.createPermission({
      role: roleIds.Student,
      collection: 'assignment_submissions',
      action: 'update',
      permissions: { 
        _and: [
          { student_id: { _eq: "$CURRENT_USER.id" } },
          { status: { _eq: "submitted" } } // Can only update if not yet graded
        ]
      },
      validation: {
        // Cannot modify grade-related fields
        marks_awarded: { _disabled: true },
        feedback: { _disabled: true },
        graded_by: { _disabled: true },
        graded_at: { _disabled: true },
        status: { _in: ["draft", "submitted"] }
      }
    });
  }
}

module.exports = {
  createPermissions
};