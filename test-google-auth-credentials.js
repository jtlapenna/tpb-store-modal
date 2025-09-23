#!/usr/bin/env node

/**
 * Test Script for Elementor SFTP Credential Retrieval with Google Auth
 * This script tests if we can retrieve credentials using Google authentication
 */

import { getSFTPCredentials, testSFTPConnection } from './scripts/get-elementor-sftp-credentials-google-auth.js';

async function testGoogleAuthCredentialRetrieval() {
  console.log('🧪 Testing Elementor SFTP Credential Retrieval with Google Auth');
  console.log('==============================================================');
  
  // Check if required environment variables are set
  const requiredEnvVars = ['ELEMENTOR_GOOGLE_EMAIL', 'ELEMENTOR_GOOGLE_PASSWORD', 'ELEMENTOR_SITE_URL'];
  const missingVars = requiredEnvVars.filter(varName => !process.env[varName]);
  
  if (missingVars.length > 0) {
    console.log('❌ Missing required environment variables:');
    missingVars.forEach(varName => console.log(`   - ${varName}`));
    console.log('');
    console.log('To test this system with Google Auth, set these environment variables:');
    console.log('export ELEMENTOR_GOOGLE_EMAIL="your-google-email@gmail.com"');
    console.log('export ELEMENTOR_GOOGLE_PASSWORD="your-google-password"');
    console.log('export ELEMENTOR_SITE_URL="https://your-site.elementor.cloud"');
    console.log('');
    console.log('Then run: node test-google-auth-credentials.js');
    console.log('');
    console.log('Note: This will open a browser window for Google authentication.');
    console.log('Make sure you have access to your Google account and 2FA is disabled or');
    console.log('you have an app-specific password set up.');
    process.exit(1);
  }
  
  console.log('✅ All required environment variables are set');
  console.log(`📧 Google Email: ${process.env.ELEMENTOR_GOOGLE_EMAIL}`);
  console.log(`🌐 Site URL: ${process.env.ELEMENTOR_SITE_URL}`);
  console.log('');
  console.log('⚠️  Note: This will open a browser window for Google authentication.');
  console.log('The browser will be visible (not headless) so you can see the process.');
  console.log('');
  
  try {
    console.log('🚀 Starting Google Auth credential retrieval test...');
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
        console.log('🎉 SUCCESS: Google Auth credential retrieval and testing completed successfully!');
        console.log('');
        console.log('This system can be used in GitHub Actions with these secrets:');
        console.log('ELEMENTOR_GOOGLE_EMAIL - Your Google email for Elementor');
        console.log('ELEMENTOR_GOOGLE_PASSWORD - Your Google password (or app password)');
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
    console.error('❌ Error during Google Auth credential retrieval:', error.message);
    console.log('');
    console.log('This might be due to:');
    console.log('- Incorrect Google credentials');
    console.log('- 2FA enabled on Google account (use app password)');
    console.log('- Elementor dashboard changes');
    console.log('- Network connectivity issues');
    console.log('- Missing dependencies');
    console.log('');
    console.log('Troubleshooting tips:');
    console.log('1. Make sure 2FA is disabled or use an app-specific password');
    console.log('2. Check if your Google account has access to Elementor');
    console.log('3. Try logging into Elementor manually first to verify access');
    process.exit(1);
  }
}

// Run the test
testGoogleAuthCredentialRetrieval();

