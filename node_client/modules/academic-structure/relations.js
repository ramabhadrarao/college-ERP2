// modules/academic-structure/relations.js - Creates relations between academic structure collections
const utils = require('../../utils');

async function createRelations() {
  // Define relationships between collections
  const relations = [
    // Departments to College
    {
      collection: 'departments',
      field: 'college_id',
      related_collection: 'college',
      meta: {
        junction_field: null,
        many_collection: 'departments',
        many_field: 'college_id',
        one_collection: 'college',
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
    
    // Departments to Faculty (HoD)
    {
      collection: 'departments',
      field: 'hod_id',
      related_collection: 'faculty',
      meta: {
        junction_field: null,
        many_collection: 'departments',
        many_field: 'hod_id',
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
    
    // Programs to Departments
    {
      collection: 'programs',
      field: 'department_id',
      related_collection: 'departments',
      meta: {
        junction_field: null,
        many_collection: 'programs',
        many_field: 'department_id',
        one_collection: 'departments',
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
    
    // Programs to Faculty (Coordinator)
    {
      collection: 'programs',
      field: 'coordinator_id',
      related_collection: 'faculty',
      meta: {
        junction_field: null,
        many_collection: 'programs',
        many_field: 'coordinator_id',
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
    
    // Branches to Programs
    {
      collection: 'branches',
      field: 'program_id',
      related_collection: 'programs',
      meta: {
        junction_field: null,
        many_collection: 'branches',
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
    
    // Branches to Faculty (Coordinator)
    {
      collection: 'branches',
      field: 'coordinator_id',
      related_collection: 'faculty',
      meta: {
        junction_field: null,
        many_collection: 'branches',
        many_field: 'coordinator_id',
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
    
    // Regulations to Programs
    {
      collection: 'regulations',
      field: 'program_id',
      related_collection: 'programs',
      meta: {
        junction_field: null,
        many_collection: 'regulations',
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
    
    // Regulations to Branches
    {
      collection: 'regulations',
      field: 'branch_id',
      related_collection: 'branches',
      meta: {
        junction_field: null,
        many_collection: 'regulations',
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
    
    // Semesters to Regulations
    {
      collection: 'semesters',
      field: 'regulation_id',
      related_collection: 'regulations',
      meta: {
        junction_field: null,
        many_collection: 'semesters',
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
    
    // Semesters to Academic Years
    {
      collection: 'semesters',
      field: 'academic_year_id',
      related_collection: 'academic_years',
      meta: {
        junction_field: null,
        many_collection: 'semesters',
        many_field: 'academic_year_id',
        one_collection: 'academic_years',
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
    
    // Batches to Programs
    {
      collection: 'batches',
      field: 'program_id',
      related_collection: 'programs',
      meta: {
        junction_field: null,
        many_collection: 'batches',
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
    
    // Batches to Branches
    {
      collection: 'batches',
      field: 'branch_id',
      related_collection: 'branches',
      meta: {
        junction_field: null,
        many_collection: 'batches',
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
    
    // Batches to Faculty (Mentor)
    {
      collection: 'batches',
      field: 'mentor_id',
      related_collection: 'faculty',
      meta: {
        junction_field: null,
        many_collection: 'batches',
        many_field: 'mentor_id',
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