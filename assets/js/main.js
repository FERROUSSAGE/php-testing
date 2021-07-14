$(document).ready(function(){
    $('.content-output').find('div:first').show();

    $('.pagination a').on('click', function(){
        if( $(this).attr('class') == 'nav-active') return false;

        let link = $(this).attr('href');
        let prevActive = $('.pagination > a.nav-active').attr('href');

        $('.pagination > a.nav-active').removeClass('nav-active');
        $(this).addClass('nav-active');

        $(prevActive).fadeOut(200, function(){
            $(link).fadeIn(100);
        });

        return false;
    });

    $('#btn-close').click(function(){
        let test = +$('#id_test').text();
        let res = {'test':test};
        $('.output-question').each(function(){
            let id = $(this).data('id');
            res[id] = $('input[name=question-' + id +']:checked').val();
        });

        $.ajax({
            url: 'index.php',
            type: 'post',
            cache: false,
            data: res,
            success: function(result){
                $('.content').html(result).fadeIn(2000);
            },
            error: function(){
                console.log("Запрос отправлен на не существующую страницу!");
            }
        });
    });

});