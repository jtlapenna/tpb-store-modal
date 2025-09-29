/**
 * Simple, bulletproof TPB Quick View Modal
 * This is a minimal implementation that just works
 */

(function() {
    'use strict';
    
    console.log('🚀 TPB Simple Modal Loading...');
    
    // Configuration
    const config = {
        home: window.location.origin,
        qv_param: 'tpb_qv'
    };
    
    // Create modal HTML
    function createModalHTML() {
        return `
            <div id="tpb-qv-overlay" class="tpb-qv-overlay" style="
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.8);
                z-index: 999999;
                display: none;
                justify-content: center;
                align-items: center;
            ">
                <div class="tpb-qv-modal" style="
                    background: white;
                    width: 90%;
                    max-width: 1200px;
                    height: 90%;
                    border-radius: 8px;
                    position: relative;
                    display: flex;
                ">
                    <button class="tpb-qv-close" style="
                        position: absolute;
                        top: 10px;
                        right: 10px;
                        background: #333;
                        color: white;
                        border: none;
                        width: 30px;
                        height: 30px;
                        border-radius: 50%;
                        cursor: pointer;
                        z-index: 1000000;
                        font-size: 18px;
                        line-height: 1;
                    ">&times;</button>
                    <iframe id="tpb-qv-iframe" style="
                        width: 100%;
                        height: 100%;
                        border: none;
                        border-radius: 8px;
                    "></iframe>
                </div>
            </div>
        `;
    }
    
    // Initialize modal
    function initModal() {
        console.log('🔧 Initializing simple modal...');
        
        // Remove existing modals if any
        const existing = document.getElementById('tpb-qv-overlay');
        if (existing) {
            existing.remove();
        }
        
        // Remove old modal elements
        const oldOverlay = document.querySelector('.tpb-qv-overlay');
        if (oldOverlay) {
            oldOverlay.remove();
        }
        
        // Remove old modal event listeners by overriding the old function
        if (window.tpbOpenModal) {
            console.log('🔄 Overriding old tpbOpenModal function');
        }
        
        // Add modal HTML
        document.body.insertAdjacentHTML('beforeend', createModalHTML());
        
        const overlay = document.getElementById('tpb-qv-overlay');
        const iframe = document.getElementById('tpb-qv-iframe');
        const closeBtn = document.querySelector('.tpb-qv-close');
        
        // Close modal function
        function closeModal() {
            console.log('❌ Closing modal...');
            overlay.style.display = 'none';
            iframe.src = 'about:blank';
            document.body.style.overflow = '';
        }
        
        // Open modal function
        function openModal(productUrl) {
            console.log('✅ Opening modal for:', productUrl);
            
            // Build iframe URL
            const url = new URL(productUrl);
            url.searchParams.set(config.qv_param, '1');
            
            // Show modal
            overlay.style.display = 'flex';
            iframe.src = url.toString();
            document.body.style.overflow = 'hidden';
            
            console.log('🎯 Modal should now be visible');
        }
        
        // Event listeners
        closeBtn.addEventListener('click', closeModal);
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                closeModal();
            }
        });
        
        // ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && overlay.style.display === 'flex') {
                closeModal();
            }
        });
        
        // Override any existing modal functions
        window.tpbOpenModal = openModal;
        window.TPB_QV = { open: openModal };
        
        // Prevent old modal from interfering
        document.addEventListener('click', function(e) {
            const target = e.target;
            if (target && (target.textContent.includes('Configure') || target.textContent.includes('Customize'))) {
                const href = target.href || target.closest('a')?.href;
                if (href && href.includes('/product/')) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    console.log('🚀 Intercepting click for:', href);
                    openModal(href);
                    return false;
                }
            }
        }, true); // Use capture phase to intercept before other handlers
        
        console.log('✅ Simple modal initialized');
    }
    
    // Find and wire up Configure Now buttons
    function wireUpButtons() {
        console.log('🔍 Looking for Configure Now buttons...');
        
        // Look for buttons with Configure Now text
        const buttons = Array.from(document.querySelectorAll('a, button')).filter(el => {
            const text = el.textContent.toLowerCase().trim();
            return text.includes('configure') || text.includes('customize');
        });
        
        console.log(`🎯 Found ${buttons.length} potential buttons`);
        
        buttons.forEach((button, index) => {
            console.log(`Button ${index + 1}:`, button.textContent.trim(), button.href);
            
            // Check if it's a product URL
            if (button.href && button.href.includes('/product/')) {
                console.log(`✅ Wiring up button: ${button.textContent.trim()}`);
                
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('🚀 Button clicked, opening modal...');
                    window.tpbOpenModal(button.href);
                });
            }
        });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initModal();
            wireUpButtons();
        });
    } else {
        initModal();
        wireUpButtons();
    }
    
    // Also try after a delay for dynamic content
    setTimeout(() => {
        wireUpButtons();
    }, 2000);
    
    console.log('🎉 TPB Simple Modal loaded');
})();
