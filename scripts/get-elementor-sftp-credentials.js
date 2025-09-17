#!/usr/bin/env node

/**
 * Elementor SFTP Credential Retriever
 * This script automatically retrieves fresh SFTP credentials from Elementor Cloud
 */

const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');

// Configuration
const ELEMENTOR_EMAIL = process.env.ELEMENTOR_EMAIL;
const ELEMENTOR_PASSWORD = process.env.ELEMENTOR_PASSWORD;
const ELEMENTOR_SITE_URL = process.env.ELEMENTOR_SITE_URL;
const OUTPUT_FILE = process.env.OUTPUT_FILE || 'sftp-credentials.json';

/**
 * Get SFTP credentials from Elementor Cloud dashboard
 */
async function getSFTPCredentials() {
  console.log('🚀 Starting Elementor SFTP credential retrieval...');
  
  if (!ELEMENTOR_EMAIL || !ELEMENTOR_PASSWORD || !ELEMENTOR_SITE_URL) {
    throw new Error('Missing required environment variables: ELEMENTOR_EMAIL, ELEMENTOR_PASSWORD, ELEMENTOR_SITE_URL');
  }

  const browser = await puppeteer.launch({
    headless: true,
    args: ['--no-sandbox', '--disable-setuid-sandbox']
  });

  try {
    const page = await browser.newPage();
    
    // Set user agent to avoid detection
    await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
    
    console.log('📝 Logging into Elementor Cloud...');
    
    // Navigate to Elementor login
    await page.goto('https://my.elementor.com/login/', { waitUntil: 'networkidle2' });
    
    // Fill login form
    await page.waitForSelector('input[name="email"]', { timeout: 10000 });
    await page.type('input[name="email"]', ELEMENTOR_EMAIL);
    await page.type('input[name="password"]', ELEMENTOR_PASSWORD);
    
    // Submit login form
    await page.click('button[type="submit"]');
    
    // Wait for dashboard to load
    await page.waitForNavigation({ waitUntil: 'networkidle2' });
    
    console.log('✅ Successfully logged into Elementor Cloud');
    
    // Navigate to hosting section
    console.log('🔍 Looking for hosting/SFTP section...');
    
    // Try different possible URLs for hosting section
    const hostingUrls = [
      `${ELEMENTOR_SITE_URL}/wp-admin/admin.php?page=elementor-hosting`,
      `${ELEMENTOR_SITE_URL}/wp-admin/admin.php?page=elementor-tools`,
      `${ELEMENTOR_SITE_URL}/wp-admin/admin.php?page=elementor-system-info`,
      'https://my.elementor.com/hosting/',
      'https://my.elementor.com/sites/'
    ];
    
    let credentials = null;
    
    for (const url of hostingUrls) {
      try {
        console.log(`🔗 Trying URL: ${url}`);
        await page.goto(url, { waitUntil: 'networkidle2', timeout: 10000 });
        
        // Look for SFTP credentials in various possible locations
        credentials = await page.evaluate(() => {
          // Look for SFTP credentials in different possible formats
          const selectors = [
            // Common SFTP credential selectors
            '[data-sftp-host]',
            '.sftp-host',
            '#sftp-host',
            '[name="sftp_host"]',
            '.hosting-sftp-host',
            '.elementor-sftp-host',
            // Look for text patterns
            'text*="Host:"',
            'text*="Server:"',
            'text*="SFTP"'
          ];
          
          for (const selector of selectors) {
            const element = document.querySelector(selector);
            if (element) {
              const host = element.textContent || element.value || element.getAttribute('data-sftp-host');
              if (host && host.includes('.')) {
                return { found: true, host: host.trim() };
              }
            }
          }
          
          // Look for credentials in page text
          const pageText = document.body.textContent;
          const hostMatch = pageText.match(/Host[:\s]+([a-zA-Z0-9.-]+)/i);
          const portMatch = pageText.match(/Port[:\s]+(\d+)/i);
          const userMatch = pageText.match(/Username[:\s]+([a-zA-Z0-9_-]+)/i);
          const passMatch = pageText.match(/Password[:\s]+([a-zA-Z0-9_-]+)/i);
          
          if (hostMatch) {
            return {
              found: true,
              host: hostMatch[1].trim(),
              port: portMatch ? portMatch[1].trim() : '22',
              username: userMatch ? userMatch[1].trim() : null,
              password: passMatch ? passMatch[1].trim() : null
            };
          }
          
          return { found: false };
        });
        
        if (credentials && credentials.found) {
          console.log('✅ Found SFTP credentials!');
          break;
        }
        
      } catch (error) {
        console.log(`❌ Failed to access ${url}: ${error.message}`);
        continue;
      }
    }
    
    if (!credentials || !credentials.found) {
      throw new Error('Could not find SFTP credentials in Elementor dashboard');
    }
    
    // If we only found host, try to get other details
    if (credentials.host && !credentials.username) {
      console.log('🔍 Found host, looking for complete credentials...');
      
      // Try to find complete credentials
      const fullCredentials = await page.evaluate(() => {
        const text = document.body.textContent;
        
        // Look for complete SFTP credential block
        const sftpBlock = text.match(/SFTP[^]*?Host[:\s]+([^\s]+)[^]*?Port[:\s]+(\d+)[^]*?Username[:\s]+([^\s]+)[^]*?Password[:\s]+([^\s]+)/is);
        
        if (sftpBlock) {
          return {
            host: sftpBlock[1].trim(),
            port: sftpBlock[2].trim(),
            username: sftpBlock[3].trim(),
            password: sftpBlock[4].trim()
          };
        }
        
        return null;
      });
      
      if (fullCredentials) {
        credentials = fullCredentials;
      }
    }
    
    // Save credentials to file
    const credentialsData = {
      timestamp: new Date().toISOString(),
      host: credentials.host,
      port: credentials.port || '22',
      username: credentials.username || 'unknown',
      password: credentials.password || 'unknown',
      source: 'elementor-dashboard'
    };
    
    fs.writeFileSync(OUTPUT_FILE, JSON.stringify(credentialsData, null, 2));
    
    console.log('✅ SFTP credentials retrieved and saved!');
    console.log(`📁 Saved to: ${OUTPUT_FILE}`);
    console.log(`🌐 Host: ${credentialsData.host}`);
    console.log(`🔌 Port: ${credentialsData.port}`);
    console.log(`👤 Username: ${credentialsData.username}`);
    console.log(`🔑 Password: ${credentialsData.password ? '***' : 'Not found'}`);
    
    return credentialsData;
    
  } finally {
    await browser.close();
  }
}

/**
 * Test SFTP connection with retrieved credentials
 */
async function testSFTPConnection(credentials) {
  console.log('🧪 Testing SFTP connection...');
  
  const { spawn } = require('child_process');
  
  return new Promise((resolve) => {
    const sftp = spawn('sftp', [
      '-P', credentials.port.toString(),
      '-o', 'ConnectTimeout=10',
      '-o', 'StrictHostKeyChecking=no',
      '-o', 'BatchMode=yes',
      `${credentials.username}@${credentials.host}`
    ]);

    sftp.stdin.write('exit\n');
    sftp.stdin.end();

    let output = '';
    sftp.stdout.on('data', (data) => {
      output += data.toString();
    });

    sftp.stderr.on('data', (data) => {
      output += data.toString();
    });

    sftp.on('close', (code) => {
      if (code === 0) {
        console.log('✅ SFTP connection successful!');
        resolve(true);
      } else {
        console.log('❌ SFTP connection failed');
        console.log('Error output:', output);
        resolve(false);
      }
    });

    sftp.on('error', (error) => {
      console.log('❌ SFTP connection error:', error.message);
      resolve(false);
    });
  });
}

/**
 * Main function
 */
async function main() {
  try {
    const credentials = await getSFTPCredentials();
    
    if (credentials.username && credentials.password && credentials.username !== 'unknown' && credentials.password !== 'unknown') {
      const isWorking = await testSFTPConnection(credentials);
      
      if (isWorking) {
        console.log('🎉 SUCCESS: Fresh SFTP credentials retrieved and tested!');
        console.log('Export these for use in GitHub Actions:');
        console.log(`export SFTP_HOST="${credentials.host}"`);
        console.log(`export SFTP_PORT="${credentials.port}"`);
        console.log(`export SFTP_USER="${credentials.username}"`);
        console.log(`export SFTP_PASS="${credentials.password}"`);
        process.exit(0);
      } else {
        console.log('⚠️  Credentials retrieved but connection test failed');
        process.exit(1);
      }
    } else {
      console.log('⚠️  Incomplete credentials retrieved');
      process.exit(1);
    }
    
  } catch (error) {
    console.error('❌ Error:', error.message);
    process.exit(1);
  }
}

if (require.main === module) {
  main();
}

module.exports = { getSFTPCredentials, testSFTPConnection };
