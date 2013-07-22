/**
 *  @var {Number} window.count_params
 *  @var {Number} window.count_children_params
 */
$(document).ready(function(){
//    var array_index = [];
    console.log(window.count_children_params);

    $('div.add-output-param ul li a').click(function(){
        var type_of_data = $(this).data('type-of-data');
        $.ajax({
            success: function(html){
                $('table.output-params').append(html);

                var b = $('button.del-output-param');
                b.off('click');
                b.on('click', del_param);

                b = $('.table.output-params div.add-array-value ul li a');
                b.off('click');
                b.on('click', click_add_output_child_param);
            },
            type: 'get',
            url: '/function/add_param_field',
            data: {
                type: type_of_data,
                index: window.count_params++,
                input_param: false,
                child: false
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

                b = $('.table.input-params div.add-array-value ul li a');
                b.off('click');
                b.on('click', click_add_input_child_param);
            },
            type: 'get',
            url: '/function/add_param_field',
            data: {
                type: type_of_data,
                index: window.count_params++,
                input_param: true,
                child: false
            },
            cache: false,
            dataType: 'html'
        });
        return false;
    });

    function click_add_input_child_param(){
        var type_of_data = $(this).data('type-of-data');
        var tr = $(this).parents('table.input-params tr');
        var index = $(tr).data('param-index');

//        if (array_index[index] === undefined){
//            array_index[index] = 0;
//        } else {
//            array_index[index]++;
//        }

        $.ajax({
            success: function(html){
                $('table.parent-param-'+index+' tbody').append(html);

                var b = $('button.del-input-child-param');
                b.off('click');
                b.on('click', del_param_child);
            },
            type: 'get',
            url: '/function/add_param_field',
            data: {
                type: type_of_data,
                index: index,
                input_param: true,
                child: true,
                child_index: window.count_children_params++
            },
            cache: false,
            dataType: 'html'
        });
        return false;
    }

    function click_add_output_child_param(){
        var type_of_data = $(this).data('type-of-data');
        var tr = $(this).parents('table.output-params tr');
        var index = $(tr).data('param-index');

//        if (array_index[index] === undefined){
//            array_index[index] = 0;
//        } else {
//            array_index[index]++;
//        }

        $.ajax({
            success: function(html){
                $('table.parent-param-'+index+' tbody').append(html);

                var b = $('button.del-output-child-param');
                b.off('click');
                b.on('click', del_param_child);
            },
            type: 'get',
            url: '/function/add_param_field',
            data: {
                type: type_of_data,
                index: index,
                input_param: false,
                child: true,
                child_index: window.count_children_params++
            },
            cache: false,
            dataType: 'html'
        });
        return false;
    }

    $('button.del-output-param').on('click', del_param);
    $('button.del-input-param').on('click', del_param);

    $('button.del-input-child-param').on('click', del_param_child);
    $('button.del-output-child-param').on('click', del_param_child);

    $('.table.input-params .add-array-value ul li a').on('click', click_add_input_child_param);
    $('.table.output-params .add-array-value ul li a').on('click', click_add_output_child_param);

    function del_param(){
        var tr = $(this).parents('tr');
        var index = $(tr).data('param-index');
        tr.remove();
        $('tr.child-params-'+index).remove();
    }

    function del_param_child(){
        var index = $(this).data('child-index');
        $('tr.tr-child-index-'+index).remove();
    }
});