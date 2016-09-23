/**
 * Created by Timko on 12.08.14.
 */
var mousewheelevt=(/Firefox/i.test(navigator.userAgent))? "DOMMouseScroll" : "mousewheel" //FF doesn't recognize mousewheel as of FF3.x

$(function(){
    $('#play_pause').on('click', function(){
        var me = $(this),
            player = $('#player');
        me.toggleClass('paused');
        if(me.hasClass('paused')){
            player[0].pause();
        }else{
            player[0].play();
        }
    });
    $('a[href^="#dream"]').on('click', function(event){
        var link = $(this),
            dBlock = link.parent();
        event.preventDefault();
        $('a[href^="#dream"]').show();
        $('.shorten').show();
        $('.full').hide();
        link.hide();
        dBlock.find('.shorten').hide();
        dBlock.find('.full').show();
    });
    $('a.delete_img').on('click', function(event){
        var title = $(this).parent().find('h4');
        if(!confirm('Вы уверены, что хотите удалить "'+title.text()+'"?')){
            event.preventDefault();
        }
    });
    $('#seo_text').appendTo("#wrap");
});