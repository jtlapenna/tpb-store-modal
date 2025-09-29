/**
 * TPB QuickView Modal Blocker
 * Prevents old modal from loading and causing conflicts
 */

(function() {
    'use strict';
    
    console.log('🚫 TPB Modal Blocker Loading...');
    
    // Immediately block old modal functions
    window.TPB_QV = {
        open: function() { 
            console.log('🚫 Old TPB_QV blocked');
            return false;
        },
        close: function() { 
            console.log('🚫 Old TPB_QV close blocked');
            return false;
        }
    };
    
    window.tpbOpenModal = function() { 
        console.log('🚫 Old tpbOpenModal blocked');
        return false;
    };
    
    // Block old modal immediately when DOM is ready
    function blockOldModal() {
        console.log('🚫 Blocking old modal elements...');
        
        // Remove old modal elements
        const oldOverlays = document.querySelectorAll('.tpb-qv-overlay:not(#tpb-qv-overlay)');
        oldOverlays.forEach(overlay => overlay.remove());
        
        const oldModals = document.querySelectorAll('.tpb-modal');
        oldModals.forEach(modal => modal.remove());
        
        // Block old modal scripts from loading
        const scripts = document.querySelectorAll('script[src*="tpb-modal.js"]');
        scripts.forEach(script => {
            if (!script.src.includes('modal.js')) {
                console.log('🚫 Removing old modal script:', script.src);
                script.remove();
            }
        });
    }
    
    // Run immediately
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', blockOldModal);
    } else {
        blockOldModal();
    }
    
    // Also run after a delay to catch dynamically loaded content
    setTimeout(blockOldModal, 100);
    setTimeout(blockOldModal, 500);
    setTimeout(blockOldModal, 1000);
    
    console.log('🚫 TPB Modal Blocker Loaded');
})();
