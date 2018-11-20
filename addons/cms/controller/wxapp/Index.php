<?php

namespace addons\cms\controller\wxapp;

use app\common\library\Auth;
use addons\cms\model\PlanAcar;
use app\common\model\Addon;
use think\Cache;
use think\console\command\make\Model;
use think\Db;
use think\Config;
use addons\cms\model\CompanyStore;
use addons\cms\model\Models;
use addons\cms\model\Cities;
use addons\cms\model\Subject;
use addons\cms\model\SecondcarRentalModelsInfo;
use addons\cms\model\Subscribe;

/**
 * 首页
 */
class Index extends Base
{

    protected $noNeedLogin = '*';

    public function _initialize()
    {
        parent::_initialize();
    }


    /**
     * 首页数据接口，返回各种类型方案丶品牌列表丶城市列表以及配置分享信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $city_id = $this->request->post('city_id');                              //参数：城市ID


        if (!$city_id) {
            $this->error('参数错误或缺失参数,请求失败', 'error');
        }

        Cache::set('city_id', $city_id);


        //返回所有类型的方案
        $useful = $this->getAllStylePlan($city_id);

        $data = ['carType' => [
            'new' => [
                //为你推荐
                'recommendList' => $useful['recommendList'],
                //专题
                'specialList' => $useful['specialList'],
                //专场
                'specialfieldList' => $useful['specialfieldList']],
        ],
            //品牌
            'brandList' => $this->getBrand(),
            //分享
            'shares' => Share::getConfigShare(),
            //预约
            'appointment' => $this->appointment()
        ];

        $this->success('请求成功', $data);
    }


    /**
     * 详情方案
     * @param null $models_id 车型ID
     * @param $city_id          城市ID
     * @param $plan_id          方案ID
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getPlans($models_id = null, $city_id = null, $plan_id)
    {
        return Db::name('models')
            ->alias('a')
            ->join('plan_acar b', 'b.models_id = a.id')
            ->join('cms_company_store c', 'b.store_id = c.id')
            ->where([
                'a.id' => $models_id == null ? ['neq', 'null'] : $models_id,
                'c.city_id' => $city_id == null ? ['neq', 'null'] : $city_id,
                'b.id' => ['neq', $plan_id]
            ])
            ->field('b.id,b.payment,b.monthly,b.guide_price,b.models_main_images,a.name as models_name')
            ->select();
    }



    /**
     * 获取品牌信息
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getBrand()
    {
        $brand = Models::with(['brand' => function ($brand) {
            $brand->where('brand.status', 'normal');
            $brand->withField('id,name,brand_logoimage');
        }, 'planacar' => function ($planacar) {
            $planacar->where('acar_status', 1);
            $planacar->withField('id');
        }])->where('models.status', 'normal')->select();

        $brandList = [];                                                      //品牌列表
        foreach ($brand as $k => $v) {

            $v['brand']['brand_logoimage'] = Config::get('upload')['cdnurl'] . $v['brand']['brand_logoimage'];

            $brandList[] = $v['brand'];

        }

        $brandList = array_values(array_unique($brandList));

        //不常用品牌放在最后
        $notOften = ['东风'];
        $notOftenCity = [];
        foreach ($brandList as $k => $v) {
            if (in_array($v['name'], $notOften)) {
                $notOftenCity[] = $v;
                unset($brandList[$k]);
            }
        }

        return array_merge($brandList, $notOftenCity);


    }

    /**
     * 获取所有类型方案
     * @param $city_id
     * @return array|bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAllStylePlan($city_id)
    {

        //获取该城市所有满足条件的方案
        $info = Share::getNewCarPlan($city_id, '', true);

        $recommendList = [];             //为你推荐（新车）
        $specialfieldList = [];          //专场（新车）

        if (!$info) {
            return false;
        }

        //将返回的方案根据类别划分
        foreach ($info as $k => $v) {

            if ($v['recommendismenu']) {
                $recommendList[] = ['id' => $v['id'], 'models_main_images' => $v['models_main_images'], 'models_name' => $v['models_name'],
                    'payment' => $v['payment'], 'monthly' => $v['monthly']];
            }
            if ($v['specialismenu']) {
                $needData = ['id' => $v['id'], 'specialimages' => $v['specialimages']];
                $specialfieldList[] = $needData;
            }

        }

        //获取专题表信息
        $specialList = Subject::field('id,title,coverimages,plan_id')
            ->where([
                'shelfismenu' => 1,
                'city_id' => $city_id
            ])
            ->select();

        //根据专题获取方案
        foreach ($specialList as $k => $v) {
            $specialList[$k]['plan_id'] = json_decode($v['plan_id'], true);

            $specialList[$k]['coverimages'] = Config::get('upload')['cdnurl'] . $specialList[$k]['coverimages'];
            $plan_arr = [];
            foreach ($specialList[$k]['plan_id']['plan_id'] as $key => $value) {
                $plan = Db::name('plan_acar')
                    ->alias('a')
                    ->join('models b', 'a.models_id = b.id')
                    ->where([
                        'a.id' => $value,
                        'a.acar_status' => 1,
                        'b.status' => 'normal'
                    ])
                    ->field('a.id,b.name as models_name,a.payment,a.monthly,a.models_main_images')
                    ->find();

                $plan['models_main_images'] = Config::get('upload')['cdnurl'] . $plan['models_main_images'];

                if ($plan) {
                    $plan_arr[] = $plan;
                }

            }

            $specialList[$k]['plan'] = $plan_arr;
            if (!empty($plan_arr)) {
                unset($specialList[$k]['plan_id']);
            }

        }

        return [
            'recommendList' => $recommendList,
            'specialList' => $specialList,
            'specialfieldList' => $specialfieldList,
        ];
    }





    /**
     * 方案车型接口
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
//    public function modelsPlan()
//    {
////        //车型ID
////        $models_id = $this->request->post('models_id');
////        //类型
////        $models_style = $this->request->post('models_style');
//
////        if (!$models_id || !$models_style) {
////            $this->error('参数错误或缺失参数', 'error');
////        }
//
//
//        $getPlans = PlanAcar::field('id,payment,monthly,guide_price,models_main_images,popularity')
//            ->with(['models' => function ($query) {
//                $query->where('models.status', 'normal');
//                $query->withField('name');
//            }, 'companystore' => function ($query) {
//                $query->where('statuss', 'normal');
//                $query->withField('city_id');
//            }])->select();
//
//        //将自己的城市单独拉出来
//        $myCity = [];
//        foreach ($getPlans as $k => $v) {
//            $getPlans[$k]['models_main_images'] = $v['models_main_images'] = Config::get('upload')['cdnurl'] . $v['models_main_images'];
//            if ($v['companystore'] && $v['companystore']['city_id']) {
//                if ($models_style == 'new') {
//                    $getPlans[$k]['style'] = 'new';
//                    $v['style'] = 'new';
//                } else {
//                    $getPlans[$k]['style'] = 'used';
//                    $v['style'] = 'used';
//                }
//
//                if ($v['companystore']['city_id'] == Cache::get('city_id')) {
//                    unset($v['companystore']);
//                    $v['models_name'] = $v['models']['name'];
//                    unset($v['models']);
//                    $myCity[] = $v;
//                    unset($getPlans[$k]);
//                } else {
//                    $getPlans[$k]['models_name'] = $v['models']['name'];
//                    unset($getPlans[$k]['models']);
//                    unset($getPlans[$k]['companystore']);
//                }
//
//            }
//        }
//
//        $data = array_merge($myCity, $getPlans);
//
//        if ($data) {
//            $this->success('请求成功', $data);
//        } else {
//            $this->error('', '');
//        }
//
//
//    }





    /**
     * 预约
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function appointment()
    {
        $appointment = Subscribe::field('id')
            ->with(['user' => function ($query) {
                $query->withField('mobile,avatar');
            }, 'newplan' => function ($query) {
                $query->withField('models_id');
            }, 'usedplan' => function ($query) {
                $query->withField('models_id');
            }, 'energyplan' => function ($query) {
                $query->withField('name');
            }])->order('id desc')->limit(10)->select();

        foreach ($appointment as $k => $v) {
            if ($v['newplan']['models_id']) {
                $appointment[$k]['models_name'] = Db::name('models')->where('id', $v['newplan']['models_id'])->value('name');
            }
            if ($v['usedplan']['models_id']) {
                $appointment[$k]['models_name'] = Db::name('models')->where('id', $v['usedplan']['models_id'])->value('name');
            }
            if ($v['energyplan']['name']) {
                $appointment[$k]['models_name'] = $v['energyplan']['name'];
            }
            $appointment[$k]['mobile'] = $v['user']['mobile'];
            $appointment[$k]['avatar'] = $v['user']['avatar'];
            unset($appointment[$k]['user']);
            unset($appointment[$k]['newplan']);
            unset($appointment[$k]['usedplan']);
            unset($appointment[$k]['energyplan']);
            unset($appointment[$k]['state_text']);
        }

        return $appointment;

    }

}
