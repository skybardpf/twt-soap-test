/**
 *  @var {Number} window.count_params
 */
$(document).ready(function(){
    var table = $('table.params');

    $('button.add-param').on('click', function(){
        var name = 'SoapFunctionParam['+window.count_params+'][name]';
        var id = 'SoapFunctionParam_'+window.count_params+'_name';
        table.append(
            '<tr class="param-'+window.count_params+'">' +
                '<td><input name="'+name+'" id="'+id+'" type="text" value="name"></td>'+
                '<td>' +
                '<select name="SoapFunctionParam['+window.count_params+'][type]" id="SoapFunctionParam_'+window.count_params+'_type">'+
                '<option value="string" selected="selected">string</option>'+
                '<option value="integer">integer</option>'+
                '<option value="bool">bool</option>'+
                '<option value="array">array</option>'+
                '<option value="date">date</option>'+
                '</select>' +
                '</td>'+
                '<td><input name="SoapFunctionParam[country][description]" id="SoapFunctionParam_country_description" type="text"></td>'+
                '<td><button class="del-param btn btn-primary" name="yt'+window.count_params+'" type="button">Удалить</button></td>'+
            '</tr>'
        );
        $('tr.param-'+window.count_params+' button.del-param').on('click', del_param);
        window.count_params++;
    });

    $('button.del-param').on('click', del_param);

    function del_param(){
        var tr = $(this).parents('tr');
        tr.remove();
    }
});