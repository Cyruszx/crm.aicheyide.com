<?php

namespace app\admin\controller;

use app\common\controller\Backend;
// use app\admin\controller\salesmanagement\Customerlisttabs;
use think\Config;
use think\Db;
use think\Cache;





/**
 * 控制台
 *
 * @icon fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Dashboard extends Backend
{

    /**
     * 查看
     */
    public function index()
    {

        $time = \fast\Date::unixtime('month');
        
        $month = date("Y-m", $time);
                
        $day = date('t', strtotime("$month +1 month -1 day"));

        $months = date("Y-m", $time + (86400 * $day));
        
        $firstday = strtotime(date('Y-m-01', strtotime($month)));

        $secondday = strtotime(date('Y-m-01', strtotime($months)));

        //新车历史成交数
        $newcount = Db::name('sales_order')
                ->where('review_the_data', "the_car")
                ->count();
        
        //新车本月成交数
        $todaynewtake = Db::name('sales_order')
                ->where('review_the_data', "the_car")
                ->where('delivery_datetime', 'between', [$firstday, $secondday])
                ->count();
        //直客成交数
        $new_direct_the_guest = Db::name('sales_order')
                ->where('review_the_data', "the_car")
                ->where('customer_source', 'direct_the_guest')
                ->where('delivery_datetime', 'between', [$firstday, $secondday])
                ->count();
        //转介绍成交数
        $new_turn_to_introduce = Db::name('sales_order')
                ->where('review_the_data', "the_car")
                ->where('customer_source', 'turn_to_introduce')
                ->where('delivery_datetime', 'between', [$firstday, $secondday])
                ->count();
        //新车本月订车数
        $todayneworder = Db::name('sales_order')
                ->where('review_the_data', 'NEQ', "the_car")
                ->where('createtime', 'between', [$firstday, $secondday])
                ->count();




        //租车历史出租数
        $rentalcount = Db::name('rental_order')
                ->where('review_the_data', "for_the_car")
                ->count();
        //租车本月成交数
        $todayrentaltake = Db::name('rental_order')
                ->where('review_the_data', "for_the_car")
                ->where('delivery_datetime', 'between', [$firstday, $secondday])
                ->count();

        //直客成交数
        $rental_direct_the_guest = Db::name('rental_order')
                ->where('review_the_data', "for_the_car")
                ->where('customer_source', 'direct_the_guest')
                ->where('delivery_datetime', 'between', [$firstday, $secondday])
                ->count();
        //转介绍成交数
        $rental_turn_to_introduce = Db::name('rental_order')
                ->where('review_the_data', "for_the_car")
                ->where('customer_source', 'turn_to_introduce')
                ->where('delivery_datetime', 'between', [$firstday, $secondday])
                ->count();
        //租车本月订车数
        $todayrentalorder = Db::name('rental_order')
                ->where('review_the_data', 'NEQ', "for_the_car")
                ->where('createtime', 'between', [$firstday, $secondday])
                ->count();





        //二手车历史成交数
        $secondcount = Db::name('second_sales_order')
                 ->where('review_the_data', "the_car")
                ->count();
        //二手车本月成交数
        $todaysecondtake = Db::name('second_sales_order')
                ->where('review_the_data', "the_car")
                ->where('delivery_datetime', 'between', [$firstday, $secondday])
                ->count();
        
        //直客成交数
        $second_direct_the_guest = Db::name('second_sales_order')
                ->where('review_the_data', "the_car")
                ->where('customer_source', 'direct_the_guest')
                ->where('delivery_datetime', 'between', [$firstday, $secondday])
                ->count();
        //转介绍成交数
        $second_turn_to_introduce = Db::name('second_sales_order')
                ->where('review_the_data', "the_car")
                ->where('customer_source', 'turn_to_introduce')
                ->where('delivery_datetime', 'between', [$firstday, $secondday])
                ->count();
        //二手车本月订车数
        $todaysecondorder = Db::name('second_sales_order')
                ->where('review_the_data', 'NEQ', "the_car")
                ->where('createtime', 'between', [$firstday, $secondday])
                ->count();




        //全款历史成交数
        $fullcount = Db::name('full_parment_order')
                ->where('review_the_data', "for_the_car")
                ->count();
        //全款车本月成交数
        $todayfulltake = Db::name('full_parment_order')
                ->where('review_the_data', "for_the_car")
                ->where('delivery_datetime', 'between', [$firstday, $secondday])
                ->count();
        
        //直客成交数
        $full_direct_the_guest = Db::name('full_parment_order')
                ->where('review_the_data', "for_the_car")
                ->where('customer_source', 'straight')
                ->where('delivery_datetime', 'between', [$firstday, $secondday])
                ->count();
        //转介绍成交数
        $full_turn_to_introduce = Db::name('full_parment_order')
                ->where('review_the_data', "for_the_car")
                ->where('customer_source', 'introduce')
                ->where('delivery_datetime', 'between', [$firstday, $secondday])
                ->count();
        //全款车本月订车数
        $todayfullorder = Db::name('full_parment_order')
                ->where('review_the_data', 'NEQ', "for_the_car")
                ->where('createtime', 'between', [$firstday, $secondday])
                ->count();
        



        //全款二手车历史成交数
        $fullsecondcount = Db::name('second_full_order')
                ->where('review_the_data', "for_the_car")
                ->count();
        //全款二手车本月成交数
        $todayfullsecondtake = Db::name('second_full_order')
                ->where('review_the_data', "for_the_car")
                ->where('delivery_datetime', 'between', [$firstday, $secondday])
                ->count();

        //直客成交数
        $fullsecond_direct_the_guest = Db::name('second_full_order')
                ->where('review_the_data', "for_the_car")
                ->where('customer_source', 'straight')
                ->where('delivery_datetime', 'between', [$firstday, $secondday])
                ->count();
        //转介绍成交数
        $fullsecond_turn_to_introduce = Db::name('second_full_order')
                ->where('review_the_data', "for_the_car")
                ->where('customer_source', 'introduce')
                ->where('delivery_datetime', 'between', [$firstday, $secondday])
                ->count();
        //全款二手车本月订车数
        $todayfullsecondorder = Db::name('second_full_order')
                ->where('review_the_data', 'NEQ', "for_the_car")
                ->where('createtime', 'between', [$firstday, $secondday])
                ->count();



        //总共成交数
        $count = $newcount + $secondcount + $rentalcount + $fullcount + $fullsecondcount;
        //总共订车数
        $todayorder = $todayneworder + $todayrentalorder + $todaysecondorder + $todayfullorder + $todayfullsecondorder;
        //本月成交总数
        $todaytake  = $todaynewtake + $todaysecondtake + $todayrentaltake + $todayfulltake + $todayfullsecondtake;
        //本月直客成交总数
        $direct_the_guest = $new_direct_the_guest + $rental_direct_the_guest + $second_direct_the_guest + $full_direct_the_guest + $fullsecond_direct_the_guest;
        //本月转介绍成交总数
        $turn_to_introduce = $new_turn_to_introduce + $rental_turn_to_introduce + $second_turn_to_introduce + $full_turn_to_introduce + $fullsecond_turn_to_introduce;


        if($todaytake !== 0){
                $guest = round(($direct_the_guest / $todaytake) * 10000)  / 10000 * 100 . '%';
        }
        else{
                $guest = 0 . '%';
        }
        if($todaytake !== 0){
                $introduce = round(($turn_to_introduce / $todaytake) * 10000) / 10000 * 100 . '%';
        }
        else{
                $introduce = 0 . '%';
        }

        $newsales = Cache::get('newsales');
        $rentalsales = Cache::get('rentalsales');
        $secondsales = Cache::get('secondsales');
        $fullsales = Cache::get('fullsales');
        $fullsecondsales = Cache::get('fullsecondsales');
       
        if(!$newsales || !$rentalosales || !$secondsales || !$fullesales || !$fullsecondsales){

                $seventtime = \fast\Date::unixtime('month', -2);
        
                $month = date("Y-m", $seventtime);
                
                $day = date('t', strtotime("$month +1 month -1 day"));
                for ($i = 0; $i < 4; $i++)
                {
                        $months = date("Y-m", $seventtime + (($i+1) * 86400 * $day));
                        $firstday = strtotime(date('Y-m-01', strtotime($month)));
                        $secondday = strtotime(date('Y-m-01', strtotime($months)));

                        //新车销售情况
                        $newtake = Db::name('sales_order')
                                ->where('review_the_data', 'the_car')
                                ->where('delivery_datetime', 'between', [$firstday, $secondday])
                                ->count();
                        //租车出租情况
                        $rentaltake = Db::name('rental_order')
                                ->where('review_the_data', 'for_the_car')
                                ->where('delivery_datetime', 'between', [$firstday, $secondday])
                                ->count();
                        //二手车销售情况      
                        $secondtake = Db::name('second_sales_order')
                                ->where('review_the_data', 'the_car')
                                ->where('delivery_datetime', 'between', [$firstday, $secondday])
                                ->count();
                        //全款车销售情况      
                        $fulltake = Db::name('full_parment_order')
                                ->where('review_the_data', 'for_the_car')
                                ->where('delivery_datetime', 'between', [$firstday, $secondday])
                                ->count();
                        //全款车销售情况      
                        $fullsecondtake = Db::name('second_full_order')
                                ->where('review_the_data', 'for_the_car')
                                ->where('delivery_datetime', 'between', [$firstday, $secondday])
                                ->count();
                        
                        
                        //新车销售情况
                        $newsales[$month . '(月)'] = $newtake;
                        //租车出租情况
                        $rentalsales[$month . '(月)'] = $rentaltake ;
                        //二手车销售情况
                        $secondsales[$month . '(月)'] = $secondtake;
                        //全款新车销售情况
                        $fullsales[$month . '(月)'] = $fulltake;
                        //全款二手车销售情况
                        $fullsecondsales[$month . '(月)'] = $fullsecondtake;

                        $month = date("Y-m", $seventtime + (($i+1) * 86400 * $day));
                        
                        $day = date('t', strtotime("$months +1 month -1 day"));

                }

                Cache::set('newsales', $newsales);
                Cache::set('rentalsales', $rentalsales);
                Cache::set('secondsales', $secondsales);
                Cache::set('fullsales', $fullsales);
                Cache::set('fullsecondsales', $fullsecondsales);


        }
        

        // pr($newsales);
        // pr($rentalsales);
        // pr($secondsales);
        // pr($fullsales);
        // die;

        

        $this->view->assign([
        //     //新车数据
        //     'newcount'            => $newcount,
        //     'todaynewtake'        => $todaynewtake,
        //     'todayneworder'       => $todayneworder,
        //     'newguest'            => $newguest,
        //     'newintroduce'        => $newintroduce,

        //      //租车数据
        //     'rentalcount'         => $rentalcount,
        //     'todayrentaltake'     => $todayrentaltake,
        //     'todayrentalorder'    => $todayrentalorder,
        //     'rentalguest'         => $rentalguest,
        //     'rentalintroduce'     => $rentalintroduce,

        //     //二手车数据
        //     'secondcount'         => $secondcount,
        //     'todaysecondtake'     => $todaysecondtake,
        //     'todaysecondorder'    => $todaysecondorder,
        //     'secondguest'         => $secondguest,
        //     'secondintroduce'     => $secondintroduce,

        //     //全款数据
        //     'fullcount'           => $fullcount,
        //     'todayfulltake'       => $todayfulltake,
        //     'todayfullorder'      => $todayfullorder,
        //     'fullguest'           => $fullguest,
        //     'fullintroduce'       => $fullintroduce,

            'count'        => $count,
            'todayorder'   => $todayorder,
            'todaytake'    => $todaytake,
             'guest'       => $guest,
             'introduce'   => $introduce,
            //销售情况
            'newsales'            => $newsales,
            'rentalsales'         => $rentalsales,
            'secondsales'         => $secondsales,
            'fullsales'           => $fullsales,
            'fullsecondsales'     => $fullsecondsales
            
        ]);

        return $this->view->fetch();
    }

}
