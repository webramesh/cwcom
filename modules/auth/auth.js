class AuthModal {
    constructor() {
        this.init();
        this.modalShown = false; // Track if modal has been shown automatically
        
        // We want the modal to appear on every page load/refresh
        // No need to check localStorage/sessionStorage anymore
        this.setupAutoShowModal(); 
    }

    init() {
        this.bindEvents();
        this.setupGravityFormHandlers();
        this.setupAjaxLogin();
        this.setupRedirectHandling();
    }
    
    // Add new method to handle redirect logic
    setupRedirectHandling() {
        // Update the redirect_to field in login forms to current URL if on tender page
        const loginForms = document.querySelectorAll('#client_login_form');
        const currentPath = window.location.pathname;
        
        // Check if current page is a tender page
        const isTenderPage = currentPath.includes('/tenders/') || 
                            currentPath.includes('/tender/') || 
                            currentPath.includes('-tender') ||
                            document.querySelector('.tender-detail-page') !== null;
        
        if (isTenderPage) {
            loginForms.forEach(form => {
                const redirectField = form.querySelector('input[name="redirect_to"]');
                if (redirectField) {
                    redirectField.value = window.location.href;
                }
            });
        }
    }

    setupGravityFormHandlers() {
        if (typeof gform !== 'undefined') {
            // Ensure gf_global is available
            if (typeof window.gf_global === 'undefined') {
                window.gf_global = {
                    gfcaptcha: {},
                    spinnerUrl: '',
                    spinnerAlt: '',
                    formId: 0
                };
            }

            // Handle form render events
            gform.addAction('gform_post_render', (formId, currentPage) => {
                const formWrapper = document.querySelector(`#gform_wrapper_${formId}`);
                if (formWrapper) {
                    // Remove loading state
                    const submitButton = formWrapper.querySelector('.gform_button');
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.value = submitButton.dataset.label || 'Submit';
                    }
                }
            });

            // Handle form submission start
            gform.addFilter('gform_spinner_target_elem', (elem, formId) => {
                const submitButton = document.querySelector(`#gform_submit_button_${formId}`);
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.dataset.label = submitButton.value;
                    submitButton.value = 'Processing...';
                }
                return elem;
            });
        }
    }

    bindEvents() {
        document.addEventListener('click', (e) => {
            // Check if we're in the client area page
            const isClientAreaPage = window.location.pathname.includes('client-area') || 
                                    document.querySelector('.client-login-container') !== null;
            
            // First check if it's a tab click
            if (e.target.matches('.auth-tab')) {
                e.preventDefault();
                const tab = e.target.dataset.tab;
                this.switchTab(tab);
                return;
            }
            
            // Then check for auth triggers
            if (this.isAuthTrigger(e.target)) {
                e.preventDefault();
                
                // If we're on the client area page, handle form switching directly
                if (isClientAreaPage) {
                    if (this.isLoginTrigger(e.target)) {
                        this.updateClientAreaForms('login');
                    } else if (this.isRegisterTrigger(e.target)) {
                        this.updateClientAreaForms('register');
                    }
                } else {
                    // If not on client area, show modal
                    if (this.isLoginTrigger(e.target)) {
                        this.openModal('login');
                    } else if (this.isRegisterTrigger(e.target)) {
                        this.openModal('register');
                    }
                }
            }
            
            // Handle switch between forms in modal and client area
            else if (e.target.matches('.switch-to-login') || e.target.closest('.switch-to-login')) {
                e.preventDefault();
                if (isClientAreaPage) {
                    // Handle client area form switching
                    this.updateClientAreaForms('login');
                } else {
                    // Handle modal form switching
                    this.switchTab('login');
                }
            } 
            else if (e.target.matches('.switch-to-register') || e.target.closest('.switch-to-register')) {
                e.preventDefault();
                if (isClientAreaPage) {
                    // Handle client area form switching
                    this.updateClientAreaForms('register');
                } else {
                    // Handle modal form switching
                    this.switchTab('register');
                }
            }
            // Handle modal close
            else if (e.target.matches('.auth-modal-close') || 
                     (e.target.matches('.auth-modal') && !e.target.matches('.auth-modal-content'))) {
                this.closeModal();
            }
        });

        // Handle form validation messages display
        if (document.querySelector('.register_form_fancy .gform_validation_errors')) {
            // Don't show modal if on client area page
            if (!window.location.pathname.includes('client-area') && !document.querySelector('.client-login-container')) {
                this.openModal('register');
            }
        }

        if (window.location.search.includes('login=failed') && !window.location.pathname.includes('client-area')) {
            this.openModal('login');
        }

        // Initialize form placeholders
        this.initializeFormPlaceholders();
    }

    // Set up AJAX login handling
    setupAjaxLogin() {
        const loginForms = document.querySelectorAll('#client_login_form');
        
        loginForms.forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                
                // Reset any previous error messages
                const errorContainer = form.closest('.client-login-form').querySelector('.login-error-message');
                if (errorContainer) {
                    errorContainer.style.display = 'none';
                    errorContainer.innerHTML = '';
                }
                
                // Get form data - Fix: Use correct field values
                const username = form.querySelector('#client_user_login').value;
                const password = form.querySelector('#client_user_pass').value;
                const remember = form.querySelector('#client_rememberme')?.checked || false;
                const redirect = form.querySelector('input[name="redirect_to"]')?.value || '';
                
                // Disable submit button and show loading state
                const submitButton = form.querySelector('#client_wp-submit');
                if (submitButton) {
                    submitButton.value = 'Getting security token...';
                    submitButton.disabled = true;
                }
                
                // STEP 1: Always get a fresh nonce first to bypass caching issues
                this.getFreshNonceAndLogin(form, username, password, remember, redirect, submitButton, errorContainer);
            });
        });
    }
    
    // Method to get fresh nonce and then perform login
    getFreshNonceAndLogin(form, username, password, remember, redirect, submitButton, errorContainer) {
        // Get a fresh nonce via AJAX to bypass any caching
        const xhr = new XMLHttpRequest();
        xhr.open('POST', cwAuth.ajaxurl, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        xhr.onload = () => {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success && response.data.nonce) {
                        // Now perform the actual login with the fresh nonce
                        if (submitButton) {
                            submitButton.value = 'Signing in...';
                        }
                        this.performLogin(username, password, remember, redirect, response.data.nonce, submitButton, errorContainer);
                    } else {
                        this.handleLoginError(submitButton, errorContainer, 'Failed to get security token. Please try again.');
                    }
                } catch (e) {
                    console.error('Failed to parse nonce response:', e);
                    this.handleLoginError(submitButton, errorContainer, 'Security token error. Please refresh the page.');
                }
            } else {
                this.handleLoginError(submitButton, errorContainer, 'Security token request failed. Please try again.');
            }
        };
        xhr.onerror = () => {
            this.handleLoginError(submitButton, errorContainer, 'Network error. Please check your connection.');
        };
        
        // Request a fresh nonce
        const formData = new URLSearchParams();
        formData.append('action', 'get_fresh_nonce');
        xhr.send(formData.toString());
    }
    
    // Method to perform the actual login with a fresh nonce
    performLogin(username, password, remember, redirect, security, submitButton, errorContainer) {
        // Debug logging
        console.log('Login attempt with fresh nonce:', { 
            username, 
            security, 
            ajaxurl: cwAuth.ajaxurl 
        });
        
        // Send AJAX login request
        const xhr = new XMLHttpRequest();
        xhr.open('POST', cwAuth.ajaxurl, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        xhr.onload = () => {
            console.log('XHR Response Status:', xhr.status);
            console.log('XHR Response Text:', xhr.responseText);
            
            if (xhr.status >= 200 && xhr.status < 400) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    
                    // Reset submit button
                    if (submitButton) {
                        submitButton.value = 'Sign In';
                        submitButton.disabled = false;
                    }
                    
                    if (response.success) {
                        // Login successful, redirect
                        window.location.href = response.data.redirect;
                    } else {
                        // Login failed, show error
                        if (errorContainer) {
                            errorContainer.innerHTML = `<div class="login_error">${response.data.message}</div>`;
                            errorContainer.style.display = 'block';
                        }
                    }
                } catch (e) {
                    console.error('Failed to parse response:', e);
                    this.handleLoginError(submitButton, errorContainer, 'Server error. Please try again or contact support.');
                }
            } else {
                // HTTP error
                console.error('HTTP Error:', xhr.status, xhr.statusText);
                let errorMessage = 'Server error. Please try again.';
                if (xhr.status === 403) {
                    errorMessage = 'Security check failed. This may be due to page caching. Please refresh the page and try again.';
                } else if (xhr.status === 404) {
                    errorMessage = 'Login service not found. Please contact support.';
                }
                this.handleLoginError(submitButton, errorContainer, errorMessage);
            }
        };
        xhr.onerror = () => {
            this.handleLoginError(submitButton, errorContainer, 'An error occurred. Please try again.');
        };
        
        // Prepare form data with correct field names
        const formData = new URLSearchParams();
        formData.append('action', 'ajax_login');
        formData.append('username', username);
        formData.append('password', password);
        formData.append('remember', remember ? 'true' : 'false');
        formData.append('redirect', redirect);
        formData.append('security', security);
        
        // Send the request
        xhr.send(formData.toString());
    }
    
    // Helper method to handle login errors
    handleLoginError(submitButton, errorContainer, message) {
        // Reset submit button
        if (submitButton) {
            submitButton.value = 'Sign In';
            submitButton.disabled = false;
        }
        
        // Show error
        if (errorContainer) {
            errorContainer.innerHTML = `<div class="login_error">${message}</div>`;
            errorContainer.style.display = 'block';
        }
    }

    initializeFormPlaceholders() {
        const loginInputs = document.querySelectorAll('form[name="login_form"] input');
        loginInputs.forEach(input => {
            if (input.name === 'log') {
                input.setAttribute('placeholder', 'Email');
                input.setAttribute('required', 'required');
            } else if (input.name === 'pwd') {
                input.setAttribute('placeholder', 'Password');
                input.setAttribute('required', 'required');
            }
        });
    }

    isAuthTrigger(element) {
        return this.isLoginTrigger(element) || this.isRegisterTrigger(element);
    }

    isLoginTrigger(element) {
        // Specifically check for the header login button first
        if (element.matches('.login-btn')) {
            return true;
        }
        return element.matches('.header_login a:not(.register-btn), .login_box a, .login_span, .log_link, .switch-to-login');
    }

    isRegisterTrigger(element) {
        // Specifically check for the header register button first
        if (element.matches('.register-btn')) {
            return true;
        }
        return element.matches('.signup_box a, .register_form_fancy_link, .reg_link, .switch-to-register, .tender_apply_footer');
    }

    openModal(tab) {
        const modal = document.querySelector('.auth-modal');
        modal.classList.add('active');
        
        // Add modal-open class to body only, without adjusting for scrollbar width
        document.body.classList.add('modal-open');
        
        // Always switch to the specified tab when opening modal
        this.switchTab(tab);
        // Re-initialize Gravity Forms when showing registration
        if (tab === 'register') {
            // Small delay to ensure DOM is ready
            setTimeout(() => {
                if (typeof window.gform !== 'undefined') {
                    if (typeof window.gform.initializeOnLoaded === 'function') {
                        window.gform.initializeOnLoaded();
                    }
                }
            }, 100);
        }

        // Clear any previous error messages when opening the modal
        const errorContainers = modal.querySelectorAll('.login-error-message');
        errorContainers.forEach(container => {
            container.style.display = 'none';
            container.innerHTML = '';
        });
    }

    closeModal() {
        const modal = document.querySelector('.auth-modal');
        modal.classList.remove('active');
        
        // Simply remove the class from body
        document.body.classList.remove('modal-open');
        
        // Remove the property if it was set for some reason
        document.documentElement.style.removeProperty('--scrollbar-width');
        
        // We don't need to store any user preferences since we want
        // the modal to show on every page load
    }

    switchTab(tab) {
        // Remove active class from all tabs first
        document.querySelectorAll('.auth-tab').forEach(t => {
            t.classList.remove('active');
        });

        // Add active class to selected tab
        const selectedTab = document.querySelector(`.auth-tab[data-tab="${tab}"]`);
        if (selectedTab) {
            selectedTab.classList.add('active');
        }

        // Hide all content sections first
        document.querySelectorAll('.auth-content > div').forEach(content => {
            content.style.display = 'none';
            content.classList.remove('active');
            content.style.position = 'absolute';
        });

        // Show selected content
        const activeContent = document.querySelector(`.auth-${tab}-content`);
        if (activeContent) {
            activeContent.style.display = 'block';
            activeContent.classList.add('active');
            activeContent.style.position = 'relative';
        }

        // Re-initialize Gravity Forms if switching to register tab
        if (tab === 'register') {
            this.initializeGravityForms();
        }
    }

    setupAutoShowModal() {
        // Only run for logged-out users
        if (document.body.classList.contains('logged-in')) {
            return;
        }
        
        // Don't show on client-area pages
        if (window.location.pathname.includes('client-area')) {
            return;
        }
        
        // For debugging - log values to console
        console.log('Auto-show modal check:');
        console.log('- Modal already shown this instance:', this.modalShown);
        console.log('- Is on client area:', window.location.pathname.includes('client-area'));
        console.log('- Is logged in:', document.body.classList.contains('logged-in'));
        
        // Wait 10 seconds before showing modal
        setTimeout(() => {
            // Double check conditions
            if (!this.modalShown && 
                !document.body.classList.contains('logged-in') && 
                !window.location.pathname.includes('client-area')) {
                
                console.log('Showing auto modal');
                this.openModal('login');
                this.modalShown = true;
            }
        }, 15000);
    }

    // New method to update client area forms
    updateClientAreaForms(tab) {
        const loginSection = document.getElementById('client-login-section');
        const registerSection = document.getElementById('client-register-section');
        
        if (!loginSection || !registerSection) return;
        
        if (tab === 'login') {
            registerSection.style.display = 'none';
            loginSection.style.display = 'block';
        } else if (tab === 'register') {
            loginSection.style.display = 'none';
            registerSection.style.display = 'block';
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.authModal = new AuthModal();
    
    // Check for URL hash and handle initial form display
    if (window.location.hash === '#register') {
        const loginSection = document.getElementById('client-login-section');
        const registerSection = document.getElementById('client-register-section');
        
        if (loginSection && registerSection) {
            loginSection.style.display = 'none';
            registerSection.style.display = 'block';
        }
    }
    
    // Add test button for debugging (temporary)
    if (window.location.pathname.includes('client-area')) {
        const testButton = document.createElement('button');
        testButton.textContent = 'Test AJAX';
        testButton.style.cssText = 'position: fixed; top: 10px; right: 10px; z-index: 9999; background: red; color: white; padding: 10px;';
        testButton.onclick = function() {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', cwAuth.ajaxurl, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
            xhr.onload = function() {
                console.log('Test AJAX Response:', xhr.status, xhr.responseText);
                alert('Test AJAX Response: ' + xhr.status + ' - ' + xhr.responseText);
            };
            xhr.send('action=test_ajax');
        };
        document.body.appendChild(testButton);
    }
});


// client area 

document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(function(button) {
        button.addEventListener('click', function() {
            const input = this.closest('.input-group').querySelector('input');
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
    
    // Password validation for registration
    document.getElementById('register-form')?.addEventListener('submit', function(e) {
        const password = document.getElementById('user_pass_register').value;
        const confirmPassword = document.getElementById('user_pass_confirm').value;
        
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match');
        }
    });
    
    // Client area login form specific enhancements
    const clientPasswordToggle = document.getElementById('password-toggle');
    const clientPasswordField = document.getElementById('client_user_pass');
    
    if (clientPasswordToggle && clientPasswordField) {
        // Password visibility toggle for client login form
        clientPasswordToggle.addEventListener('click', function() {
            const type = clientPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
            clientPasswordField.setAttribute('type', type);
            
            // Toggle icon
            const icon = clientPasswordToggle.querySelector('i');
            if (type === 'password') {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        });
    }
    
    // Add focus effect on input fields
    const clientLoginInputs = document.querySelectorAll('#client_login_form .input');
    if (clientLoginInputs.length > 0) {
        clientLoginInputs.forEach(input => {
            // Add focus animation
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('input-focused');
            });
            
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('input-focused');
                }
            });
            
            // Initialize with class if input has value
            if (input.value) {
                input.parentElement.classList.add('input-focused');
            }
        });
    }
    
    // Enhance form submission with visual feedback
    const clientLoginForm = document.getElementById('client_login_form');
    if (clientLoginForm) {
        clientLoginForm.addEventListener('submit', function(e) {
            const submitButton = document.getElementById('client_wp-submit');
            if (submitButton) {
                submitButton.value = 'Signing in...';
                submitButton.classList.add('submitting');
            }
            
            // Form validation will be handled by the browser's native validation
            // or by WordPress on the server side
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const loginSection = document.getElementById('client-login-section');
    const registerSection = document.getElementById('client-register-section');
    const switchToRegisterLinks = document.querySelectorAll('.switch-to-register');
    const switchToLoginLinks = document.querySelectorAll('.switch-to-login');
    
    // If there's a hash in URL for registration, show registration form
    if (window.location.hash === '#register') {
        if (loginSection && registerSection) {
            loginSection.style.display = 'none';
            registerSection.style.display = 'block';
        }
    }
    
    // NOTE: The click event handlers for switching forms are now handled by the AuthModal class
    // We keep this code minimal to avoid conflicts
});

