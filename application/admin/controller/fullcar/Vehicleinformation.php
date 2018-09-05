<?php

namespace app\admin\controller\fullcar;

use app\common\controller\Backend;
use think\DB;
use think\Config;
use app\common\library\Email;
use think\Cache;

/**
 * 全款进件列管理
 *
 * @icon fa fa-circle-o
 */
class Vehicleinformation extends Backend
{

    /**
     * Fullpeople模型对象
     * @var \app\admin\model\Fullpeople
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Fullpeople;

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

        $this->loadlang('order/fullparmentorder');

        $prepare_total = Db::name("full_parment_order")
            ->where("review_the_data", "is_reviewing_true")
            ->where("car_new_inventory_id", null)
            ->count();

        $already_total = Db::name("full_parment_order")
            ->where("review_the_data", "for_the_car")
            ->where("car_new_inventory_id", "not null")
            ->count();

        $this->view->assign([
            'prepare_total' => $prepare_total,
            'already_total' => $already_total
        ]);
        return $this->view->fetch();
    }

    //待提车
    public function prepare_lift_car()
    {
        $this->model = model('full_parment_order');
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username', true);
            $total = $this->model
                ->with(['planfull' => function ($query) {
                    $query->withField('full_total_price');
                }, 'admin' => function ($query) {
                    $query->withField('nickname');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }])
                ->where($where)
                ->where(function ($query) {
                    $query->where("car_new_inventory_id", null)
                        ->where("review_the_data", "is_reviewing_true");
                })
                ->order($sort, $order)
                ->count();


            $list = $this->model
                ->with(['planfull' => function ($query) {
                    $query->withField('full_total_price');
                }, 'admin' => function ($query) {
                    $query->withField('nickname');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }])
                ->where($where)
                ->where(function ($query) {
                    $query->where("car_new_inventory_id", null)
                        ->where("review_the_data", "is_reviewing_true");
                })
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            foreach ($list as $k => $row) {
                $row->visible(['id', 'order_no', 'username', 'createtime', 'phone', 'id_card', 'amount_collected', 'review_the_data']);
                $row->visible(['planfull']);
                $row->getRelation('planfull')->visible(['full_total_price']);
                $row->visible(['admin']);
                $row->getRelation('admin')->visible(['nickname']);
                $row->visible(['models']);
                $row->getRelation('models')->visible(['name']);

            }


            $list = collection($list)->toArray();

            $result = array('total' => $total, "rows" => $list);
            return json($result);
        }

        return $this->view->fetch();

    }

    //已提车
    public function already_lift_car()
    {

        $this->model = model('full_parment_order');
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username', true);
            $total = $this->model
                ->with(['planfull' => function ($query) {
                    $query->withField('full_total_price');
                }, 'admin' => function ($query) {
                    $query->withField('nickname');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }, 'carnewinventory' => function ($query) {
                    $query->withField('frame_number,engine_number,licensenumber,household,4s_shop');
                }])
                ->where($where)
                ->where(function ($query) {
                    $query->where("car_new_inventory_id", "not null")
                        ->where("review_the_data", "for_the_car");
                })
                ->order($sort, $order)
                ->count();


            $list = $this->model
                ->with(['planfull' => function ($query) {
                    $query->withField('full_total_price');
                }, 'admin' => function ($query) {
                    $query->withField('nickname');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }, 'carnewinventory' => function ($query) {
                    $query->withField('frame_number,engine_number,licensenumber,household,4s_shop');
                }])
                ->where($where)
                ->where(function ($query) {
                    $query->where("car_new_inventory_id", "not null")
                        ->where("review_the_data", "for_the_car");
                })
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            foreach ($list as $k => $row) {
                $row->visible(['id', 'order_no', 'username', 'createtime', 'phone', 'id_card', 'amount_collected', 'review_the_data']);
                $row->visible(['planfull']);
                $row->getRelation('planfull')->visible(['full_total_price']);
                $row->visible(['admin']);
                $row->getRelation('admin')->visible(['nickname']);
                $row->visible(['models']);
                $row->getRelation('models')->visible(['name']);
                $row->visible(['carnewinventory']);
                $row->getRelation('carnewinventory')->visible(['frame_number', 'licensenumber', 'engine_number', 'household', '4s_shop']);

            }


            $list = collection($list)->toArray();

            $result = array('total' => $total, "rows" => $list);
            return json($result);
        }

        return $this->view->fetch();

    }

    //选择库存车
    public function choose_stock($ids = null)
    {
        if ($this->request->isPost()) {

            $id = input("post.id");

            Db::name("full_parment_order")
                ->where("id", $ids)
                ->update([
                    'car_new_inventory_id' => $id,
                    'review_the_data' => "for_the_car",
                    'delivery_datetime' => time()
                ]);

            Db::name("car_new_inventory")
                ->where("id", $id)
                ->setField("statuss", 0);

            $channel = "demo-full_takecar";
            $content = "销售提交的全款车单，可以进行提车处理";
            goeary_push($channel, $content);

            $full_info = Db::name("full_parment_order")
            ->where("id",$ids)
            ->field("admin_id,models_id,username,phone,customer_source,introduce_name,introduce_phone,introduce_card")
            ->find();

            //如果是转介绍,到介绍人表
            if($full_info['customer_source']=="introduce"){
                $insert_data = [
                    'models_id'=>$full_info['models_id'],
                    'admin_id'=>$full_info['admin_id'],
                    'referee_name'=>$full_info['introduce_name'],
                    'referee_phone'=>$full_info['introduce_phone'],
                    'referee_idcard'=>$full_info['introduce_card'],
                    'customer_name'=>$full_info['username'],
                    'customer_phone'=>$full_info['phone'],
                    'buy_way'=>"全款车"
                ];

                Db::name("referee")->insert($insert_data);

                $last_id = Db::name("referee")->getLastInsID();

                Db::name("full_parment_order")
                ->where("id",$ids)
                ->setField("referee_id",$last_id);
            }


            $this->success('', '', $ids);
        }
        $stock = Db::name("car_new_inventory")
            ->alias("i")
            ->join("crm_models m", "i.models_id=m.id")
            ->where("statuss", 1)
            ->field("i.id,m.name,i.licensenumber,i.frame_number,i.engine_number,i.household,i.4s_shop,i.note")
            ->select();

        $this->view->assign([
            'stock' => $stock
        ]);

        $seventtime = \fast\Date::unixtime('day', -14);
        $fullonesales = $fulltwosales = [];
        for ($i = 0; $i < 8; $i++)
            {
                $month = date("Y-m", $seventtime + ($i * 86400 * 30));
                //销售一部
                $one_sales = DB::name('auth_group_access')->where('group_id', '18')->select();
                foreach($one_sales as $k => $v){
                    $one_admin[] = $v['uid'];
                }
                $fullonetake = Db::name('full_parment_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $one_admin)
                        ->where('delivery_datetime', 'between', [$seventtime + ($i * 86400 * 30), $seventtime + (($i + 1) * 86400 * 30)])
                        ->count();
                //销售二部
                $two_sales = DB::name('auth_group_access')->where('group_id', '22')->field('uid')->select();
                foreach($two_sales as $k => $v){
                    $two_admin[] = $v['uid'];
                }
                $fulltwotake = Db::name('full_parment_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $two_admin)
                        ->where('createtime', 'between', [$seventtime + ($i * 86400 * 30), $seventtime + (($i + 1) * 86400 * 30)])
                        ->count();

                //全款车提车
                $fullonesales[$month] = $fullonetake;
                //全款车订车
                $fulltwosales[$month] = $fulltwotake;
            }
            Cache::set('fullonesales', $fullonesales);
            Cache::set('fulltwosales', $fulltwosales);


        return $this->view->fetch();
    }

    /**查看详细资料 */
    public function show_order($ids = null)
    {
        $this->model = new \app\admin\model\FullParmentOrder;
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }


        //身份证正反面（多图）
        $id_cardimages = explode(',', $row['id_cardimages']);

        //驾照正副页（多图）
        $drivers_licenseimages = explode(',', $row['drivers_licenseimages']);

        //申请表（多图）
        $application_formimages = explode(',', $row['application_formimages']);

        /**不必填 */
        //银行卡照（可多图）
        $bank_cardimages = $row['bank_cardimages'] == '' ? [] : explode(',', $row['bank_cardimages']);

        //通话清单（文件上传）
        $call_listfiles = $row['call_listfiles'] == '' ? [] : explode(',', $row['call_listfiles']);

        $this->view->assign(
            [
                'row' => $row,
                'cdn' => Config::get('upload')['cdnurl'],
                'id_cardimages' => $id_cardimages,
                'drivers_licenseimages' => $drivers_licenseimages,
                'application_formimages' => $application_formimages,
                'bank_cardimages' => $bank_cardimages,
                'call_listfiles' => $call_listfiles,
            ]
        );
        return $this->view->fetch();
    }

    //查看订单表和库存表所有信息
    public function show_order_and_stock($ids = null)
    {
        $this->model = new \app\admin\model\FullParmentOrder;
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }

        $data = DB::name('car_new_inventory')
            ->where('id', $row['car_new_inventory_id'])
            ->find();

        //身份证正反面（多图）
        $id_cardimages = explode(',', $row['id_cardimages']);

        //驾照正副页（多图）
        $drivers_licenseimages = explode(',', $row['drivers_licenseimages']);

        //申请表（多图）
        $application_formimages = explode(',', $row['application_formimages']);

        /**不必填 */
        //银行卡照（可多图）
        $bank_cardimages = $row['bank_cardimages'] == '' ? [] : explode(',', $row['bank_cardimages']);

        //通话清单（文件上传）
        $call_listfiles = $row['call_listfiles'] == '' ? [] : explode(',', $row['call_listfiles']);

        $this->view->assign(
            [
                'row' => $row,
                'cdn' => Config::get('upload')['cdnurl'],
                'id_cardimages' => $id_cardimages,
                'drivers_licenseimages' => $drivers_licenseimages,
                'application_formimages' => $application_formimages,
                'bank_cardimages' => $bank_cardimages,
                'call_listfiles' => $call_listfiles,
            ]
        );

        $row['createtime'] = date("Y-m-d", $row['createtime']);
        $row['delivery_datetime'] = date("Y-m-d", $row['delivery_datetime']);

        $this->view->assign($data);
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

}
