jQuery.fn.center = function () {
    this.css("top", (($(window).height() - this.outerHeight()) / 2) + $(window).scrollTop() + "px");
    this.css("left", (($(window).width() - this.outerWidth()) / 2) + $(window).scrollLeft() + "px");
    return this;
}
var popup = function(content){
    this.content = content;
    this.show = function(){
        var popupBlock = $('#popup');
        if(!popupBlock.length){
            popupBlock = $('<div id="popup"><span class="popup_close">x</span><div class="popup_content"></div></div>');
            $('body').append(popupBlock);
            popupBlock = $('#popup');
            $('.popup_close').on('click', this.hide);
        }
        popupBlock.find('.popup_content').html(this.content);
        popupBlock.center().show();
    }
    this.hide = function(){
        var popupBlock = $('#popup');
        if(popupBlock.length){
            popupBlock.hide();
        }
    }
}
jQuery.fn.getSelectedText = function(){
    var txt = '';
    if(window.getSelection)
        txt = window.getSelection().toString();
    else if(document.getSelection)
        txt = document.getSelection();
    else if(document.selection)
        txt = document.selection.createRange().text;
    if(txt != '' && this.text().indexOf(txt.replace(/\n/g,'')) >= 0){
        return txt;
    }
    return '';
}
$(function(){
    $('.popup_link').on('click', function(event){
        var link = $(this);
        event.preventDefault();
        $.ajax({
            url: this.href,
            type: 'post',
            data: {ReturnUrl: window.location.pathname},
            success: function(content){
                content = $(content);
                if(link.hasClass('quote_img')){
                    var parent = link.parent(),
                        txt = parent.getSelectedText();
                    if(txt == ''){
                        txt = parent.text();
                    }
                    var quote = '[q]'+$.trim(txt)+'[/q]';
                    content.find('textarea').val(quote);
                }
                var pop_up = new popup(content);
                pop_up.show();
            }
        });
    });
    $(document).on(mousewheelevt, function(){
        var pop_up = $('#popup');
        if(pop_up.length && pop_up.is(':visible')){
            pop_up.center();
        }
    });
});