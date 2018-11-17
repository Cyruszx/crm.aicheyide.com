define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'cms/coupon/index',
                    add_url: 'cms/coupon/add',
                    edit_url: 'cms/coupon/edit',
                    del_url: 'cms/coupon/del',
                    multi_url: 'cms/coupon/multi',
                    table: 'cms_coupon',
                }
            });

            var table = $("#table");

            table.on('load-success.bs.table', function (e, data) {

                $(".btn-add").data("area", ["80%", "80%"]);
                $(".btn-editone").data("area", ["80%", "80%"]);

            });


            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'coupon_name', title: __('Coupon_name')},
                        {field: 'coupon_amount', title: __('优惠卷金额')},
                        {field: 'circulation', title: __('Circulation')},
                        {field: 'City_ids', title: __('City_ids'),formatter:function (v,r,i) {
                            return '<strong class="text-success">'+ r.cities_name+'</strong>';
                        }},
                        {field: 'display_diagramimages', title: __('Display_diagramimages'), formatter: Table.api.formatter.images},
                        {field: 'threshold', title: __('Threshold')},
                        {field: 'models_ids', title: __('Models_ids'),table: table, buttons: [
                            {
                                name: 'details', text: '查看可用车型', title: '查看可用车型', icon: 'fa fa-eye', classname: 'btn btn-xs btn-primary btn-dialog btn-details',
                                url: 'cms/coupon/details', 
                                hidden:function(row){
                                    if(row.models_ids !== ''){ 
                                        return false; 
                                    }  
                                    else if(row.models_ids == ''){
                                        return true;
                                    }
                                }
                            },
                            {
                                name: 'null',text: '所有车型皆可用',title:'所有车型皆可用',icon: 'fa fa-eye-slash',classname: 'btn btn-xs btn-danger btn-primary',
                                hidden:function(row){  /**所有车型皆可用 */
                                    if(row.models_ids == ''){ 
                                        return false; 
                                    }
                                    else if(row.models_ids !== ''){
                                        return true;
                                    }
                                }
                            },
                        ],

                        operate: false, formatter: Table.api.formatter.buttons},
                        {field: 'membership_grade', title: __('Membership_grade')},
                        {field: 'release_datetime', title: __('Release_datetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'validity_datetime', title: __('Validity_datetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'limit_collar', title: __('Limit_collar')},
                        {field: 'ismenu', title: __('是否上架'),formatter: Controller.api.formatter.toggle,searchList:{"1":"是","0":"否"},},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
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
            bindevent: function (value, row, index) {
                //是否上架
                $(document).on('click', "input[name='row[ismenu]']", function () {
                    var name = $("input[name='row[name]']");
                    name.prop("placeholder", $(this).val() == 1 ? name.data("placeholder-menu") : name.data("placeholder-node"));
                });
                $("input[name='row[ismenu]']:checked").trigger("click");
            
                Form.api.bindevent($("form[role=form]"));
            },
            events: {
                operate: {
                    'click .btn-editone': function (e, value, row, index) {
                        e.stopPropagation();
                        e.preventDefault();
                        var table = $(this).closest('table');
                        var options = table.bootstrapTable('getOptions');
                        var ids = row[options.pk];
                        row = $.extend({}, row ? row : {}, {ids: ids});
                        var url = options.extend.edit_url;
                        Fast.api.open(Table.api.replaceurl(url, row, table), __('Edit'), $(this).data() || {});
                    },
                    
                },
            },
            formatter: {
                operate: function (value, row, index) {
                    var table = this.table;
                    // 操作配置
                    var options = table ? table.bootstrapTable('getOptions') : {};
                    // 默认按钮组
                    var buttons = $.extend([], this.buttons || []);
                    
                    return Table.api.buttonlink(this, buttons, value, row, index, 'operate');
                },
                /**
                 * 是否
                 * @param value
                 * @param row
                 * @param index
                 * @returns {string}
                 */
                //上架
                toggle: function (value, row, index) {

                    var color = typeof this.color !== 'undefined' ? this.color : 'success';
                    var yes = typeof this.yes !== 'undefined' ? this.yes : 1;
                    var no = typeof this.no !== 'undefined' ? this.no : 0;
                    return "<a href='javascript:;' data-toggle='tooltip' title='" + __('Click to toggle') + "' class='btn-change' data-id='"
                            + row.id + "' data-params='" + this.field + "=" + (value ? no : yes) + "'><i class='fa fa-toggle-on " + (value == yes ? 'text-' + color : 'fa-flip-horizontal text-gray') + " fa-2x'></i></a>";
                    

                }

            },
            
        }
    };
    return Controller;
});