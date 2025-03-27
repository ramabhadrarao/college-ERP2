// modules/academic-structure.js - Main entry point for academic structure module
const utils = require('../utils');
const academicCollections = require('./academic-structure/collections');
const academicFields = require('./academic-structure/fields');
const academicRelations = require('./academic-structure/relations');
const academicPermissions = require('./academic-structure/permissions');

async function import_academicStructure(roleIds) {
  // Create collections for academic structure
  await academicCollections.createCollections();
  
  // Create fields for each collection
  await academicFields.createFields();
  
  // Create relationships between collections
  await academicRelations.createRelations();
  
  // Set up permissions for each role
  await academicPermissions.createPermissions(roleIds);
}

module.exports = {
  import: import_academicStructure
};