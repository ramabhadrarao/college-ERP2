// modules/curriculum.js - Main entry point for curriculum module
const utils = require('../utils');
const curriculumCollections = require('./curriculum/collections');
const curriculumFields = require('./curriculum/fields');
const curriculumRelations = require('./curriculum/relations');
const curriculumPermissions = require('./curriculum/permissions');

async function import_curriculum(roleIds) {
  // Create collections for curriculum
  await curriculumCollections.createCollections();
  
  // Create fields for each collection
  await curriculumFields.createFields();
  
  // Create relationships between collections
  await curriculumRelations.createRelations();
  
  // Set up permissions for each role
  await curriculumPermissions.createPermissions(roleIds);
}

module.exports = {
  import: import_curriculum
};
