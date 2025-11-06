# Option 1: Clean Restart - Execution Plan
**Date:** November 5, 2025  
**Scheduled:** Tomorrow  
**Status:** Ready to Execute

---

## Pre-Execution Checklist (Do Tonight)

### Elementor Settings (Do First)
- [ ] Document current Elementor settings (screenshots)
- [ ] Try quick fixes:
  - [ ] Disable DOM Optimization
  - [ ] Disable Improved CSS Loading
  - [ ] Change CSS Print Method to "Internal Embedding"
  - [ ] Regenerate CSS
  - [ ] Clear all caches
  - [ ] Test modal
- [ ] Document results (worked? still broken?)

### Backup Current State
- [ ] Current broken version already archived? (Check `archive/current-broken-version-*`)
- [ ] If not, we'll archive before restore

### Prepare Archive
- [ ] Verify archive exists: `archive/from-commit-5c0d229-final/`
- [ ] Verify archive contains:
  - [ ] `plugins-tpb-quickview-modal/` folder
  - [ ] `mu-plugins-tpb-quickview/` folder

---

## Execution Steps (Tomorrow)

### Automated Option: Use Restore Script (Recommended)

**Quick Start:**
```bash
cd /Users/jeff/Projects/tpb-store-modal
./restore-from-archive.sh
```

**What the Script Does:**
1. ✅ Verifies archive exists (`archive/from-commit-5c0d229-final/`)
2. ✅ Archives current broken version (timestamped backup)
3. ✅ Removes current plugin and mu-plugin
4. ✅ Restores archive version
5. ✅ Verifies restoration (checks key files exist)

**Script Output:**
- Color-coded status messages (green = success, yellow = warning, red = error)
- Step-by-step progress indicators
- Verification results
- Summary with next steps

**If Script Fails:**
- Check error messages (red text)
- Verify archive exists: `ls -la archive/from-commit-5c0d229-final/`
- Verify paths are correct in script
- Fall back to manual steps below

**After Script Completes:**
- Proceed to **Step 3: Set Elementor Settings** (script handles Steps 1-2)

---

### Manual Option: Step-by-Step (If Script Not Used)

### Step 1: Archive Current Broken Version (5 min)

**If not already archived:**

```bash
# Create timestamped backup
cd /Users/jeff/Projects/tpb-store-modal
mkdir -p archive/current-broken-version-$(date +%Y%m%d-%H%M%S)

# Copy current plugin
cp -r local-site/app/public/wp-content/plugins/tpb-quickview-modal \
      archive/current-broken-version-$(date +%Y%m%d-%H%M%S)/plugins-tpb-quickview-modal

# Copy current mu-plugin
cp -r local-site/app/public/wp-content/mu-plugins/tpb-quickview \
      archive/current-broken-version-$(date +%Y%m%d-%H%M%S)/mu-plugins-tpb-quickview
```

**Verify:**
- [ ] Archive folder created
- [ ] Files copied successfully

---

### Step 2: Restore Archive Version (10 min)

**Restore Plugin:**
```bash
# Remove current plugin
rm -rf local-site/app/public/wp-content/plugins/tpb-quickview-modal

# Restore from archive
cp -r archive/from-commit-5c0d229-final/plugins-tpb-quickview-modal \
      local-site/app/public/wp-content/plugins/tpb-quickview-modal
```

**Restore MU-Plugin:**
```bash
# Remove current mu-plugin
rm -rf local-site/app/public/wp-content/mu-plugins/tpb-quickview

# Restore from archive
cp -r archive/from-commit-5c0d229-final/mu-plugins-tpb-quickview \
      local-site/app/public/wp-content/mu-plugins/tpb-quickview
```

**Verify:**
- [ ] Plugin restored
- [ ] MU-plugin restored
- [ ] Files exist in correct locations

---

### Step 3: Set Elementor Settings (5 min)

**Go to WordPress Admin:**

1. **Elementor → Settings → Advanced**
   - DOM Optimization: **OFF**
   - Improved CSS Loading: **OFF**
   - CSS Print Method: **Internal Embedding**
   - Save

2. **Elementor → Tools → Regenerate CSS**
   - Click "Regenerate CSS & Data"
   - Wait for completion

**Verify:**
- [ ] Elementor settings saved
- [ ] CSS regenerated

---

### Step 4: Clear All Caches (5 min)

**Clear WordPress Cache:**
- Use caching plugin (if installed)
- Or clear object cache manually

**Clear Browser Cache:**
- Hard refresh: Cmd+Shift+R (Mac) or Ctrl+Shift+R (Windows)
- Or clear browser cache completely

**Clear Server Cache:**
- Clear OPcache (if applicable)
- Restart PHP-FPM or Apache (if possible)

**Verify:**
- [ ] WordPress cache cleared
- [ ] Browser cache cleared
- [ ] Server cache cleared (if applicable)

---

### Step 5: Test Legacy Version (15 min)

**Test Each Modal Type:**

1. **Flower Station Modal**
   - [ ] Click trigger button
   - [ ] Modal opens
   - [ ] Stepper loads
   - [ ] Steps are interactive
   - [ ] Products display
   - [ ] Cart button visible
   - [ ] Cart button clickable

2. **Category Station Modal**
   - [ ] Click trigger button
   - [ ] Modal opens
   - [ ] Stepper loads
   - [ ] Steps are interactive
   - [ ] Products display
   - [ ] Cart button visible
   - [ ] Cart button clickable

3. **QuickCheckout Modal**
   - [ ] Click trigger button
   - [ ] Modal opens
   - [ ] Stepper loads
   - [ ] Steps are interactive
   - [ ] Products display
   - [ ] Cart button visible
   - [ ] Cart button clickable

**Document Results:**
- [ ] What works?
- [ ] What doesn't work?
- [ ] Any console errors?
- [ ] Any visual issues?

---

### Step 6: Add Minimal Cart Fix (10 min)

**If Legacy Version Works But Cart Issues:**

**File to Edit:** `local-site/app/public/wp-content/plugins/tpb-quickview-modal/assets/css/modal.css`

**Add Minimal Cart Z-Index Rules:**
```css
/* Minimal cart visibility fix */
body.tpb-modal-open .elementor-menu-cart__toggle_button,
body.tpb-modal-open .elementor-menu-cart__container {
  position: relative;
  z-index: 2147483647 !important;
}

/* Ensure cart drawer stays visible */
body.tpb-modal-open .elementor-menu-cart__container,
body.tpb-modal-open .elementor-menu-cart__main {
  opacity: 1 !important;
  visibility: visible !important;
}
```

**Verify:**
- [ ] CSS file updated
- [ ] Cache cleared
- [ ] Cart visible above modal
- [ ] Cart button clickable

---

### Step 7: Final Testing (15 min)

**Test All Functionality:**

- [ ] Modal opens/closes
- [ ] Stepper works for all types
- [ ] Products display correctly
- [ ] Cart visible above modal
- [ ] Cart button clickable
- [ ] Cart drawer opens/closes
- [ ] No console errors
- [ ] No visual issues
- [ ] Works on different browsers
- [ ] Works on different devices

**Document:**
- [ ] All tests passed?
- [ ] Any remaining issues?
- [ ] What needs fixing?

---

## Success Criteria

**Option 1 is Successful If:**
- ✅ Modal opens and closes
- ✅ Stepper loads and works
- ✅ Products display
- ✅ Cart visible above modal
- ✅ Cart button clickable
- ✅ No major console errors
- ✅ Visual design correct

**Option 1 Failed If:**
- ❌ Modal doesn't open
- ❌ Stepper doesn't load
- ❌ Multiple console errors
- ❌ Visual design broken
- ❌ Cart still not visible

---

## If Option 1 Succeeds

**Next Steps:**
1. Document what worked
2. Add any remaining polish
3. Test thoroughly
4. Deploy to production

**Time Estimate:** 1-2 hours total

---

## If Option 1 Fails

**Next Steps:**
1. Check browser console for errors
2. Check WordPress error logs
3. Follow `WORDPRESS_ELEMENTOR_DEBUGGING_GUIDE.md`
4. Or proceed to from-scratch approach

**Time Estimate:** 1-3 days debugging, or 2-3 weeks from-scratch

---

## Quick Reference Commands

### Automated (Recommended)
**Run Restore Script:**
```bash
cd /Users/jeff/Projects/tpb-store-modal
./restore-from-archive.sh
```

**Script Location:** `restore-from-archive.sh`  
**Script Purpose:** Automates Steps 1-2 (archive current, restore archive)  
**Script Requirements:** 
- Archive must exist at `archive/from-commit-5c0d229-final/`
- Script must be executable (`chmod +x restore-from-archive.sh`)

### Manual Commands (If Script Not Used)

**Archive Current:**
```bash
cd /Users/jeff/Projects/tpb-store-modal
mkdir -p archive/current-broken-version-$(date +%Y%m%d-%H%M%S)
cp -r local-site/app/public/wp-content/plugins/tpb-quickview-modal \
      archive/current-broken-version-$(date +%Y%m%d-%H%M%S)/plugins-tpb-quickview-modal
cp -r local-site/app/public/wp-content/mu-plugins/tpb-quickview \
      archive/current-broken-version-$(date +%Y%m%d-%H%M%S)/mu-plugins-tpb-quickview
```

**Restore Archive:**
```bash
cd /Users/jeff/Projects/tpb-store-modal
rm -rf local-site/app/public/wp-content/plugins/tpb-quickview-modal
rm -rf local-site/app/public/wp-content/mu-plugins/tpb-quickview
cp -r archive/from-commit-5c0d229-final/plugins-tpb-quickview-modal \
      local-site/app/public/wp-content/plugins/tpb-quickview-modal
cp -r archive/from-commit-5c0d229-final/mu-plugins-tpb-quickview \
      local-site/app/public/wp-content/mu-plugins/tpb-quickview
```

**Verify Files:**
```bash
ls -la local-site/app/public/wp-content/plugins/tpb-quickview-modal/
ls -la local-site/app/public/wp-content/mu-plugins/tpb-quickview/
```

---

## Time Estimate

| Step | Time | Total |
|------|------|-------|
| Archive Current | 5 min | 5 min |
| Restore Archive | 10 min | 15 min |
| Set Elementor | 5 min | 20 min |
| Clear Caches | 5 min | 25 min |
| Test Legacy | 15 min | 40 min |
| Add Cart Fix | 10 min | 50 min |
| Final Testing | 15 min | 65 min |

**Total:** ~1 hour

---

## Notes

- **Automated Script:** Use `./restore-from-archive.sh` to automate Steps 1-2 (recommended)
- **Elementor Settings:** Most critical step - must be done correctly
- **Cache Clearing:** Essential - old cached files will break things
- **Testing:** Thorough testing prevents issues later
- **Documentation:** Document everything for future reference

## Script Details for Agents

**Script Name:** `restore-from-archive.sh`  
**Location:** Project root (`/Users/jeff/Projects/tpb-store-modal/`)  
**Executable:** Yes (chmod +x already applied)

**Script Behavior:**
- **Exit on Error:** Script uses `set -e` - exits immediately if any command fails
- **Color Output:** Uses ANSI colors for readability (green=success, yellow=warning, red=error)
- **Verification:** Automatically verifies archive exists and restoration succeeded
- **Backup:** Creates timestamped backup before removing current version
- **Safety:** Checks for files before removing/restoring

**Script Requirements:**
- Archive directory: `archive/from-commit-5c0d229-final/`
- Archive must contain: `plugins-tpb-quickview-modal/` and `mu-plugins-tpb-quickview/`
- Project root: `/Users/jeff/Projects/tpb-store-modal`

**Script Output:**
- Step-by-step progress with color-coded status
- Verification results for each step
- Summary with next steps after completion
- Error messages if anything fails

**If Script Fails:**
- Check error message (red text in output)
- Verify archive exists: `ls -la archive/from-commit-5c0d229-final/`
- Check file permissions: `ls -la restore-from-archive.sh`
- Verify paths in script match actual project structure
- Fall back to manual commands in "Quick Reference Commands" section

---

**Created:** November 5, 2025  
**Status:** Ready to execute tomorrow

