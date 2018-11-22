<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/11/20
 * Time: 16:09
 */

namespace addons\cms\controller\wxapp;

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
use app\common\library\Auth;
use addons\cms\model\PlanAcar;
use app\common\model\Addon;

class Share extends Base
{
    protected $noNeedLogin = '*';

    /**
     * 方案详情接口
     */
    public function plan_details()
    {
        $plan_id = $this->request->post('plan_id');                   //参数：方案ID
        $user_id = $this->request->post('user_id');                   //参数：用户ID
        $cartype = $this->request->post('cartype');                   //车辆类型

        if (!$plan_id || !$user_id || !$cartype) {
            $this->error('参数错误或缺失参数,请求失败', 'error');
        }
        switch ($cartype) {
            case 'new':
                $data = $this->newcar_details($plan_id, $user_id);
                break;
            case 'used':
                $data = $this->used_details($plan_id, $user_id);
                break;
            case 'logistics':
                $data = $this->logistics_details($plan_id, $user_id);
                break;
            default:
                $this->error('参数错误', '');
        }

        $this->success('请求成功', $data);


    }

    /**
     * @param $plan_id
     * @param $user_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function newcar_details($plan_id, $user_id)
    {
        //获取该方案的详细信息
        $plans = PlanAcar::field('id,models_id,payment,monthly,nperlist,modelsimages,guide_price,models_main_images,
specialimages,popularity')
            ->with(['models' => function ($models) {
                $models->withField('name,vehicle_configuration');
            }, 'label' => function ($label) {
                $label->withField('name,lableimages,rotation_angle');
            }, 'companystore' => function ($companystore) {
                $companystore->withField('store_name,store_address,company_name,phone');
            }])->find([$plan_id]);

        //方案标签图片加入CDN
        if ($plans['label'] && $plans['label']['lableimages']) {
            $plans['label']['lableimages'] = Config::get('upload')['cdnurl'] . $plans['label']['lableimages'];
        }

        $plans['models']['vehicle_configuration'] = json_decode($plans['models']['vehicle_configuration'], true);

        $plans['models_main_images'] = $plans['models_main_images'] ? Config::get('upload')['cdnurl'] . $plans['models_main_images'] : '';
        $plans['modelsimages'] = $plans['modelsimages'] ? Config::get('upload')['cdnurl'] . $plans['modelsimages'] : '';
        $plans['specialimages'] = $plans['specialimages'] ? Config::get('upload')['cdnurl'] . $plans['specialimages'] : '';
        //查看同城市同车型不同的方案
        $different_schemes = $this->getPlans($plans['models_id'], Cache::get('city_id'), $plan_id);

        //查看其它方案的属性名
        if ($different_schemes) {
            //为其他方案封面图片加入CDN
            foreach ($different_schemes as $k => $v) {
                $different_schemes[$k]['models_main_images'] = Config::get('upload')['cdnurl'] . $different_schemes[$k]['models_main_images'];
            }
            $plans['different_schemes'] = $different_schemes;
        } else {
            $plans['different_schemes'] = null;
        }

        //获取其他方案
        $allModel = $this->getPlans('', '', $plan_id);

        $reallyOther = null;

        //如果有其他方案，随机得到其他的方案
        if ($allModel) {
            $reallyOther = [];
            $keys = array_keys($allModel);

            shuffle($keys);

            foreach ($keys as $k => $v) {
                if ($k > 7) {
                    break;
                }

                $allModel[$v]['models_main_images'] = Config::get('upload')['cdnurl'] . $allModel[$v]['models_main_images'];

                $reallyOther[] = $allModel[$v];
            }
        }

        $collection = $this->getCollectionFabulous('cms_collection', $plan_id, $user_id);         //判断用户是否收藏该方案

        $fabulous = $this->getCollectionFabulous('cms_fabulous', $plan_id, $user_id);             //判断用户是否点赞该方案

        $appointment = $this->getAppointment($user_id,$plan_id,'new');
        $plans['collection'] = $collection ? 1 : 0;
        $plans['fabulous'] = $fabulous ? 1 : 0;
        $plans['appointment'] = $appointment?1:0;

        return [
            'plan' => $plans,
            'guesslike' => $reallyOther
        ];

    }

    public function used_details($plan_id, $user_id)
    {

    }

    public function logistics_details($plan_id, $user_id)
    {

    }


    /**
     * 省份-城市接口
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function cityList()
    {
        if (Cache::get('cityList')) {
            $this->success('请求成功', Cache::get('cityList'));
        }

        $province = self::getCityList();
        Cache::set('cityList', $province);

        if ($province) {
            $this->success('请求成功', ['cityList' => $province]);
        } else {
            $this->error();
        }

    }


    /**
     * 模糊搜索城市接口
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function searchCity()
    {
        //搜索栏内容
        $cities_name = $this->request->post('cities_name');

        if (!$cities_name) {
            $this->error('参数错误或缺失参数,请求失败', 'error');
        }

        //获取搜索的数据
        $searchCityList = Cities::field('id,cities_name')
            ->where([
                'status' => 'normal',
                'pid' => ['neq', 'null'],
                'cities_name' => ['like', '%' . $cities_name . '%']
            ])
            ->select();

        if ($searchCityList) {
            $this->success('请求成功', $searchCityList);
        } else {
            $this->error();
        }

    }


    /**
     * 点赞接口
     */
    public function fabulousInterface()
    {
        $user_id = $this->request->post('user_id');
        $plan_id = $this->request->post('plan_id');

        if (!$user_id || !$plan_id) {
            $this->error('参数错误或缺失参数,请求失败', 'error');
        }
        $check = Db::name('cms_fabulous')
            ->where([
                'user_id' => $user_id,
                'planacar_id' => $plan_id
            ])
            ->find();

        if ($check) {
            $this->error('', '该用户已经点赞过了');
        }

        $res = Db::name('cms_fabulous')->insert(['planacar_id' => $plan_id, 'user_id' => $user_id, 'fabuloustime' => time()]);

        $res ? $this->success('请求成功', 'success') : $this->error('', 'error');

    }

    /**
     * 收藏接口
     */
    public function collectionInterface()
    {
        $user_id = $this->request->post('user_id');
        $plan_id = $this->request->post('plan_id');

        if (!$user_id || !$plan_id) {
            $this->error('参数错误或缺失参数,请求失败', 'error');
        }
        $check = Db::name('cms_collection')
            ->where([
                'user_id' => $user_id,
                'planacar_id' => $plan_id
            ])
            ->find();

        if ($check) {
            $this->error('', '该用户已经收藏过了');
        }

        $res = Db::name('cms_collection')->insert(['planacar_id' => $plan_id, 'user_id' => $user_id, 'collectiontime' => time()]);

        $res ? $this->success('请求成功', 'success') : $this->error('', 'error');

    }


    /**
     * style仅限：fabulous点赞,share分享,sign签到
     * 增加积分接口
     * @throws \think\Exception
     */
    public function integral()
    {
        $style = $this->request->post('style');                         //添加积分方式

        $user_id = $this->request->post('user_id');                     //用户ID


        if (!$style || !$user_id || !in_array($style, ['fabulous', 'share', 'sign'])) {
            $this->error('参数错误或缺失参数,请求失败', 'error');
        }

        $rule = Db::name('config')
            ->where('group', 'integral')
            ->value('value');

        $rule = json_decode($rule, true);

        $res = Db::name('user')
            ->where('id', $user_id)
            ->setInc('score', intval($rule[$style]));

        $res ? $this->success('', 'success') : $this->error('', 'error');

    }

    /**
     * 搜索车型接口
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function searchModels()
    {

        $queryModels = $this->request->post('queryModels');

        if (!$queryModels) {
            $this->error('参数错误或缺失参数,请求失败', 'error');
        }

        //新车车型
        $new_models = $this->getModels($queryModels, 'planacar');
        //二手车车型
        $used_models = $this->getModels($queryModels, 'secondcarplan');

        if ($new_models || $used_models) {
            $this->success('请求成功', ['new' => $new_models, 'used' => $used_models]);
        } else {
            $this->error('', '');
        }


    }


    /**
     * 得到对应的车型
     * @param $queryModels            搜索内容
     * @param $withTable              关联的表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getModels($queryModels, $withTable)
    {
        //模糊查询对应车型
        $models = Models::field('id,name')
            ->with([$withTable => function ($query) use ($withTable) {
                if ($withTable == 'planacar') {
                    $query->where('acar_status', 1);
                }
                $query->withField('id');
            }])->where(function ($query) use ($queryModels) {
                $query->where([
                    'name' => ['like', '%' . $queryModels . '%']
                ]);
            })->select();

        $check = [];
        $duplicate_models = [];
        //根据车型名称去重
        foreach ($models as $k => $v) {
            if (in_array($v['name'], $check)) {
                continue;
            } else {
                array_push($check, $v['name']);
            }
            unset($v[$withTable]);
            $v['style'] = $withTable == 'planacar' ? 'new' : 'used';
            $duplicate_models[] = $v;

        }

        return $duplicate_models;
    }

    /**
     * 得到是否有点赞或者收藏
     * @param $plan_id
     * @param $user_id
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCollectionFabulous($tableName, $plan_id, $user_id)
    {
        return Db::name($tableName)
            ->where([
                'planacar_id' => $plan_id,
                'user_id' => $user_id
            ])
            ->find();
    }

    public function getAppointment($user_id,$plan_id,$planType)
    {
        $planField = null;
        switch ($planType){
            case 'new':
                $planField = 'plan_acar_id';
                break;
            case 'used':
                $planField = 'secondcar_rental_models_info_id';
                break;
            case 'logistics':
                $planField = 'logistics_project_id';
                break;
            default:
                return '参数错误';
        }

        return Db::name('subscribe')
            ->where([
                'user_id' => $user_id,
                $planField => $plan_id
            ]);
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


    public static function getEnergy($city_id)
    {
        $plans = CompanyStore::field('id')
            ->with(['logistics' => function ($query) {
                $query->withField('id,name,payment,monthly,nperlist,total_price,models_main_images');
            }, 'city' => function ($query) use ($city_id) {
                $query->where([
                    'city.status' => 'normal',
                    'city.id' => $city_id
                ]);

                $query->withField('cities_name');

            }])->where('statuss', 'normal')->select();

        foreach ($plans as $k => $v) {
            if ($v['logistics']['models_main_images']) {
                $v['logistics']['models_main_images'] = Config::get('upload')['cdnurl'] . $v['logistics']['models_main_images'];
            }
            if ($v['logistics']['id']) {
                $plans[$k] = $v['logistics'];
            } else {
                unset($plans[$k]);
            }

        }

        return $plans;
    }


    /**
     * 新车方案
     * @param $city
     * @param null $limit
     * @param bool $duplicate
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getNewCarPlan($city, $limit = null, $duplicate = false)
    {
        $check = [];                     //检查方案车型是否重复
        $info = CompanyStore::field('id,store_name')->with(['city' => function ($query) use ($city) {
            $query->where('city.id', $city);
            $query->withField('id');
        }, 'planacar' => function ($planacar) {
            $planacar->where([
                'acar_status' => 1,
            ]);
            $planacar->withField(['id', 'models_id', 'payment', 'monthly', 'subjectismenu', 'popularity', 'specialimages', 'specialismenu', 'models_main_images',
                'guide_price', 'flashviewismenu', 'recommendismenu', 'subject_id', 'label_id']);
        }])->where(function ($query) {
            $query->where([
                'statuss' => 'normal',
            ]);
        })->limit(!$limit ? '' : $limit)->select();

        $info = collection($info)->toArray();


        $planList = [];
        foreach ($info as $k => $v) {

            if ($v['planacar']['models_main_images']) {
                $v['planacar']['models_main_images'] = Config::get('upload')['cdnurl'] . $v['planacar']['models_main_images'];
            }

            if ($v['planacar']['specialimages']) {
                $v['planacar']['specialimages'] = Config::get('upload')['cdnurl'] . $v['planacar']['specialimages'];
            }

            if ($v['planacar']) {

                if ($duplicate) {
                    if (in_array($v['planacar']['models_id'], $check)) {              //根据方案的车型名去重
                        continue;
                    } else {
                        array_push($check, $v['planacar']['models_id']);
                    }
                }

                if ($v['planacar']['models_id']) {        //根据车型ID获取车型
                    $models_name = Db::name('models')
                        ->where([
                            'id' => $v['planacar']['models_id'],
                            'status' => 'normal'
                        ])
                        ->value('name');
                    if ($models_name) {
                        $v['planacar']['models_name'] = $models_name;
                    }

                }


                if ($v['planacar']['label_id']) {           //根据标签ID获取标签
                    $labels = Db::name('cms_label')
                        ->where([
                            'status' => 'normal',
                            'id' => $v['planacar']['label_id']
                        ])
                        ->field('name,lableimages')
                        ->find();

                    $labels['lableimages'] = Config::get('upload')['cdnurl'] . $labels['lableimages'];

                    if ($labels) {
                        $v['planacar']['labels'] = $labels;
                    }
                }


                $planList[] = $v['planacar'];
            }


        }
        return $planList;
    }


    /**
     * 二手车方案
     * @param $city_id
     * @param string $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getUsedPlan($city_id, $limit = '')
    {
        $plans = CompanyStore::field('id')
            ->with(['secondcarinfo' => function ($query) {
                $query->withField('id,models_id,newpayment,monthlypaymen,periods,totalprices,models_main_images,guide_price');
            }, 'city' => function ($query) use ($city_id) {
                $query->where([
                    'city.status' => 'normal',
                    'city.id' => $city_id
                ]);

                $query->withField('cities_name');

            }])->limit(!$limit ? '' : $limit)->where('statuss', 'normal')->select();

        //加入车型名称并返回方案
        $usedPlan = [];
        foreach ($plans as $k => $v) {
            if ($v['secondcarinfo'] && $v['secondcarinfo']['models_id']) {
                $v['secondcarinfo']['models_name'] = Db::name('models')
                    ->where('id', $v['secondcarinfo']['models_id'])
                    ->value('name');

                $v['secondcarinfo']['models_main_images'] = Config::get('upload')['cdnurl'] . $v['secondcarinfo']['models_main_images'];

                $usedPlan[] = $v['secondcarinfo'];
            }

        }

        return $usedPlan;
    }

    /**
     * 获取配置表信息
     * @return mixed
     */
    public static function getConfigShare()
    {
        return json_decode(Db::name('config')
            ->where('name', 'share')
            ->value('value'), true);

    }


    /**
     * 获取省份-城市
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getCityList()
    {
        $citys = Cities::where([
            'status' => 'normal',
        ])->field('id,pid,name,province_letter,cities_name')->select();

        //将省份单独拿出来
        $province = [];
        foreach ($citys as $k => $v) {
            if ($v['pid'] == 0) {
                unset($v['cities_name']);
                $province[$v['province_letter']][] = $v;

                //删除省份的数据，保留城市
                unset($citys[$k]);
            }

        }

        //省份加入对应的城市
        foreach ($province as $k => $v) {

            foreach ($v as $kk => $vv) {
                $temporary = [];

                foreach ($citys as $key => $value) {
                    if ($value['pid'] == $vv['id']) {
                        unset($value['name']);
                        unset($value['province_letter']);
                        $temporary[] = $value;
                    }
                }

                //加入满足条件的城市数据
                $province[$k][$kk]['citys'] = $temporary;
            }

        }

        return $province;
    }
}