function initVASSlider(id, initVal, initState, leftAnchor, rightAnchor) {
    $('#'+id).slider({
        value: initVal,
        min: 0,
        max: 100,
        step: 1,
        disabled: !initState,
        change: function(event, ui) {
            if ($("#nextpage").css('display') == 'none'){ 
                $("#nextpage").slideDown(); 
            }
            
            $('#'+id+'value').val(ui.value);
            $('#'+id+' .ui-slider-handle').show();
        }
    })
    .each(function() {
        var opt = $(this).data().uiSlider.options;
        var vals = opt.max - opt.min;
        
        for (var i = 0; i <= vals; i += 10) {
            
            if ((i > 0) && (i < vals)) {
                var postfix = "";
            } else {
                if (i == 0) {
                    var postfix = " "+leftAnchor;
                } else {
                    var postfix = " "+rightAnchor;
                }
            }
            
            var el = $('<label>' + (i + opt.min) + postfix + '</label>').css('left', (i/vals*100) + '%');
            $('#'+id).append(el);
        }
    });
}