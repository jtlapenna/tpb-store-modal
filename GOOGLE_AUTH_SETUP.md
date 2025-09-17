# Google Auth SFTP Credentials System Setup

## 🎯 **What This System Does**

This system automatically retrieves fresh SFTP credentials from your Elementor Cloud dashboard using Google OAuth authentication, eliminating the need to manually update credentials when they change (every 24 hours).

## 🔧 **How It Works**

1. **Google OAuth Login**: Uses Puppeteer to log into Elementor Cloud via Google authentication
2. **Credential Extraction**: Scrapes the SFTP credentials from the hosting section
3. **Connection Testing**: Tests the retrieved credentials to ensure they work
4. **Deployment**: Uses the fresh credentials for SFTP deployment
5. **Cleanup**: Removes credential files after deployment

## 📋 **Setup Instructions**

### **Step 1: Prepare Your Google Account**

**Important**: For automated systems, you'll need to either:

**Option A: Disable 2FA (Not Recommended)**
- Turn off 2-Factor Authentication on your Google account
- Use your regular Google password

**Option B: Use App-Specific Password (Recommended)**
1. Go to [Google Account Security](https://myaccount.google.com/security)
2. Enable 2-Factor Authentication if not already enabled
3. Go to "App passwords" section
4. Generate a new app password for "Elementor Deploy"
5. Use this app password instead of your regular password

### **Step 2: Add GitHub Secrets**

Go to your GitHub repository → Settings → Secrets and variables → Actions, and add these secrets:

```
ELEMENTOR_GOOGLE_EMAIL=your-google-email@gmail.com
ELEMENTOR_GOOGLE_PASSWORD=your-google-password-or-app-password
ELEMENTOR_SITE_URL=https://your-site.elementor.cloud
SITE_URL=https://your-site.elementor.cloud
DEPLOY_TOKEN=your-deploy-token
```

### **Step 3: Test the System Locally**

1. **Set environment variables:**
   ```bash
   export ELEMENTOR_GOOGLE_EMAIL="your-google-email@gmail.com"
   export ELEMENTOR_GOOGLE_PASSWORD="your-google-password-or-app-password"
   export ELEMENTOR_SITE_URL="https://your-site.elementor.cloud"
   ```

2. **Run the test:**
   ```bash
   node test-google-auth-credentials.js
   ```

3. **Expected behavior:**
   - A browser window will open
   - It will navigate to Elementor login
   - It will click "Sign in with Google"
   - It will handle the Google OAuth flow
   - It will extract SFTP credentials
   - It will test the connection

4. **Expected output:**
   ```
   🧪 Testing Elementor SFTP Credential Retrieval with Google Auth
   ==============================================================
   ✅ All required environment variables are set
   📧 Google Email: your-google-email@gmail.com
   🌐 Site URL: https://your-site.elementor.cloud
   
   🚀 Starting Google Auth credential retrieval test...
   🔐 Handling Google OAuth authentication...
   🖱️ Clicked Google sign-in button
   🌐 Redirected to Google OAuth page
   ✅ Successfully authenticated with Google
   ✅ Successfully logged into Elementor Cloud via Google
   🔍 Looking for hosting/SFTP section...
   ✅ Found SFTP credentials!
   📁 Saved to: sftp-credentials.json
   🌐 Host: your-sftp-host.elementor.cloud
   🔌 Port: 22
   👤 Username: your-username
   🔑 Password: ***
   
   🧪 Testing SFTP connection...
   ✅ SFTP connection successful!
   🎉 SUCCESS: Google Auth credential retrieval and testing completed successfully!
   ```

### **Step 4: Enable the Google Auth Workflow**

1. **Rename the workflow file:**
   ```bash
   mv .github/workflows/deploy-with-google-auth.yml .github/workflows/deploy.yml
   ```

2. **Commit and push:**
   ```bash
   git add .
   git commit -m "feat: implement Google Auth SFTP credential retrieval system"
   git push
   ```

## 🚀 **How to Use**

### **Automatic Deployment**
- Push to `main` branch → Google Auth login → Fresh credentials retrieved → Deployment happens
- No manual intervention needed
- Credentials are automatically updated every 24 hours

### **Manual Deployment**
- Go to Actions tab in GitHub
- Click "Deploy with Google Auth SFTP Credentials"
- Click "Run workflow"

## 🔍 **Troubleshooting**

### **If Google Auth Fails:**

1. **Check your Google credentials:**
   - Make sure email is correct
   - Use app-specific password if 2FA is enabled
   - Verify account has access to Elementor

2. **Common issues:**
   - 2FA enabled (use app password)
   - Google account doesn't have Elementor access
   - Network connectivity issues
   - Browser automation blocked

3. **Debug mode:**
   ```bash
   DEBUG=true node test-google-auth-credentials.js
   ```

### **If Credential Extraction Fails:**

1. **Check Elementor access:**
   - Log into Elementor manually first
   - Verify you can see SFTP credentials
   - Check if dashboard layout changed

2. **Test manually:**
   - Navigate to hosting section in Elementor
   - Look for SFTP credentials
   - Note the exact location/format

### **If SFTP Connection Fails:**

1. **Check if credentials are complete:**
   - Host should contain a domain name
   - Port should be a number (usually 22)
   - Username should not be "unknown"
   - Password should not be "unknown"

2. **Test manually:**
   ```bash
   sftp -P 22 username@hostname
   ```

## 📊 **System Benefits**

- ✅ **No more manual credential updates**
- ✅ **Handles 24-hour credential expiration**
- ✅ **Works with Google OAuth authentication**
- ✅ **Automatic testing of credentials**
- ✅ **Secure credential handling**
- ✅ **Browser automation for complex auth flows**

## 🔒 **Security Notes**

- Credentials are only stored temporarily during deployment
- No credentials are logged or saved permanently
- All credential files are cleaned up after deployment
- Uses secure Google OAuth authentication
- App-specific passwords are recommended over regular passwords

## 🎉 **Success Indicators**

When working correctly, you should see:
- ✅ "Successfully authenticated with Google"
- ✅ "Fresh credentials retrieved successfully via Google Auth"
- ✅ "SFTP connection test successful"
- ✅ "Deploy Theme Files with Fresh Credentials" succeeds
- ✅ "Deploy MU-Plugin Files with Fresh Credentials" succeeds
- ✅ "Flush Caches" completes

## ⚠️ **Important Notes**

- **Browser Window**: The test will open a visible browser window (not headless) so you can see the process
- **Google Auth**: Make sure your Google account has access to Elementor
- **App Passwords**: Recommended for security when using 2FA
- **Network**: Ensure your network allows browser automation

---

**Ready to test? Run `node test-google-auth-credentials.js` with your Google credentials!**
