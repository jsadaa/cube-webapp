// Home carousel
$(document).ready(function () {
    bulmaCarousel.attach('#carousel', {
        slidesToScroll: 1,
        slidesToShow: 1,
        loop: true,
        infinite: true,
        autoplay: true,
        duration: 1000,
        pagination: false,
        autoplaySpeed: 5000,
    });
});

// Catalog filter
$('#filter-button').click(function() {
    var appellation = $('#appellation option:selected:not(:disabled)').val() || '';
    var cepage = $('#cepage option:selected:not(:disabled)').val() || '';
    var region = $('#region option:selected:not(:disabled)').val() || '';
    var famille = $('#famille option:selected:not(:disabled)').val() || '';
    var fournisseur = $('#fournisseur option:selected:not(:disabled)').val() || '';

    $('.card').each(function() {
        var appellationMatch = $(this).find('.appellation .value').text().trim() === appellation || appellation === 'Tous' || appellation === '';
        var cepageMatch = $(this).find('.cepage .value').text().trim() === cepage || cepage === 'Tous' || cepage === '';
        var regionMatch = $(this).find('.region .value').text().trim() === region || region === 'Tous' || region === '';
        var familleMatch = $(this).find('.famille .value').text().trim() === famille || famille === 'Tous' || famille === '';
        var fournisseurMatch = $(this).find('.fournisseur .value').text().trim() === fournisseur || fournisseur === 'Tous' || fournisseur === '';

        if (appellationMatch && cepageMatch && regionMatch && familleMatch && fournisseurMatch) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
});

// Catalog filter reset
$('#reset-button').click(function() {
    $('#appellation').val($('#appellation option:disabled').val());
    $('#cepage').val($('#cepage option:disabled').val());
    $('#region').val($('#region option:disabled').val());
    $('#famille').val($('#famille option:disabled').val());
    $('#fournisseur').val($('#fournisseur option:disabled').val());
    $('.card').show();
});

// error notification
$(document).ready(function () {
    $('.notification .delete').click(function() {
        $(this).parent().hide();
    });
});

// Panier payment method
$('#moyen-paiement').change(function() {
    if ($(this).val() === 'reception') {
        $('.paiement-field').addClass('is-hidden');
        $('.paiement-field input').removeAttr('required').attr('disabled', 'disabled');
    } else {
        $('.paiement-field').removeClass('is-hidden');
        $('.paiement-field input').attr('required', 'required').removeAttr('disabled');
    }
});

// theme switcher
$('#dark-mode').click(function() {
    document.documentElement.setAttribute('data-theme', document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
    // change #theme-button content conditionally
    $('#theme-button').text(document.documentElement.getAttribute('data-theme') === 'dark' ? 'light_mode' : 'dark_mode');
    localStorage.setItem('theme', document.documentElement.getAttribute('data-theme'));
});

$(document).ready(function() {
    if (localStorage.getItem('theme') === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
        $('#theme-button').text('light_mode');
    } else {
        document.documentElement.setAttribute('data-theme', 'light');
        $('#theme-button').text('dark_mode');
    }
});