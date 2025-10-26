/**
 * TPB Modal State Management
 * Centralized state management to prevent conflicts and improve reliability
 */

(function() {
    'use strict';
    
    // Modal state singleton
    window.TPBModalState = {
        
        // State properties
        isOpen: false,
        currentModalType: null,
        activeStepper: null,
        isLoading: false,
        lastOpenedUrl: null,
        
        // State change listeners
        listeners: [],
        
        /**
         * Initialize state management
         */
        init: function() {
            console.log('🔧 TPB Modal State Management initialized');
            this.reset();
            this.setupCartZIndexHandling();
        },
        
        /**
         * Setup cart z-index handling for modal overlay
         */
        setupCartZIndexHandling: function() {
            var self = this;
            
            // Listen for modal state changes
            this.addListener(function(event, data, state) {
                if (event === 'opened') {
                    // Modal just opened - raise cart z-index
                    self.raiseCartZIndex();
                } else if (event === 'closed') {
                    // Modal just closed - reset cart z-index
                    self.resetCartZIndex();
                }
            });
        },
        
        /**
         * Raise cart z-index to appear above modal overlay
         */
        raiseCartZIndex: function() {
            console.log('🛒 Raising cart z-index above modal overlay');
            
            // Find all possible cart elements
            var cartSelectors = [
                '[class*="cart"]',
                '[class*="woocommerce-cart"]',
                '[class*="elementor-cart"]',
                '.cart-icon',
                '.cart-button',
                '.woocommerce-cart-icon',
                '.elementor-cart-icon'
            ];
            
            var allElements = document.querySelectorAll('*');
            var cartElements = [];
            
            // Find elements matching cart selectors
            cartSelectors.forEach(function(selector) {
                try {
                    var elements = document.querySelectorAll(selector);
                    elements.forEach(function(el) {
                        if (!cartElements.includes(el)) {
                            cartElements.push(el);
                        }
                    });
                } catch (e) {
                    // Skip invalid selectors
                }
            });
            
            // Also find by class name pattern
            Array.prototype.forEach.call(allElements, function(el) {
                if (el.className && typeof el.className === 'string') {
                    var classes = el.className.split(' ');
                    if (classes.some(function(cls) { return cls.toLowerCase().indexOf('cart') !== -1; })) {
                        if (!cartElements.includes(el)) {
                            cartElements.push(el);
                        }
                    }
                }
            });
            
            // Set high z-index on cart elements
            cartElements.forEach(function(el) {
                var currentZIndex = parseInt(window.getComputedStyle(el).zIndex) || 1;
                if (currentZIndex < 99999999) {
                    el.style.zIndex = '99999999';
                    el.dataset.originalZIndex = currentZIndex.toString();
                    console.log('🛒 Raised z-index for cart element:', el);
                }
            });
            
            console.log('🛒 Found and raised z-index for', cartElements.length, 'cart elements');
        },
        
        /**
         * Reset cart z-index to original values
         */
        resetCartZIndex: function() {
            console.log('🛒 Resetting cart z-index to original values');
            
            // Find all elements with original z-index stored
            var allElements = document.querySelectorAll('*');
            Array.prototype.forEach.call(allElements, function(el) {
                if (el.dataset && el.dataset.originalZIndex) {
                    el.style.zIndex = el.dataset.originalZIndex;
                    delete el.dataset.originalZIndex;
                }
            });
        },
        
        /**
         * Reset state to initial values
         */
        reset: function() {
            this.isOpen = false;
            this.currentModalType = null;
            this.activeStepper = null;
            this.isLoading = false;
            this.lastOpenedUrl = null;
            this.notifyListeners('reset');
        },
        
        /**
         * Set modal as opening
         * @param {string} modalType - Type of modal
         * @param {string} url - URL that triggered the modal
         */
        setOpening: function(modalType, url) {
            if (this.isOpen) {
                console.warn('⚠️ Modal already open, closing previous modal first');
                this.close();
            }
            
            this.isLoading = true;
            this.currentModalType = modalType;
            this.lastOpenedUrl = url;
            this.notifyListeners('opening', { modalType, url });
        },
        
        /**
         * Set modal as opened
         * @param {string} stepperName - Name of active stepper
         */
        setOpened: function(stepperName) {
            this.isOpen = true;
            this.isLoading = false;
            this.activeStepper = stepperName;
            this.notifyListeners('opened', { stepperName });
        },
        
        /**
         * Set modal as closing
         */
        setClosing: function() {
            this.isLoading = true;
            this.notifyListeners('closing');
        },
        
        /**
         * Set modal as closed
         */
        setClosed: function() {
            this.isOpen = false;
            this.isLoading = false;
            this.currentModalType = null;
            this.activeStepper = null;
            this.notifyListeners('closed');
        },
        
        /**
         * Check if modal can be opened
         * @param {string} modalType - Type of modal to open
         * @returns {boolean} True if can open
         */
        canOpen: function(modalType) {
            if (this.isLoading) {
                console.warn('⚠️ Modal is currently loading, please wait');
                return false;
            }
            
            if (this.isOpen && this.currentModalType === modalType) {
                console.warn('⚠️ Modal of type ' + modalType + ' is already open');
                return false;
            }
            
            return true;
        },
        
        /**
         * Get current state
         * @returns {Object} Current state object
         */
        getState: function() {
            return {
                isOpen: this.isOpen,
                currentModalType: this.currentModalType,
                activeStepper: this.activeStepper,
                isLoading: this.isLoading,
                lastOpenedUrl: this.lastOpenedUrl
            };
        },
        
        /**
         * Add state change listener
         * @param {Function} callback - Callback function
         * @returns {Function} Unsubscribe function
         */
        addListener: function(callback) {
            this.listeners.push(callback);
            
            // Return unsubscribe function
            return function() {
                var index = this.listeners.indexOf(callback);
                if (index > -1) {
                    this.listeners.splice(index, 1);
                }
            }.bind(this);
        },
        
        /**
         * Notify all listeners of state change
         * @param {string} event - Event type
         * @param {Object} data - Event data
         */
        notifyListeners: function(event, data) {
            this.listeners.forEach(function(listener) {
                try {
                    listener(event, data, this.getState());
                } catch (error) {
                    console.error('Error in modal state listener:', error);
                }
            }.bind(this));
        },
        
        /**
         * Wait for modal to be ready
         * @param {Function} callback - Callback when ready
         * @param {number} timeout - Timeout in milliseconds
         */
        waitForReady: function(callback, timeout) {
            var self = this;
            var startTime = Date.now();
            
            function check() {
                if (!self.isLoading && (self.isOpen || !self.currentModalType)) {
                    callback();
                } else if (Date.now() - startTime > (timeout || 5000)) {
                    console.error('Modal state timeout');
                    callback(new Error('Modal state timeout'));
                } else {
                    setTimeout(check, 50);
                }
            }
            
            check();
        },
        
        /**
         * Force close modal (emergency)
         */
        forceClose: function() {
            console.warn('🚨 Force closing modal due to error');
            this.reset();
            
            // Try to close any open modals
            var overlay = document.querySelector('#tpb-qv-overlay');
            if (overlay) {
                overlay.style.display = 'none';
                document.body.classList.remove('tpb-modal-open');
            }
        }
    };
    
    // Initialize on load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            window.TPBModalState.init();
        });
    } else {
        window.TPBModalState.init();
    }
    
    console.log('🔧 TPB Modal State Management loaded');
})();
