// modules/core-schema/collections.js - Creates collections for core schema module
const utils = require('../../utils');

async function createCollections() {
  // College collection
  await utils.createCollection({
    collection: 'college',
    meta: {
      icon: 'school',
      display_template: '{{name}}',
      sort_field: 'id',
      note: 'College/Institution information'
    },
    schema: {
      name: 'college'
    }
  });

  // System Settings collection
  await utils.createCollection({
    collection: 'system_settings',
    meta: {
      icon: 'settings',
      display_template: '{{setting_key}}: {{setting_value}}',
      sort_field: 'id',
      note: 'System settings and configuration'
    },
    schema: {
      name: 'system_settings'
    }
  });

  // Blood Groups collection
  await utils.createCollection({
    collection: 'blood_groups',
    meta: {
      icon: 'favorite',
      display_template: '{{blood_group}}',
      sort_field: 'id',
      note: 'Blood group reference data'
    },
    schema: {
      name: 'blood_groups'
    }
  });

  // Gender collection
  await utils.createCollection({
    collection: 'gender',
    meta: {
      icon: 'person',
      display_template: '{{name}}',
      sort_field: 'id',
      note: 'Gender reference data'
    },
    schema: {
      name: 'gender'
    }
  });

  // Nationality collection
  await utils.createCollection({
    collection: 'nationality',
    meta: {
      icon: 'flag',
      display_template: '{{name}}',
      sort_field: 'id',
      note: 'Nationality reference data'
    },
    schema: {
      name: 'nationality'
    }
  });

  // Religion collection
  await utils.createCollection({
    collection: 'religion',
    meta: {
      icon: 'place_of_worship',
      display_template: '{{name}}',
      sort_field: 'id',
      note: 'Religion reference data'
    },
    schema: {
      name: 'religion'
    }
  });

  // Caste collection
  await utils.createCollection({
    collection: 'caste',
    meta: {
      icon: 'groups',
      display_template: '{{name}} ({{category}})',
      sort_field: 'id',
      note: 'Caste reference data'
    },
    schema: {
      name: 'caste'
    }
  });

  // Sub Caste collection
  await utils.createCollection({
    collection: 'sub_caste',
    meta: {
      icon: 'subtitles',
      display_template: '{{name}}',
      sort_field: 'id',
      note: 'Sub caste reference data'
    },
    schema: {
      name: 'sub_caste'
    }
  });

  // States collection
  await utils.createCollection({
    collection: 'states',
    meta: {
      icon: 'map',
      display_template: '{{name}} ({{country}})',
      sort_field: 'id',
      note: 'States reference data'
    },
    schema: {
      name: 'states'
    }
  });

  // Districts collection
  await utils.createCollection({
    collection: 'districts',
    meta: {
      icon: 'location_city',
      display_template: '{{name}}',
      sort_field: 'id',
      note: 'Districts reference data'
    },
    schema: {
      name: 'districts'
    }
  });

  // Academic Years collection
  await utils.createCollection({
    collection: 'academic_years',
    meta: {
      icon: 'date_range',
      display_template: '{{year_name}}',
      sort_field: 'id',
      note: 'Academic years'
    },
    schema: {
      name: 'academic_years'
    }
  });

  // Rooms collection
  await utils.createCollection({
    collection: 'rooms',
    meta: {
      icon: 'meeting_room',
      display_template: '{{room_number}} ({{building}})',
      sort_field: 'id',
      note: 'Classrooms and other rooms'
    },
    schema: {
      name: 'rooms'
    }
  });
}

module.exports = {
  createCollections
};