// modules/faculty-management.js - Main entry point for faculty management module
const utils = require('../utils');
const facultyCollections = require('./faculty-management/collections');
const facultyFields = require('./faculty-management/fields');
const facultyRelations = require('./faculty-management/relations');
const facultyPermissions = require('./faculty-management/permissions');

async function import_facultyManagement(roleIds) {
  // Create collections for faculty management
  await facultyCollections.createCollections();
  
  // Create fields for each collection
  await facultyFields.createFields();
  
  // Create relationships between collections
  await facultyRelations.createRelations();
  
  // Set up permissions for each role
  await facultyPermissions.createPermissions(roleIds);
}

module.exports = {
  import: import_facultyManagement
};