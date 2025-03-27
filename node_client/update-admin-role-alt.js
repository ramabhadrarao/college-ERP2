// update-admin-role-alt.js
const axios = require('axios');
const config = require('./config');

async function updateAdminRoleAlt() {
  try {
    // Authenticate
    console.log('Authenticating...');
    const authResponse = await axios.post(`${config.directusUrl}/auth/login`, {
      email: config.adminEmail,
      password: config.adminPassword,
    });
    
    const token = authResponse.data.data.access_token;
    console.log('Authentication successful');
    
    // Get user info to find the role
    console.log('Getting user information...');
    const meResponse = await axios.get(`${config.directusUrl}/users/me`, {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });
    
    const roleId = meResponse.data.data.role;
    console.log(`Found role ID: ${roleId}`);
    
    // First, get current role data
    console.log('Getting current role data...');
    const roleResponse = await axios.get(`${config.directusUrl}/roles/${roleId}`, {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });
    
    const currentRole = roleResponse.data.data;
    console.log('Current role settings:');
    console.log('- Admin Access:', currentRole.admin_access);
    console.log('- App Access:', currentRole.app_access);
    
    // Update the role with PATCH method
    console.log('Updating role...');
    const updateResponse = await axios.patch(`${config.directusUrl}/roles/${roleId}`, {
      admin_access: true,
      app_access: true
    }, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    });
    
    console.log('Update response status:', updateResponse.status);
    
    // Verify the update
    console.log('Verifying update...');
    const verifyResponse = await axios.get(`${config.directusUrl}/roles/${roleId}`, {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });
    
    const updatedRole = verifyResponse.data.data;
    console.log('Updated role settings:');
    console.log('- Admin Access:', updatedRole.admin_access);
    console.log('- App Access:', updatedRole.app_access);
    
    if (updatedRole.admin_access && updatedRole.app_access) {
      console.log('✅ Successfully updated role to have admin access!');
    } else {
      console.log('❌ Role update failed. The settings did not change.');
      console.log('Consider updating the role manually through the Directus admin interface.');
    }
    
  } catch (error) {
    console.error('Failed to update role:', error.response?.data?.errors || error.message);
    console.error('Status code:', error.response?.status);
    console.error('Consider updating the role manually through the Directus admin interface.');
  }
}

updateAdminRoleAlt();