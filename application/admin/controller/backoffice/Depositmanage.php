<?php

namespace app\admin\controller\backoffice;

use app\common\controller\Backend;
use app\admin\model\PlanAcar;
use app\admin\model\Models;
use app\admin\model\SalesOrder;
use app\admin\model\FinancialPlatform;
use think\Db;
use think\Session;

/**
 * 客户定金
 *
 * @icon fa fa-circle-o
 */
class Depositmanage extends Backend
{

    /**
     * CustomerDownpayment模型对象
     * @var \app\admin\model\CustomerDownpayment
     */
    protected $model = null;
    protected $noNeedRight = ['index', 'new_car', 'used_car', 'rent_car', 'full_car', 'edit', 'edit_used', 'edit_rent', 'edit_full'];

    public function _initialize()
    {
        parent::_initialize();

    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


    /**
     * 查看
     */
    public function index()
    {


        return $this->view->fetch();
    }

    /**
     * 新车定金
     * @return string|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function new_car()
    {
        $current = $this->current_login();

        $this->model = model('SalesOrder');
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username', true);
            $total = $this->model
                ->with(['admin' => function ($query) {
                    $query->withField(['nickname', 'avatar']);
                }, 'models' => function ($query) {
                    $query->withField(['name']);
                }, 'planacar' => function ($query) {
                    $query->withField(['payment', 'monthly', 'nperlist', 'tail_section', 'gps']);
                }, 'customerdownpayment'])
                ->where(function ($query) use ($current) {
                    if(in_array($this->auth->id,$current['back'])){
                        $sales = $this->get_sales();
                        $query->where('admin_id','in',$sales);
                    }
                    $query->where('order_no', 'not null');
                })
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['admin' => function ($query) {
                    $query->withField(['nickname', 'avatar']);
                }, 'models' => function ($query) {
                    $query->withField(['name']);
                }, 'planacar' => function ($query) {
                    $query->withField(['payment', 'monthly', 'nperlist', 'tail_section', 'gps']);
                }, 'customerdownpayment'])
                ->where(function ($query) use ($current) {
                    if(in_array($this->auth->id,$current['back'])){
                        $sales = $this->get_sales();
                        $query->where('admin_id','in',$sales);
                    }
                    $query->where('order_no', 'not null');
                })
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();

            foreach ($list as $key => $value) {
                if ($value['city']) {
                    if ($value['detailed_address']) {
                        $list[$key]['city'] = $value['city'] . '/' . $value['detailed_address'];
                    }
                }

            }
            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 二手车
     * return string|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function used_car()
    {
        $current = $this->current_login();
        $this->model = model('SecondSalesOrder');
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username', true);
            $total = $this->model
                ->with(['admin' => function ($query) {
                    $query->withField(['nickname', 'avatar']);
                }, 'models' => function ($query) {
                    $query->withField(['name']);
                }, 'plansecond' => function ($query) {
                    $query->withField(['kilometres', 'newpayment', 'monthlypaymen', 'periods', 'totalprices', 'tailmoney']);
                }, 'customerdownpayment'])
                ->where(function ($query) use ($current) {
                    if(in_array($this->auth->id,$current['back'])){
                        $sales = $this->get_sales();
                        $query->where('admin_id','in',$sales);
                    }
                    $query->where('order_no', 'not null');
                })
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['admin' => function ($query) {
                    $query->withField(['nickname', 'avatar']);
                }, 'models' => function ($query) {
                    $query->withField(['name']);
                }, 'plansecond' => function ($query) {
                    $query->withField(['kilometres', 'newpayment', 'monthlypaymen', 'periods', 'totalprices', 'tailmoney']);
                }, 'customerdownpayment'])
                ->where(function ($query) use ($current) {
                    if(in_array($this->auth->id,$current['back'])){
                        $sales = $this->get_sales();
                        $query->where('admin_id','in',$sales);
                    }
                    $query->where('order_no', 'not null');
                })
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();

            foreach ($list as $key => $value) {
                if ($value['city']) {
                    if ($value['detailed_address']) {
                        $list[$key]['city'] = $value['city'] . '/' . $value['detailed_address'];
                    }
                }

            }
            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 租车定金
     * @return string|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function rent_car()
    {
        $current = $this->current_login();
        $this->model = model('RentalOrder');
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username', true);
            $total = $this->model
                ->with(['admin' => function ($query) {
                    $query->withField(['nickname', 'avatar']);
                }, 'models' => function ($query) {
                    $query->withField(['name']);
                }, 'customerdownpayment'])
                ->where(function ($query) use ($current) {
                    if(in_array($this->auth->id,$current['back'])){
                        $sales = $this->get_sales();
                        $query->where('admin_id','in',$sales);
                    }
                    $query->where('order_no', 'not null');
                })
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['admin' => function ($query) {
                    $query->withField(['nickname', 'avatar']);
                }, 'models' => function ($query) {
                    $query->withField(['name']);
                }, 'customerdownpayment'])
                ->where(function ($query) use ($current) {
                    if(in_array($this->auth->id,$current['back'])){
                        $sales = $this->get_sales();
                        $query->where('admin_id','in',$sales);
                    }
                    $query->where('order_no', 'not null');
                })
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();

//            foreach ($list as $key => $value) {
//                if ($value['city']) {
//                    if ($value['detailed_address']) {
//                        $list[$key]['city'] = $value['city'] . '/' . $value['detailed_address'];
//                    }
//                }
//
//            }
            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 全款车定金
     * @return string|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function full_car()
    {
        $current = $this->current_login();

        $this->model = model('FullParmentOrder');
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username', true);
            $total = $this->model
                ->with(['admin' => function ($query) {
                    $query->withField(['nickname', 'avatar']);
                }, 'models' => function ($query) {
                    $query->withField(['name']);
                }, 'planfull' => function ($query) {
                    $query->withField(['full_total_price']);
                }, 'customerdownpayment'])
                ->where(function ($query) use ($current) {
                    if(in_array($this->auth->id,$current['back'])){
                        $sales = $this->get_sales();
                        $query->where('admin_id','in',$sales);
                    }
                    $query->where('order_no', 'not null');
                })
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['admin' => function ($query) {
                    $query->withField(['nickname', 'avatar']);
                }, 'models' => function ($query) {
                    $query->withField(['name']);
                }, 'planfull' => function ($query) {
                    $query->withField(['full_total_price']);
                }, 'customerdownpayment'])
                ->where(function ($query) use ($current) {
                    if(in_array($this->auth->id,$current['back'])){
                        $sales = $this->get_sales();
                        $query->where('admin_id','in',$sales);
                    }
                    $query->where('order_no', 'not null');
                })
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();

            foreach ($list as $key => $value) {
                if ($value['city']) {
                    if ($value['detailed_address']) {
                        $list[$key]['city'] = $value['city'] . '/' . $value['detailed_address'];
                    }
                }

            }
            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 新车定金编辑
     * @param null $ids
     * @return string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit($ids = NULL)
    {
        $order_info = Db::name('sales_order')
            ->where('id', $ids)
            ->field('customer_downpayment_id,bond')
            ->find();

        if ($order_info['customer_downpayment_id']) {
            $row = Db::name('customer_downpayment')
                ->where(function ($query) use ($order_info) {
                    $query->where([
                        'id' => $order_info['customer_downpayment_id'],
                        'car_type' => '新车'
                    ]);
                })
                ->find();

            $row['bond'] = $order_info['bond'];

            $this->view->assign([
                'row' => $row
            ]);
        }


        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $order = $this->request->post("order/a");
            if ($params) {
                $params['car_type'] = '新车';
                try {
                    Db::name('sales_order')
                        ->where('id', $ids)
                        ->setField('bond', $order['bond']);
                    if ($order_info['customer_downpayment_id']) {
                        $result = Db::name('customer_downpayment')
                            ->where('id', $order_info['customer_downpayment_id'])
                            ->update($params);
                    } else {
                        $result = Db::name('customer_downpayment')->insert($params);

                        $last_id = Db::name('customer_downpayment')->getLastInsID();

                        Db::name('sales_order')
                            ->where('id', $ids)
                            ->setField('customer_downpayment_id', $last_id);
                    }


                    if ($result !== false) {
                        $this->success();
                    } else {
                        $this->error($row->getError());
                    }
                } catch (\think\exception\PDOException $e) {
                    $this->error($e->getMessage());
                } catch (\think\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }

        $this->view->assign("money_list", $this->money_list());
        return $this->view->fetch();
    }


    /**
     * 二手车定金编辑
     * @param null $ids
     * @return string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit_used($ids = NULL)
    {
        $order_info = Db::name('second_sales_order')
            ->where('id', $ids)
            ->field('customer_downpayment_id,bond')
            ->find();

        if ($order_info['customer_downpayment_id']) {
            $row = Db::name('customer_downpayment')
                ->where(function ($query) use ($order_info) {
                    $query->where([
                        'id' => $order_info['customer_downpayment_id'],
                        'car_type' => '二手车'
                    ]);
                })
                ->find();

            $row['bond'] = $order_info['bond'];

            $this->view->assign([
                'row' => $row
            ]);
        }


        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $order = $this->request->post("order/a");
            if ($params) {
                try {
                    if ($order['bond']) {
                        Db::name('second_sales_order')
                            ->where('id', $ids)
                            ->setField('bond', $order['bond']);
                    }

                    $params['car_type'] = '二手车';
                    if ($order_info['customer_downpayment_id']) {
                        $result = Db::name('customer_downpayment')
                            ->where('id', $order_info['customer_downpayment_id'])
                            ->update($params);
                    } else {
                        $result = Db::name('customer_downpayment')->insert($params);

                        $last_id = Db::name('customer_downpayment')->getLastInsID();

                        Db::name('second_sales_order')
                            ->where('id', $ids)
                            ->setField('customer_downpayment_id', $last_id);
                    }
                    if ($result !== false) {
                        $this->success();
                    } else {
                        $this->error($row->getError());
                    }
                } catch (\think\exception\PDOException $e) {
                    $this->error($e->getMessage());
                } catch (\think\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("money_list", $this->money_list());
        return $this->view->fetch();
    }


    /**
     * 租车定金编辑
     * @param null $ids
     * @return string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit_rent($ids = NULL)
    {
        $order_info = Db::name('rental_order')
            ->where('id', $ids)
            ->field('customer_downpayment_id,bond')
            ->find();

        if ($order_info['customer_downpayment_id']) {
            $row = Db::name('customer_downpayment')
                ->where(function ($query) use ($order_info) {
                    $query->where([
                        'id' => $order_info['customer_downpayment_id'],
                        'car_type' => '租车'
                    ]);
                })
                ->find();

            $row['bond'] = $order_info['bond'];

            $this->view->assign([
                'row' => $row
            ]);
        }


        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $order = $this->request->post("order/a");
            if ($params) {
                try {
                    $params['car_type'] = '租车';
                    Db::name('rental_order')
                        ->where('id', $ids)
                        ->setField('bond', $order['bond']);

                    if ($order_info['customer_downpayment_id']) {
                        $result = Db::name('customer_downpayment')
                            ->where('id', $order_info['customer_downpayment_id'])
                            ->update($params);

                    } else {
                        $result = Db::name('customer_downpayment')->insert($params);
                        $last_id = Db::name('customer_downpayment')->getLastInsID();

                        Db::name('rental_order')
                            ->where('id', $ids)
                            ->setField('customer_downpayment_id', $last_id);
                    }

                    if ($result !== false) {
                        $this->success();
                    } else {
                        $this->error($row->getError());
                    }
                } catch (\think\exception\PDOException $e) {
                    $this->error($e->getMessage());
                } catch (\think\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("money_list", $this->money_list());
        return $this->view->fetch();
    }

    /**
     * 全款车定金编辑
     * @param null $ids
     * @return string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit_full($ids = NULL)
    {
        $order_info = Db::name('full_parment_order')
            ->where('id', $ids)
            ->field('customer_downpayment_id,bond')
            ->find();

        if ($order_info['customer_downpayment_id']) {
            $row = Db::name('customer_downpayment')
                ->where(function ($query) use ($order_info) {
                    $query->where([
                        'id' => $order_info['customer_downpayment_id'],
                        'car_type' => '全款车'
                    ]);
                })
                ->find();

            $row['bond'] = $order_info['bond'];

            $this->view->assign([
                'row' => $row
            ]);
        }


        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $order = $this->request->post("order/a");
            if ($params) {
                try {
                    $params['car_type'] = '全款车';

                    Db::name('full_parment_order')
                        ->where('id', $ids)
                        ->setField('bond', $order['bond']);

                    if ($order_info['customer_downpayment_id']) {
                        $result = Db::name('customer_downpayment')
                            ->where('id', $order_info['customer_downpayment_id'])
                            ->update($params);
                    } else {
                        $result = Db::name('customer_downpayment')->insert($params);

                        $last_id = Db::name('customer_downpayment')->getLastInsID();

                        Db::name('full_parment_order')
                            ->where('id', $ids)
                            ->setField('customer_downpayment_id', $last_id);
                    }

                    if ($result !== false) {
                        $this->success();
                    } else {
                        $this->error($row->getError());
                    }
                } catch (\think\exception\PDOException $e) {
                    $this->error($e->getMessage());
                } catch (\think\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("money_list", $this->money_list());
        return $this->view->fetch();
    }


    //打款状态
    public function money_list()
    {
        return ['1' => '已打款', '2' => '未打款'];
    }

    //当前登录
    public function current_login()
    {
        $this->model = model("Admin");
        $back = $this->model
            ->where("rule_message", 'in',["message13",'message20','message24'])
            ->field("id")
            ->select();

        $backArray = array();
        $backArray['back'] = array();
        $backArray['admin'] = array();

        foreach ($back as $value) {
            array_push($backArray['back'], $value['id']);
        }

        $superAdmin = $this->model->where("rule_message", "message21")
            ->field("id")
            ->select();

        foreach ($superAdmin as $value) {
            array_push($backArray['admin'], $value['id']);
        }

        return $backArray;
    }

    /**
     * 如是内勤登录，得到对应部门销售的信息
     * @return array
     */
    public function get_sales()
    {
        $message = Db::name('admin')
            ->where('id', $this->auth->id)
            ->value('rule_message');

        switch ($message) {
            case 'message13':
                return Db::name('admin')
                    ->where('rule_message', 'message8')
                    ->column('id');
            case 'message20':
                return Db::name('admin')
                    ->where('rule_message', 'message9')
                    ->column('id');
            case 'message24':
                return Db::name('admin')
                    ->where('rule_message', 'message23')
                    ->column('id');
        }

    }
}
