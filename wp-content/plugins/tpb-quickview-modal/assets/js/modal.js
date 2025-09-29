/**
 * TPB QuickView Modal JavaScript
 * Clean, reliable modal functionality
 */

(function($) {
    'use strict';
    
    console.log('🚀 TPB QuickView Modal Plugin Loading...');
    
    // Configuration
    const config = {
        overlaySelector: '#tpb-qv-overlay',
        iframeSelector: '#tpb-qv-iframe',
        closeSelector: '.tpb-qv-close',
        triggerSelector: '[data-tpb-modal="true"], a[href*="/product/"]',
        qvParam: 'tpb_qv_iframe',
        debug: true
    };
    
    // Modal object
    const Modal = {
        
        // Initialize modal
        init: function() {
            console.log('🔧 Initializing TPB QuickView Modal...');
            
            this.bindEvents();
            this.detectButtons();
            this.setupModal();
            
            console.log('✅ TPB QuickView Modal initialized');
        },
        
        // Bind event listeners
        bindEvents: function() {
            const self = this;
            
            // Close button
            $(document).on('click', config.closeSelector, function(e) {
                e.preventDefault();
                self.close();
            });
            
            // Overlay click to close
            $(document).on('click', config.overlaySelector, function(e) {
                if (e.target === this) {
                    self.close();
                }
            });
            
            // ESC key to close
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && self.isOpen()) {
                    self.close();
                }
            });
            
            // Modal trigger clicks
            $(document).on('click', config.triggerSelector, function(e) {
                const href = $(this).attr('href');
                if (href && href.includes('/product/')) {
                    e.preventDefault();
                    self.open(href);
                }
            });
        },
        
        // Detect Configure Now buttons
        detectButtons: function() {
            console.log('🔍 Detecting Configure Now buttons...');
            
            const buttons = $('a, button').filter(function() {
                const text = $(this).text().toLowerCase().trim();
                return text.includes('configure') || text.includes('customize');
            });
            
            console.log(`🎯 Found ${buttons.length} potential buttons`);
            
            buttons.each(function() {
                const $this = $(this);
                const href = $this.attr('href');
                
                if (href && href.includes('/product/')) {
                    console.log(`✅ Wiring up button: ${$this.text().trim()}`);
                    $this.attr('data-tpb-modal', 'true');
                }
            });
        },
        
        // Setup modal HTML
        setupModal: function() {
            // Ensure modal exists
            if ($(config.overlaySelector).length === 0) {
                console.log('⚠️ Modal HTML not found, creating...');
                this.createModal();
            }
        },
        
        // Create modal HTML (fallback)
        createModal: function() {
            const modalHTML = `
                <div id="tpb-qv-overlay" class="tpb-qv-overlay" style="display: none;">
                    <div class="tpb-qv-modal">
                        <button class="tpb-qv-close" aria-label="Close modal">&times;</button>
                        <iframe id="tpb-qv-iframe" class="tpb-qv-iframe" src="about:blank"></iframe>
                    </div>
                </div>
            `;
            $('body').append(modalHTML);
        },
        
        // Open modal
        open: function(url) {
            console.log('✅ Opening modal for:', url);
            
            const $overlay = $(config.overlaySelector);
            const $iframe = $(config.iframeSelector);
            
            if ($overlay.length === 0) {
                console.error('❌ Modal overlay not found');
                return;
            }
            
            // Build iframe URL
            const iframeUrl = this.buildIframeUrl(url);
            console.log('🔗 Iframe URL:', iframeUrl);
            
            // Show modal
            $overlay.addClass('is-open');
            $overlay.show(); // Force display
            $iframe.attr('src', iframeUrl);
            $('body').addClass('tpb-qv-locked');
            
            // Force visibility with multiple approaches
            setTimeout(() => {
                $overlay.css('display', 'flex');
                $overlay.css('visibility', 'visible');
                $overlay.css('opacity', '1');
            }, 100);
            
            // Debug info
            if (config.debug) {
                this.showDebugInfo('Modal opened', {
                    url: url,
                    iframeUrl: iframeUrl,
                    overlayVisible: $overlay.is(':visible')
                });
            }
            
            console.log('🎯 Modal should now be visible');
        },
        
        // Close modal
        close: function() {
            console.log('❌ Closing modal...');
            
            const $overlay = $(config.overlaySelector);
            const $iframe = $(config.iframeSelector);
            
            $overlay.removeClass('is-open');
            $iframe.attr('src', 'about:blank');
            $('body').removeClass('tpb-qv-locked');
            
            console.log('✅ Modal closed');
        },
        
        // Check if modal is open
        isOpen: function() {
            return $(config.overlaySelector).hasClass('is-open');
        },
        
        // Build iframe URL with query parameter
        buildIframeUrl: function(url) {
            const urlObj = new URL(url);
            urlObj.searchParams.set(config.qvParam, '1');
            return urlObj.toString();
        },
        
        // Show debug information
        showDebugInfo: function(message, data) {
            if (!config.debug) return;
            
            const debugInfo = `
                <div class="tpb-qv-debug">
                    <strong>${message}</strong><br>
                    ${JSON.stringify(data, null, 2)}
                </div>
            `;
            
            $('.tpb-qv-debug').remove();
            $('body').append(debugInfo);
            
            // Remove after 3 seconds
            setTimeout(() => {
                $('.tpb-qv-debug').fadeOut();
            }, 3000);
        }
    };
    
    // Initialize when document is ready
    $(document).ready(function() {
        Modal.init();
    });
    
    // Also try after a delay for dynamic content
    setTimeout(function() {
        Modal.detectButtons();
    }, 2000);
    
    // Expose Modal to global scope for debugging
    window.TPBModal = Modal;
    
    console.log('🎉 TPB QuickView Modal Plugin loaded');
    
})(jQuery);
