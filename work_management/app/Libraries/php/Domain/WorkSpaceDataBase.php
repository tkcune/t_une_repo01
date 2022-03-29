<?php

namespace App\Libraries\php\Domain;

use App\Models\Date;
use Illuminate\Support\Facades\DB;
use App\Libraries\php\Service\ZeroPadding;

/**
 * 作業場所データベース動作クラス
 * PersonnelDataBase 作業管理システム人員データベース動作クラスの内容を流用
 */

class WorkSpaceDataBase
{

    /**
     * 選択した作業場所の情報を取得するメソッド
     * @param $client 顧客ID
     * @param $select_id 選択したID
     *
     * @var   $data 取得データ
     *
     * @return  array $data
     */
    public static function get($client_id, $select_id)
    {
        $data = DB::select('SELECT sb1.client_id,sb1.space_id,sb1.name, sb1.management_personnel_id,
        sb1.post_code,sb1.prefectural_office_location, sb1.address,sb1.URL,sb1.remarks,sb1.created_at,sb1.updated_at, sb2.name
        AS high_name, high_id, high_id, dcji01.name AS management_name FROM dcsb01 as sb1
        left join dccmks on sb1.space_id = dccmks.lower_id
        left join dcsb01 as sb2 on dccmks.high_id = sb2.space_id
        left join dcji01 on sb1.management_personnel_id = dcji01.personnel_id
        where sb1.client_id = ? and sb1.space_id = ? order by sb1.space_id;', [$client_id, $select_id]);

        return $data;
    }

    /**
     * 作業場所の情報を取得するメソッド
     * @param $space 作業場所ID
     * @param $select_id 選択したID
     *
     * @var   $data 取得データ
     *
     * @return  array $data
     */
    public static function getData($client, $select_id)
    {

        $data = DB::select('select * from dcsb01 where client_id = ?
            and space_id = ?', [$client, $select_id]);

        return $data;
    }

    /**
     * 全ての作業場所を取得するメソッド
     * @param $client 顧客ID
     *
     * @var   $data 取得データ
     *
     * @return  array $data
     */
    public static function getAll($client)
    {

        $data = DB::select('SELECT sb1.client_id,sb1.space_id,sb1.name, sb1.management_personnel_id, sb1.post_code,sb1.prefectural_office_location,
        sb1.address,sb1.URL,sb1.remarks,sb1.created_at,sb1.updated_at,sb2.name
        AS high_name,high_id,dcji01.name AS management_name FROM dcsb01 AS sb1
                            left join dccmks on sb1.space_id = dccmks.lower_id
                            left join dcsb01 as sb2 on dccmks.high_id = sb2.space_id
                            left join dcji01 on sb1.management_personnel_id = dcji01.personnel_id
                            where sb1.client_id = ? order by sb1.space_id', [$client]);

        return $data;
    }
    /**
     * 最新の作業場所を取得
     * @param $space 作業場所ID
     *
     * @var   $id Id
     *
     * @return  array $id
     */

    public static function getId($client_id)
    {

        $id = DB::select('select space_id from dcsb01 where client_id = ?
            order by space_id desc limit 1', [$client_id]);

        return $id;
    }

    /**
     * 最新の作業場所IDを生成
     * @param $space 作業場所ID
     *
     * @var   $id Id
     *
     * @return  array $personnel_id
     */

    public static function getNewId($client_id)
    {

        $id = DB::select('select space_id from dcsb01 where client_id = ?
            order by space_id desc limit 1', [$client_id]);

        if (empty($id)) {
            $space_id = "sb00000001";
        } else {
            //登録する番号を作成
            $padding = new ZeroPadding();
            $space_id = $padding->padding($id[0]->space_id);
        }

        return $space_id;
    }

    /**
     * 選択した作業場所データを取得
     * @param $client 顧客ID
     * @param $select_id 選択したID
     *
     * @var   $data 取得データ
     *
     * @return  array $data
     */
    public static function getClick($client, $space_id)
    {

        $data = DB::select('select dcsb01.space_id, name, management_personnel_id,
            post_code, prefectural_office_location, address, URL, remarks, dcsb01.created_at,
            dcsb01.updated_at, high_id from dcsb01 inner join dccmks on dcsb01.space_id = dccmks.lower_id
            where dcsb01.space_id = ?', [$client, $space_id]);

        return $data;
    }

    /**
     * 選択した作業場所の配下を取得
     * @param $space 作業場所ID
     * @param $select_id 選択したID
     *
     * @var   $data 取得データ
     *
     * @return  array $data
     */
    public static function getList($client_id, $select_id)
    {

        $data = DB::select('SELECT sb1.client_id,sb1.space_id,sb1.name, sb1.management_personnel_id,
        sb1.post_code,sb1.prefectural_office_location, sb1.address,sb1.URL,sb1.remarks,sb1.created_at,sb1.updated_at,sb2.name
        AS high_name, high_id, dcji01.name AS management_name FROM dcsb01 as sb1
		left join dccmks on sb1.space_id = dccmks.lower_id
        left join dcsb01 as sb2 on dccmks.high_id = sb2.space_id
        left join dcji01 on sb1.management_personnel_id = dcji01.personnel_id
        where sb1.client_id = ? and dccmks.high_id = ? order by sb1.space_id', [$client_id, $select_id]);

        return $data;
    }

    /**
     * 選択した作業場所の上位IDを取得
     * @param $space　作業場所ID
     * @param $space_id 選択したID
     *
     * @var   $high_id 取得データ
     *
     * @return  array $high_id
     */
    public static function getHighId($client, $space_id)
    {

        $high_id = DB::select('select high_id from dcsb01 inner join dccmks on dcsb01.space_id = dccmks.lower_id
        where dcsb01.client_id = ? and dcsb01.space_id = ?', [$client, $space_id]);

        return $high_id;
    }

    /**
     * 検索
     * @param $space 作業場所ID
     * @param $search 検索文字
     *
     * @var   $data 取得データ
     *
     * @return  array $data
     */
    public static function search($space, $search)
    {
        $data = DB::select('select dcsb01.space_id, name, management_personnel_id, post_code,
        prefectural_office_location, address, URL, remarks, dcsb01.created_at, dcsb01.updated_at,
        high_id, lower_id from dcsb01 inner join dccmks on dcsb01.space_id = dccmks.lower_id
        and dcsb01.name like ?', [$space, '%' . $search . '%']);

        return $data;
    }

    /**
     * 登録
     * @param $sapce_id 作業場所ID
     * @param $name 作業場所名称
     * @param $post_code 郵便番号
     * @param $prefectural_office_location 都道府県
     * @param $address 市区町村
     * @param $URL アドレス
     * @param $remarks 備考
     */
    public static function insert(
        $client_id,
        $space_id,
        $name,
        $management_personnel_id,
        $post_code,
        $prefectural_office_location,
        $address,
        $URL,
        $remarks
    ) {

        //dcsb01のカラム列
        $dcsb_columns = 'client_id, space_id, name, management_personnel_id, post_code, prefectural_office_location,
        address, URL, remarks';
        //カラム列のホルダー
        $dcsb_holder = '?, ?, ?, ?, ?, ?, ?, ?, ?';

        DB::insert(
            'insert into dcsb01(' . $dcsb_columns . ') VALUE (' . $dcsb_holder . ')',
            [$client_id, $space_id, $name, $management_personnel_id, $post_code, $prefectural_office_location, $address, $URL, $remarks]
        );
    }

    /**
     * 複製
     * @param $space_id 作業場所ID
     * @param $name 作業場所名称
     * @param $management_personnel_id 管理者ID
     * @param $post_code 郵便番号
     * @param $prefectural_office_location 都道府県
     * @param $address 市区町村
     * @param $URL アドレス
     * @param $remarks 備考
     */
    public static function copy(
        $space_id,
        $name,
        $management_personnel_id,
        $post_code,
        $prefectural_office_location,
        $address,
        $URL,
        $remarks
    ) {
        //dcsb01のカラム列
        $dcsb_columns = 'space_id, name, management_personnel_id, post_code, prefectural_office_location,
        address, URL, remarks, created_at, updated_at';
        //カラム列のホルダー
        $dcsb_holder = '?, ?, ?, ?, ?, ?, ?, ?, ?, ?';

        DB::insert(
            'insert into dcsb01 (' . $dcsb_columns . ') VALUE (' . $dcsb_holder . ')',
            [$space_id, $name, $management_personnel_id, $post_code, $prefectural_office_location, $address, $URL, $remarks]
        );
    }

    /**
     * 最上位作業場所データの取得
     * @param $client 顧客ID
     *
     * @var   $data 取得データ
     *
     * @return  array $data
     */
    public static function getTop($client_id)
    {

        $data = DB::select('select * from dcsb01 order by client_id = ? limit 1', [$client_id]);

        return $data;
    }

    public static function getClickTop($client, $high_id)
    {

        $data = DB::select('select * from dcsb01 where client_id = ? and space_id = ?', [$client, $high_id]);

        return $data;
    }

    /**
     * 更新
     * @param $name　作業場所名称
     * @param $post_code 郵便番号
     * @param $prefectural_office_location 都道府県
     * @param $address 市区町村
     * @param $URL アドレス
     * @param $remarks 備考
     * @param $space_id 作業場所ID
     *
     */

    public static function update(
        $client_id,
        $space_id,
        $name,
        $management_personnel_id,
        $post_code,
        $prefectural_office_location,
        $address,
        $URL,
        $remarks
    ) {
        DB::update(
            'update dcsb01 set name = ?,management_personnel_id = ?, post_code = ?,prefectural_office_location = ?,address = ? , URL = ? ,remarks = ?
            where client_id=? and space_id = ?',
            [$name, $management_personnel_id, $post_code, $prefectural_office_location, $address, $URL, $remarks, $client_id, $space_id]
        );
    }

    /**
     * 削除
     * @param $space_id 作業場所ID
     *
     */
    public static function delete($client_id, $lower_id)
    { {

            DB::delete(
                'delete from dcsb01 where client_id = ?
                and space_id = ?',
                [$client_id, $lower_id]
            );
        }
    }
}
