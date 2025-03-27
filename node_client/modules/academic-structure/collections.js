// modules/academic-structure/collections.js - Creates collections for academic structure module
const utils = require('../../utils');

async function createCollections() {
  // Departments Collection
  await utils.createCollection({
    collection: 'departments',
    meta: {
      icon: 'account_balance',
      display_template: '{{name}} ({{code}})',
      sort_field: 'id',
      note: 'Academic departments'
    },
    schema: {
      name: 'departments'
    }
  });

  // Programs Collection
  await utils.createCollection({
    collection: 'programs',
    meta: {
      icon: 'school',
      display_template: '{{name}} ({{code}})',
      sort_field: 'id',
      note: 'Academic programs (degree courses)'
    },
    schema: {
      name: 'programs'
    }
  });

  // Branches Collection
  await utils.createCollection({
    collection: 'branches',
    meta: {
      icon: 'device_hub',
      display_template: '{{name}} ({{code}})',
      sort_field: 'id',
      note: 'Academic branches under programs'
    },
    schema: {
      name: 'branches'
    }
  });

  // Regulations Collection
  await utils.createCollection({
    collection: 'regulations',
    meta: {
      icon: 'gavel',
      display_template: '{{name}} ({{code}})',
      sort_field: 'id',
      note: 'Academic regulations and rules'
    },
    schema: {
      name: 'regulations'
    }
  });

  // Semesters Collection
  await utils.createCollection({
    collection: 'semesters',
    meta: {
      icon: 'date_range',
      display_template: '{{name}}',
      sort_field: 'id',
      note: 'Academic semesters'
    },
    schema: {
      name: 'semesters'
    }
  });

  // Batches Collection
  await utils.createCollection({
    collection: 'batches',
    meta: {
      icon: 'group',
      display_template: '{{name}} ({{start_year}}-{{end_year}})',
      sort_field: 'id',
      note: 'Student batches'
    },
    schema: {
      name: 'batches'
    }
  });

  // Student Types Collection
  await utils.createCollection({
    collection: 'student_types',
    meta: {
      icon: 'person',
      display_template: '{{name}}',
      sort_field: 'id',
      note: 'Student classification types'
    },
    schema: {
      name: 'student_types'
    }
  });
}

module.exports = {
  createCollections
};