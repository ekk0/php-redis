<?php
/**
 * Author: ekk0
 * Date: 2018/9/24 17:56
 */
namespace app\admin\model;
use app\admin\model\Redis;

class Order extends \think\Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'order';
    /**
     * 生产订单号
     * @param int $uid
     * @return int
     */
    static public function buildOrderId($uid = 0){
        if($uid > 0){
            $order = $uid.time().mt_rand(1000,9999);
            return $order;
        }
    }
    /**
     * 写入订单
     * @param $uid
     * @param $order_id
     */
    static public function addOrder($uid ,$order_id ,$good_id){

            $data['good_id'] = $good_id;
            $data['order_id'] = $order_id;
            $data['uid'] = $uid;
            $data['add_time'] = time();
            if(Order::insert($data) !==false){
                //dump(self::getLastSql());
                return self::getLastInsID();
            }
            return false;
    }


    /**
     * 查询redis队列数量
     */
    static public function getGoodTotal(){
        $good_count = (Redis::getConn())->lLen(Redis::$good_total_field);
        if($good_count <= 0){
            return 0;
        }else{
            return (int) $good_count;
        }
    }


    /**
     * 减去redis库存
     * @param int $good_id
     * @return bool
     */
    static public function setDecRedisLlen(){
        if(Redis::getConn()->lPop(Redis::$good_total_field)!==false){
            return true;
        }
        return false;
    }


}