<?php

namespace App\Libraries\php\Service;

/**
 *作業場所詳細画面に必要な変数を渡すクラス
 */

class SpaceObject
{

    /**　
     * プロパティ
     * @var $client_id 顧客ID
     * @var $space_id 作業場所ID
     * @var $name 作業場所名
     * @var $management_number 管理者番号
     * @var $post_code 郵便番号
     * @var $prefectural_office_location 都道府県
     * @var $address 市区町村
     * @var $URL URL
     * @var $remarks 備考
     * @var $created_at 登録日
     * @var $updated_at 修正日
     *
     */

    public $client_id;
    public $space_id;
    public $name;
    public $management_number;
    public $post_code;
    public $prefectural_office_location;
    public $address;
    public $URL;
    public $remarks;
    public $created_at;
    public $updated_at;


    /**
     * 作業場所詳細画面に必要な変数をセットする
     * @param $space_details 作業場所データ
     *
     */
    public function setSpaceObject($space_details)
    {
        $this->name = $space_details[0]->name;
        $this->space_id = $space_details[0]->space_id;
        $this->management_number = $space_details[0]->management_personnel_id;
        $this->post_code = $space_details[0]->post_code;
        $this->prefectural_office_location = $space_details[0]->prefectural_office_location;
        $this->address = $space_details[0]->address;
        $this->URL = $space_details[0]->URL;
        $this->remarks = $space_details[0]->remarks;
        $this->created_at = $space_details[0]->created_at;
        $this->updated_at = $space_details[0]->updated_at;
    }
}
