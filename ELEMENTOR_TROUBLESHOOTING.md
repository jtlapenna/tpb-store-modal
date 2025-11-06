# Elementor Settings Troubleshooting Guide
**Date:** November 5, 2025  
**Issue:** Elementor configuration likely causing modal problems

---

## Quick Elementor Checks (Do Tonight)

### 1. Elementor → Settings → Advanced

**Check These Settings:**

#### DOM Optimization
- **Location:** Elementor → Settings → Advanced → DOM Optimization
- **Problem:** Can break JavaScript execution
- **Fix:** **Disable** this setting
- **Action:** Toggle OFF, save, regenerate CSS

#### Improved CSS Loading
- **Location:** Elementor → Settings → Advanced → Improved CSS Loading
- **Problem:** Can prevent CSS from loading properly
- **Fix:** **Disable** this setting
- **Action:** Toggle OFF, save, regenerate CSS

#### CSS Print Method
- **Location:** Elementor → Settings → Advanced → CSS Print Method
- **Problem:** External file method can cause loading issues
- **Fix:** Try "Internal Embedding" instead
- **Action:** Change to "Internal Embedding", save

#### Inline Font Icons
- **Location:** Elementor → Settings → Advanced → Inline Font Icons
- **Problem:** Usually fine, but can cause conflicts
- **Fix:** Leave as default (usually OFF)
- **Action:** Verify setting, don't change unless needed

---

### 2. Elementor → Tools → Regenerate CSS

**Critical Step After Any Setting Change:**

1. Go to: **Elementor → Tools**
2. Click: **"Regenerate CSS & Data"**
3. Wait for completion (can take 1-5 minutes)
4. Clear all caches
5. Test modal

**Why:** Elementor caches CSS, must regenerate after settings changes

---

### 3. Elementor Menu Cart Widget Settings

**If Using Elementor Menu Cart:**

1. Edit page in Elementor
2. Find menu cart widget
3. Check settings:
   - Cart icon size
   - Cart position
   - Cart drawer settings
4. Save page
5. Regenerate CSS

**Common Issues:**
- Cart widget z-index conflicts
- Cart drawer positioning
- Cart button visibility

---

### 4. Elementor Version Compatibility

**Check Version:**

1. Go to: **WordPress Admin → Plugins**
2. Find: **Elementor**
3. Check version number
4. **Minimum:** Elementor 3.0+
5. **Recommended:** Latest stable version

**If Outdated:**
- Update Elementor
- Test after update
- May need to regenerate CSS

**If Too New:**
- Check Elementor changelog
- Look for breaking changes
- May need to wait for modal compatibility update

---

### 5. Elementor Pro (If Using)

**Check Pro Version:**

1. Go to: **WordPress Admin → Plugins**
2. Find: **Elementor Pro**
3. Check version number
4. Ensure compatible with Elementor core version

**Common Pro Issues:**
- Pro features interfering with modal
- Pro widgets conflicting
- Pro templates causing conflicts

**Fix:**
- Update Elementor Pro
- Or temporarily deactivate to test

---

## Elementor Settings Checklist (Do Before Option 1)

**Tonight/Tomorrow Morning:**

- [ ] **DOM Optimization:** Disabled
- [ ] **Improved CSS Loading:** Disabled
- [ ] **CSS Print Method:** Set to "Internal Embedding"
- [ ] **Regenerate CSS:** Done after settings changes
- [ ] **Elementor Version:** Checked and updated if needed
- [ ] **Elementor Pro:** Checked and updated if needed
- [ ] **Menu Cart Widget:** Settings verified
- [ ] **All Caches Cleared:** WordPress, browser, server

---

## Elementor-Specific Issues We've Seen

### Issue 1: DOM Optimization Breaking Scripts

**Symptoms:**
- Modal scripts don't execute
- Console errors about undefined variables
- Stepper doesn't load

**Fix:**
1. Elementor → Settings → Advanced
2. Disable "DOM Optimization"
3. Regenerate CSS
4. Clear cache
5. Test

---

### Issue 2: CSS Loading Conflicts

**Symptoms:**
- Modal styles not applying
- Z-index not working
- Cart not visible

**Fix:**
1. Elementor → Settings → Advanced
2. Disable "Improved CSS Loading"
3. Change "CSS Print Method" to "Internal Embedding"
4. Regenerate CSS
5. Clear cache
6. Test

---

### Issue 3: Elementor Z-Index Conflicts

**Symptoms:**
- Cart behind modal
- Modal behind other elements
- Overlapping issues

**Fix:**
1. Check Elementor → Settings → Style
2. Look for global z-index rules
3. Regenerate CSS
4. May need to adjust modal CSS z-index values

---

### Issue 4: Elementor Menu Cart Widget

**Symptoms:**
- Cart button not visible
- Cart drawer doesn't open
- Cart in wrong position

**Fix:**
1. Edit page in Elementor
2. Find menu cart widget
3. Check widget settings
4. Save page
5. Regenerate CSS
6. Clear cache

---

## Quick Test After Elementor Changes

**Test Steps:**

1. **Open Modal**
   - Click trigger button
   - Modal should open smoothly

2. **Check Stepper**
   - Stepper should load
   - Steps should be interactive
   - Products should display

3. **Check Cart**
   - Cart button visible above modal
   - Cart button clickable
   - Cart drawer opens/closes

4. **Check Styling**
   - Modal looks correct
   - No layout issues
   - No overlapping elements

**If Any Fail:**
- Check browser console for errors
- Check WordPress error logs
- Document specific failure
- Try next Elementor setting change

---

## Elementor Settings to Try (In Order)

### Try 1: Disable DOM Optimization
**Time:** 5 minutes  
**Risk:** Low  
**Impact:** High

### Try 2: Disable Improved CSS Loading
**Time:** 5 minutes  
**Risk:** Low  
**Impact:** Medium

### Try 3: Change CSS Print Method
**Time:** 5 minutes  
**Risk:** Low  
**Impact:** Medium

### Try 4: Regenerate CSS
**Time:** 1-5 minutes  
**Risk:** None  
**Impact:** High (required after changes)

### Try 5: Update Elementor
**Time:** 10-15 minutes  
**Risk:** Medium  
**Impact:** High (if outdated)

---

## Before Starting Option 1 Tomorrow

**Do These Tonight:**

1. **Document Current Elementor Settings**
   - Take screenshots
   - Write down current values
   - So you can restore if needed

2. **Try Quick Fixes**
   - Disable DOM Optimization
   - Disable Improved CSS Loading
   - Regenerate CSS
   - Test modal

3. **If Still Broken:**
   - Document what you tried
   - Note any errors in console
   - Ready for Option 1 tomorrow

---

## Elementor Settings for Option 1

**When We Restore Archive Tomorrow:**

**Recommended Elementor Settings:**
- DOM Optimization: **OFF**
- Improved CSS Loading: **OFF**
- CSS Print Method: **Internal Embedding**
- Inline Font Icons: **OFF** (default)

**After Restore:**
1. Set these Elementor settings
2. Regenerate CSS
3. Clear all caches
4. Test modal

---

## Elementor Version Requirements

**For Modal System:**

- **Elementor Core:** 3.0+ (latest recommended)
- **Elementor Pro:** Compatible with core version
- **WordPress:** 5.0+ (required by Elementor)

**Check Compatibility:**
- Elementor → Settings → About
- Verify versions match requirements
- Update if needed

---

## Common Elementor Error Messages

### "Elementor Frontend not loaded"
**Cause:** Elementor scripts not loading  
**Fix:** Regenerate CSS, clear cache, check for conflicts

### "jQuery is not defined"
**Cause:** jQuery conflict or missing  
**Fix:** Check jQuery dependency, check theme/plugins

### "Cannot read property of undefined"
**Cause:** Script execution order issue  
**Fix:** Disable DOM Optimization, regenerate CSS

### "Z-index not working"
**Cause:** CSS conflicts or stacking context  
**Fix:** Regenerate CSS, check modal CSS z-index values

---

## Summary

**If Elementor Settings Are the Issue:**

1. **Tonight:** Check and adjust Elementor settings
2. **Regenerate CSS:** Always after settings changes
3. **Clear Caches:** WordPress, browser, server
4. **Test:** Verify modal works
5. **Tomorrow:** If still broken, proceed with Option 1

**Most Likely Fix:**
- Disable DOM Optimization
- Disable Improved CSS Loading
- Regenerate CSS
- Clear caches

**Time Estimate:** 15-30 minutes to try fixes

---

**Created:** November 5, 2025  
**Status:** Ready for use tonight

