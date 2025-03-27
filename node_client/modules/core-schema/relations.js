// modules/core-schema/relations.js - Creates relations between core schema collections
const utils = require('../../utils');

async function createRelations() {
  // Define relationships between collections
  const relations = [
    // Sub Caste to Caste
    {
      collection: 'sub_caste',
      field: 'caste_id',
      related_collection: 'caste',
      meta: {
        junction_field: null,
        many_collection: 'sub_caste',
        many_field: 'caste_id',
        one_collection: 'caste',
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
    
    // Districts to States
    {
      collection: 'districts',
      field: 'state_id',
      related_collection: 'states',
      meta: {
        junction_field: null,
        many_collection: 'districts',
        many_field: 'state_id',
        one_collection: 'states',
        one_field: 'id',
        one_collection_field: null,
        one_allowed_collections: null,
        junction_field_data: null,
        sort_field: null,
      },
      schema: {
        on_delete: 'CASCADE',
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