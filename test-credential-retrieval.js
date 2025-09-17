#!/usr/bin/env node

/**
 * Test Script for Elementor SFTP Credential Retrieval
 * This script tests if we can retrieve credentials without actually logging in
 */

const { getSFTPCredentials, testSFTPConnection } = require('./scripts/get-elementor-sftp-credentials.js');

async function testCredentialRetrieval() {
  console.log('🧪 Testing Elementor SFTP Credential Retrieval System');
  console.log('==================================================');
  
  // Check if required environment variables are set
  const requiredEnvVars = ['ELEMENTOR_EMAIL', 'ELEMENTOR_PASSWORD', 'ELEMENTOR_SITE_URL'];
  const missingVars = requiredEnvVars.filter(varName => !process.env[varName]);
  
  if (missingVars.length > 0) {
    console.log('❌ Missing required environment variables:');
    missingVars.forEach(varName => console.log(`   - ${varName}`));
    console.log('');
    console.log('To test this system, set these environment variables:');
    console.log('export ELEMENTOR_EMAIL="your-email@example.com"');
    console.log('export ELEMENTOR_PASSWORD="your-password"');
    console.log('export ELEMENTOR_SITE_URL="https://your-site.elementor.cloud"');
    console.log('');
    console.log('Then run: node test-credential-retrieval.js');
    process.exit(1);
  }
  
  console.log('✅ All required environment variables are set');
  console.log(`📧 Email: ${process.env.ELEMENTOR_EMAIL}`);
  console.log(`🌐 Site URL: ${process.env.ELEMENTOR_SITE_URL}`);
  console.log('');
  
  try {
    console.log('🚀 Starting credential retrieval test...');
    const credentials = await getSFTPCredentials();
    
    console.log('');
    console.log('📋 Retrieved Credentials:');
    console.log(`   Host: ${credentials.host}`);
    console.log(`   Port: ${credentials.port}`);
    console.log(`   Username: ${credentials.username}`);
    console.log(`   Password: ${credentials.password ? '***' : 'Not found'}`);
    console.log(`   Timestamp: ${credentials.timestamp}`);
    console.log('');
    
    if (credentials.username && credentials.password && 
        credentials.username !== 'unknown' && credentials.password !== 'unknown') {
      
      console.log('🧪 Testing SFTP connection...');
      const isWorking = await testSFTPConnection(credentials);
      
      if (isWorking) {
        console.log('🎉 SUCCESS: Credential retrieval and testing completed successfully!');
        console.log('');
        console.log('This system can be used in GitHub Actions with these secrets:');
        console.log('ELEMENTOR_EMAIL - Your Elementor Cloud email');
        console.log('ELEMENTOR_PASSWORD - Your Elementor Cloud password');
        console.log('ELEMENTOR_SITE_URL - Your Elementor Cloud site URL');
        console.log('SITE_URL - Your WordPress site URL for cache flushing');
        console.log('DEPLOY_TOKEN - Your deployment token for cache flushing');
        process.exit(0);
      } else {
        console.log('⚠️  Credentials retrieved but SFTP connection test failed');
        console.log('This might be due to:');
        console.log('- Incorrect credentials');
        console.log('- Network restrictions');
        console.log('- SFTP server issues');
        process.exit(1);
      }
    } else {
      console.log('⚠️  Incomplete credentials retrieved');
      console.log('The system found some information but not complete SFTP credentials');
      process.exit(1);
    }
    
  } catch (error) {
    console.error('❌ Error during credential retrieval:', error.message);
    console.log('');
    console.log('This might be due to:');
    console.log('- Incorrect login credentials');
    console.log('- Elementor dashboard changes');
    console.log('- Network connectivity issues');
    console.log('- Missing dependencies');
    process.exit(1);
  }
}

// Run the test
testCredentialRetrieval();
