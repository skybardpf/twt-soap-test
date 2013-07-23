/**
 *  @var {Number} window.count_params
 *  @var {Number} window.count_children_params
 *  @var {Number} window.count_table_elements
 */
$(document).ready(function(){
    window.count_params = 1000;
    window.count_children_params = 10000;
    window.count_table_elements = 20000;

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

        $.ajax({
            success: function(html){
                $('table.parent-param-'+index+' tbody').append(html);

                var b = $('button.del-input-child-param');
                b.off('click');
                b.on('click', del_param_child);

                b = $('.table.input-params div.add-table-element-value ul li a');
                b.off('click');
                b.on('click', click_add_input_table_element);
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

        $.ajax({
            success: function(html){
                $('table.parent-param-'+index+' tbody').append(html);

                var b = $('button.del-output-child-param');
                b.off('click');
                b.on('click', del_param_child);

                b = $('.table.output-params div.add-table-element-value ul li a');
                b.off('click');
                b.on('click', click_add_output_table_element);
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

    function click_add_input_table_element(){
        var type_of_data = $(this).data('type-of-data');
        var tr = $(this).parents('table.input-params tr.child-table-element');
        var parent_index = $(tr).data('parent-index');
        var child_index = $(tr).data('child-index');

        $.ajax({
            success: function(html){
                $('table.table-element-param-'+parent_index+'-'+child_index+' tbody').append(html);

                var b = $('button.del-input-element-table');
                b.off('click');
                b.on('click', del_element_table);
            },
            type: 'get',
            url: '/function/add_element_table',
            data: {
                type: type_of_data,
                input_param: true,
                index: parent_index,
                child_index: child_index,
                element_index: window.count_table_elements++
            },
            cache: false,
            dataType: 'html'
        });
        return false;
    }

    function click_add_output_table_element(){
        var type_of_data = $(this).data('type-of-data');
        var tr = $(this).parents('table.output-params tr.child-table-element');
        var parent_index = $(tr).data('parent-index');
        var child_index = $(tr).data('child-index');

        $.ajax({
            success: function(html){
                $('table.table-element-param-'+parent_index+'-'+child_index+' tbody').append(html);

                var b = $('button.del-output-element-table');
                console.log(b);
                b.off('click');
                b.on('click', del_element_table);
            },
            type: 'get',
            url: '/function/add_element_table',
            data: {
                type: type_of_data,
                input_param: false,
                index: parent_index,
                child_index: child_index,
                element_index: window.count_table_elements++
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

    $('button.del-output-element-table').on('click', del_element_table);
    $('button.del-input-element-table').on('click', del_element_table);

    $('.table.input-params .add-array-value ul li a').on('click', click_add_input_child_param);
    $('.table.output-params .add-array-value ul li a').on('click', click_add_output_child_param);

    $('.table.input-params div.add-table-element-value ul li a').on('click', click_add_input_table_element);
    $('.table.output-params div.add-table-element-value ul li a').on('click', click_add_output_table_element);

    function del_param(){
        var tr = $(this).parents('tr');
        var index = $(tr).data('param-index');

//        $('tr.child-params-'+index).remove();
        console.log(tr.parents('table'));
        console.log(tr.parents('table').find('tr.child-params-'+index));

        tr.parents('table').find('tr.child-params-'+index).remove();
        tr.remove();
    }

    function del_param_child(){
        var index = $(this).data('child-index');
        $('tr.child-index-'+index).remove();
        $('tr.tr-child-index-'+index).remove();
    }

    function del_element_table(){
        console.log($(this));
        var index = $(this).data('element-table');
        $('tr.tr-element-table-index-'+index).remove();
    }
});