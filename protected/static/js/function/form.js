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

                b = $('.table.input-params .add-array-value ul li a');
                b.off('click');
                b.on('click', click_child_add_param);
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

    /**
     * Добавить значение для массива.
     */
    function click_child_add_param(){
        var type_of_data = $(this).data('type-of-data');
//        var index = window.count_params;
//        window.count_params++;

        var tr = $(this).parents('table.input-params tr');
        var index = $(tr).data('param-index');
        console.log($(tr));
        console.log(index);

        $.ajax({
            success: function(html){
                $('table.parent-param-'+index).append(html);

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
    }

    $('button.del-output-param').on('click', del_param);
    $('button.del-input-param').on('click', del_param);

    function del_param(){
        console.log($(this));
        var tr = $(this).parents('tr');
        var index = $(tr).data('param-index');
//        $('tr.child-params-'+index).remove();
        tr.remove();
    }

    function del_param_child(){
        console.log($(this));
        console.log('del_param_child');
//        var tr = $(this).parents('tr');
//        var index = $(tr).data('param-index');
//        $('tr.child-params-'+index).remove();
//        tr.remove();
    }
});