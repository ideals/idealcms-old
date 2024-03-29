// noinspection JSJQueryEfficiency,JSUnresolvedFunction

$(document).ready(function () {
    const addonField = getAddonFieldName();
    // Получаем список подключенных к странице аддонов
    const addons = $.parseJSON($('#' + addonField).val());

    // Получаем список доступных для добавления аддонов
    // const available = $.parseJSON($('#available_addons').val());

    // Получаем количество табов на странице не являющихся аддонами
    // Табами не относящимися к аддонам считаем те табы у ссылок которых нет атрибута "id"
    const addonTabs = $('ul.nav-tabs li a[id]');
    const allTabs = $('ul.nav-tabs li a');
    const countNotAddonTabs = allTabs.length - addonTabs.length;

    // Строим список подключённых аддонов в html-виде
    let addonsHtml = '<div class="list-group">';
    if (addons != null) {
        for (let i = 0; i < addons.length; i++) {
            addonsHtml += '<div class="list-group-item"><div class="input-group"><span class="form-control">' + addons[i][2] + '</span>';
            addonsHtml += '<span class="input-group-btn remove-addon"><button class="btn btn-default" type="button">&times;</button></span></div></div>';
        }
    }
    addonsHtml += '</div>';

    // Отображаем список аддонов на странице
    $('#addonsList').html(addonsHtml).children().eq(0).sortable({
        stop: function sortDayaArray(event, ui)
        {
            const curr = $.parseJSON($('#' + addonField).val());
            const fr = ui.item.data("movedfrom"),
                to = ui.item.index();
            if (fr !== to) {
                const el = curr.splice(fr, 1)[0];
                curr.splice(to, 0, el);
                $('#' + addonField).val(JSON.stringify(curr));
                const tabsOffset = 2;
                const tabs = $("#tabs");
                const $el = tabs.children().eq(fr + tabsOffset).remove();
                $el.insertAfter(tabs.children().get(to + tabsOffset - 1));
            }
        },
        start: function (event, ui) {
            ui.item.data("movedfrom", ui.item.index());
        }
    }).disableSelection();

    // todo ручная сортировка списка аддонов
    // и её отражение в поле ввода addonField и в списке вкладок

    // todo удаление любого аддона
    // выдача предупреждения об удалении данных и отражение этого события в поле ввода

    // todo редактирование названия аддона для этого элемента
    // отражение в поле ввода addonField и в списке вкладок

    // Навешиваем событие на удаление аддонов на странице
    $("#addon-confirm-modal .btn.btn-primary").click(function () {
        const pos = +$('#addon-confirm-modal').data("related");
        if (pos < 0) {
            return;
        }
        $('#addonsList > .list-group').children().eq(pos).remove();
        const addonField = getAddonFieldName();
        const curr = $.parseJSON($('#' + addonField).val());
        curr.splice(pos, 1);
        $('#' + addonField).val(JSON.stringify(curr));
        const tabs = $("#tabs");
        const idDivContent = tabs.children().eq(pos + countNotAddonTabs).find("a").attr("href");

        if (tabs.children().eq(pos + countNotAddonTabs).hasClass('active')) {
            const idDivContentActive = tabs.children().eq(pos + countNotAddonTabs - 1).find("a").attr("href");
            tabs.children().eq(pos + countNotAddonTabs - 1).addClass("active");
            $(idDivContentActive).addClass("active");
        }

        tabs.children().eq(pos + countNotAddonTabs).remove();
        $(idDivContent).remove();
    });

    // Навешиваем событие на список аддонов на странице
    $('#addonsList').click(function (e) {
        const $b = $(e.target).closest('span.input-group-btn.remove-addon > button');
        if ($b.length) {
            $('#addon-confirm-modal').data("related", $b.parents(".list-group-item").index()).modal({
                show: true,
                data: {item: $b}
            });
        }
    });

    // Навешиваем событие на кнопку для отображения поля ввода для выбора аддона для добавления
    $('#add-addon-button').click(function () {
        $(this).toggle();
        $('#add-addon').toggleClass('hide');
        $('#addonsList').toggleClass('full-form');
    });

    // Навешиваем событие на кнопку добавления аддона после выбора из select
    $('#add-addon-add').click(function () {
        const addonName = $('select#add-addon-select').val();
        const addonField = getAddonFieldName();
        const addons = $.parseJSON($('#' + addonField).val()); // список подключенных к странице аддонов

        // Ищем максимальный ID
        let maxId = 0;
        if (addons != null) {
            for (let i = 0; i < addons.length; i++) {
                maxId = (addons[i][0] > maxId) ? addons[i][0] : maxId;
            }
        }
        const newId = Number(maxId); // - 0 нужен для приведения типа maxId

        // Переданные параметры нужно записать в глобальную переменную idObject
        //window.idObject['action'] = action;

        // Пытаемся получить заголовок и содержимое новой вкладки
        // В случае удачи — добавляем новую вкладку
        $.get(
            "",
            {
                mode: 'ajax',
                controller: '\\Ideal\\Field\\Addon',
                action: 'add',
                par: window.idObject.par,
                id: window.idObject.id,
                addonName: addonName,
                addonField: addonField,
                groupName: 'general', // todo могут ли быть аддоны в аддонах или вложенных вкладках
                newId: newId
            },
            onAddNewTab,
            "json"
        );
    });

    // Навешиваем событие на кнопку отмены добавления аддона
    $('#add-addon-hide').click(function () {
        $('#add-addon-button').toggle();
        $('#add-addon').toggleClass('hide');
        $('#addonsList').toggleClass('full-form');
    });

    // Добавление новой вкладки ко вкладкам редактирования элемента
    function onAddNewTab(data)
    {
        // Скрываем поле добавления вкладки
        $('#add-addon-button').toggle();
        $('#add-addon').toggleClass('hide');
        $('#addonsList').toggleClass('full-form');

        // Добавляем в список вкладок для редактирования
        $('div#addonsList > div.list-group')
            .append('<div class="list-group-item"><div class="input-group"><span class="form-control">' + data.name +
                '</span><span class="input-group-btn remove-addon"><button class="btn btn-default" type="button">&times;</button></span></div></div>');

        // Добавляем вкладку к списку вкладок
        $('#tabs').append(data.header);

        // Добавляем собственно само содержимое вкладок
        $('#tabs-content').append(data.content);

        // Записываем в поле аддона новый список элементов
        const addonField = getAddonFieldName();
        let curr = $.parseJSON($('#' + addonField).val());
        if (curr == null) {
            curr = [];
        }
        const dataList = $.parseJSON(data.list);
        curr = curr.concat(dataList);
        curr = JSON.stringify(curr);
        $('#' + addonField).val(curr);
    }
});
