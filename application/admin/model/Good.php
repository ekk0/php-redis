<?php
/**
 * Author: ekk0
 * Date: 2018/9/24 17:56
 */
namespace app\admin\model;
use app\admin\model\Redis;
use app\admin\controller\Order;

class Good extends \think\Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'good';


    /**
     * 查询mysql库存剩余数量
     * @param $uid
     */
    static public function QueryGoodTotal(){
        $good_count = Good::where(['id'=>(new Order())->good_id])->sum("counts");
        if($good_count <= 0){
            return 0;
        }else{
            return (int) $good_count;
        }
    }
    /**
     * 减去库存
     */
    static public function setDecQueryCounts(){
        if(Good::where(['id'=>(new Order())->good_id])->setDec('counts') !==false){
            return true;
        }
        return false;
    }

}