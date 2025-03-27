// modules/faculty-management/permissions.js - Creates permissions for faculty management collections
const utils = require('../../utils');

async function createPermissions(roleIds) {
  // Define all faculty management collections
  const facultyCollections = [
    'faculty',
    'faculty_additional_details',
    'work_experiences',
    'faculty_qualifications',
    'publication_type',
    'intellectual_property_status',
    'funding_agency',
    'workshop_type',
    'mdp_fdp_type',
    'award_category',
    'conference_role',
    'teaching_activities',
    'research_publications',
    'books_and_chapters',
    'conference_proceedings',
    'honours_awards',
    'intellectual_property',
    'research_consultancy',
    'workshops_seminars',
    'mdp_fdp',
    'professional_activities'
  ];
  
  // Reference tables that are generally read-only except for administrators
  const referenceCollections = [
    'publication_type',
    'intellectual_property_status',
    'funding_agency',
    'workshop_type',
    'mdp_fdp_type',
    'award_category',
    'conference_role'
  ];
  
  // Faculty profile-related collections
  const profileCollections = [
    'faculty',
    'faculty_additional_details',
    'work_experiences',
    'faculty_qualifications'
  ];
  
  // Faculty achievement collections
  const achievementCollections = [
    'teaching_activities',
    'research_publications',
    'books_and_chapters',
    'conference_proceedings',
    'honours_awards',
    'intellectual_property',
    'research_consultancy',
    'workshops_seminars',
    'mdp_fdp',
    'professional_activities'
  ];

  // Define permissions for Administrator role - full access
  if (roleIds.Administrator) {
    for (const collection of facultyCollections) {
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
    // Faculty can read all collections
    for (const collection of facultyCollections) {
      await utils.createPermission({
        role: roleIds.Faculty,
        collection: collection,
        action: 'read',
        permissions: {},
        validation: {}
      });
    }
    
    // Faculty can update their own profile
    for (const collection of profileCollections) {
      if (collection === 'faculty') {
        await utils.createPermission({
          role: roleIds.Faculty,
          collection: collection,
          action: 'update',
          permissions: { _and: [{ id: { _eq: "$CURRENT_USER.id" } }] },
          validation: {}
        });
      } else {
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
    }
    
    // Faculty can manage their own achievements
    for (const collection of achievementCollections) {
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
  }
  
  // Define permissions for Department Head role
  if (roleIds['Department Head']) {
    // Department Heads can read all collections
    for (const collection of facultyCollections) {
      await utils.createPermission({
        role: roleIds['Department Head'],
        collection: collection,
        action: 'read',
        permissions: {},
        validation: {}
      });
    }
    
    // Department Heads can manage faculty in their department
    await utils.createPermission({
      role: roleIds['Department Head'],
      collection: 'faculty',
      action: 'create',
      permissions: {},
      validation: { department_id: { _eq: "$CURRENT_USER.department_id" } }
    });
    
    await utils.createPermission({
      role: roleIds['Department Head'],
      collection: 'faculty',
      action: 'update',
      permissions: { _and: [{ department_id: { _eq: "$CURRENT_USER.department_id" } }] },
      validation: {}
    });
    
    // Department Heads can view and manage faculty achievements in their department
    for (const collection of achievementCollections) {
      await utils.createPermission({
        role: roleIds['Department Head'],
        collection: collection,
        action: 'update',
        permissions: { _and: [{ faculty: { department_id: { _eq: "$CURRENT_USER.department_id" } } }] },
        validation: {}
      });
    }
  }
  
  // Define permissions for Student role
  if (roleIds.Student) {
    // Students can only read faculty and their public achievements
    const studentReadableCollections = [
      'faculty',
      'teaching_activities',
      'research_publications',
      'books_and_chapters',
      'conference_proceedings',
      'honours_awards',
      'intellectual_property'
    ];
    
    for (const collection of studentReadableCollections) {
      let permissions = {};
      
      // For faculty achievement collections, students can only see items that have visibility set to 'show'
      if (collection !== 'faculty') {
        permissions = { _and: [{ visibility: { _eq: "show" } }] };
      }
      
      await utils.createPermission({
        role: roleIds.Student,
        collection: collection,
        action: 'read',
        permissions: permissions,
        validation: {}
      });
    }
  }
}

module.exports = {
  createPermissions
};