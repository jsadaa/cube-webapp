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

$('#filter-button').click(function() {
    var appellation = $('#appellation option:selected:not(:disabled)').val() || '';
    var cepage = $('#cepage option:selected:not(:disabled)').val() || '';
    var region = $('#region option:selected:not(:disabled)').val() || '';
    var famille = $('#famille option:selected:not(:disabled)').val() || '';
    var fournisseur = $('#fournisseur option:selected:not(:disabled)').val() || '';
    
    // now, for each element with class "card", we check if its chilren with classes "appellation", "cepage", "region", "famille" and "fournisseur" match the selected values (it could be "all", a specific value or an empty string)
    $('.card').each(function() {
        var appellationMatch = $(this).find('.appellation .value').text().trim() === appellation || appellation === 'Tous' || appellation === '';
        var cepageMatch = $(this).find('.cepage .value').text().trim() === cepage || cepage === 'Tous' || cepage === '';
        var regionMatch = $(this).find('.region .value').text().trim() === region || region === 'Tous' || region === '';
        var familleMatch = $(this).find('.famille .value').text().trim() === famille || famille === 'Tous' || famille === '';
        var fournisseurMatch = $(this).find('.fournisseur .value').text().trim() === fournisseur || fournisseur === 'Tous' || fournisseur === '';
        
        // if all the conditions are met, we show the card, otherwise we hide it
        if (appellationMatch && cepageMatch && regionMatch && familleMatch && fournisseurMatch) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
});

$('#reset-button').click(function() {
    $('#appellation').val($('#appellation option:disabled').val());
    $('#cepage').val($('#cepage option:disabled').val());
    $('#region').val($('#region option:disabled').val());
    $('#famille').val($('#famille option:disabled').val());
    $('#fournisseur').val($('#fournisseur option:disabled').val());
    $('.card').show();
});