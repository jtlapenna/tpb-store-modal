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
                    // Modal just opened - raise cart z-index with a slight delay
                    // to ensure cart elements are rendered
                    setTimeout(function() {
                        self.raiseCartZIndex();
                    }, 50);
                } else if (event === 'closed') {
                    // Modal just closed - reset cart z-index
                    self.resetCartZIndex();
                }
            });
            
            // Also listen to DOM changes for body.tpb-modal-open class
            // This provides an additional trigger for when modal opens
            var observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'class') {
                        var isModalOpen = document.body.classList.contains('tpb-modal-open');
                        if (isModalOpen && self.isOpen) {
                            setTimeout(function() {
                                self.raiseCartZIndex();
                            }, 50);
                        } else if (!isModalOpen) {
                            self.resetCartZIndex();
                        }
                    }
                });
            });
            
            observer.observe(document.body, {
                attributes: true,
                attributeFilter: ['class']
            });
            
            // Also watch for cart drawer opening/closing
            var cartObserver = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    // Check if cart drawer/panel is opening with more generic selectors
                    var toggleButton = document.querySelector('.elementor-menu-cart__toggle_button[aria-expanded="true"]');
                    var hasOpenDrawer = document.querySelectorAll('[class*="cart"][class*="open"], [class*="drawer"][class*="open"], [class*="panel"][class*="open"], [aria-expanded="true"][class*="cart"]');
                    
                    if (toggleButton || hasOpenDrawer.length > 0) {
                        console.log('🛒 Cart drawer is open');
                        // Hide the toggle button icon when drawer is open
                        var cartIcons = document.querySelectorAll('.eicon-cart, .eicon-cart-medium, [class*="cart-icon"], [class*="woocommerce-cart-icon"], .elementor-button-icon');
                        cartIcons.forEach(function(icon) {
                            // Hide icons that are part of the toggle button but not in the drawer
                            if (!icon.closest('.elementor-menu-cart__main')) {
                                icon.style.display = 'none';
                                console.log('🛒 Hiding cart icon:', icon);
                            }
                        });
                        
                        // Also hide the parent button if it has the icon
                        var toggleBtn = document.querySelector('.elementor-menu-cart__toggle_button');
                        if (toggleBtn) {
                            var iconInside = toggleBtn.querySelector('.eicon-cart, .eicon-cart-medium');
                            if (iconInside) {
                                iconInside.style.display = 'none';
                                console.log('🛒 Hiding icon inside toggle button');
                            }
                        }
                        
                        // Ensure drawer itself has high z-index
                        var drawer = document.querySelector('.elementor-menu-cart__main');
                        if (drawer) {
                            drawer.style.zIndex = '99999999';
                        }
                    } else {
                        // If drawer is not open, show icons and raise z-index for modal
                        var cartIcons = document.querySelectorAll('.eicon-cart, .eicon-cart-medium, [class*="cart-icon"], [class*="woocommerce-cart-icon"], .elementor-button-icon');
                        cartIcons.forEach(function(icon) {
                            icon.style.display = '';
                        });
                        
                        // If drawer is not open and modal is open, raise z-index
                        if (self.isOpen) {
                            self.raiseCartZIndex();
                        }
                    }
                });
            });
            
            // Observe all elements for cart drawer changes
            cartObserver.observe(document.body, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ['class']
            });
        },
        
        /**
         * Raise cart z-index to appear above modal overlay
         */
        raiseCartZIndex: function() {
            console.log('🛒 Raising cart z-index above modal overlay');
            
            // Find all cart elements, then filter to exclude drawer/panel
            var allElements = document.querySelectorAll('*');
            var cartElements = [];
            
            // Find all elements with cart-related classes
            Array.prototype.forEach.call(allElements, function(el) {
                if (el.className && typeof el.className === 'string') {
                    var classes = el.className.split(' ');
                    var hasCartClass = classes.some(function(cls) { 
                        return cls.toLowerCase().indexOf('cart') !== -1 ||
                               cls.toLowerCase().indexOf('woocommerce-cart') !== -1 ||
                               cls.toLowerCase().indexOf('elementor-cart') !== -1;
                    });
                    
                    // Check if this is a drawer, panel, or menu (and we don't want those)
                    var fullClassString = el.className.toLowerCase();
                    var isExcluded = 
                        fullClassString.indexOf('menu-cart__main') !== -1 ||
                        fullClassString.indexOf('menu-cart__product') !== -1 ||
                        fullClassString.indexOf('menu-cart__footer') !== -1 ||
                        fullClassString.indexOf('menu-cart__close') !== -1 ||
                        fullClassString.indexOf('menu-cart__toggle') !== -1 || // Exclude toggle button
                        fullClassString.indexOf('elementor-menu-cart__toggle_button') !== -1 || // Exclude toggle button
                        fullClassString.indexOf('eicon-cart') !== -1 || // Exclude cart icons (e.g., eicon-cart-medium)
                        (fullClassString.indexOf('drawer') !== -1 && fullClassString.indexOf('toggle') === -1) ||
                        (fullClassString.indexOf('panel') !== -1 && fullClassString.indexOf('toggle') === -1) ||
                        fullClassString.indexOf('slideout') !== -1 ||
                        fullClassString.indexOf('side-panel') !== -1 ||
                        // Also exclude if parent has menu-cart__main or menu-cart__toggle
                        (el.closest && (
                            el.closest('.elementor-menu-cart__main') ||
                            el.closest('.elementor-menu-cart__toggle')
                        ));
                    
                    // Only include cart elements that are NOT drawers/panels
                    if (hasCartClass && !isExcluded) {
                        if (!cartElements.includes(el)) {
                            cartElements.push(el);
                        }
                    }
                }
            });
            
            // Set extremely high z-index on cart elements and their parents
            cartElements.forEach(function(el) {
                var currentZIndex = parseInt(window.getComputedStyle(el).zIndex) || 1;
                
                // Set on the element itself
                if (currentZIndex < 99999999) {
                    el.style.zIndex = '99999999';
                    el.dataset.originalZIndex = currentZIndex.toString();
                    el.dataset.cartZIndexed = 'true';
                    console.log('🛒 Raised z-index for cart element:', el);
                }
                
                // Also set on parent elements up to 3 levels deep
                var parent = el.parentElement;
                var levels = 0;
                while (parent && parent !== document.body && levels < 3) {
                    var parentZIndex = parseInt(window.getComputedStyle(parent).zIndex);
                    if (!parentZIndex || parentZIndex < 99999999) {
                        parent.style.zIndex = '99999999';
                        if (!parent.dataset.originalZIndex) {
                            parent.dataset.originalZIndex = parentZIndex ? parentZIndex.toString() : 'auto';
                        }
                        parent.dataset.cartZIndexed = 'true';
                        console.log('🛒 Raised z-index for parent element:', parent);
                    }
                    parent = parent.parentElement;
                    levels++;
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
                if (el.dataset && el.dataset.cartZIndexed) {
                    if (el.dataset.originalZIndex && el.dataset.originalZIndex !== 'auto') {
                        el.style.zIndex = el.dataset.originalZIndex;
                    } else {
                        el.style.zIndex = '';
                    }
                    delete el.dataset.originalZIndex;
                    delete el.dataset.cartZIndexed;
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
