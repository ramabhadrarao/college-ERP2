// index.js - Main orchestrator for importing SQL tables to Directus
const utils = require('./utils');

// Import module-specific scripts
const coreModule = require('./modules/core-schema');
const academicStructureModule = require('./modules/academic-structure');
const curriculumModule = require('./modules/curriculum');
const facultyModule = require('./modules/faculty-management');
// Uncomment as modules are developed
// const studentModule = require('./modules/student-management');
// const attendanceModule = require('./modules/attendance-system');
// const examinationModule = require('./modules/examination-system');
// const libraryModule = require('./modules/library-management');
// const hostelModule = require('./modules/hostel-management');
// const transportationModule = require('./modules/transportation');
// const financeModule = require('./modules/finance');
// const mentoringModule = require('./modules/mentoring-support');
// const notificationsModule = require('./modules/notifications-communication');
// const documentModule = require('./modules/document-management');

// Define the roles to be created
const roles = [
  { name: 'Administrator', admin_access: true, description: 'Full system access' },
  { name: 'Faculty', admin_access: false, description: 'Faculty members with course management access' },
  { name: 'Student', admin_access: false, description: 'Student access for viewing courses and submissions' },
  { name: 'Department Head', admin_access: false, description: 'Department management access' },
  { name: 'Librarian', admin_access: false, description: 'Library management access' },
  { name: 'Finance Officer', admin_access: false, description: 'Finance management access' },
  { name: 'Hostel Warden', admin_access: false, description: 'Hostel management access' },
  { name: 'Transport Manager', admin_access: false, description: 'Transportation management access' },
  { name: 'Exam Coordinator', admin_access: false, description: 'Examination management access' }
];

async function createRoles() {
  const createdRoles = {};
  
  for (const role of roles) {
    try {
      const created = await utils.createRole(role);
      createdRoles[role.name] = created.id;
    } catch (error) {
      console.error(`Failed to create role ${role.name}:`, error);
    }
  }
  
  return createdRoles;
}

// Main execution function
async function main() {
  try {
    // Authenticate with Directus
    await utils.authenticate();
    
    // Create roles first
    console.log('Creating roles...');
    const roleIds = await createRoles();
    
    // Import modules in a specific sequence
    // Core schema must be first as other modules depend on it
    console.log('Importing core schema...');
    await coreModule.import(roleIds);
    
    // Import subsequent modules
    console.log('Importing academic structure...');
    await academicStructureModule.import(roleIds);
    
    console.log('Importing curriculum module...');
    await curriculumModule.import(roleIds);
    
    console.log('Importing faculty management...');
    await facultyModule.import(roleIds);
    
    // Uncomment and implement other modules as they are developed
    // console.log('Importing student management...');
    // await studentModule.import(roleIds);
    
    // console.log('Importing attendance system...');
    // await attendanceModule.import(roleIds);
    
    // Additional modules can be added similarly
    
    console.log('Import process completed successfully!');
  } catch (error) {
    console.error('Import process failed:', error);
    // Optionally, you might want to add more detailed error handling
    // For example, logging the full error stack trace
    if (error.stack) {
      console.error('Error stack trace:', error.stack);
    }
  }
}

// Execute the main function
main();