define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'secondhandcar/secondvehicleinformation/index',
                    add_url: 'secondhandcar/secondvehicleinformation/add',
                    edit_url: 'secondhandcar/secondvehicleinformation/edit',
                    del_url: 'secondhandcar/secondvehicleinformation/del',
                    multi_url: 'secondhandcar/secondvehicleinformation/multi',
                    table: 'secondcar_rental_models_info',
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
                        // {field: 'sales_id', title: __('Sales_id')},
                        {field: 'licenseplatenumber', title: __('Licenseplatenumber')},
                        // {field: 'models_id', title: __('Models_id')},
                        {field: 'models.name', title: __('Models.name')},
                        {field: 'kilometres', title: __('Kilometres'), operate:'BETWEEN'},
                        {field: 'companyaccount', title: __('Companyaccount')},
                        {field: 'newpayment', title: __('Newpayment')},
                        {field: 'monthlypaymen', title: __('Monthlypaymen')},
                        {field: 'periods', title: __('Periods')},
                        {field: 'totalprices', title: __('Totalprices')},
                        {field: 'drivinglicenseimages', title: __('Drivinglicenseimages'), formatter: Table.api.formatter.images},
                        {field: 'vin', title: __('Vin')},
                        {field: 'expirydate', title: __('Expirydate'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'annualverificationdate', title: __('Annualverificationdate'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'carcolor', title: __('Carcolor')},
                        {field: 'aeratedcard', title: __('Aeratedcard')},
                        {field: 'volumekeys', title: __('Volumekeys')},
                        {field: 'Parkingposition', title: __('Parkingposition')},
                        {field: 'shelfismenu', title: __('Shelfismenu'), formatter: Table.api.formatter.toggle},
                        // {field: 'shelf_text', title: __('Shelf'), operate:false},
                        {field: 'vehiclestate', title: __('Vehiclestate')},
                        {field: 'note', title: __('Note')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'models.name', title: __('Models.name')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, 
                        buttons: [
                            {name: 'detail', text: '销售预定', title: '销售预定', icon: 'fa fa-share', classname: 'btn btn-xs btn-info btn-dialog btn-newCustomer', url: 'secondhandcar/Secondvehicleinformation/salesbook',
                                success:function(data, ret){
                                }, 
                                error:function(data,ret){

                                }
                            }
                        ],
                        events: Table.api.events.operate,formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
         //销售预定
         salesbook:function(){
 
            // $(".btn-add").data("area", ["300px","200px"]);
            Table.api.init({
               
            });
            Form.api.bindevent($("form[role=form]"), function(data, ret){
                //这里是表单提交处理成功后的回调函数，接收来自php的返回数据
                Fast.api.close(data);//这里是重点
                // console.log(data);
                // Toastr.success("成功");//这个可有可无
            }, function(data, ret){
                // console.log(data); 
                Toastr.success("失败"); 
            });
            // Controller.api.bindevent();
            // console.log(Config.id);
 
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                $(document).on('click', "input[name='row[shelfismenu]']", function () {
                    var name = $("input[name='row[name]']");
                    name.prop("placeholder", $(this).val() == 1 ? name.data("placeholder-menu") : name.data("placeholder-node"));
                });
                $("input[name='row[shelfismenu]']:checked").trigger("click");
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});