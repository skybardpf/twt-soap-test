/**
 *  @global {Array} runningServiceTests
 */
$(document).ready(function(){
    var CONST_TEST_STATUS_OK = 1;
    var CONST_TEST_STATUS_DEFAULT = 2;
    var CONST_TEST_STATUS_ERROR = 3;
    var CONST_TEST_STATUS_RUN = 5;

    $('.icon-play-circle').on('click', eventClickRun);

    if (window.runningServiceTests !== undefined && typeof window.runningServiceTests === 'object' && window.runningServiceTests.length > 0){
        pollingRunTest();
    }

    /**
     * Обработчик нажатия на кнопку "Запуск теста".
     */
    function eventClickRun(){
        var icon = $(this);
        var tr = icon.parents('tr');
        var id = tr.data('service-id');

        cssIconRun(icon, true);
        cssTestStatus(tr, CONST_TEST_STATUS_RUN, id);
        tr.find('.td-test-result-text').text('Выполняется');
        tr.find('.td-runtime').text('---');
        tr.find('.td-date-start').text('---');
        tr.find('.td-last-return').text('---');

        runTest(id, function(ret, message, data){
            if (!ret){
                cssIconRun(icon, false);
                cssTestStatus(tr, CONST_TEST_STATUS_DEFAULT, id);
                alert(message || 'Не удалось запустить тесты. Попробуйте позже.');
            } else {
                updateRowData(id, data);
            }
        });
        return false;
    }

    /**
     * Перезапускаем тесты, при перезагрузке страницы.
     */
    function pollingRunTest(){
        var timer = null;
        $.ajax({
            type: 'POST',
            dataType: "json",
            url: '/service/polling_run_tests/ids/'+window.runningServiceTests
        }).done(function(data, success) {
                if (success == 'success'){
                    for (var i= 0, l=data.length; i<l; i++){
                        var ind = window.runningServiceTests.indexOf(data[i]['id']);
                        if (ind !== -1){
                            window.runningServiceTests.splice(ind, 1);
                        }
                        updateRowData(data[i]['id'], data[i]);
                    }
                }
                if (window.runningServiceTests.length > 0){
                    if(timer === null){
                        timer = setTimeout(pollingRunTest,5000);
                    }
                } else if(timer !== null){
                    clearTimeout(timer);
                }
            });
    }

    /**
     * Обновляем данные в строке таблицы.
     * @param {Number} service_id
     * @param {Array} data
     */
    function updateRowData(service_id, data){
        var tr = $('#grid-list-services .row-service-id-'+service_id);
        if (!tr){
            return;
        }
        var icon = tr.find('.icon-play-circle');
        cssIconRun(icon, false);
        cssTestStatus(tr, data.test_result, service_id);

        tr.find('.td-test-result-text').text(data.test_result_text);
        tr.find('.td-runtime').text(data.runtime + ' сек.');
        tr.find('.td-date-start').text(data.date_start);
        tr.find('.td-last-return').text(data.last_return);
    }

    function cssIconRun(icon, run){
//        var title = icon.parents('a');
        if (run){
//            icon.off('click');
//            icon.removeClass();
            icon.addClass('hide');

//            title.data('original-title', 'Тест запущен');
        } else {
            icon.removeClass('hide');
//            icon.addClass('icon-play-circle');
//            title.data('original-title', 'Запуск тест');
//            icon.on('click', eventClickRun);
        }
    }

    /**
     * Помечаем цветом строку с тестом, в зависимости от его статуса.
     * @param {jQuery} tr
     * @param {Number} status
     * @param {Number} service_id
     * @return void
     */
    function cssTestStatus(tr, status, service_id){
        var cl = 'warning';
        if (status == CONST_TEST_STATUS_RUN){ // run
            cl = 'info';
        } else if (status == CONST_TEST_STATUS_OK){
            cl = 'success';
        } else if (status == CONST_TEST_STATUS_ERROR){
            cl = 'error';
        }
        tr.removeClass();
        tr.addClass(cl);
        tr.addClass('row-service-id-'+service_id);
    }

    /**
     * Запускаем тест на выполнение.
     * @param {Number} service_id
     * @param {Function} callback
     * @return void
     */
    function runTest(service_id, callback){
        if (service_id === undefined || service_id <= 0){
            callback(false, 'Не указан идентификатор функции.');
        } else {
            $.ajax({
                dataType: "json",
                url: '/service/run_tests/id/'+service_id
            }).done(function(data, success) {
                if (success == 'success' && data.success){
                    callback(true, '', data.data);
                } else {
                    callback(false, data.message);
                }
            }).fail(function(err){
                if (err.status == 504){
                    runningServiceTests.push(String(service_id));
                    pollingRunTest();
                }
//                callback(false, 'Статус: ' + err.status + '. Сообщение: ' + err.responseText);
            });
        }
    }
});