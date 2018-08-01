define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'newcars/newnventory/index',
                    add_url: 'newcars/newnventory/add',
                    edit_url: 'newcars/newnventory/edit',
                    del_url: 'newcars/newnventory/del',
                    multi_url: 'newcars/newnventory/multi',
                    table: 'car_new_inventory',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                searchFormVisible: true,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        // {field: 'models_id', title: __('Models_id')},
                        {field: 'models.name', title: __('Models.name')},
                        {field: 'carnumber', title: __('Carnumber')},
                        {field: 'reservecar', title: __('Reservecar')},
                        {field: 'carprocess', title: __('Carprocess'), visible:false, searchList: {"1":__('Carprocess 1'),"0":__('Carprocess 0')}},
                        {field: 'carprocess_text', title: __('Carprocess'), operate:false},
                        {field: 'pledge', title: __('Pledge'), visible:false, searchList: {"1":__('Pledge 1'),"0":__('Pledge 0')}},
                        {field: 'pledge_text', title: __('Pledge'), operate:false},
                        {field: 'licensenumber', title: __('Licensenumber')},
                        {field: 'note', title: __('Note')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'models.id', title: __('Models.id')},
                        {field: 'models.brand_id', title: __('Models.brand_id')},
                        // {field: 'models.name', title: __('Models.name')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});