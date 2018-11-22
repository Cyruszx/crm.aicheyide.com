<?php

namespace addons\cms\model;

use think\Model;

class CompanyStore extends Model
{
    // 表名
    protected $name = 'cms_company_store';

    // 自动写入时间戳字段
//    protected $autoWriteTimestamp = 'int';

//    // 定义时间戳字段名
//    protected $createTime = 'createtime';
//    protected $updateTime = 'updatetime';

    // 追加属性
//    protected $append = [
//        'status_text'
//    ];


//    public function getStatusList()
//    {
//        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
//    }


//    public function getStatusTextAttr($value, $data)
//    {
//        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
//        $list = $this->getStatusList();
//        return isset($list[$value]) ? $list[$value] : '';
//    }


    public function city()
    {
        return $this->belongsTo('Cities', 'city_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function planacar()
    {
        return $this->hasOne('PlanAcar', 'store_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function secondcarinfo()
    {
        return $this->hasOne('SecondcarRentalModelsInfo', 'store_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    /**
     * 统计门店下新车所有可卖车型个数
     * @return \think\model\relation\HasMany
     */
    public function planacarCount()
    {
        return $this->hasMany('PlanAcar', 'store_id', 'id');
    }

    /**
     * 统计门店下二手车所有可卖车型个数
     * @return \think\model\relation\HasMany
     */
    public function usedcarCount()
    {
        return $this->hasMany('UsedCar', 'store_id', 'id');
    }

    /**
     * 统计门店下新能源车所有可卖车型个数
     * @return \think\model\relation\HasMany
     */
    public function logisticsCount()
    {
        return $this->hasMany('Logistics', 'store_id', 'id');
    }

    /**
     * 查询门店下有多少张优惠券
     * @param $store_id 门店Id
     * @param $user_id 用户ID
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getLogistics($store_id,$user_id)
    {

        return collection(Coupon::whereLike('store_ids','%,'.$store_id.',%','like')
//            ->whereNotLike('user_id','%,'.$user_id.',%','not like')
            ->where(function ($q) use($user_id) {
            $q->where([
//                'user_id'=>['like','%,'.$user_id.',%'],
                'ismenu'=>1,//正常上架状态
                'release_datetime'=>['LT',time()],//领用截至日期小于当前时间
                'circulation'=>['GT',0], // 发放总量大于0
                'remaining_amount'=>['GT',0]]); // 剩余总量大于0
        })->select())->toArray();
    }

    public function logistics()
    {
        return $this->hasOne('Logistics', 'store_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


}
