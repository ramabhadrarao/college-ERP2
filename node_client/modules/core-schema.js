// modules/core-schema.js - Main entry point for core schema module
const utils = require('../utils');
const coreCollections = require('./core-schema/collections');
const coreFields = require('./core-schema/fields');
const coreRelations = require('./core-schema/relations');
const corePermissions = require('./core-schema/permissions');

async function import_coreSchema(roleIds) {
  // Create collections for core schema
  await coreCollections.createCollections();
  
  // Create fields for each collection
  await coreFields.createFields();
  
  // Create relationships between collections
  await coreRelations.createRelations();
  
  // Set up permissions for each role
  await corePermissions.createPermissions(roleIds);
}

module.exports = {
  import: import_coreSchema
};