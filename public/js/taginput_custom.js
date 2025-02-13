$('.tagsinput')
    .tokenfield({
        createTokensOnBlur: true,
    })
    .on('tokenfield:createtoken', function (e) {
        var data = e.attrs.value.split('|')
        e.attrs.value = data[1] || data[0]
        e.attrs.label = data[1] ? data[0] + ' (' + data[1] + ')' : data[0]
    })
    .on('tokenfield:createdtoken', function (e) {
        // Über-simplistic e-mail validation
        var re = /\S+@\S+\.\S+/
        var valid = re.test(e.attrs.value)
        if (!valid) {
            $(e.relatedTarget).addClass('invalid')
        }
    })
    .on('tokenfield:createtoken', function (event) {
        var existingTokens = $(this).tokenfield('getTokens');
        $.each(existingTokens, function (index, token) {
            if (token.value === event.attrs.value)
                event.preventDefault();
        });
    })
    .on('tokenfield:edittoken', function (e) {
        if (e.attrs.label !== e.attrs.value) {
            var label = e.attrs.label.split(' (')
            e.attrs.value = label[0] + '|' + e.attrs.value
        }
    });
