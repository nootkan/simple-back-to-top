// Admin JavaScript for Back to Top Plugin Settings
jQuery(document).ready(function($) {
    var currentTarget = null;
    
    // Handle symbol button clicks
    $('.symbol-btn').on('click', function(e) {
        e.preventDefault();
        var symbol = $(this).data('symbol');
        $('input[name="sbt_button_text"]').val(symbol);
    });
    
    // Handle color preview clicks
    $('.color-preview').on('click', function() {
        currentTarget = $(this).data('target');
        var currentColor = $('input[name="' + currentTarget + '"]').val();
        $('#hex-input').val(currentColor);
        $('.color-swatch').removeClass('selected');
        $('.color-swatch[data-color="' + currentColor + '"]').addClass('selected');
        $('#simple-color-picker').show();
    });
    
    // Handle color swatch clicks
    $('.color-swatch').on('click', function() {
        var color = $(this).data('color');
        $('.color-swatch').removeClass('selected');
        $(this).addClass('selected');
        $('#hex-input').val(color);
    });
    
    // Handle hex input changes
    $('#hex-input').on('input', function() {
        var hex = $(this).val();
        if (hex.match(/^#[0-9A-F]{6}$/i)) {
            $('.color-swatch').removeClass('selected');
            $('.color-swatch[data-color="' + hex.toLowerCase() + '"]').addClass('selected');
        }
    });
    
    // Apply color
    $('#apply-color').on('click', function() {
        var color = $('#hex-input').val();
        if (color && currentTarget) {
            $('input[name="' + currentTarget + '"]').val(color);
            $('.color-preview[data-target="' + currentTarget + '"]').css('background-color', color);
        }
        $('#simple-color-picker').hide();
    });
    
    // Cancel color selection
    $('#cancel-color').on('click', function() {
        $('#simple-color-picker').hide();
    });
    
    // Close on overlay click
    $('.color-picker-overlay').on('click', function(e) {
        if (e.target === this) {
            $('#simple-color-picker').hide();
        }
    });
    
    // Update color field inputs when typed
    $('.color-field').on('input', function() {
        var color = $(this).val();
        var target = $(this).attr('name');
        if (color.match(/^#[0-9A-F]{6}$/i)) {
            $('.color-preview[data-target="' + target + '"]').css('background-color', color);
        }
    });
    
    // Handle shape selection changes
    $('#button-shape-select').on('change', function() {
        var shape = $(this).val();
        if (shape === 'custom') {
            $('#border-radius-control').show();
        } else {
            $('#border-radius-control').hide();
        }
    });
    
    // Handle border radius slider
    $('#border-radius-slider').on('input', function() {
        var value = $(this).val();
        $('#radius-value').text(value + '%');
    });
    
    // Handle button size slider
    $('#button-size-slider').on('input', function() {
        var value = $(this).val();
        $('#size-value').text(value + 'px');
    });
    
    // Handle shadow enable/disable
    $('#enable-shadow-checkbox').on('change', function() {
        if ($(this).is(':checked')) {
            $('#shadow-options').show();
        } else {
            $('#shadow-options').hide();
        }
    });
    
    // Handle shadow intensity slider
    $('#shadow-intensity-slider').on('input', function() {
        var value = $(this).val();
        $('#intensity-value').text(value + '%');
    });
    
    // Handle border enable/disable
    $('#enable-border-checkbox').on('change', function() {
        if ($(this).is(':checked')) {
            $('#border-options').show();
        } else {
            $('#border-options').hide();
        }
    });
    
    // Handle border width slider
    $('#border-width-slider').on('input', function() {
        var value = $(this).val();
        $('#border-width-value').text(value + 'px');
    });
});