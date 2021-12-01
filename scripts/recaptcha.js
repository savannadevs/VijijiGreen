var widgetId1;
var onloadCallback = function() {
    widgetId1 = grecaptcha.render('g-recaptcha-1', {
        'sitekey' : '-q'
    });
};