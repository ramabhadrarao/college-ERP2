// check-connection.js - Script to check Directus API connectivity
const axios = require('axios');
const config = require('./config');

async function checkConnection() {
  try {
    console.log(`Testing connection to Directus API at ${config.directusUrl}...`);
    
    // Check if the server is up
    const response = await axios.get(`${config.directusUrl}/server/ping`);
    
    if (response.data === 'pong') {
      console.log('✅ Connection successful! Server is up and responding.');
      
      // Try to authenticate
      try {
        const authResponse = await axios.post(`${config.directusUrl}/auth/login`, {
          email: config.adminEmail,
          password: config.adminPassword,
        });
        
        if (authResponse.data && authResponse.data.data && authResponse.data.data.access_token) {
          console.log('✅ Authentication successful!');
          
          // Check collections endpoint with the token
          const token = authResponse.data.data.access_token;
          try {
            const collectionResponse = await axios.get(`${config.directusUrl}/collections`, {
              headers: {
                'Authorization': `Bearer ${token}`
              }
            });
            
            if (collectionResponse.data && collectionResponse.data.data) {
              console.log(`✅ Successfully fetched collections. Found ${collectionResponse.data.data.length} collections.`);
              console.log('🚀 All systems ready! You can proceed with the import process.');
              return true;
            }
          } catch (error) {
            console.error('❌ Failed to fetch collections:', error.response?.data || error.message);
          }
        }
      } catch (authError) {
        console.error('❌ Authentication failed:', authError.response?.data || authError.message);
        console.log('Please check your admin email and password in config.js');
      }
    }
  } catch (error) {
    console.error('❌ Connection failed:', error.message);
    if (error.code === 'ECONNREFUSED') {
      console.log(`Make sure Directus is running at ${config.directusUrl}`);
    }
  }
  
  return false;
}

// Execute the check
checkConnection();