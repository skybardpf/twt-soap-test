/**
 *  @global {Array} runningTests
 */
$(document).ready(function(){
    var CONST_TEST_STATUS_OK = 1;
    var CONST_TEST_STATUS_DEFAULT = 2;
    var CONST_TEST_STATUS_ERROR = 3;
    var CONST_TEST_STATUS_RUN = 5;

    $('.icon-play-circle').on('click', eventClickRun);

    if (runningTests !== undefined && typeof runningTests === 'object' && runningTests.length > 0){
        pollingRunTest();
    }

    /**
     * Обработчик нажатия на кнопку "Запуск теста".
     */
    function eventClickRun(){
        var icon = $(this);
        var tr = icon.parents('tr');
        var id = tr.data('test-id');

        cssIconRun(icon, true);
        cssTestStatus(tr, CONST_TEST_STATUS_RUN, id);
//        tr.find('.td-test-result').text('Запущен');
        tr.find('.td-runtime').text('---');
        tr.find('.td-date-start').text('---');
        tr.find('.td-last-return').text('---');

        runTest(id, function(ret, message, data){
            if (!ret){
                cssIconRun(icon, false);
                cssTestStatus(tr, CONST_TEST_STATUS_DEFAULT, id);
                alert(message || 'Не удалось запустить тест. Попробуйте позже.');
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
            url: '/test/polling_run_tests/ids/'+runningTests
        }).done(function(data, success) {
            if (success == 'success'){
                for (var i= 0, l=data.length; i<l; i++){
                    var ind = runningTests.indexOf(data[i]['id']);
                    if (ind !== -1){
                        runningTests.splice(ind, 1);
                    }
                    updateRowData(data[i]['id'], data[i]);
                }
            }
            if (runningTests.length > 0){
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
     * @param {Number} test_id
     * @param {Array} data
     */
    function updateRowData(test_id, data){
        var tr = $('#grid-list-tests .row-test-id-'+test_id);
        if (!tr){
            return;
        }
        var icon = tr.find('.icon-play-circle');
        cssIconRun(icon, false);
        cssTestStatus(tr, data.test_result, test_id);

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
     * @param {Number} test_id
     * @return void
     */
    function cssTestStatus(tr, status, test_id){
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
        tr.addClass('row-test-id-'+test_id);
    }

    /**
     * Запускаем тест на выполнение.
     * @param {Number} test_id
     * @param {Function} callback
     * @return void
     */
    function runTest(test_id, callback){
        if (test_id === undefined || test_id <= 0){
            callback(false, 'Не указан идентификатор теста.');
        } else {
            $.ajax({
                dataType: "json",
                url: '/test/run/id/'+test_id
            }).done(function(data, success) {
                if (success == 'success' && data.success){
                    callback(true, '', data.data);
                } else {
                    callback(false, data.message);
                }
            }).fail(function(err){
                if (err.status == 504){
                    runningTests.push(String(test_id));
                    pollingRunTest();
                }
//                callback(false, 'Статус: ' + err.status + '. Сообщение: ' + err.responseText);
            });
        }
    }
});