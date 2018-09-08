<?php

namespace app\admin\controller\banking;

use app\common\controller\Backend;
use think\Db;


/**
 * 多表格示例
 *
 * @icon fa fa-table
 * @remark 当一个页面上存在多个Bootstrap-table时该如何控制按钮和表格
 */
class Exchangeplatformtabs extends Backend
{

    protected $model = null;
    protected $dataLimit = false; //表示不启用，显示所有数据
    protected $searchFields = 'username,licensenumber,frame_number';
    protected $noNeedRight = ['index', 'new_car', 'yue_da_car', 'other_car', 'nanchong_driver', 'edit', 'change_platform', 'batch_change_platform', 'details', 'loan'];

//    protected $multiFields = 'batch';
    public function _initialize()
    {

        parent::_initialize();

        $this->model = new \app\admin\model\SalesOrder();
    }


    /**
     * 查看
     */
    public function index()
    {

        $this->loadlang('banking/exchangeplatformtabs');
        $new_car = $this->getCar("new_car");
        $yue_da_car = $this->getCar("yueda_car");
        $other_car = $this->getCar("other_car");
        $nan_chong = Db::name("nanchong_driver")->count();

        $this->view->assign([
            'new_total' => $new_car[0],
            'yue_total' => $yue_da_car[0],
            'other_total' => $other_car[0],
            'nan_chong' => $nan_chong
        ]);


        return $this->view->fetch();
    }

    /**
     * 新车
     * @return string|\think\response\Json
     */
    public function new_car()
    {

        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {

            list($where, $sort, $order, $offset, $limit) = $this->buildparams("newinventory.licensenumber", true);
            $total = $this->model
                ->with(['mortgage' => function ($query) {
                    $query->withField('lending_date,bank_card,invoice_monney,registration_code,tax,business_risks,insurance,firm_stage,mortgage_type');
                }, 'newinventory' => function ($query) {
                    $query->withField('household,licensenumber,frame_number');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }, 'planacar' => function ($query) {
                    $query->withField('payment,monthly,nperlist');
                }])
                ->where('mortgage_type', 'new_car')
                ->whereOr('mortgage_type', null)
                ->where($where)
                ->order("mortgage.lending_date desc")
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['mortgage' => function ($query) {
                    $query->withField('lending_date,bank_card,invoice_monney,registration_code,tax,business_risks,insurance,firm_stage,mortgage_type');
                }, 'newinventory' => function ($query) {
                    $query->withField('household,licensenumber,frame_number');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }, 'planacar' => function ($query) {
                    $query->withField('payment,monthly,nperlist');
                }])
                ->where('mortgage_type', 'new_car')
                ->whereOr('mortgage_type', null)
                ->where($where)
                ->order("mortgage.lending_date desc")
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();

    }

    /**
     * 悦达车
     * @return bool|\think\response\Json
     */
    public function yue_da_car()
    {
        if ($this->request->isAjax()) {
            $res = $this->getCar("yueda_car");

            $result = array("total" => $res[0], "rows" => $res[1]);

            return json($result);
        }
        return true;
    }

    /**
     * 其他车
     * @return bool|\think\response\Json
     */
    public function other_car()
    {
        if ($this->request->isAjax()) {

            $res = $this->getCar("other_car");

            $result = array("total" => $res[0], "rows" => $res[1]);

            return json($result);
        }
        return true;
    }

    /**
     * 南充司机
     * @return \think\response\Json
     */
    public function nanchong_driver()
    {

        if ($this->request->isAjax()) {

            $total = Db::name("nanchong_driver")->count();
            $list = Db::name("nanchong_driver")->select();

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }

    }

    /**
     * 得到需要查询的内容
     * @param null $condition
     * @return array
     */
    public function getCar($condition = null)
    {
        list($where, $sort, $order, $offset, $limit) = $this->buildparams("newinventory.licensenumber", true);
        $total = $this->model
            ->with(['mortgage' => function ($query) {
                $query->withField('lending_date,bank_card,invoice_monney,registration_code,tax,business_risks,insurance,firm_stage,mortgage_type');
            }, 'newinventory' => function ($query) {
                $query->withField('household,licensenumber,frame_number');
            }, 'models' => function ($query) {
                $query->withField('name');
            }, 'planacar' => function ($query) {
                $query->withField('payment,monthly,nperlist');
            }])
            ->where('mortgage_type', $condition)
            ->where($where)
            ->order("mortgage.lending_date desc")
            ->order($sort, $order)
            ->count();

        $list = $this->model
            ->with(['mortgage' => function ($query) {
                $query->withField('lending_date,bank_card,invoice_monney,registration_code,tax,business_risks,insurance,firm_stage,mortgage_type');
            }, 'newinventory' => function ($query) {
                $query->withField('household,licensenumber,frame_number');
            }, 'models' => function ($query) {
                $query->withField('name');
            }, 'planacar' => function ($query) {
                $query->withField('payment,monthly,nperlist');
            }])
            ->where('mortgage_type', $condition)
            ->where($where)
            ->order("mortgage.lending_date desc")
            ->order($sort, $order)
            ->limit($offset, $limit)
            ->select();

        return array($total, $list);
    }

    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {

        $this->model = new \app\admin\model\Mortgage();


        $gage_id = Db::name("sales_order")
            ->where("id", $ids)
            ->field("mortgage_id")
            ->find()['mortgage_id'];

        $row = $this->model->get($gage_id);

        if ($row) {
            $this->view->assign("row", $row);
        }


        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = basename(str_replace('\\', '/', get_class($this->model)));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : true) : $this->modelValidate;
                        $row->validate($validate);
                    }

                    if ($row) {
                        $result = $row->allowField(true)->save($params);
                    } else {
                        $params['mortgage_type'] = 'new_car';

                        $this->model->data($params);
                        $this->model->save();

                        $result = Db::name("sales_order")
                            ->where("id", $ids)
                            ->setField("mortgage_id", $this->model->id);

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

        return $this->view->fetch();
    }

    /**
     * 更改平台
     * @param null $ids
     * @return string
     */
    public function change_platform($ids = null)
    {
        $motagage = Db::name("sales_order")
            ->where("id", $ids)
            ->field("mortgage_id")
            ->find()['mortgage_id'];

        if ($motagage) {
            $row = Db::name("mortgage")
                ->where("id", $motagage)
                ->find();

            $this->view->assign('my_type', $row['mortgage_type']);
        }

        $this->view->assign('mortgage_type_list', ['new_car' => '新车', 'yueda_car' => '悦达', 'other_car' => '其他']);

        if ($this->request->isPost()) {
            $params = $this->request->post("mortgage_type");


            if ($motagage) {
                $result = Db::name("mortgage")
                    ->where("id", $motagage)
                    ->setField("mortgage_type", $params);
            } else {
                Db::name("mortgage")->insert(['mortgage_type' => $params]);

                $last_id = Db::name("mortgage")->getLastInsID();

                $result = Db::name("sales_order")
                    ->where("id", $ids)
                    ->setField("mortgage_id", $last_id);
            }


            if ($result !== false) {
                $this->success();
            } else {
                $this->error($row->getError());
            }

        }
        return $this->view->fetch();
    }

    /**
     * 批量更改平台
     * @param null $ids
     * @return string
     */
    public function batch_change_platform($ids = null)
    {
        $change = Db::name("sales_order")
            ->alias("so")
            ->join("mortgage m", "so.mortgage_id = m.id")
            ->where("so.id", "in", $ids)
            ->field("m.mortgage_type")
            ->find();

        if ($change) {
            $this->view->assign('mortgage_type', $change['mortgage_type']);
        }

        $this->view->assign('mortgage_type_list', ['new_car' => '新车', 'yueda_car' => '悦达', 'other_car' => '其他']);

        if ($this->request->isPost()) {
            $params = $this->request->post("mortgage_type");

            $use_id = Db::name("sales_order")
                ->where("id", "in", $ids)
                ->field("mortgage_id,id")
                ->select();

            foreach ($use_id as $k => $v) {
                if ($v['mortgage_id']) {
                    Db::name("mortgage")
                        ->where("id", $v['mortgage_id'])
                        ->setField("mortgage_type", $params);
                } else {
                    Db::name("mortgage")->insert(['mortgage_type' => $params]);

                    $last = Db::name("mortgage")->getLastInsID();

                    Db::name("sales_order")
                        ->where("id", $v['id'])
                        ->setField("mortgage_id", $last);
                }
            }

            $this->success();

        }
        return $this->view->fetch();
    }


    /**
     * 查看详情信息
     * @param null $ids
     * @return string
     */
    public function details($ids = null)
    {
        $res = Db::view("crm_plan_acar_view", "id,lending_date,household,createtime,bank_card,username,id_card,phone,detailed_address,name,invoice_monney,registration_code,tax,business_risks,insurance,payment,delivery_datetime,licensenumber,mortgage_type,margin,tail_section,gps,note,4s_shop,engine_number,frame_number,presentationcondition,car_imgeas,plan_name,emergency_contact_1,emergency_contact_2,family_members,id_cardimages,drivers_licenseimages,residence_bookletimages,housingimages,bank_cardimages,application_formimages,call_listfiles,deposit_contractimages,deposit_receiptimages,genderdata,city")
            ->where("id", $ids)
            ->select();
        $res = $res[0];

        $this->view->assign([
            'row' => $res
        ]);

        return $this->view->fetch();
    }

    /**
     * 输入放款时间
     * @param null $ids
     * @return string
     */
    public function loan($ids = null)
    {

        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
//              pr($params);die();

                try {

                    $mortgage_id = Db::name("sales_order")
                        ->field("id,mortgage_id")
                        ->where("id", "in", $ids)
                        ->select();


                    foreach ($mortgage_id as $k => $v) {

                        if ($v['mortgage_id']) {
                            Db::name("mortgage")
                                ->where("id", $v['mortgage_id'])
                                ->update([
                                    "lending_date" => $params['lending_date'],
                                    'firm_stage' => $params['firm_stage']
                                ]);
                        } else {
                            Db::name("mortgage")->insert($params);

                            $last_id = Db::name("mortgage")->getLastInsID();

                            Db::name("sales_order")
                            ->where("id",$v['id'])
                            ->setField("mortgage_id",$last_id);
                        }

                    }

                    $result = 1;

                    if ($result !== false) {
                        $this->success();
                    } else {
                        $this->error();
                    }
                } catch (\think\exception\PDOException $e) {
                    $this->error($e->getMessage());
                } catch (\think\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }


        return $this->view->fetch();

    }

}

