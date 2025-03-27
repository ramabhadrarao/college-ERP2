// modules/core-schema/permissions.js - Creates permissions for core schema collections
const utils = require('../../utils');

async function createPermissions(roleIds) {
  // Define all core schema collections
  const coreCollections = [
    'college',
    'system_settings',
    'blood_groups',
    'gender',
    'nationality',
    'religion',
    'caste',
    'sub_caste',
    'states',
    'districts',
    'academic_years',
    'rooms'
  ];
  
  // Reference collections that are generally read-only except for administrators
  const referenceCollections = [
    'blood_groups',
    'gender',
    'nationality',
    'religion',
    'caste',
    'sub_caste',
    'states',
    'districts'
  ];
  
  // System configuration collections
  const systemCollections = [
    'college',
    'system_settings',
    'academic_years'
  ];

  // Define permissions for Administrator role - full access
  if (roleIds.Administrator) {
    for (const collection of coreCollections) {
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
    // Faculty can read all reference collections
    for (const collection of coreCollections) {
      await utils.createPermission({
        role: roleIds.Faculty,
        collection: collection,
        action: 'read',
        permissions: {},
        validation: {}
      });
    }
    
    // Faculty can update room status
    await utils.createPermission({
      role: roleIds.Faculty,
      collection: 'rooms',
      action: 'update',
      permissions: {},
      validation: {
        // Can only update specific fields
        status: { _nnull: true }
      },
      fields: ['status']
    });
  }
  
  // Define permissions for Department Head role
  if (roleIds['Department Head']) {
    // Department Heads can read all reference collections
    for (const collection of coreCollections) {
      await utils.createPermission({
        role: roleIds['Department Head'],
        collection: collection,
        action: 'read',
        permissions: {},
        validation: {}
      });
    }
    
    // Department Heads can manage rooms
    await utils.createPermission({
      role: roleIds['Department Head'],
      collection: 'rooms',
      action: 'create',
      permissions: {},
      validation: {}
    });
    
    await utils.createPermission({
      role: roleIds['Department Head'],
      collection: 'rooms',
      action: 'update',
      permissions: {},
      validation: {}
    });
  }
  
  // Define permissions for Student role
  if (roleIds.Student) {
    // Students can only read some core reference collections
    const studentReadableCollections = [
      'blood_groups',
      'gender',
      'nationality', 
      'religion',
      'caste',
      'academic_years'
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
    
    // Students can view active academic years and rooms only
    await utils.createPermission({
      role: roleIds.Student,
      collection: 'academic_years',
      action: 'read',
      permissions: { _and: [{ status: { _in: ["active", "upcoming"] } }] },
      validation: {}
    });
    
    await utils.createPermission({
      role: roleIds.Student,
      collection: 'rooms',
      action: 'read',
      permissions: { _and: [{ status: { _eq: "active" } }] },
      validation: {}
    });
  }
}

module.exports = {
  createPermissions
};