// check-admin.js - Script to verify admin access and permissions in Directus
const axios = require('axios');
const config = require('./config');

async function checkAdminAccess() {
  try {
    console.log('Testing Directus admin access for user:', config.adminEmail);
    
    // Step 1: Authenticate
    console.log('\n1. Authenticating...');
    const authResponse = await axios.post(`${config.directusUrl}/auth/login`, {
      email: config.adminEmail,
      password: config.adminPassword,
    });
    
    if (!authResponse.data?.data?.access_token) {
      console.error('❌ Authentication failed. No access token received.');
      return;
    }
    
    const token = authResponse.data.data.access_token;
    console.log('✅ Authentication successful! Token acquired.');
    
    // Step 2: Get current user and role info
    console.log('\n2. Checking user and role information...');
    const meResponse = await axios.get(`${config.directusUrl}/users/me`, {
      headers: { 'Authorization': `Bearer ${token}` }
    });
    
    const user = meResponse.data.data;
    console.log(`- User: ${user.first_name} ${user.last_name} (${user.email})`);
    console.log(`- Role ID: ${user.role}`);
    
    // Step 3: Get role details
    console.log('\n3. Checking role details...');
    const roleResponse = await axios.get(`${config.directusUrl}/roles/${user.role}`, {
      headers: { 'Authorization': `Bearer ${token}` }
    });
    
    const role = roleResponse.data.data;
    console.log(`- Role Name: ${role.name}`);
    console.log(`- Admin Access: ${role.admin_access ? 'YES ✅' : 'NO ❌'}`);
    console.log(`- App Access: ${role.app_access ? 'YES ✅' : 'NO ❌'}`);
    
    // Step 4: Test collections access
    console.log('\n4. Testing collections access...');
    try {
      const collectionsResponse = await axios.get(`${config.directusUrl}/collections`, {
        headers: { 'Authorization': `Bearer ${token}` }
      });
      
      console.log(`✅ Collections access: SUCCESS (${collectionsResponse.data.data.length} collections found)`);
    } catch (error) {
      console.error('❌ Collections access: FAILED', error.response?.data || error.message);
    }
    
    // Step 5: Test creating and deleting a test collection
    console.log('\n5. Testing ability to create and delete a test collection...');
    const testCollectionName = `test_collection_${Date.now()}`;
    try {
      await axios.post(`${config.directusUrl}/collections`, {
        collection: testCollectionName,
        meta: {
          icon: 'box',
          note: 'Test collection - will be deleted',
          display_template: '{{id}}'
        },
        schema: {
          name: testCollectionName,
          fields: [{ field: 'name', type: 'string' }]
        }
      }, {
        headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' }
      });
      
      console.log(`✅ Test collection "${testCollectionName}" created successfully`);
      
      await axios.delete(`${config.directusUrl}/collections/${testCollectionName}`, {
        headers: { 'Authorization': `Bearer ${token}` }
      });
      console.log(`✅ Test collection "${testCollectionName}" deleted successfully`);
    } catch (error) {
      console.error('❌ Collection creation/deletion test FAILED', error.response?.data || error.message);
    }
    
    // Step 6: Check permissions
    console.log('\n6. Checking permissions...');
    try {
      const permissionsResponse = await axios.get(`${config.directusUrl}/permissions/role/${user.role}`, {
        headers: { 'Authorization': `Bearer ${token}` }
      });
      console.log(`✅ Permissions access: SUCCESS (${permissionsResponse.data.data.length} records found)`);
    } catch (error) {
      console.error('❌ Permissions access: FAILED', error.response?.data || error.message);
    }
    
    console.log('\n=== SUMMARY ===');
    if (role.admin_access) {
      console.log('✅ Your account has ADMIN ACCESS rights in Directus');
    } else {
      console.log('❌ Your account does NOT have admin access.');
    }
  } catch (error) {
    console.error('❌ Test failed:', error.response?.data || error.message);
  }
}

// Run the test
checkAdminAccess();