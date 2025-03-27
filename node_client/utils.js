// utils.js - Utility functions for Directus API interaction with existence checks
const axios = require('axios');
const config = require('./config');

// Store the last authentication time
let lastAuthTime = 0;
const AUTH_REFRESH_INTERVAL = 5 * 60 * 1000; // 5 minutes in milliseconds
/**
 * Authenticate with Directus and set the API token
 */
/**
 * Authenticate with Directus and set the API token
 * @param {boolean} force Force re-authentication even if token is still valid
 */
async function authenticate(force = false) {
  const now = Date.now();
  
  // If we have a token and it's not time to refresh, use the existing one
  if (config.apiToken && !force && (now - lastAuthTime < AUTH_REFRESH_INTERVAL)) {
    console.log('Using existing authentication token');
    return config.apiToken;
  }
  
  try {
    console.log('Authenticating with Directus...');
    const response = await axios.post(`${config.directusUrl}/auth/login`, {
      email: config.adminEmail,
      password: config.adminPassword,
    });

    config.apiToken = response.data.data.access_token;
    lastAuthTime = now;
    console.log('Authentication successful');
    return config.apiToken;
  } catch (error) {
    console.error('Authentication failed:', error.response?.data || error.message);
    throw error;
  }
}

/**
 * Get Axios instance with authentication headers
 */
function getApiClient() {
  return axios.create({
    baseURL: `${config.directusUrl}`,
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${config.apiToken}`
    }
  });
}
/**
 * Execute an API request with automatic re-authentication on 401/403 errors
 */
async function executeApiRequest(requestFn) {
  try {
    return await requestFn();
  } catch (error) {
    // If we get an authentication error, try to re-authenticate once
    if (error.response && (error.response.status === 401 || error.response.status === 403)) {
      console.log('Authentication token expired, re-authenticating...');
      await authenticate(true); // Force re-authentication
      
      // Retry the request with the new token
      try {
        return await requestFn();
      } catch (retryError) {
        console.error('Request failed after re-authentication:', retryError.response?.data || retryError.message);
        throw retryError;
      }
    }
    
    throw error;
  }
}

/**
 * Check if a collection exists
 */

/**
 * Check if a collection exists
 */
async function collectionExists(collectionName) {
  return executeApiRequest(async () => {
    const api = getApiClient();
    try {
      const response = await api.get(`/collections/${collectionName}`);
      return response.status === 200;
    } catch (error) {
      if (error.response?.status === 404) {
        return false;
      }
      throw error;
    }
  });
}

/**
 * Check if a field exists in a collection
 */
async function fieldExists(collection, fieldName) {
  return executeApiRequest(async () => {
    const api = getApiClient();
    try {
      const response = await api.get(`/fields/${collection}/${fieldName}`);
      return response.status === 200;
    } catch (error) {
      if (error.response?.status === 404) {
        return false;
      }
      throw error;
    }
  });
}

/**
 * Check if a relation exists
 */
async function relationExists(collection, field) {
  const api = getApiClient();
  try {
    const response = await api.get(`/relations/${collection}/${field}`);
    return response.status === 200;
  } catch (error) {
    if (error.response?.status === 404) {
      return false;
    }
    throw error;
  }
}

/**
 * Create a collection in Directus if it doesn't exist
 */
async function createCollection(collection) {
  return executeApiRequest(async () => {
    // Check if collection already exists
    const exists = await collectionExists(collection.collection);
    if (exists) {
      console.log(`Collection already exists: ${collection.collection}`);
      return false;
    }

    const api = getApiClient();
    try {
      await api.post('/collections', collection);
      console.log(`Collection created: ${collection.collection}`);
      return true;
    } catch (error) {
      if (error.response?.status === 400 && error.response?.data?.errors?.[0]?.message?.includes('Collection already exists')) {
        console.log(`Collection already exists: ${collection.collection}`);
        return false;
      }
      console.error(`Error creating collection ${collection.collection}:`, error.response?.data || error.message);
      throw error;
    }
  });
}

/**
 * Create a field in a collection if it doesn't exist
 */
async function createField(collection, field) {
  return executeApiRequest(async () => {
    // Check if field already exists
    const exists = await fieldExists(collection, field.field);
    if (exists) {
      console.log(`Field already exists: ${collection}.${field.field}`);
      return false;
    }

    const api = getApiClient();
    try {
      await api.post(`/fields/${collection}`, field);
      console.log(`Field created: ${collection}.${field.field}`);
      return true;
    } catch (error) {
      if (error.response?.status === 400 && error.response?.data?.errors?.[0]?.message?.includes('Field already exists')) {
        console.log(`Field already exists: ${collection}.${field.field}`);
        return false;
      }
      console.error(`Error creating field ${collection}.${field.field}:`, error.response?.data || error.message);
      throw error;
    }
  });
}


/**
 * Create a relation between collections if it doesn't exist
 */
async function createRelation(relation) {
  // Check if relation already exists
  const exists = await relationExists(relation.collection, relation.field);
  if (exists) {
    console.log(`Relation already exists: ${relation.collection}.${relation.field} -> ${relation.related_collection}`);
    return false;
  }

  const api = getApiClient();
  try {
    await api.post('/relations', relation);
    console.log(`Relation created: ${relation.collection}.${relation.field} -> ${relation.related_collection}`);
    return true;
  } catch (error) {
    if (error.response?.status === 400 && error.response?.data?.errors?.[0]?.message?.includes('Relation already exists')) {
      console.log(`Relation already exists: ${relation.collection}.${relation.field} -> ${relation.related_collection}`);
      return false;
    }
    console.error(`Error creating relation:`, error.response?.data || error.message);
    throw error;
  }
}

/**
 * Check if a role exists
 */
async function roleExists(roleName) {
  const api = getApiClient();
  try {
    const response = await api.get('/roles');
    const roles = response.data.data;
    return roles.some(role => role.name === roleName);
  } catch (error) {
    console.error(`Error checking if role exists: ${roleName}`, error.response?.data || error.message);
    throw error;
  }
}

/**
 * Get role ID by name
 */
async function getRoleIdByName(roleName) {
  const api = getApiClient();
  try {
    const response = await api.get('/roles');
    const roles = response.data.data;
    const role = roles.find(r => r.name === roleName);
    return role ? role.id : null;
  } catch (error) {
    console.error(`Error getting role ID for: ${roleName}`, error.response?.data || error.message);
    throw error;
  }
}

/**
 * Create a role in Directus if it doesn't exist
 */
async function createRole(role) {
  // Check if role already exists
  const exists = await roleExists(role.name);
  if (exists) {
    console.log(`Role already exists: ${role.name}`);
    const roleId = await getRoleIdByName(role.name);
    return { id: roleId, name: role.name };
  }

  const api = getApiClient();
  try {
    const response = await api.post('/roles', role);
    console.log(`Role created: ${role.name}`);
    return response.data.data;
  } catch (error) {
    console.error(`Error creating role ${role.name}:`, error.response?.data || error.message);
    throw error;
  }
}

/**
 * Check if a permission already exists
 */
async function permissionExists(permission) {
  const api = getApiClient();
  try {
    const response = await api.get('/permissions', {
      params: {
        filter: {
          role: { _eq: permission.role },
          collection: { _eq: permission.collection },
          action: { _eq: permission.action }
        }
      }
    });
    return response.data.data && response.data.data.length > 0;
  } catch (error) {
    console.error(`Error checking if permission exists:`, error.response?.data || error.message);
    throw error;
  }
}

/**
 * Create a permission for a role if it doesn't exist
 */
async function createPermission(permission) {
  // Check if permission already exists
  const exists = await permissionExists(permission);
  if (exists) {
    console.log(`Permission already exists for role ${permission.role} on collection ${permission.collection} (${permission.action})`);
    return false;
  }

  const api = getApiClient();
  try {
    await api.post('/permissions', permission);
    console.log(`Permission created for role ${permission.role} on collection ${permission.collection} (${permission.action})`);
    return true;
  } catch (error) {
    console.error(`Error creating permission:`, error.response?.data || error.message);
    throw error;
  }
}

/**
 * Check if a user exists
 */
async function userExists(email) {
  const api = getApiClient();
  try {
    const response = await api.get('/users', {
      params: {
        filter: {
          email: { _eq: email }
        }
      }
    });
    return response.data.data && response.data.data.length > 0;
  } catch (error) {
    console.error(`Error checking if user exists: ${email}`, error.response?.data || error.message);
    throw error;
  }
}

/**
 * Create a user in Directus if it doesn't exist
 */
async function createUser(user) {
  // Check if user already exists
  const exists = await userExists(user.email);
  if (exists) {
    console.log(`User already exists: ${user.email}`);
    return null;
  }

  const api = getApiClient();
  try {
    const response = await api.post('/users', user);
    console.log(`User created: ${user.email}`);
    return response.data.data;
  } catch (error) {
    if (error.response?.status === 400 && error.response?.data?.errors?.[0]?.message?.includes('User email already exists')) {
      console.log(`User already exists: ${user.email}`);
      return null;
    }
    console.error(`Error creating user ${user.email}:`, error.response?.data || error.message);
    throw error;
  }
}

/**
 * Create an SQL type to Directus type mapping
 */
function mapSqlTypeToDirectus(sqlType, length = null) {
  sqlType = sqlType.toUpperCase();
  
  if (sqlType.includes('INT') || sqlType === 'TINYINT') {
    return { type: 'integer' };
  } else if (sqlType === 'DECIMAL' || sqlType === 'FLOAT' || sqlType === 'DOUBLE') {
    return { type: 'decimal' };
  } else if (sqlType === 'VARCHAR' || sqlType === 'CHAR') {
    return { type: 'string', length: length };
  } else if (sqlType === 'TEXT' || sqlType.includes('TEXT')) {
    return { type: 'text' };
  } else if (sqlType === 'DATE') {
    return { type: 'date' };
  } else if (sqlType === 'TIME') {
    return { type: 'time' };
  } else if (sqlType === 'TIMESTAMP' || sqlType === 'DATETIME') {
    return { type: 'timestamp' };
  } else if (sqlType === 'BOOLEAN') {
    return { type: 'boolean' };
  } else if (sqlType === 'UUID') {
    return { type: 'uuid' };
  } else if (sqlType === 'YEAR') {
    return { type: 'integer' };
  } else {
    return { type: 'string' };
  }
}

module.exports = {
  authenticate,
  getApiClient,
  createCollection,
  createField,
  createRelation,
  createRole,
  createPermission,
  createUser,
  mapSqlTypeToDirectus,
  collectionExists,
  fieldExists,
  relationExists,
  roleExists,
  getRoleIdByName,
  permissionExists,
  userExists
};