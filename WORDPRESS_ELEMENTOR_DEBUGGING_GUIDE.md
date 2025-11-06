# WordPress/Elementor Configuration Debugging Guide
**Date:** November 5, 2025  
**Scenario:** Clean restart from archive works in code, but still broken due to environment/configuration

---

## What This Means

If clean restart fails, it's likely:

1. **WordPress Configuration Issues**
   - Plugin conflicts
   - Theme conflicts
   - WordPress version incompatibility
   - Database corruption

2. **Elementor Configuration Issues**
   - Elementor version mismatch
   - Elementor settings conflicts
   - Elementor template conflicts
   - Elementor widget conflicts

3. **Server Environment Issues**
   - PHP version/configuration
   - Missing PHP extensions
   - Server caching (OPcache, etc.)
   - File permissions

4. **Caching Issues**
   - WordPress object cache
   - Browser cache
   - CDN cache
   - Server-side caching

---

## Step 1: Identify the Problem

### 1.1 Check Browser Console

**Open DevTools (F12) → Console Tab**

**Look for:**
- JavaScript errors (red text)
- Failed script loads (404 errors)
- Syntax errors
- Reference errors (undefined variables)

**Common Errors:**
```
❌ Uncaught ReferenceError: jQuery is not defined
   → jQuery not loaded or wrong version

❌ Failed to load resource: .../modal-state.js 404
   → File path incorrect or file missing

❌ Uncaught TypeError: Cannot read property 'build' of undefined
   → Stepper not loading or wrong export

❌ CORS error
   → Server configuration issue
```

**Action:** Document all errors, take screenshots

---

### 1.2 Check Network Tab

**Open DevTools (F12) → Network Tab**

**Check:**
- Are JS/CSS files loading? (Status 200)
- Are files returning 404? (File not found)
- Are files blocked? (CORS, CSP errors)
- Are files cached? (304 status = cached)

**Look for:**
```
✅ modal-state.js → 200 OK (loaded)
✅ modal-flower-handler.js → 200 OK (loaded)
✅ flower-stepper-modal.js → 200 OK (loaded)
❌ flower-stepper-modal.js → 404 Not Found (missing)
❌ modal-state.js → 403 Forbidden (permission issue)
```

**Action:** Verify all required files are loading

---

### 1.3 Check WordPress Error Logs

**Location:** Usually in:
- `wp-content/debug.log` (if WP_DEBUG enabled)
- Server error logs (var/log/apache2/error.log, etc.)
- Hosting control panel error logs

**Enable WordPress Debugging:**
```php
// wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

**Look for:**
- PHP errors
- Plugin conflicts
- Memory issues
- Database errors

**Action:** Check logs after opening modal, document errors

---

### 1.4 Check Elementor Console

**Elementor → Tools → Replace URL → Check Console**

**Or check browser console for Elementor errors:**
```
❌ Elementor Frontend not loaded
❌ Elementor Pro required
❌ Elementor version mismatch
```

**Action:** Verify Elementor is active and compatible

---

## Step 2: Diagnose Specific Issues

### 2.1 Plugin Conflicts

**Test:**
1. Deactivate ALL plugins except:
   - Elementor
   - Elementor Pro (if using)
   - WooCommerce (if using)
   - TPB QuickView Modal

2. Test modal - does it work?
   - ✅ Works → Plugin conflict identified
   - ❌ Still broken → Not a plugin conflict

3. Reactivate plugins one at a time
4. Test after each activation
5. When it breaks, you've found the conflict

**Common Conflicting Plugins:**
- Caching plugins (WP Super Cache, W3 Total Cache)
- Security plugins (Wordfence, iThemes Security)
- Minification plugins (Autoptimize, WP Rocket)
- Other modal/lightbox plugins

**Fix:**
- Configure conflicting plugin to exclude modal scripts
- Or find alternative plugin
- Or modify conflicting plugin settings

---

### 2.2 Theme Conflicts

**Test:**
1. Switch to default WordPress theme (Twenty Twenty-Four)
2. Test modal - does it work?
   - ✅ Works → Theme conflict
   - ❌ Still broken → Not a theme conflict

**If Theme Conflict:**
- Check theme's `functions.php` for conflicting code
- Check theme's JavaScript files for conflicts
- Check theme's CSS for z-index conflicts
- Contact theme developer

**Common Theme Issues:**
- Theme enqueues conflicting jQuery version
- Theme has global z-index rules
- Theme has modal/overlay system
- Theme's JavaScript conflicts with modal scripts

---

### 2.3 Elementor Configuration

**Check Elementor Settings:**

1. **Elementor → Settings → Advanced**
   - CSS Print Method: Should be "External File" or "Internal Embedding"
   - DOM Optimization: Try disabling
   - Improved CSS Loading: Try disabling

2. **Elementor → Settings → Style**
   - Default Generic Fonts: Check for conflicts
   - Default Colors: Check for conflicts

3. **Elementor → Tools → Regenerate CSS**
   - Click "Regenerate CSS & Data"
   - Clear cache after

4. **Elementor → Tools → Replace URL**
   - Check for URL mismatches

**Common Elementor Issues:**
- Elementor's z-index rules conflict
- Elementor's JavaScript conflicts
- Elementor template conflicts with modal
- Elementor Pro features interfering

**Fix:**
- Disable Elementor features one at a time
- Test after each disable
- Identify conflicting feature

---

### 2.4 WordPress Configuration

**Check:**

1. **WordPress Version**
   - Minimum: WordPress 5.0+
   - Recommended: Latest stable
   - Check: Dashboard → Updates

2. **PHP Version**
   - Minimum: PHP 7.4
   - Recommended: PHP 8.0+
   - Check: Tools → Site Health → Info → Server

3. **Memory Limits**
   - PHP Memory: 256MB minimum
   - WordPress Memory: 64MB minimum
   - Check: wp-config.php or php.ini

4. **File Permissions**
   - Plugin files: 644 (readable)
   - Plugin directories: 755 (executable)
   - Check: `ls -la` in plugin directory

**Fix:**
- Update WordPress if outdated
- Update PHP if outdated
- Increase memory limits
- Fix file permissions

---

### 2.5 Server Caching

**Check:**

1. **OPcache (PHP)**
   - May cache old PHP files
   - Restart PHP-FPM or Apache
   - Or disable OPcache temporarily

2. **Object Cache (WordPress)**
   - Redis, Memcached, etc.
   - Clear object cache
   - Or disable temporarily

3. **CDN Cache**
   - Cloudflare, etc.
   - Purge CDN cache
   - Or disable CDN temporarily

4. **Browser Cache**
   - Hard refresh: Cmd+Shift+R (Mac) or Ctrl+Shift+R (Windows)
   - Or clear browser cache completely

**Fix:**
- Clear all caches
- Test in incognito/private mode
- Disable caching temporarily to test

---

## Step 3: Fix Common Issues

### Issue 1: Scripts Not Loading

**Symptoms:**
- Console shows 404 errors
- Network tab shows failed requests
- Modal doesn't open

**Diagnosis:**
```bash
# Check if files exist
ls -la local-site/app/public/wp-content/plugins/tpb-quickview-modal/assets/js/

# Check file permissions
chmod 644 local-site/app/public/wp-content/plugins/tpb-quickview-modal/assets/js/*.js

# Check WordPress can access files
# Visit: /wp-content/plugins/tpb-quickview-modal/assets/js/modal-state.js
# Should see JavaScript code, not 404
```

**Fix:**
- Verify file paths in `tpb-quickview-modal.php`
- Check `.htaccess` rules (if Apache)
- Check file permissions
- Verify plugin is activated

---

### Issue 2: jQuery Conflicts

**Symptoms:**
- Console: "jQuery is not defined"
- Modal scripts fail to load
- Elementor breaks

**Diagnosis:**
```javascript
// In browser console:
console.log(typeof jQuery); // Should be "function"
console.log(jQuery.fn.jquery); // Should show version
```

**Fix:**
- Ensure jQuery is enqueued before modal scripts
- Check for theme/plugin that removes jQuery
- Add jQuery as dependency in `wp_enqueue_script()`

---

### Issue 3: Z-Index Conflicts

**Symptoms:**
- Cart not visible
- Modal behind other elements
- Elements overlapping incorrectly

**Diagnosis:**
```css
/* In browser DevTools → Elements → Computed tab */
/* Check z-index values of: */
- .tpb-qv-overlay
- .elementor-menu-cart__toggle_button
- .elementor-3995
```

**Fix:**
- Check for theme CSS overriding z-index
- Check for Elementor CSS conflicts
- Add more specific selectors
- Use `!important` if necessary

---

### Issue 4: Elementor Cart Conflicts

**Symptoms:**
- Cart button doesn't work
- Cart drawer doesn't open
- Cart appears in wrong position

**Diagnosis:**
- Check Elementor → Settings → Advanced
- Check Elementor menu cart widget settings
- Check for custom Elementor CSS

**Fix:**
- Disable Elementor DOM optimization
- Regenerate Elementor CSS
- Check Elementor menu cart widget configuration
- Verify Elementor Pro version (if using)

---

### Issue 5: WordPress Hooks Not Firing

**Symptoms:**
- Scripts not enqueued
- Templates not loading
- Functions not executing

**Diagnosis:**
```php
// Add to functions.php temporarily:
add_action('wp_enqueue_scripts', function() {
    error_log('wp_enqueue_scripts fired');
}, 999);
```

**Fix:**
- Check plugin is activated
- Check for `exit` or `die` in plugin
- Check for PHP errors preventing execution
- Verify WordPress hooks are firing

---

## Step 4: Systematic Debugging Process

### Phase 1: Isolate the Problem

1. **Test in Clean Environment**
   - New WordPress install
   - Only Elementor + TPB Modal
   - Default theme
   - No other plugins

2. **Does it work?**
   - ✅ Yes → Problem is plugin/theme conflict
   - ❌ No → Problem is in modal code or Elementor

---

### Phase 2: Identify Conflict Source

**If it works in clean environment:**

1. **Add plugins one at a time**
   - Test after each addition
   - When it breaks, you've found the conflict

2. **Switch themes**
   - Test with different themes
   - When it breaks, you've found the conflict

3. **Check Elementor settings**
   - Disable features one at a time
   - When it works, you've found the conflict

---

### Phase 3: Fix the Conflict

**Options:**

1. **Configure Conflicting Plugin/Theme**
   - Exclude modal scripts from optimization
   - Exclude modal CSS from minification
   - Adjust conflicting settings

2. **Modify Modal Code**
   - Add compatibility layer
   - Use different hooks
   - Adjust timing/priority

3. **Find Alternative**
   - Replace conflicting plugin
   - Use different theme
   - Use different Elementor settings

---

## Step 5: Specific Fixes for Common Scenarios

### Scenario A: Caching Plugin Conflict

**Problem:** Caching plugin minifies/combines scripts, breaking modal

**Fix:**
```php
// In caching plugin settings, exclude:
/wp-content/plugins/tpb-quickview-modal/assets/js/
/wp-content/plugins/tpb-quickview-modal/assets/css/
```

**Or in caching plugin:**
- Disable JavaScript minification
- Disable CSS minification
- Exclude modal scripts from optimization

---

### Scenario B: Security Plugin Blocking Scripts

**Problem:** Security plugin blocks script execution

**Fix:**
- Add modal plugin to whitelist
- Allow script execution for modal files
- Disable security plugin temporarily to test

---

### Scenario C: Elementor DOM Optimization

**Problem:** Elementor's DOM optimization breaks modal scripts

**Fix:**
1. Elementor → Settings → Advanced
2. Disable "DOM Optimization"
3. Regenerate CSS
4. Clear cache
5. Test

---

### Scenario D: PHP Version/Extensions

**Problem:** Missing PHP extensions or wrong version

**Check:**
```php
// Create test file: phpinfo.php
<?php phpinfo(); ?>
// Visit: /phpinfo.php
// Check for required extensions
```

**Required:**
- PHP 7.4+ or 8.0+
- JSON extension
- cURL extension (for API calls)

**Fix:**
- Update PHP version
- Install missing extensions
- Contact hosting provider

---

### Scenario E: File Permissions

**Problem:** WordPress can't read plugin files

**Check:**
```bash
ls -la local-site/app/public/wp-content/plugins/tpb-quickview-modal/
```

**Fix:**
```bash
# Set correct permissions
find local-site/app/public/wp-content/plugins/tpb-quickview-modal -type f -exec chmod 644 {} \;
find local-site/app/public/wp-content/plugins/tpb-quickview-modal -type d -exec chmod 755 {} \;
```

---

## Step 6: When to Give Up on Environment Fix

### Red Flags (Consider From Scratch):

1. **Multiple Conflicting Plugins**
   - 3+ plugins causing issues
   - Can't disable them (required for site)
   - Fixing conflicts takes >1 week

2. **Theme is Incompatible**
   - Theme has fundamental conflicts
   - Can't switch themes
   - Theme developer won't help

3. **Elementor Version Issues**
   - Elementor version too old/new
   - Can't update Elementor
   - Elementor Pro conflicts

4. **Server Limitations**
   - Hosting provider restrictions
   - Can't modify PHP settings
   - Can't install extensions

5. **Time Investment**
   - Debugging takes >1 week
   - Multiple issues compound
   - Unclear root cause

**At This Point:**
- Start from scratch with minimal integration
- Reuse all stepper logic (2,910 lines)
- Reuse all templates (197 lines)
- Reuse all CSS (1,727 lines)
- Build simple integration (~200 lines)
- Add features incrementally

---

## Debugging Checklist

### Before Starting Clean Restart:
- [ ] Enable WordPress debugging
- [ ] Check browser console for errors
- [ ] Check network tab for failed loads
- [ ] Check WordPress error logs
- [ ] Document all errors found

### After Clean Restart Fails:
- [ ] Test in clean environment (no other plugins)
- [ ] Test with default theme
- [ ] Check Elementor settings
- [ ] Check PHP version and extensions
- [ ] Check file permissions
- [ ] Clear all caches
- [ ] Test in incognito mode

### If Still Broken:
- [ ] Identify specific error message
- [ ] Research error online
- [ ] Check plugin/theme compatibility
- [ ] Contact plugin/theme developers
- [ ] Consider from-scratch approach

---

## Tools for Debugging

### Browser Tools:
- **Chrome DevTools** (F12)
  - Console tab (JavaScript errors)
  - Network tab (file loading)
  - Elements tab (DOM inspection)
  - Sources tab (debugging)

- **Firefox Developer Tools** (F12)
  - Similar to Chrome

### WordPress Tools:
- **Query Monitor Plugin**
  - Shows all enqueued scripts
  - Shows hook execution
  - Shows database queries

- **Debug Bar Plugin**
  - Shows PHP errors
  - Shows hook execution
  - Shows performance data

### Server Tools:
- **PHP Error Logs**
- **Apache/Nginx Error Logs**
- **Server Access Logs**

---

## Expected Timeline

### If Simple Issue (Caching, Permissions):
- **Time:** 1-2 hours
- **Steps:** Clear cache, fix permissions, test

### If Plugin Conflict:
- **Time:** 1-2 days
- **Steps:** Identify conflict, configure or replace plugin

### If Theme Conflict:
- **Time:** 2-3 days
- **Steps:** Identify conflict, modify theme or switch

### If Elementor Issue:
- **Time:** 1-3 days
- **Steps:** Adjust Elementor settings, update, or work around

### If Multiple Issues:
- **Time:** 1+ weeks
- **Steps:** Fix one at a time, or consider from-scratch

---

## Decision Point

**After 1 week of debugging:**
- If progress: Continue debugging
- If stuck: Start from scratch
- If unclear: Get second opinion or hire developer

---

**Created:** November 5, 2025  
**Status:** Ready for use when needed

