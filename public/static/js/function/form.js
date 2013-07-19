/**
 *  @var {Number} window.count_params
 */
$(document).ready(function(){
    $('div.add-output-param ul li a').click(function(){
        var type_of_data = $(this).data('type-of-data');
        $.ajax({
            success: function(html){
                $('table.output-params').append(html);

                var b = $('button.del-output-param');
                b.off('click');
                b.on('click', del_param);
            },
            type: 'get',
            url: '/function/add_param_field',
            data: {
                type: type_of_data,
                index: window.count_params++,
                input_param: false
            },
            cache: false,
            dataType: 'html'
        });
        return false;
    });

    $('div.add-input-param ul li a').click(function(){
        var type_of_data = $(this).data('type-of-data');
        $.ajax({
            success: function(html){
                $('table.input-params').append(html);

                var b = $('button.del-input-param');
                b.off('click');
                b.on('click', del_param);
            },
            type: 'get',
            url: '/function/add_param_field',
            data: {
                type: type_of_data,
                index: window.count_params++,
                input_param: true
            },
            cache: false,
            dataType: 'html'
        });
        return false;
    });


    $('button.del-output-param').on('click', del_param);
    $('button.del-input-param').on('click', del_param);

    function del_param(){
        console.log($(this));
        var tr = $(this).parents('tr');
        tr.remove();
    }
});