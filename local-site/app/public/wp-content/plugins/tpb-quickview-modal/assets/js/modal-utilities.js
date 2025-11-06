/**
 * TPB Modal Utilities
 * Shared functions to reduce brittleness and code duplication
 */

(function() {
    'use strict';
    
    // Shared modal detection logic
    window.TPBModalUtils = {
        
        /**
         * Check if we're in modal mode (iframe or direct DOM)
         * @returns {boolean}
         */
        isInModalMode: function() {
            var params = new URLSearchParams(location.search);
            return params.get('tpb_qv_iframe') === '1' || 
                   params.get('tpb_qv') === '1' || 
                   document.getElementById('tpb-qv-native') !== null ||
                   document.querySelector('.tpb-qv-stepper-container') !== null;
        },
        
        /**
         * Get modal type from URL or context
         * @param {string} url - The URL to analyze
         * @returns {string} Modal type
         */
        getModalType: function(url) {
            if (url.includes('flower-station')) return 'flower-station';
            if (url.includes('category-station')) return 'category-station';
            if (url.includes('quick-checkout')) return 'quick-checkout';
            if (url.includes('menu-boards')) return 'menu-boards';
            if (url.includes('branded-stations')) return 'branded-stations';
            return 'unknown';
        },
        
        /**
         * Get product image URL based on modal type
         * @param {string} modalType - The modal type
         * @returns {string} Product image URL
         * @deprecated Use individual handler image URLs instead
         */
        getProductImageUrl: function(modalType) {
            console.warn('getProductImageUrl is deprecated. Use individual handler image URLs instead.');
            return '';
        },
        
        /**
         * Get modal title based on modal type
         * @param {string} modalType - The modal type
         * @returns {string} Modal title
         * @deprecated Use individual handler titles instead
         */
        getModalTitle: function(modalType) {
            console.warn('getModalTitle is deprecated. Use individual handler titles instead.');
            return '';
        },
        
        /**
         * Get modal description based on modal type
         * @param {string} modalType - The modal type
         * @returns {string} Modal description
         * @deprecated Use individual handler descriptions instead
         */
        getModalDescription: function(modalType) {
            console.warn('getModalDescription is deprecated. Use individual handler descriptions instead.');
            return '';
        },
        
        /**
         * Standardized error handling
         * @param {string} context - Context where error occurred
         * @param {Error} error - The error object
         * @param {string} additionalInfo - Additional context
         */
        handleError: function(context, error, additionalInfo) {
            console.error('🚨 TPB Modal Error in ' + context + ':', error);
            if (additionalInfo) {
                console.error('Additional info:', additionalInfo);
            }
            
            // Show user-friendly error message
            this.showToast('An error occurred. Please try again.', 'error');
        },
        
        /**
         * Show toast notification
         * @param {string} message - Message to show
         * @param {string} type - Type of toast (success, error, info)
         */
        showToast: function(message, type) {
            var toast = document.createElement('div');
            toast.className = 'tpb-toast tpb-toast-' + (type || 'info');
            toast.textContent = message;
            toast.setAttribute('role', 'status');
            toast.setAttribute('aria-live', 'polite');
            
            // Add styles
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'error' ? '#e74c3c' : type === 'success' ? '#27ae60' : '#3498db'};
                color: white;
                padding: 12px 20px;
                border-radius: 4px;
                z-index: 10000;
                font-family: inherit;
                font-size: 14px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            `;
            
            document.body.appendChild(toast);
            
            // Auto-remove after 3 seconds
            setTimeout(function() {
                if (toast && toast.remove) {
                    toast.remove();
                }
            }, 3000);
        },
        
        /**
         * Debounce function to prevent excessive calls
         * @param {Function} func - Function to debounce
         * @param {number} wait - Wait time in milliseconds
         * @returns {Function} Debounced function
         */
        debounce: function(func, wait) {
            var timeout;
            return function executedFunction() {
                var later = function() {
                    clearTimeout(timeout);
                    func.apply(this, arguments);
                }.bind(this);
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },
        
        /**
         * Throttle function to limit execution frequency
         * @param {Function} func - Function to throttle
         * @param {number} limit - Time limit in milliseconds
         * @returns {Function} Throttled function
         */
        throttle: function(func, limit) {
            var inThrottle;
            return function() {
                var args = arguments;
                var context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(function() {
                        inThrottle = false;
                    }, limit);
                }
            };
        },
        
        /**
         * Safe DOM query selector with error handling
         * @param {string} selector - CSS selector
         * @param {Element} context - Context element (optional)
         * @returns {Element|null} Found element or null
         */
        safeQuerySelector: function(selector, context) {
            try {
                return (context || document).querySelector(selector);
            } catch (error) {
                this.handleError('safeQuerySelector', error, 'Selector: ' + selector);
                return null;
            }
        },
        
        /**
         * Safe DOM query selector all with error handling
         * @param {string} selector - CSS selector
         * @param {Element} context - Context element (optional)
         * @returns {NodeList} Found elements or empty NodeList
         */
        safeQuerySelectorAll: function(selector, context) {
            try {
                return (context || document).querySelectorAll(selector);
            } catch (error) {
                this.handleError('safeQuerySelectorAll', error, 'Selector: ' + selector);
                return document.createDocumentFragment().querySelectorAll('*'); // Empty NodeList
            }
        }
    };
    
    console.log('🔧 TPB Modal Utilities loaded');
})();
