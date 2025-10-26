# TPB Store Modal - Corrected Deployment Guide

## What You Actually Need to Deploy

Based on your actual project structure, you need to deploy:

### 1. **The Plugin** (`wp-content/plugins/tpb-quickview-modal/`)
   - Location on live site: `/wp-content/plugins/tpb-quickview-modal/`
   - This is the main modal plugin with all functionality

### 2. **The MU-Plugin** (`wp-content/mu-plugins/tpb-quickview/`)
   - Location on live site: `/wp-content/mu-plugins/tpb-quickview/`
   - This contains the loader and API endpoints

### 3. **Child Theme** (Optional - if you made theme customizations)
   - Location in `local-site/app/public/wp-content/themes/hello-elementor-child-tpb/`
   - Only needs deployment if you modified theme files

## Current GitHub Actions Status

Your `.github/workflows/deploy.yml` is configured to deploy:
- ✅ MU-Plugin (`wp-content/mu-plugins/tpb-quickview/`)
- ❌ **Plugin NOT configured for deployment** (`wp-content/plugins/tpb-quickview-modal/` needs to be added)

## What You Need to Do

### Option 1: Manual Deployment (Recommended)

1. **Upload the Plugin via SFTP**:
   ```bash
   # From your local machine
   cd wp-content/plugins/tpb-quickview-modal
   
   # Upload via SFTP client (FileZilla, Cyberduck, etc.)
   # Navigate to /wp-content/plugins/ on live site
   # Upload entire tpb-quickview-modal directory
   ```

2. **The MU-Plugin will deploy automatically** when you merge to main (if configured)

### Option 2: Update GitHub Actions to Deploy Plugin

Add plugin deployment to `.github/workflows/deploy.yml`:

```yaml
      - name: SFTP deploy plugin
        uses: wlixcc/SFTP-Deploy-Action@v1.2.4
        with:
          server: ${{ secrets.SFTP_HOST }}
          port: ${{ secrets.SFTP_PORT }}
          username: ${{ secrets.SFTP_USER }}
          password: ${{ secrets.SFTP_PASS }}
          sftp_only: true
          local_path: 'wp-content/plugins/tpb-quickview-modal'
          remote_path: '/html/wp-content/plugins/'
          delete_remote_files: false
```

## Actual Files to Deploy

### Essential Files from `wp-content/plugins/tpb-quickview-modal/`:

```
tpb-quickview-modal/
├── tpb-quickview-modal.php              # Main plugin file
├── admin-test-page.php
├── assets/
│   ├── css/
│   │   ├── modal-template.css            # CSS with cart z-index fixes
│   │   ├── modal.css
│   │   └── iframe-content.css
│   └── js/
│       ├── modal-state.js                 # NEW: Cart z-index management
│       ├── modal-flower-handler.js        # Modified
│       ├── modal-category-handler.js      # Modified
│       ├── modal-quickcheckout-handler.js
│       ├── modal-branded-handler.js
│       ├── modal-menu-boards-handler.js
│       ├── cart-integration.js
│       ├── flower-stepper-modal.js        # Modified
│       ├── category-stepper-modal.js      # Modified
│       ├── quickcheckout-stepper-modal.js
│       └── modal-utilities.js
└── templates/                             # All modal templates
```

### Essential Files from `wp-content/mu-plugins/tpb-quickview/`:

```
tpb-quickview/
├── load.php                               # Modified to load modal-state.js
├── api-qv.php                             # API endpoints
└── assets/js/                             # Native content handlers
```

## Deployment Steps

### Step 1: Prepare for Deployment

```bash
# Create deployment package
mkdir -p manual-deploy
cp -r wp-content/plugins/tpb-quickview-modal manual-deploy/
cp -r wp-content/mu-plugins/tpb-quickview manual-deploy/

# Create zip for easy upload
zip -r tpb-modal-deployment.zip manual-deploy/
```

### Step 2: Upload to Live Site

#### Via SFTP:
1. Connect to your live site via SFTP
2. Navigate to `/wp-content/plugins/`
3. Upload the `tpb-quickview-modal/` directory
4. Navigate to `/wp-content/mu-plugins/`
5. Upload the `tpb-quickview/` directory (or let GitHub Actions do this)

#### Via WordPress File Manager:
1. Log into WordPress Admin
2. Go to hosting file manager
3. Navigate to `/wp-content/plugins/`
4. Upload the plugin directory
5. Extract if needed

### Step 3: Activate Plugin

1. Go to WordPress Admin → Plugins
2. Find "TPB QuickView Modal"
3. Click "Activate"

### Step 4: Clear Caches

1. **Clear WordPress cache** (if using caching plugin)
2. **Clear Elementor cache**: Elementor → Tools → Regenerate CSS & Data
3. **Clear browser cache**
4. **Clear CDN cache** (if using Cloudflare, etc.)

### Step 5: Test Everything

- ✅ Open Flower Station modal
- ✅ Open Category Station modal
- ✅ Open Quick Checkout modal
- ✅ Test add to cart functionality
- ✅ Verify cart icon appears above modal overlay
- ✅ Verify cart drawer behavior
- ✅ Test product image loading
- ✅ Test stepper functionality

## Notes

- **No child theme changes** were made - all work is in the plugin
- **MU-Plugin** handles the loading and API
- **Main plugin** contains all the modal functionality

## If Something Goes Wrong

1. **Check file permissions**: Files should be 644, directories 755
2. **Check error logs**: `/wp-content/debug.log` and server error logs
3. **Check browser console**: For JavaScript errors
4. **Verify plugin activation**: WordPress Admin → Plugins
5. **Check file paths**: Verify files uploaded to correct locations
