// modules/academic-structure/permissions.js - Creates permissions for academic structure collections
const utils = require('../../utils');

async function createPermissions(roleIds) {
  // Define all academic structure collections
  const academicCollections = [
    'departments',
    'programs',
    'branches',
    'regulations',
    'semesters',
    'batches',
    'student_types'
  ];

  // Define permissions for Administrator role - full access
  if (roleIds.Administrator) {
    for (const collection of academicCollections) {
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
  
  // Define permissions for Department Head role
  if (roleIds['Department Head']) {
    // Department Heads can read all academic structure collections
    for (const collection of academicCollections) {
      await utils.createPermission({
        role: roleIds['Department Head'],
        collection: collection,
        action: 'read',
        permissions: {},
        validation: {}
      });
    }
    
    // Department Heads can manage their own department
    await utils.createPermission({
      role: roleIds['Department Head'],
      collection: 'departments',
      action: 'update',
      permissions: { _and: [{ id: { _eq: "$CURRENT_USER.department_id" } }] },
      validation: {}
    });
    
    // Department Heads can manage programs in their department
    await utils.createPermission({
      role: roleIds['Department Head'],
      collection: 'programs',
      action: 'create',
      permissions: {},
      validation: { department_id: { _eq: "$CURRENT_USER.department_id" } }
    });
    
    await utils.createPermission({
      role: roleIds['Department Head'],
      collection: 'programs',
      action: 'update',
      permissions: { _and: [{ department_id: { _eq: "$CURRENT_USER.department_id" } }] },
      validation: {}
    });
    
    await utils.createPermission({
      role: roleIds['Department Head'],
      collection: 'programs',
      action: 'delete',
      permissions: { _and: [{ department_id: { _eq: "$CURRENT_USER.department_id" } }] },
      validation: {}
    });
    
    // Department Heads can manage branches under programs in their department
    await utils.createPermission({
      role: roleIds['Department Head'],
      collection: 'branches',
      action: 'create',
      permissions: {},
      validation: { program: { department_id: { _eq: "$CURRENT_USER.department_id" } } }
    });
    
    await utils.createPermission({
      role: roleIds['Department Head'],
      collection: 'branches',
      action: 'update',
      permissions: { _and: [{ program: { department_id: { _eq: "$CURRENT_USER.department_id" } } }] },
      validation: {}
    });
    
    await utils.createPermission({
      role: roleIds['Department Head'],
      collection: 'branches',
      action: 'delete',
      permissions: { _and: [{ program: { department_id: { _eq: "$CURRENT_USER.department_id" } } }] },
      validation: {}
    });
    
    // Department Heads can manage regulations for their department's programs/branches
    await utils.createPermission({
      role: roleIds['Department Head'],
      collection: 'regulations',
      action: 'create',
      permissions: {},
      validation: { program: { department_id: { _eq: "$CURRENT_USER.department_id" } } }
    });
    
    await utils.createPermission({
      role: roleIds['Department Head'],
      collection: 'regulations',
      action: 'update',
      permissions: { _and: [{ program: { department_id: { _eq: "$CURRENT_USER.department_id" } } }] },
      validation: {}
    });
    
    await utils.createPermission({
      role: roleIds['Department Head'],
      collection: 'regulations',
      action: 'delete',
      permissions: { _and: [{ program: { department_id: { _eq: "$CURRENT_USER.department_id" } } }] },
      validation: {}
    });
    
    // Department Heads can manage batches for their department's programs/branches
    await utils.createPermission({
      role: roleIds['Department Head'],
      collection: 'batches',
      action: 'create',
      permissions: {},
      validation: { program: { department_id: { _eq: "$CURRENT_USER.department_id" } } }
    });
    
    await utils.createPermission({
      role: roleIds['Department Head'],
      collection: 'batches',
      action: 'update',
      permissions: { _and: [{ program: { department_id: { _eq: "$CURRENT_USER.department_id" } } }] },
      validation: {}
    });
    
    await utils.createPermission({
      role: roleIds['Department Head'],
      collection: 'batches',
      action: 'delete',
      permissions: { _and: [{ program: { department_id: { _eq: "$CURRENT_USER.department_id" } } }] },
      validation: {}
    });
  }
  
  // Define permissions for Faculty role
  if (roleIds.Faculty) {
    // Faculty can read all academic structure collections
    for (const collection of academicCollections) {
      await utils.createPermission({
        role: roleIds.Faculty,
        collection: collection,
        action: 'read',
        permissions: {},
        validation: {}
      });
    }
    
    // Faculty coordinators can update their program
    await utils.createPermission({
      role: roleIds.Faculty,
      collection: 'programs',
      action: 'update',
      permissions: { _and: [{ coordinator_id: { _eq: "$CURRENT_USER.id" } }] },
      validation: {}
    });
    
    // Faculty coordinators can update their branch
    await utils.createPermission({
      role: roleIds.Faculty,
      collection: 'branches',
      action: 'update',
      permissions: { _and: [{ coordinator_id: { _eq: "$CURRENT_USER.id" } }] },
      validation: {}
    });
    
    // Faculty mentors can update their batches
    await utils.createPermission({
      role: roleIds.Faculty,
      collection: 'batches',
      action: 'update',
      permissions: { _and: [{ mentor_id: { _eq: "$CURRENT_USER.id" } }] },
      validation: {}
    });
  }
  
  // Define permissions for Student role
  if (roleIds.Student) {
    // Students can only read academic structure collections
    for (const collection of academicCollections) {
      await utils.createPermission({
        role: roleIds.Student,
        collection: collection,
        action: 'read',
        permissions: {},
        validation: {}
      });
    }
  }
}

module.exports = {
  createPermissions
};