// modules/faculty-management/relations.js - Creates relations between faculty management collections
const utils = require('../../utils');

async function createRelations() {
  // Define relationships between collections
  const relations = [
    // Faculty to Gender
    {
      collection: 'faculty',
      field: 'gender_id',
      related_collection: 'gender',
      meta: {
        junction_field: null,
        many_collection: 'faculty',
        many_field: 'gender_id',
        one_collection: 'gender',
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
    
    // Faculty to Blood Group
    {
      collection: 'faculty',
      field: 'blood_group_id',
      related_collection: 'blood_groups',
      meta: {
        junction_field: null,
        many_collection: 'faculty',
        many_field: 'blood_group_id',
        one_collection: 'blood_groups',
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
    
    // Faculty to Department
    {
      collection: 'faculty',
      field: 'department_id',
      related_collection: 'departments',
      meta: {
        junction_field: null,
        many_collection: 'faculty',
        many_field: 'department_id',
        one_collection: 'departments',
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
    
    // Faculty Additional Details to Faculty
    {
      collection: 'faculty_additional_details',
      field: 'faculty_id',
      related_collection: 'faculty',
      meta: {
        junction_field: null,
        many_collection: 'faculty_additional_details',
        many_field: 'faculty_id',
        one_collection: 'faculty',
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
    
    // Faculty Additional Details to Nationality
    {
      collection: 'faculty_additional_details',
      field: 'nationality_id',
      related_collection: 'nationality',
      meta: {
        junction_field: null,
        many_collection: 'faculty_additional_details',
        many_field: 'nationality_id',
        one_collection: 'nationality',
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
    
    // Faculty Additional Details to Religion
    {
      collection: 'faculty_additional_details',
      field: 'religion_id',
      related_collection: 'religion',
      meta: {
        junction_field: null,
        many_collection: 'faculty_additional_details',
        many_field: 'religion_id',
        one_collection: 'religion',
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
    
    // Faculty Additional Details to Caste
    {
      collection: 'faculty_additional_details',
      field: 'caste_id',
      related_collection: 'caste',
      meta: {
        junction_field: null,
        many_collection: 'faculty_additional_details',
        many_field: 'caste_id',
        one_collection: 'caste',
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
    
    // Faculty Additional Details to Sub Caste
    {
      collection: 'faculty_additional_details',
      field: 'sub_caste_id',
      related_collection: 'sub_caste',
      meta: {
        junction_field: null,
        many_collection: 'faculty_additional_details',
        many_field: 'sub_caste_id',
        one_collection: 'sub_caste',
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
    
    // Work Experiences to Faculty
    {
      collection: 'work_experiences',
      field: 'faculty_id',
      related_collection: 'faculty',
      meta: {
        junction_field: null,
        many_collection: 'work_experiences',
        many_field: 'faculty_id',
        one_collection: 'faculty',
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
    
    // Faculty Qualifications to Faculty
    {
      collection: 'faculty_qualifications',
      field: 'faculty_id',
      related_collection: 'faculty',
      meta: {
        junction_field: null,
        many_collection: 'faculty_qualifications',
        many_field: 'faculty_id',
        one_collection: 'faculty',
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
  
  // Define a helper function to create faculty-related entity relations
  const createFacultyEntityRelations = (collection, specificRelations = []) => {
    const defaultRelations = [
      // Entity to Faculty
      {
        collection: collection,
        field: 'faculty_id',
        related_collection: 'faculty',
        meta: {
          junction_field: null,
          many_collection: collection,
          many_field: 'faculty_id',
          one_collection: 'faculty',
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
    
    return [...defaultRelations, ...specificRelations];
  };
  
  // Add relations for teaching activities
  const teachingActivitiesRelations = createFacultyEntityRelations('teaching_activities', [
    // Teaching Activities to Semester
    {
      collection: 'teaching_activities',
      field: 'semester_id',
      related_collection: 'semesters',
      meta: {
        junction_field: null,
        many_collection: 'teaching_activities',
        many_field: 'semester_id',
        one_collection: 'semesters',
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
    // Teaching Activities to Academic Year
    {
      collection: 'teaching_activities',
      field: 'academic_year_id',
      related_collection: 'academic_years',
      meta: {
        junction_field: null,
        many_collection: 'teaching_activities',
        many_field: 'academic_year_id',
        one_collection: 'academic_years',
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
  ]);
  
  // Add relations for research publications
  const researchPublicationsRelations = createFacultyEntityRelations('research_publications', [
    // Research Publications to Publication Type
    {
      collection: 'research_publications',
      field: 'type_id',
      related_collection: 'publication_type',
      meta: {
        junction_field: null,
        many_collection: 'research_publications',
        many_field: 'type_id',
        one_collection: 'publication_type',
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
  ]);
  
      // Add relations for conference proceedings
  const conferenceProceedingsRelations = createFacultyEntityRelations('conference_proceedings', [
    // Conference Proceedings to Conference Role
    {
      collection: 'conference_proceedings',
      field: 'role_id',
      related_collection: 'conference_role',
      meta: {
        junction_field: null,
        many_collection: 'conference_proceedings',
        many_field: 'role_id',
        one_collection: 'conference_role',
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
  ]);
  
  // Add relations for honours and awards
  const honoursAwardsRelations = createFacultyEntityRelations('honours_awards', [
    // Honours and Awards to Award Category
    {
      collection: 'honours_awards',
      field: 'category_id',
      related_collection: 'award_category',
      meta: {
        junction_field: null,
        many_collection: 'honours_awards',
        many_field: 'category_id',
        one_collection: 'award_category',
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
  ]);
  
  // Add relations for intellectual property
  const intellectualPropertyRelations = createFacultyEntityRelations('intellectual_property', [
    // Intellectual Property to IP Status
    {
      collection: 'intellectual_property',
      field: 'status_id',
      related_collection: 'intellectual_property_status',
      meta: {
        junction_field: null,
        many_collection: 'intellectual_property',
        many_field: 'status_id',
        one_collection: 'intellectual_property_status',
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
  ]);
  
  // Add relations for research consultancy
  const researchConsultancyRelations = createFacultyEntityRelations('research_consultancy', [
    // Research Consultancy to Funding Agency
    {
      collection: 'research_consultancy',
      field: 'agency_id',
      related_collection: 'funding_agency',
      meta: {
        junction_field: null,
        many_collection: 'research_consultancy',
        many_field: 'agency_id',
        one_collection: 'funding_agency',
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
  ]);
  
  // Add relations for workshops and seminars
  const workshopsSeminarsRelations = createFacultyEntityRelations('workshops_seminars', [
    // Workshops Seminars to Workshop Type
    {
      collection: 'workshops_seminars',
      field: 'type_id',
      related_collection: 'workshop_type',
      meta: {
        junction_field: null,
        many_collection: 'workshops_seminars',
        many_field: 'type_id',
        one_collection: 'workshop_type',
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
  ]);
  
  // Add relations for MDP/FDP
  const mdpFdpRelations = createFacultyEntityRelations('mdp_fdp', [
    // MDP/FDP to MDP/FDP Type
    {
      collection: 'mdp_fdp',
      field: 'type_id',
      related_collection: 'mdp_fdp_type',
      meta: {
        junction_field: null,
        many_collection: 'mdp_fdp',
        many_field: 'type_id',
        one_collection: 'mdp_fdp_type',
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
  ]);
  
  // Add relations for professional activities
  const professionalActivitiesRelations = createFacultyEntityRelations('professional_activities', []);
  
  // Create a combined array of all relations
  const allRelations = [
    ...relations,
    ...teachingActivitiesRelations,
    ...researchPublicationsRelations,
    ...conferenceProceedingsRelations,
    ...honoursAwardsRelations,
    ...intellectualPropertyRelations,
    ...researchConsultancyRelations,
    ...workshopsSeminarsRelations,
    ...mdpFdpRelations,
    ...professionalActivitiesRelations
  ];

  // Create all the relations
  for (const relation of allRelations) {
    await utils.createRelation(relation);
  }
}

module.exports = {
  createRelations
};