<?php 

    namespace App\Libraries\php\Logic;

    use Illuminate\Support\Facades\DB;

    /**
     * 移動する部署の移動先が配下部署でないかを判断する動作クラス
     */ 
    class JudgmentHierarchy{

        /**
         * コンストラクタ
         * @param array $this->high_lists 上位部署IDリスト
         */
        public function __construct(){
            $this->high_lists = [];
        }

        /**
         * 移動先に上位部署が含まれていないかを判断する
         * @param string $client_id 顧客ID
         * @param string $select_id 選択部署ID
         * @param array $copy_id 移動する部署の複製ID
         * 
         * @var array $high_id 上位ID
         * 
         * @return boolean
         */
        public function judgmentHierarchy($client_id,$select_id,$copy_id){
            
            //移動先の上位IDを取得
            $high_id = DB::select('select high_id from dccmks where client_id = ? and lower_id = ?',[$client_id,$select_id]);

            //移動先の部署に更に上位部署があるかどうかの判断
            if(!empty($high_id)){
                //上位IDを配列に格納
                array_push($this->high_lists,$high_id);
                $select_id = $high_id[0]->high_id;
                $this->judgmentHierarchy($client_id,$select_id,$copy_id);
            }
            foreach($this->high_lists as $high_list){
                if($high_list[0]->high_id == $copy_id){
                    return false;
                }
            }
            return true;
        }
    }
