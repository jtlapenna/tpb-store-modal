/**
 * Stacking Context Diagnostic Tool
 * Run this in browser console to find what's blocking the cart
 */

(function() {
    console.log('=== STACKING CONTEXT DIAGNOSTIC ===\n');
    
    // Find cart elements
    const cartWrapper = document.querySelector('.elementor-menu-cart__toggle_wrapper');
    const cartButton = document.querySelector('.elementor-menu-cart__toggle_button');
    const modalOverlay = document.querySelector('.tpb-qv-overlay');
    
    console.log('1. ELEMENT DETECTION:');
    console.log('Cart Wrapper:', cartWrapper ? '✓ Found' : '✗ Not found');
    console.log('Cart Button:', cartButton ? '✓ Found' : '✗ Not found');
    console.log('Modal Overlay:', modalOverlay ? '✓ Found' : '✗ Not found');
    console.log('');
    
    // Function to check if element creates stacking context
    function createsStackingContext(el) {
        if (!el) return null;
        
        const style = window.getComputedStyle(el);
        const issues = [];
        
        // Check for stacking context creators
        if (style.position === 'fixed' || style.position === 'sticky') {
            issues.push(`position: ${style.position}`);
        }
        if (style.position === 'absolute' || style.position === 'relative') {
            const zIndex = style.zIndex;
            if (zIndex !== 'auto') {
                issues.push(`position: ${style.position} + z-index: ${zIndex}`);
            }
        }
        if (parseFloat(style.opacity) < 1) {
            issues.push(`opacity: ${style.opacity}`);
        }
        if (style.transform !== 'none') {
            issues.push(`transform: ${style.transform}`);
        }
        if (style.filter !== 'none') {
            issues.push(`filter: ${style.filter}`);
        }
        if (style.perspective !== 'none') {
            issues.push(`perspective: ${style.perspective}`);
        }
        if (style.clipPath !== 'none') {
            issues.push(`clip-path: ${style.clipPath}`);
        }
        if (style.mask !== 'none') {
            issues.push(`mask: ${style.mask}`);
        }
        if (style.mixBlendMode !== 'normal') {
            issues.push(`mix-blend-mode: ${style.mixBlendMode}`);
        }
        if (style.isolation === 'isolate') {
            issues.push('isolation: isolate');
        }
        if (style.willChange !== 'auto') {
            issues.push(`will-change: ${style.willChange}`);
        }
        if (style.contain && style.contain !== 'none') {
            issues.push(`contain: ${style.contain}`);
        }
        
        return issues;
    }
    
    // Trace cart button's ancestor chain
    console.log('2. CART BUTTON ANCESTOR CHAIN (STACKING CONTEXT CREATORS):');
    if (cartButton) {
        let current = cartButton;
        let level = 0;
        
        while (current && current !== document.body) {
            const issues = createsStackingContext(current);
            const style = window.getComputedStyle(current);
            
            if (issues.length > 0) {
                console.log(`\n   Level ${level}: ${current.tagName}${current.className ? '.' + current.className.split(' ')[0] : ''}`);
                console.log(`   ⚠️  CREATES STACKING CONTEXT:`);
                issues.forEach(issue => console.log(`      - ${issue}`));
                console.log(`   Current z-index: ${style.zIndex}`);
                console.log(`   Position: ${style.position}`);
            }
            
            current = current.parentElement;
            level++;
        }
    }
    
    console.log('\n3. Z-INDEX VALUES:');
    if (cartWrapper) {
        const style = window.getComputedStyle(cartWrapper);
        console.log(`Cart Wrapper z-index: ${style.zIndex} (position: ${style.position})`);
    }
    if (cartButton) {
        const style = window.getComputedStyle(cartButton);
        console.log(`Cart Button z-index: ${style.zIndex} (position: ${style.position})`);
    }
    if (modalOverlay) {
        const style = window.getComputedStyle(modalOverlay);
        console.log(`Modal Overlay z-index: ${style.zIndex} (position: ${style.position})`);
    }
    
    console.log('\n4. RECOMMENDATIONS:');
    console.log('');
    console.log('If you see stacking context creators above (⚠️), those are preventing');
    console.log('the cart from appearing above the modal overlay.');
    console.log('');
    console.log('Solutions:');
    console.log('1. Remove transform/filter/opacity from parent elements');
    console.log('2. Move cart button to <body> level (outside all containers)');
    console.log('3. Use a different modal approach (see alternatives below)');
    console.log('');
    console.log('=== END DIAGNOSTIC ===');
})();
