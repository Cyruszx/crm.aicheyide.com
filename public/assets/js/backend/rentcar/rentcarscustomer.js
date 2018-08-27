define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'rentcar/rentcarscustomer/index',
                    table: 'rental_order',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'licenseplatenumber', title: __('车牌号')}, 
                        {field: 'order_no', title: __('订单编号')}, 
                        {field: 'createtime', title: __('订车时间'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'delivery_datetime', title: __('提车时间'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'models_name', title: __('租车型号')}, 
                        {field: 'sales_name', title: __('销售员')}, 

                        {field: 'username', title: __('客户姓名')}, 
                        {field: 'phone', title: __('手机号')},
                        {field: 'id_card', title: __('身份证号')},
                        
                        {field: 'genderdata', title: __('性别'), searchList: {"male":__('男'),"female":__('女')}, formatter: Table.api.formatter.normal},
                        {field: 'cash_pledge', title: __('押金（元）')},
                        {field: 'rental_price', title: __('租金（元）')},
                        {field: 'tenancy_term', title: __('租期（月）')},

                        {field: 'operate', title: __('Operate'), table: table, buttons: [
                            {name: 'rentalDetails', text: '查看详细资料', title: '查看订单详细资料' ,icon: 'fa fa-eye',classname: 'btn btn-xs btn-primary btn-dialog btn-rentalDetails', 
                                url: 'rentcar/rentcarscustomer/rentaldetails', callback:function(data){
                                    console.log(data)
                                }
                            } 
                            ],
                        
                            events: Table.api.events.operate,
                             
                            formatter: Table.api.formatter.operate
                           
                        }
                    ]
                ]
            });

            

            // 为表格绑定事件
            Table.api.bindevent(table);

            table.on('load-success.bs.table', function (e, data) {
                // console.log(data);
               
                $(".btn-rentalDetails").data("area", ["95%", "95%"]);
            })

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