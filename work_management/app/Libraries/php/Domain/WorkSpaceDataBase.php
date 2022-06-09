<?php

namespace App\Libraries\php\Domain;

use Illuminate\Support\Facades\DB;
use App\Facades\OutputLog;
use App\Libraries\php\Service\ZeroPadding;
use App\Libraries\php\Service\DatabaseException;

/**
 * 作業場所データベース動作クラス
 */
class WorkSpaceDataBase
{

    /**
     * 選択した作業場所の情報を取得するメソッド
     * @param $client_id 顧客ID
     * @param $select_id 選択したID
     *
     * @var   $data 取得データ
     *
     * @return  array $data
     */
    public static function get($client_id, $select_id)
    {
        $data = DB::select('SELECT sb1.client_id,sb1.space_id,sb1.name, sb1.management_personnel_id,
        sb1.post_code,sb1.address1, sb1.address2,sb1.URL,sb1.remarks,sb1.created_at,sb1.updated_at, sb2.name
        AS high_name, high_id, dcji01.name AS management_name FROM dcsb01 as sb1
        left join dccmks on sb1.space_id = dccmks.lower_id
        left join dcsb01 as sb2 on dccmks.high_id = sb2.space_id
        left join dcji01 on sb1.management_personnel_id = dcji01.personnel_id
        where sb1.client_id = ? and sb1.space_id = ? order by sb1.space_id;', [$client_id, $select_id]);

        return $data;
    }

    /**
     * 全ての作業場所データを取得するメソッド
     * @param $client_id 顧客ID
     *
     * @var   $data 取得データ
     *
     * @return  array $data
     */
    public static function getAll($client_id)
    {

        $data = DB::select('SELECT sb1.client_id,sb1.space_id,sb1.name, sb1.management_personnel_id,
        sb1.post_code,sb1.address1, sb1.address2,sb1.URL,sb1.remarks,sb1.created_at,sb1.updated_at, sb2.name
        AS high_name,high_id,dcji01.name AS management_name FROM dcsb01 AS sb1
        left join dccmks on sb1.space_id = dccmks.lower_id
        left join dcsb01 as sb2 on dccmks.high_id = sb2.space_id
        left join dcji01 on sb1.management_personnel_id = dcji01.personnel_id
        where sb1.client_id = ? order by sb1.space_id', [$client_id]);
        return $data;
    }

    /**
     * 最新の作業場所を取得
     * @param $client_id 顧客ID
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
     * 一覧の作業場所データの取得
     * @param $client_id 顧客ID
     *
     * @var   $data 取得データ
     *
     * @return  array $data
     */
    public static function getList($client_id)
    {
        $data = DB::select(
            'SELECT sb1.client_id,sb1.space_id,sb1.name,sb1.management_personnel_id,
            sb1.post_code,sb1.address1, sb1.address2,sb1.URL,sb1.remarks,sb1.created_at,sb1.updated_at,sb2.name
            AS high_name,high_id,dcji01.name AS management_name FROM dcsb01 AS sb1
            left join dccmks on sb1.space_id = dccmks.lower_id
            left join dcsb01 as sb2 on dccmks.high_id = sb2.space_id
            left join dcji01 on sb1.management_personnel_id = dcji01.personnel_id
            where sb1.client_id = ? and dccmks.high_id = "sb00000000" order by sb1.space_id',
            [$client_id]
        );

        return $data;
    }

    /**
     * 選択した作業場所の配下を取得
     * @param $client_id 顧客ID
     * @param $select_id 選択したID
     *
     * @var   $data 取得データ
     *
     * @return  array $data
     */
    public static function getSelectList($client_id, $select_id)
    {

        $data = DB::select('SELECT sb1.client_id,sb1.space_id,sb1.name, sb1.management_personnel_id,
        sb1.post_code,sb1.address1, sb1.address2,sb1.URL,sb1.remarks,sb1.created_at,sb1.updated_at,sb2.name
        AS high_name, high_id, dcji01.name AS management_name FROM dcsb01 as sb1
		left join dccmks on sb1.space_id = dccmks.lower_id
        left join dcsb01 as sb2 on dccmks.high_id = sb2.space_id
        left join dcji01 on sb1.management_personnel_id = dcji01.personnel_id
        where sb1.client_id = ? and dccmks.high_id = ? order by sb1.space_id', [$client_id, $select_id]);

        return $data;
    }

    /**
     * 上位IDの取得
     * @param $client_id 顧客ID
     * @param $select_id 選択ID
     *
     * @var   $id 取得id
     *
     * @return  array $id
     */
    public static function getHigh($client_id, $select_id)
    {

        $id = DB::select('SELECT high_id FROM `dccmks` WHERE client_id = ? and lower_id = ?;', [$client_id, $select_id]);

        return $id;
    }

    /**
     * 登録
     * @param $client_id 顧客ID
     * @param $sapce_id 作業場所ID
     * @param $name 作業場所名称
     * @param $managment_personnel_id 管理者ID
     * @param $post_code 郵便番号
     * @param $address1 住所1
     * @param $address2 住所2
     * @param $URL アドレス
     * @param $remarks 備考
     */
    public static function insert($client_id, $space_id, $name, $management_personnel_id, $post_code, $address1, $address2, $URL, $remarks)
    {
        DB::insert(
            'insert into dcsb01
            (client_id,space_id,name,management_personnel_id, post_code,address1,address2,URL,remarks)
             value (?,?,?,?,?,?,?,?,?)',
            [$client_id, $space_id, $name, $management_personnel_id, $post_code, $address1, $address2, $URL, $remarks]
        );
    }

    /**
     * 作業場所を複製するメソッド
     *
     * @param $copy_id 複製ID
     * @param $client_id 顧客ID
     * @param $high 上位ID
     * @param $number 複製直前の最新の作業場所ID
     *
     */
    public function copy($copy_id, $client_id, $high, $number)
    {

        //複製するデータの取得
        try {
            $copy_space = DB::select('select * from dcsb01 where client_id = ?
                and space_id = ?', [$client_id, $copy_id]);
        } catch (\Exception $e) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');
            DatabaseException::common($e);
            return redirect()->route('pssb01.index');
        }

        //複製開始前の状態で、複製するデータに直下配下があるかどうかの確認
        try {
            $lists = DB::select(
                'select * from dccmks where client_id = ? and substring(lower_id, 1, 2) = "sb" and substring(lower_id, 3, 10) <= ? and high_id = ?',
                [$client_id, $number, $copy_id]
            );
        } catch (\Exception $e) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');
            DatabaseException::common($e);
            return redirect()->route('pssb01.index');
        }

        //顧客IDに対応した最新の作業場所IDを取得
        try {
            $id = DB::select('select space_id from dcsb01 where client_id = ?
                order by space_id desc limit 1', [$client_id]);
        } catch (\Exception $e) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');
            DatabaseException::common($e);
            return redirect()->route('pssb01.index');
        }

        //登録する番号を作成
        $padding = new ZeroPadding();
        $space_id = $padding->padding($id[0]->space_id);

        //データベースに作業場所情報を登録
        try {
            DB::insert(
                'insert into dcsb01 (client_id,space_id,name, management_personnel_id,post_code,address1,address2,URL,remarks)
                 value (?,?,?,?,?,?,?,?,?)',
                [
                    $client_id,
                    $space_id,
                    $copy_space[0]->name,
                    $copy_space[0]->management_personnel_id,
                    $copy_space[0]->post_code,
                    $copy_space[0]->address1,
                    $copy_space[0]->address2,
                    $copy_space[0]->URL,
                    $copy_space[0]->remarks
                ]
            );
        } catch (\Exception $e) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');
            DatabaseException::common($e);
            return redirect()->route('pssb01.index');
        }

        //データベースに階層情報を登録
        try {
            DB::insert(
                'insert into dccmks
                (client_id,lower_id,high_id)
                VALUE (?,?,?)',
                [$client_id, $space_id, $high]
            );
        } catch (\Exception $e) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');
            DatabaseException::common($e);
            return redirect()->route('pssb01.index');
        }

        if (isset($lists)) {
            foreach ($lists as $list) {
                $copy_id = $list->lower_id;
                $high = $space_id;
                $this->copy($copy_id, $client_id, $high, $number);
            }
        }
    }

    /**
     * 更新
     * @param $client_id 顧客ID
     * @param $sapce_id 作業場所ID
     * @param $name 作業場所名称
     * @param $managment_number 管理者ID
     * @param $post_code 郵便番号
     * @param $address1 住所1
     * @param $address2 住所2
     * @param $URL アドレス
     * @param $remarks 備考
     *
     */

    public static function update($client_id, $space_id, $name, $management_number, $post_code, $address1, $address2, $URL, $remarks)
    {
        DB::update(
            'update dcsb01 set name = ?,management_personnel_id = ?, post_code = ?,address1 = ?,
            address2 = ? , URL = ? ,remarks = ? where client_id=? and space_id = ?',
            [$name, $management_number, $post_code, $address1, $address2, $URL, $remarks, $client_id, $space_id]
        );
    }

    /**
     * 削除
     * @param $client_id 作業場所ID
     * @param $lower_id 下位ID
     *
     */
    public static function delete($client_id, $lower_id)
    { {

            DB::delete(
                'delete from dcsb01 where client_id = ? and space_id = ?',
                [$client_id, $lower_id]
            );
        }
    }

    /**
     * 検索した作業場所データを取得するメソッド
     * @param $client_id 顧客ID
     * @param $select_id 選択したID
     * @param $search 検索文字
     *
     * @var   $data 取得データ
     *
     * @return  array $data
     */
    public static function getSearchList($client_id, $select_id, $search)
    {

        $data = DB::select('SELECT sb1.client_id,sb1.space_id,sb1.name,sb1.management_personnel_id,
            sb1.post_code,sb1.address1, sb1.address2,sb1.URL,sb1.remarks,sb1.created_at,sb1.updated_at,sb2.name
            AS high_name,high_id,dcji01.name AS management_name FROM dcsb01 as sb1
            left join dccmks on sb1.space_id = dccmks.lower_id
            left join dcsb01 as sb2 on dccmks.high_id = sb2.space_id
            left join dcji01 on sb1.management_personnel_id = dcji01.personnel_id
            where sb1.client_id = ? and dccmks.high_id = ? and sb1.name like ? order by sb1.space_id', [$client_id, $select_id, '%' . $search . '%']);

        return $data;
    }

    /**
     * 作業場所概要画面で検索した作業場所データを取得するメソッド
     * @param $client_id 顧客ID
     * @param $search 検索文字
     *
     * @var   $data 取得データ
     *
     * @return  array $data
     */
    public static function getSearchTop($client_id, $search)
    {
        $data = DB::select('SELECT sb1.client_id,sb1.space_id,sb1.name,sb1.management_personnel_id,
            sb1.post_code,sb1.address1, sb1.address2,sb1.URL,sb1.remarks,sb1.created_at,sb1.updated_at,sb2.name
            AS high_name,high_id,dcji01.name AS management_name FROM dcsb01 as sb1
            left join dccmks on sb1.space_id = dccmks.lower_id
            left join dcsb01 as sb2 on dccmks.high_id = sb2.space_id
            left join dcji01 on sb1.management_personnel_id = dcji01.personnel_id
            where sb1.client_id = ?  and dccmks.high_id = "sb00000000" and sb1.name like ? order by sb1.space_id',
            [$client_id, '%' . $search . '%']);

        return $data;
    }
}
