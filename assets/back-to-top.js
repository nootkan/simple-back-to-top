jQuery(document).ready(function($) {
    
    // Get scroll offset from PHP (passed via wp_localize_script would be ideal, but using default for simplicity)
    var scrollOffset = 300; // This should ideally come from the admin settings
    var $backToTop = $('#back-to-top');
    var isVisible = false;
    
    // Function to show/hide button based on scroll position
    function toggleButton() {
        var scrollTop = $(window).scrollTop();
        
        if (scrollTop > scrollOffset && !isVisible) {
            $backToTop.removeClass('hide').addClass('show').fadeIn(300);
            isVisible = true;
        } else if (scrollTop <= scrollOffset && isVisible) {
            $backToTop.removeClass('show').addClass('hide').fadeOut(300);
            isVisible = false;
        }
    }
    
    // Check scroll position on page load
    toggleButton();
    
    // Listen for scroll events
    $(window).on('scroll', function() {
        toggleButton();
    });
    
    // Handle click event for smooth scrolling to top
    $backToTop.on('click', function(e) {
        e.preventDefault();
        
        $('html, body').animate({
            scrollTop: 0
        }, {
            duration: 800,
            easing: 'swing'
        });
    });
    
    // Handle keyboard accessibility (Enter and Space keys)
    $backToTop.on('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            $(this).click();
        }
    });
    
    // Make button focusable for accessibility
    $backToTop.attr('tabindex', '0');
    $backToTop.attr('role', 'button');
    $backToTop.attr('aria-label', 'Back to top');
});