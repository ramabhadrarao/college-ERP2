// modules/faculty-management/collections.js - Creates collections for faculty management module
const utils = require('../../utils');

async function createCollections() {
  // Faculty Collection
  await utils.createCollection({
    collection: 'faculty',
    meta: {
      icon: 'person',
      display_template: '{{first_name}} {{last_name}} ({{regdno}})',
      sort_field: 'id',
      note: 'Faculty information'
    },
    schema: {
      name: 'faculty'
    }
  });

  // Faculty Additional Details Collection
  await utils.createCollection({
    collection: 'faculty_additional_details',
    meta: {
      icon: 'contact_page',
      display_template: 'Faculty: {{faculty_id}}',
      sort_field: 'id',
      note: 'Additional faculty details'
    },
    schema: {
      name: 'faculty_additional_details'
    }
  });

  // Work Experiences Collection
  await utils.createCollection({
    collection: 'work_experiences',
    meta: {
      icon: 'work',
      display_template: '{{institution_name}} ({{from_date}} to {{to_date}})',
      sort_field: 'id',
      note: 'Faculty work experiences'
    },
    schema: {
      name: 'work_experiences'
    }
  });

  // Faculty Qualifications Collection
  await utils.createCollection({
    collection: 'faculty_qualifications',
    meta: {
      icon: 'school',
      display_template: '{{degree}} in {{specialization}}',
      sort_field: 'id',
      note: 'Faculty educational qualifications'
    },
    schema: {
      name: 'faculty_qualifications'
    }
  });

  // Publication Type Table
  await utils.createCollection({
    collection: 'publication_type',
    meta: {
      icon: 'article',
      display_template: '{{name}}',
      sort_field: 'id',
      note: 'Types of publications'
    },
    schema: {
      name: 'publication_type'
    }
  });

  // Intellectual Property Status Table
  await utils.createCollection({
    collection: 'intellectual_property_status',
    meta: {
      icon: 'verified',
      display_template: '{{name}}',
      sort_field: 'id',
      note: 'Statuses of intellectual property'
    },
    schema: {
      name: 'intellectual_property_status'
    }
  });

  // Funding Agency Table
  await utils.createCollection({
    collection: 'funding_agency',
    meta: {
      icon: 'attach_money',
      display_template: '{{name}}',
      sort_field: 'id',
      note: 'Research funding agencies'
    },
    schema: {
      name: 'funding_agency'
    }
  });

  // Workshop Type Table
  await utils.createCollection({
    collection: 'workshop_type',
    meta: {
      icon: 'event',
      display_template: '{{name}}',
      sort_field: 'id',
      note: 'Types of workshops'
    },
    schema: {
      name: 'workshop_type'
    }
  });

  // MDP/FDP Type Table
  await utils.createCollection({
    collection: 'mdp_fdp_type',
    meta: {
      icon: 'school',
      display_template: '{{name}}',
      sort_field: 'id',
      note: 'Types of MDP/FDP'
    },
    schema: {
      name: 'mdp_fdp_type'
    }
  });

  // Award Category Table
  await utils.createCollection({
    collection: 'award_category',
    meta: {
      icon: 'emoji_events',
      display_template: '{{name}}',
      sort_field: 'id',
      note: 'Categories of awards'
    },
    schema: {
      name: 'award_category'
    }
  });

  // Conference Role Table
  await utils.createCollection({
    collection: 'conference_role',
    meta: {
      icon: 'record_voice_over',
      display_template: '{{name}}',
      sort_field: 'id',
      note: 'Roles in conferences'
    },
    schema: {
      name: 'conference_role'
    }
  });

  // Faculty Teaching Activities Table
  await utils.createCollection({
    collection: 'teaching_activities',
    meta: {
      icon: 'menu_book',
      display_template: '{{course_name}}',
      sort_field: 'id',
      note: 'Faculty teaching activities'
    },
    schema: {
      name: 'teaching_activities'
    }
  });

  // Research Publications Table
  await utils.createCollection({
    collection: 'research_publications',
    meta: {
      icon: 'description',
      display_template: '{{title}}',
      sort_field: 'id',
      note: 'Faculty research publications'
    },
    schema: {
      name: 'research_publications'
    }
  });

  // Books and Chapters Table
  await utils.createCollection({
    collection: 'books_and_chapters',
    meta: {
      icon: 'auto_stories',
      display_template: '{{title}}',
      sort_field: 'id',
      note: 'Books and chapters authored by faculty'
    },
    schema: {
      name: 'books_and_chapters'
    }
  });

  // Conference Proceedings Table
  await utils.createCollection({
    collection: 'conference_proceedings',
    meta: {
      icon: 'groups',
      display_template: '{{conference_title}} - {{paper_title}}',
      sort_field: 'id',
      note: 'Conference proceedings'
    },
    schema: {
      name: 'conference_proceedings'
    }
  });

  // Honours and Awards Table
  await utils.createCollection({
    collection: 'honours_awards',
    meta: {
      icon: 'military_tech',
      display_template: '{{award_title}}',
      sort_field: 'id',
      note: 'Faculty honours and awards'
    },
    schema: {
      name: 'honours_awards'
    }
  });

  // Intellectual Property Table
  await utils.createCollection({
    collection: 'intellectual_property',
    meta: {
      icon: 'gavel',
      display_template: '{{title}}',
      sort_field: 'id',
      note: 'Faculty intellectual property'
    },
    schema: {
      name: 'intellectual_property'
    }
  });

  // Research and Consultancy Projects Table
  await utils.createCollection({
    collection: 'research_consultancy',
    meta: {
      icon: 'science',
      display_template: '{{project_title}}',
      sort_field: 'id',
      note: 'Research and consultancy projects'
    },
    schema: {
      name: 'research_consultancy'
    }
  });

  // Workshops and Seminars Table
  await utils.createCollection({
    collection: 'workshops_seminars',
    meta: {
      icon: 'people',
      display_template: '{{title}}',
      sort_field: 'id',
      note: 'Workshops and seminars'
    },
    schema: {
      name: 'workshops_seminars'
    }
  });

  // MDP/FDP Details Table
  await utils.createCollection({
    collection: 'mdp_fdp',
    meta: {
      icon: 'co_present',
      display_template: '{{title}}',
      sort_field: 'id',
      note: 'Management/Faculty Development Programs'
    },
    schema: {
      name: 'mdp_fdp'
    }
  });

  // Other Professional Activities Table
  await utils.createCollection({
    collection: 'professional_activities',
    meta: {
      icon: 'business_center',
      display_template: '{{activity_title}}',
      sort_field: 'id',
      note: 'Professional activities'
    },
    schema: {
      name: 'professional_activities'
    }
  });
}

module.exports = {
  createCollections
};