# TPB Store Modal - Deployment Guide

## Overview

This project consists of two main components:
1. **Plugin**: `wp-content/plugins/tpb-quickview-modal/` - The main modal plugin
2. **MU-Plugin**: `wp-content/mu-plugins/tpb-quickview/` - Loader and API endpoints

## Current Status

Your project is on the `modal-clean-rebuild` branch. To deploy to production, you have several options:

## Deployment Options

### Option 1: Automated GitHub Actions Deployment (Recommended)

#### For Production
1. **Merge to main branch**: 
   ```bash
   git checkout main
   git merge modal-clean-rebuild
   git push origin main
   ```
   This will automatically trigger the deployment workflow via SFTP.

#### For Staging
1. **Create/switch to dev branch**:
   ```bash
   git checkout -b dev
   git push origin dev
   ```
   Or if already exists:
   ```bash
   git checkout dev
   git merge modal-clean-rebuild
   git push origin dev
   ```

#### What Gets Deployed
- ✅ Child theme: `wp-content/themes/hello-elementor-child-tpb/`
- ✅ MU-Plugin: `wp-content/mu-plugins/tpb-quickview/`
- ❌ **Main Plugin: `wp-content/plugins/tpb-quickview-modal/` is NOT deployed by current workflows**

### Option 2: Manual SFTP Upload

Since the current GitHub Actions only deploy the MU-plugin and theme, **the main plugin needs to be uploaded manually**:

1. **What to upload**:
   - `wp-content/plugins/tpb-quickview-modal/` → Upload to `/wp-content/plugins/tpb-quickview-modal/` on your live site

2. **How to upload**:
   - Use your hosting provider's file manager
   - Or use SFTP client (FileZilla, Cyberduck, etc.)
   - Connect to your live site via SFTP
   - Navigate to `/wp-content/plugins/`
   - Upload the entire `tpb-quickview-modal/` directory

3. **What to upload**:
   ```
   wp-content/plugins/tpb-quickview-modal/
   ├── tpb-quickview-modal.php
   ├── assets/
   │   ├── css/
   │   └── js/
   ├── templates/
   └── admin-test-page.php
   ```

### Option 3: Update GitHub Actions Workflow

To automate plugin deployment, update `.github/workflows/deploy.yml`:

```yaml
# Add after line 42:
      - name: Deploy plugin
        run: |
          # SFTP deploy plugin
```

Add this step to deploy the plugin automatically.

## Required Files to Deploy

### Essential Files (MUST Deploy)

#### Plugin Files
```
wp-content/plugins/tpb-quickview-modal/
├── tpb-quickview-modal.php
├── assets/css/modal-template.css    # ← Modified for cart z-index
├── assets/js/
│   ├── modal-state.js               # ← NEW: Cart z-index management
│   ├── modal-flower-handler.js       # ← Modified for cart z-index
│   ├── modal-category-handler.js     # ← Modified for cart z-index
│   ├── modal-quickcheckout-handler.js
│   ├── flower-stepper-modal.js      # ← Modified for spacing
│   ├── category-stepper-modal.js     # ← Modified for spacing
│   ├── quickcheckout-stepper-modal.js
│   ├── modal-branded-handler.js
│   └── modal-menu-boards-handler.js
└── templates/                        # All template files
```

#### MU-Plugin Files
```
wp-content/mu-plugins/tpb-quickview/
├── load.php                         # ← Modified to enqueue modal-state.js
├── api-qv.php
└── assets/js/                       # Native content files
```

### Files That Should NOT Be Deployed
- ❌ `local-site/` directory (local development only)
- ❌ `_working-docs/` (documentation only)
- ❌ `archive/` (backups)

## Pre-Deployment Checklist

- [ ] Test all modals locally
- [ ] Verify cart functionality works
- [ ] Check that cart icon appears above modal overlay
- [ ] Verify cart icon hides when drawer opens
- [ ] Test add to cart functionality
- [ ] Verify product images load correctly
- [ ] Clear browser cache and test

## Post-Deployment Steps

### On Live Site

1. **Activate the Plugin**:
   - Go to WordPress Admin → Plugins
   - Find "TPB QuickView Modal"
   - Click "Activate"

2. **Clear All Caches**:
   - WordPress cache (if using WP Super Cache, W3 Total Cache, etc.)
   - Browser cache
   - CDN cache (if using Cloudflare, etc.)
   - Elementor cache (Elementor → Tools → Regenerate CSS & Data)

3. **Verify Permissions**:
   - Check file permissions on uploaded files (should be 644 for files, 755 for directories)
   - Verify PHP can execute the plugin

4. **Test Everything**:
   - Open each modal type
   - Add products to cart
   - Verify cart behavior with modal overlay
   - Check product images load
   - Test stepper functionality

## Important Notes

### Database Changes
- **No database migrations required** - This is a plugin, not a theme
- All data is stored in WordPress products (post type)

### Compatibility
- ✅ WordPress 5.8+
- ✅ WooCommerce 6.0+
- ✅ PHP 7.4+
- ✅ Elementor 3.0+

### Dependencies
The plugin depends on:
1. **WooCommerce** - For cart functionality
2. **Elementor** - For page builder integration
3. **jQuery** - Already included in WordPress

### Environment Variables
No environment variables needed - all configuration is in the WordPress database.

## Troubleshooting

### If Modal Doesn't Appear
1. Check browser console for JavaScript errors
2. Verify plugin is activated
3. Check file permissions
4. Clear all caches

### If Cart Doesn't Work
1. Check WooCommerce is active
2. Verify jQuery is loading
3. Check browser console for errors
4. Verify API endpoints are accessible

### If CSS Doesn't Load
1. Check file permissions on CSS files
2. Verify cache is cleared
3. Check browser network tab for 404 errors
4. Ensure CSS file is in correct directory

## Manual Deployment Script

If you prefer to deploy manually, here's a quick script:

```bash
# 1. Create deployment directory
mkdir -p deployment-package

# 2. Copy plugin files
cp -r wp-content/plugins/tpb-quickview-modal deployment-package/

# 3. Copy MU-plugin files  
cp -r wp-content/mu-plugins/tpb-quickview deployment-package/

# 4. Create zip for upload
zip -r deployment-$(date +%Y%m%d).zip deployment-package/

# 5. Upload to your hosting via SFTP or hosting file manager
```

## GitHub Actions Secrets Required

For automated deployment, you need these secrets configured in GitHub:

- `SFTP_HOST` - Your SFTP server hostname
- `SFTP_PORT` - SFTP port (usually 22)
- `SFTP_USER` - SFTP username
- `SFTP_PASS` - SFTP password
- `SITE_URL` - Your website URL
- `DEPLOY_TOKEN` - Deployment token for cache flushing

## Support

If you encounter issues:
1. Check the browser console for errors
2. Check server error logs
3. Verify all files uploaded successfully
4. Ensure all dependencies are installed
5. Test with different browsers

## Security Notes

- ⚠️ **Never commit sensitive credentials to git**
- ✅ Use GitHub Secrets for deployment credentials
- ✅ Keep WordPress and plugins updated
- ✅ Regular backups before deployment
- ✅ Test on staging first before production
