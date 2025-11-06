# WordPress/Elementor Configuration Issues - Resolution Process
**Date:** November 5, 2025  
**Scenario:** Clean restart fails due to environment/configuration, not code

---

## Quick Answer: What Do We Do?

### If Clean Restart Fails Due to Configuration:

**Step 1: Diagnose (1-2 hours)**
- Check browser console for errors
- Check WordPress error logs
- Identify specific failure point

**Step 2: Fix (1-5 days depending on issue)**
- Fix identified configuration issue
- Test after each fix
- Document what worked

**Step 3: If Fixing Takes >1 Week**
- Consider from-scratch approach
- Reuse 94% of code (steppers, templates, CSS)
- Build minimal integration layer

---

## Modal System Dependencies

### WordPress Dependencies

**Required:**
- WordPress 5.0+ (hooks: `wp_enqueue_scripts`, `wp_footer`, `init`)
- PHP 7.4+ (for modern JavaScript features)
- jQuery (WordPress bundled version)

**WordPress Hooks Used:**
```php
wp_enqueue_scripts  // Load CSS/JS files
wp_footer           // Inject modal HTML
init                 // CPB integration
wp_ajax_*           // AJAX endpoints
body_class          // Add body classes
```

**Potential Conflicts:**
- Other plugins using same hooks
- Theme modifying hooks
- Caching plugins interfering

---

### Elementor Dependencies

**Required:**
- Elementor plugin active
- Elementor menu cart widget (for cart functionality)

**Elementor Integration:**
- Uses Elementor's cart classes (`.elementor-menu-cart__*`)
- Lifts Elementor sections above modal (`.elementor-3995`)
- Works with Elementor's z-index system

**Potential Conflicts:**
- Elementor DOM optimization
- Elementor CSS optimization
- Elementor version incompatibility
- Elementor Pro features interfering

---

### JavaScript Dependencies

**Required:**
- jQuery (WordPress bundled)
- Modern browser (ES5+ support)

**jQuery Usage:**
- All handlers use jQuery for DOM manipulation
- Steppers use vanilla JS (no jQuery dependency)
- Modal state uses jQuery for event handling

**Potential Conflicts:**
- Theme removes jQuery
- Plugin conflicts with jQuery version
- jQuery loaded in wrong order

---

## Common Configuration Issues & Fixes

### Issue 1: Scripts Not Loading

**Symptoms:**
- Console: "Failed to load resource: 404"
- Network tab: Red failed requests
- Modal doesn't open

**Diagnosis:**
```bash
# Check files exist
ls -la wp-content/plugins/tpb-quickview-modal/assets/js/

# Check file permissions
chmod 644 wp-content/plugins/tpb-quickview-modal/assets/js/*.js

# Test direct access
# Visit: /wp-content/plugins/tpb-quickview-modal/assets/js/modal-state.js
# Should see JavaScript, not 404
```

**Fixes:**
1. **File Permissions:**
   ```bash
   chmod 644 wp-content/plugins/tpb-quickview-modal/assets/js/*.js
   chmod 755 wp-content/plugins/tpb-quickview-modal/assets/js/
   ```

2. **Plugin Not Activated:**
   - WordPress Admin → Plugins
   - Verify "TPB QuickView Modal" is active

3. **Wrong File Paths:**
   - Check `tpb-quickview-modal.php` enqueue paths
   - Verify `TPB_QV_PLUGIN_URL` constant

4. **.htaccess Blocking:**
   - Check `.htaccess` for blocking rules
   - Temporarily rename `.htaccess` to test

---

### Issue 2: jQuery Not Available

**Symptoms:**
- Console: "jQuery is not defined"
- All modal scripts fail
- Elementor may also break

**Diagnosis:**
```javascript
// Browser console:
console.log(typeof jQuery); // Should be "function"
```

**Fixes:**
1. **Check jQuery Dependency:**
   ```php
   // In tpb-quickview-modal.php, ensure:
   wp_enqueue_script('tpb-modal-state', ..., ['jquery'], ...);
   ```

2. **Theme Removing jQuery:**
   - Check theme's `functions.php`
   - Look for `wp_deregister_script('jquery')`
   - Remove or comment out

3. **Plugin Conflict:**
   - Deactivate plugins one at a time
   - Test after each deactivation
   - When jQuery works, you've found the conflict

---

### Issue 3: Elementor Cart Conflicts

**Symptoms:**
- Cart button not visible
- Cart drawer doesn't open
- Cart appears in wrong position

**Diagnosis:**
1. Elementor → Settings → Advanced
2. Check "DOM Optimization" setting
3. Check "Improved CSS Loading" setting

**Fixes:**
1. **Disable Elementor Optimization:**
   - Elementor → Settings → Advanced
   - Disable "DOM Optimization"
   - Regenerate CSS
   - Clear cache

2. **Regenerate Elementor CSS:**
   - Elementor → Tools → Regenerate CSS & Data
   - Wait for completion
   - Clear all caches

3. **Check Elementor Menu Cart Widget:**
   - Edit page in Elementor
   - Find menu cart widget
   - Verify settings
   - Save and regenerate CSS

---

### Issue 4: Caching Issues

**Symptoms:**
- Changes don't appear
- Old code still running
- Inconsistent behavior

**Fixes:**
1. **WordPress Cache:**
   - Clear object cache
   - Clear transients
   - Use plugin like "WP-Optimize"

2. **Browser Cache:**
   - Hard refresh: Cmd+Shift+R (Mac) or Ctrl+Shift+R (Windows)
   - Or clear browser cache completely
   - Or test in incognito mode

3. **Server Cache:**
   - Clear OPcache (PHP)
   - Restart PHP-FPM or Apache
   - Clear CDN cache (if using)

4. **Plugin Cache:**
   - WP Super Cache: Clear cache
   - W3 Total Cache: Clear all caches
   - WP Rocket: Clear cache

---

### Issue 5: PHP/Server Issues

**Symptoms:**
- PHP errors in logs
- Scripts fail silently
- 500 errors

**Diagnosis:**
```php
// Enable debugging in wp-config.php:
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

// Check: wp-content/debug.log
```

**Fixes:**
1. **PHP Version:**
   - Minimum: PHP 7.4
   - Recommended: PHP 8.0+
   - Check: Tools → Site Health → Info

2. **PHP Memory:**
   ```php
   // wp-config.php:
   define('WP_MEMORY_LIMIT', '256M');
   ```

3. **PHP Extensions:**
   - Required: json, curl
   - Check: phpinfo() or Site Health

---

## Systematic Debugging Process

### Phase 1: Isolate (1-2 hours)

**Test in Minimal Environment:**
1. Deactivate ALL plugins except:
   - Elementor
   - WooCommerce (if using)
   - TPB QuickView Modal

2. Switch to default WordPress theme

3. Test modal - does it work?
   - ✅ **Yes** → Problem is plugin/theme conflict
   - ❌ **No** → Problem is in modal code or Elementor

---

### Phase 2: Identify Conflict (1-3 days)

**If it works in minimal environment:**

1. **Add plugins back one at a time**
   - Test after each addition
   - When it breaks, you've found the conflict

2. **Switch themes**
   - Test with different themes
   - When it breaks, you've found the conflict

3. **Check Elementor settings**
   - Disable features one at a time
   - When it works, you've found the conflict

---

### Phase 3: Fix Conflict (1-5 days)

**Options:**

1. **Configure Conflicting Plugin/Theme**
   - Exclude modal scripts from optimization
   - Adjust conflicting settings
   - Find workaround

2. **Modify Modal Code**
   - Add compatibility layer
   - Use different hooks
   - Adjust timing

3. **Find Alternative**
   - Replace conflicting plugin
   - Use different theme
   - Use different Elementor settings

---

## When to Give Up on Configuration Fix

### Red Flags (Consider From Scratch):

1. **Multiple Conflicts**
   - 3+ plugins causing issues
   - Can't disable them (required)
   - Fixing takes >1 week

2. **Theme Incompatible**
   - Theme has fundamental conflicts
   - Can't switch themes
   - Theme developer won't help

3. **Elementor Issues**
   - Elementor version problems
   - Can't update Elementor
   - Elementor Pro conflicts

4. **Server Limitations**
   - Hosting provider restrictions
   - Can't modify PHP
   - Can't install extensions

5. **Time Investment**
   - Debugging >1 week
   - Multiple compounding issues
   - Unclear root cause

**At This Point:**
- Start from scratch
- Reuse 94% of code (steppers, templates, CSS)
- Build minimal integration (~200 lines)
- Add features incrementally

---

## Expected Timeline

| Issue Type | Time to Fix | Complexity |
|------------|-------------|------------|
| **Caching** | 1-2 hours | Low |
| **File Permissions** | 1 hour | Low |
| **jQuery Conflict** | 2-4 hours | Medium |
| **Plugin Conflict** | 1-2 days | Medium |
| **Theme Conflict** | 2-3 days | Medium-High |
| **Elementor Issue** | 1-3 days | Medium-High |
| **Multiple Issues** | 1+ weeks | High |

---

## Decision Framework

### After Clean Restart Fails:

```
1. Check browser console (5 min)
   ├─ Errors found? → Research and fix
   └─ No errors? → Continue

2. Check WordPress logs (10 min)
   ├─ Errors found? → Research and fix
   └─ No errors? → Continue

3. Test in minimal environment (1 hour)
   ├─ Works? → Plugin/theme conflict
   │   └─ Add back one at a time, identify conflict
   └─ Doesn't work? → Code or Elementor issue
       └─ Check Elementor settings

4. Fix identified issue (1-5 days)
   ├─ Fixed? → Test thoroughly
   └─ Can't fix? → Consider from-scratch

5. If fixing takes >1 week
   └─ Start from scratch
       └─ Reuse 94% of code
       └─ Build minimal integration
```

---

## What We'd Do Step-by-Step

### Day 1: Initial Diagnosis (2-4 hours)

1. **Enable WordPress Debugging**
   ```php
   // wp-config.php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   ```

2. **Check Browser Console**
   - Open DevTools (F12)
   - Open modal
   - Document all errors

3. **Check Network Tab**
   - Verify all files loading
   - Check for 404s or blocked files

4. **Check WordPress Logs**
   - `wp-content/debug.log`
   - Document all errors

---

### Day 2-3: Isolate Problem (4-8 hours)

1. **Test in Minimal Environment**
   - Deactivate all plugins except essentials
   - Switch to default theme
   - Test modal

2. **If Works:**
   - Add plugins back one at a time
   - Test after each
   - Identify conflict

3. **If Doesn't Work:**
   - Check Elementor settings
   - Check PHP version
   - Check file permissions

---

### Day 4-5: Fix Issue (8-16 hours)

1. **For Plugin Conflict:**
   - Configure plugin to exclude modal
   - Or find alternative plugin
   - Or modify modal for compatibility

2. **For Theme Conflict:**
   - Modify theme code
   - Or switch themes
   - Or contact theme developer

3. **For Elementor Issue:**
   - Adjust Elementor settings
   - Update Elementor
   - Or work around issue

---

### Day 6-7: Test & Verify (4-8 hours)

1. **Test All Functionality**
   - Modal opens/closes
   - Stepper works
   - Cart visible
   - All interactions work

2. **Test on Different Browsers**
   - Chrome
   - Firefox
   - Safari

3. **Test on Different Devices**
   - Desktop
   - Tablet
   - Mobile

---

## If Configuration Fixing Fails

### After 1 Week of Debugging:

**If Still Broken:**
- Start from scratch
- Reuse all stepper logic (2,910 lines)
- Reuse all templates (197 lines)
- Reuse all CSS (1,727 lines)
- Build minimal integration (~200 lines)
- Total: 2-3 weeks for complete rebuild

**You're NOT losing work:**
- 94% of code is reusable
- Only integration layer needs rebuild
- Stepper logic is solid and proven

---

## Summary

**If Clean Restart Fails:**

1. **Diagnose** (1-2 hours)
   - Check console, logs, network

2. **Isolate** (1-3 days)
   - Test in minimal environment
   - Identify conflict source

3. **Fix** (1-5 days)
   - Fix identified issue
   - Test thoroughly

4. **If >1 Week:**
   - Start from scratch
   - Reuse 94% of code
   - Build minimal integration

**Most Common Issues:**
- Caching (1-2 hours to fix)
- Plugin conflicts (1-2 days to fix)
- Elementor settings (1-3 days to fix)

**Most Likely Outcome:**
- Issue identified and fixed in 1-5 days
- Or start from scratch in 2-3 weeks

---

**Created:** November 5, 2025  
**Status:** Ready for use

