<?php

namespace app\common\model;

use think\Model;

class CarNewInventory extends Model
{
    // 表名
    protected $name = 'car_new_inventory';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'carprocess_text',
        'pledge_text'
    ];
    

    
    public function getCarprocessList()
    {
        return ['1' => __('Carprocess 1')];
    }     

    public function getPledgeList()
    {
        return ['1' => __('Pledge 1')];
    }     


    public function getCarprocessTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['carprocess'];
        $list = $this->getCarprocessList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getPledgeTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['pledge'];
        $list = $this->getPledgeList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function models()
    {
        return $this->belongsTo('Models', 'models_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
