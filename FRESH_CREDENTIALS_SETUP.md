# Fresh SFTP Credentials System Setup

## 🎯 **What This System Does**

This system automatically retrieves fresh SFTP credentials from your Elementor Cloud dashboard every time you deploy, eliminating the need to manually update credentials when they change (every 24 hours).

## 🔧 **How It Works**

1. **Automated Login**: Uses Puppeteer to log into your Elementor Cloud dashboard
2. **Credential Extraction**: Scrapes the SFTP credentials from the hosting section
3. **Connection Testing**: Tests the retrieved credentials to ensure they work
4. **Deployment**: Uses the fresh credentials for SFTP deployment
5. **Cleanup**: Removes credential files after deployment

## 📋 **Setup Instructions**

### **Step 1: Add GitHub Secrets**

Go to your GitHub repository → Settings → Secrets and variables → Actions, and add these secrets:

```
ELEMENTOR_EMAIL=your-email@example.com
ELEMENTOR_PASSWORD=your-elementor-password
ELEMENTOR_SITE_URL=https://your-site.elementor.cloud
SITE_URL=https://your-site.elementor.cloud
DEPLOY_TOKEN=your-deploy-token
```

### **Step 2: Test the System Locally**

1. **Set environment variables:**
   ```bash
   export ELEMENTOR_EMAIL="your-email@example.com"
   export ELEMENTOR_PASSWORD="your-password"
   export ELEMENTOR_SITE_URL="https://your-site.elementor.cloud"
   ```

2. **Run the test:**
   ```bash
   node test-credential-retrieval.js
   ```

3. **Expected output:**
   ```
   🧪 Testing Elementor SFTP Credential Retrieval System
   ==================================================
   ✅ All required environment variables are set
   📧 Email: your-email@example.com
   🌐 Site URL: https://your-site.elementor.cloud
   
   🚀 Starting credential retrieval test...
   📝 Logging into Elementor Cloud...
   ✅ Successfully logged into Elementor Cloud
   🔍 Looking for hosting/SFTP section...
   ✅ Found SFTP credentials!
   📁 Saved to: sftp-credentials.json
   🌐 Host: your-sftp-host.elementor.cloud
   🔌 Port: 22
   👤 Username: your-username
   🔑 Password: ***
   
   🧪 Testing SFTP connection...
   ✅ SFTP connection successful!
   🎉 SUCCESS: Credential retrieval and testing completed successfully!
   ```

### **Step 3: Enable the Fresh Credentials Workflow**

1. **Rename the workflow file:**
   ```bash
   mv .github/workflows/deploy-with-fresh-credentials.yml .github/workflows/deploy.yml
   ```

2. **Commit and push:**
   ```bash
   git add .
   git commit -m "feat: implement fresh SFTP credential retrieval system"
   git push
   ```

## 🚀 **How to Use**

### **Automatic Deployment**
- Push to `main` branch → Fresh credentials are retrieved → Deployment happens
- No manual intervention needed
- Credentials are automatically updated every 24 hours

### **Manual Deployment**
- Go to Actions tab in GitHub
- Click "Deploy with Fresh SFTP Credentials"
- Click "Run workflow"

## 🔍 **Troubleshooting**

### **If Credential Retrieval Fails:**

1. **Check your login credentials:**
   ```bash
   node test-credential-retrieval.js
   ```

2. **Common issues:**
   - Wrong email/password
   - Elementor dashboard layout changed
   - Network connectivity issues
   - Missing dependencies

3. **Debug mode:**
   ```bash
   DEBUG=true node test-credential-retrieval.js
   ```

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
- ✅ **Automatic testing of credentials**
- ✅ **Secure credential handling**
- ✅ **Fallback to manual package if needed**

## 🔒 **Security Notes**

- Credentials are only stored temporarily during deployment
- No credentials are logged or saved permanently
- All credential files are cleaned up after deployment
- Uses secure authentication methods

## 🎉 **Success Indicators**

When working correctly, you should see:
- ✅ "Fresh credentials retrieved successfully"
- ✅ "SFTP connection test successful"
- ✅ "Deploy Theme Files with Fresh Credentials" succeeds
- ✅ "Deploy MU-Plugin Files with Fresh Credentials" succeeds
- ✅ "Flush Caches" completes

---

**Ready to test? Run `node test-credential-retrieval.js` with your credentials!**
