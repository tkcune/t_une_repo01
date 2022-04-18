<?php

namespace App\Libraries\php\Domain;

use App\Models\Date;
use Illuminate\Support\Facades\DB;
use App\Libraries\php\Service\ZeroPadding;

/**
 * 作業管理システム人員データベース動作クラス
 */

class PersonnelDataBase
{

    /**
     * 選択した人員の情報を取得するメソッド
     * @param $client 顧客ID
     * @param $select_id 選択したID
     *
     * @var   $data 取得データ
     *
     * @return  array $data
     */
    public static function get($client, $select_id)
    {

        $data = DB::select(
            'SELECT ji1.client_id,ji1.personnel_id,ji1.name,ji1.email,ji1.password,ji1.password_update_day,
                    ji1.status,ji1.management_personnel_id,ji1.login_authority,ji1.system_management,ji1.operation_start_date,
                    ji1.operation_end_date,ji1.remarks,ji1.created_at,ji1.updated_at,dcbs01.name AS high_name,lower_id,high_id,ji2.name AS management_name
                    FROM dcji01 AS ji1
                    left join dccmks on ji1.personnel_id = dccmks.lower_id
                    left join dcbs01 on dccmks.high_id = dcbs01.department_id
                    left join dcji01 AS ji2 on ji1.management_personnel_id = ji2.personnel_id
                    where ji1.client_id = ? and ji1.personnel_id = ?',
            [$client, $select_id]
        );

        //登録日・修正日のフォーマットを変換
        $date = new Date();
        $date->formatDate($data);

        return $data;
    }

    /**
     * 人員の情報を取得するメソッド
     * @param $client 顧客ID
     * @param $select_id 選択したID
     *
     * @var   $data 取得データ
     *
     * @return  array $data
     */
    public static function getData($client, $select_id)
    {

        $data = DB::select('select * from dcji01 where client_id = ? and personnel_id = ?', [$client, $select_id]);

        return $data;
    }

    /**
     * 全ての人員の情報を取得するメソッド
     * @param $client 顧客ID
     *
     * @var   $data 取得データ
     *
     * @return  array $data
     */
    public static function getAll($client)
    {

        $data = DB::select('select dcji01.client_id ,personnel_id,name,email,password,password_update_day,status,management_personnel_id,login_authority,system_management,operation_start_date,operation_end_date,
            remarks,dcji01.created_at, dcji01.updated_at ,high_id ,lower_id from dcji01 inner join dccmks on dcji01.personnel_id = dccmks.lower_id and dcji01.client_id = ?', [$client]);

        return $data;
    }

    /**
     * 一覧の人員データの取得
     * @param $client 顧客ID
     *
     * @var   $data 取得データ
     * @var   $date App\Models\Date;
     *
     * @return  array $data
     */
    public static function getList($client_id)
    {

        $data = DB::select(
            'SELECT ji1.client_id,ji1.personnel_id,ji1.name,ji1.email,ji1.password,ji1.password_update_day,
                    ji1.status,ji1.management_personnel_id,ji1.login_authority,ji1.system_management,ji1.operation_start_date,
                    ji1.operation_end_date,ji1.remarks,ji1.created_at,ji1.updated_at,dcbs01.name AS high_name,high_id,ji2.name AS management_name
                    FROM dcji01 AS ji1
                    left join dccmks on ji1.personnel_id = dccmks.lower_id
                    left join dcbs01 on dccmks.high_id = dcbs01.department_id
                    left join dcji01 AS ji2 on ji1.management_personnel_id = ji2.personnel_id
                    where ji1.client_id = ? order by ji1.personnel_id',
            [$client_id]
        );

        //登録日・修正日のフォーマットを変換
        $date = new Date();
        $date->formatDate($data);

        return $data;
    }

    /**
     * 選択部署の一覧人員データの取得
     * @param $client 顧客ID
     *
     * @var   $data 取得データ
     * @var   $date App\Models\Date;
     *
     * @return  array $data
     */

    public static function getSelectList($client_id, $select_id)
    {

        $data = DB::select(
            'SELECT ji1.client_id,ji1.personnel_id,ji1.name,ji1.email,ji1.password,ji1.password_update_day,
                    ji1.status,ji1.management_personnel_id,ji1.login_authority,ji1.system_management,ji1.operation_start_date,
                    ji1.operation_end_date,ji1.remarks,ji1.created_at,ji1.updated_at,dcbs01.name AS high_name,high_id,ji2.name AS management_name
                    FROM dcji01 AS ji1
                    left join dccmks on ji1.personnel_id = dccmks.lower_id
                    left join dcbs01 on dccmks.high_id = dcbs01.department_id
                    left join dcji01 AS ji2 on ji1.management_personnel_id = ji2.personnel_id
                    where ji1.client_id = ? and high_id = ? order by ji1.personnel_id',
            [$client_id, $select_id]
        );

        //登録日・修正日のフォーマットを変換
        $date = new Date();
        $date->formatDate($data);

        return $data;
    }

    /**
     * システム管理者リストの取得
     * @param $client 顧客ID
     *
     * @var   $list システム管理者リスト
     *
     * @return  array $list
     */

    public static function getSystemManagement($client_id)
    {

        $list = DB::select('SELECT name,personnel_id from dcji01 where client_id = ?
            and system_management = 1 order by personnel_id', [$client_id]);

        return $list;
    }

    /**
     * 最新の人員IDを取得
     * @param $client 顧客ID
     *
     * @var   $id Id
     *
     * @return  array $id
     */

    public static function getId($client_id)
    {

        $id = DB::select('select personnel_id from dcji01 where client_id = ?
            order by personnel_id desc limit 1', [$client_id]);

        return $id;
    }

    /**
     * 最新の人員IDを生成
     * @param $client 顧客ID
     *
     * @var   $id Id
     *
     * @return  array $personnel_id
     */

    public static function getNewId($client_id)
    {

        $id = DB::select('select personnel_id from dcji01 where client_id = ?
            order by personnel_id desc limit 1', [$client_id]);

        if (empty($id)) {
            $personnel_id = "ji00000001";
        } else {
            //登録する番号を作成
            $padding = new ZeroPadding();
            $personnel_id = $padding->padding($id[0]->personnel_id);
        }

        return $personnel_id;
    }

    /**
     * 選択した人員データを取得
     * @param $client 顧客ID
     * @param $select_id 選択したID
     *
     * @var   $data 取得データ
     *
     * @return  array $data
     */
    public static function getClick($client, $select_id)
    {

        $data = DB::select('select dcji01.client_id,personnel_id,name,email,password,
            password_update_day,status,management_personnel_id,login_authority,system_management,operation_start_date,
            operation_end_date,remarks,dcji01.created_at,dcji01.updated_at,high_id from dcji01 inner join dccmks
            on dcji01.personnel_id = dccmks.lower_id where dcji01.client_id = ?
            and dcji01.personnel_id = ?', [$client, $select_id]);

        return $data;
    }

    /**
     * 選択した人員の所属部署を取得
     * @param $client 顧客ID
     * @param $select_id 選択したID
     *
     * @var   $data 取得データ
     *
     * @return  array $data
     */
    public static function getClickDepartment($client, $select_id)
    {

        $data = DB::select('select high_id from dccmks where client_id = ?
            and lower_id = ?', [$client, $select_id]);

        return $data;
    }

    /**
     * 選択した人員の上位IDを取得
     * @param $client 顧客ID
     * @param $personnel_id 選択したID
     *
     * @var   $high_id 取得データ
     *
     * @return  array $high_id
     */
    public static function getHighId($client, $personnel_id)
    {

        $high_id = DB::select('select high_id from dcji01 inner join dccmks on dcji01.personnel_id = dccmks.lower_id where dcji01.client_id = ?
            and dcji01.personnel_id = ?', [$client, $personnel_id]);

        return $high_id;
    }

    /**
     * 検索
     * @param $client 顧客ID
     * @param $search 検索文字
     *
     * @var   $data 取得データ
     *
     * @return  array $data
     */
    public static function search($client, $select_id, $search)
    {

        $data = DB::select('SELECT ji1.client_id,ji1.personnel_id,ji1.name,ji1.email,ji1.password,ji1.password_update_day,
            ji1.status,ji1.management_personnel_id,ji1.login_authority,ji1.system_management,ji1.operation_start_date,
            ji1.operation_end_date,ji1.remarks,ji1.created_at,ji1.updated_at,dcbs01.name AS high_name,high_id,ji2.name AS management_name
            FROM dcji01 AS ji1
            left join dccmks on ji1.personnel_id = dccmks.lower_id
            left join dcbs01 on dccmks.high_id = dcbs01.department_id
            left join dcji01 AS ji2 on ji1.management_personnel_id = ji2.personnel_id
            where ji1.client_id = ? and high_id = ? and ji1.name like ? order by ji1.personnel_id', [$client, $select_id, '%' . $search . '%']);

        //登録日・修正日のフォーマットを変換
        $date = new Date();
        $date->formatDate($data);

        return $data;
    }

    /**
     * 作業場所概要画面で検索した人員データを取得するメソッド
     * @param $client 顧客ID
     * @param $select_id 選択したID
     *
     * @var   $data 取得データ
     *
     * @return  array $data
     */
    public static function getSearchTop($client_id, $search2)
    {

        $data = DB::select('SELECT ji1.client_id,ji1.personnel_id,ji1.name,ji1.email,ji1.password,ji1.password_update_day,
            ji1.status,ji1.management_personnel_id,ji1.login_authority,ji1.system_management,ji1.operation_start_date,
            ji1.operation_end_date,ji1.remarks,ji1.created_at,ji1.updated_at,dcbs01.name
            AS high_name,high_id,ji2.name AS management_name FROM dcji01 as ji1
            left join dccmks  on ji1.personnel_id = dccmks.lower_id
            left join dcbs01 on dccmks.high_id = dcbs01.department_id
            left join dcji01 AS ji2 on ji1.management_personnel_id = ji2.personnel_id
            where ji1.client_id = ? and ji1.name like ? order by ji1.personnel_id', [$client_id, '%' . $search2 . '%']);

        //登録日・修正日のフォーマットを変換
        $date = new Date();
        $date->formatDate($data);


        return $data;
    }

    /**
     * 登録
     * @param $client_id 顧客ID
     * @param $personnel_id 人員ID
     * @param $name 名前
     * @param $email メールアドレス
     * @param $password パスワード
     * @param $status 状態
     * @param $login_authority ログイン権限
     * @param $system_management 管理者権限
     * @param $remarks 備考
     */
    public static function insert($client_id, $personnel_id, $name, $email, $password, $status, $login_authority, $system_management, $remarks)
    {

        $date = new Date();

        DB::insert(
            'insert into dcji01
            (client_id,
            personnel_id,
            name,
            email,
            password,
            password_update_day,
            status,
            management_personnel_id,
            login_authority,
            system_management,
            operation_start_date,
            remarks
            )
            VALUE (?,?,?,?,?,?,?,?,?,?,?,?)',
            [
                $client_id,
                $personnel_id,
                $name,
                $email,
                $password,
                $date->today(),
                $status,
                $personnel_id,
                $login_authority,
                $system_management,
                $date->today(),
                $remarks
            ]
        );
    }

    /**
     * 複製
     * @param $client_id 顧客ID
     * @param $personnel_id 人員ID
     * @param $name 名前
     * @param $email メールアドレス
     * @param $password パスワード
     * @param $password_update_dayパスワード更新日
     * @param $status 状態
     * @param $management_personnel_id 管理者ID
     * @param $login_authority ログイン権限
     * @param $system_management 管理者権限
     * @param $operation_start_date 稼働開始日
     * @param $operation_end_date 稼働終了日
     * @param $remarks 備考
     */
    public static function copy(
        $client_id,
        $personnel_id,
        $name,
        $email,
        $password,
        $password_update_day,
        $status,
        $management_personnel_id,
        $login_authority,
        $system_management,
        $operation_start_date,
        $operation_end_date,
        $remarks
    ) {
        DB::insert(
            'insert into dcji01
                (client_id,
                personnel_id,
                name,
                email,
                password,
                password_update_day,
                status,
                management_personnel_id,
                login_authority,
                system_management,
                operation_start_date,
                operation_end_date,
                remarks)
                VALUE (?,?,?,?,?,?,?,?,?,?,?,?,?)',
            [
                $client_id,
                $personnel_id,
                $name,
                $email,
                $password,
                $password_update_day,
                $status,
                $management_personnel_id,
                $login_authority,
                $system_management,
                $operation_start_date,
                $operation_end_date,
                $remarks
            ]
        );
    }

    /**
     * 更新
     * @param $postPassword 送信されたパスワード
     * @param $name 名前
     * @param $status 状態
     * @param $mail メールアドレス
     * @param $password パスワード
     * @param $login_authority ログイン権限
     * @param $system_management 管理者権限
     * @param $start_day 稼働開始日
     * @param $finish_day 稼働終了日
     * @param $remarks 備考
     * @param $client_id 顧客ID
     * @param $personnel_id 人員ID
     *
     */

    public static function update($postPassword, $name, $status, $mail, $password, $management_number, $login_authority, $system_management, $start_day, $finish_day, $remarks, $client_id, $personnel_id)
    {

        if ($postPassword == "ValidationOK") {
            DB::update(
                'update dcji01 set name = ?,status = ?,email = ?,management_personnel_id = ?,login_authority = ?,system_management = ?,operation_start_date = ?,operation_end_date = ?,remarks = ? where client_id = ? and personnel_id = ?',
                [$name, $status, $mail, $management_number, $login_authority, $system_management, $start_day, $finish_day, $remarks, $client_id, $personnel_id]
            );
        } else {
            DB::update(
                'update dcji01 set name = ?,status = ?,email = ?,password = ?,management_personnel_id = ?,login_authority = ?,system_management = ?,operation_start_date = ?,operation_end_date = ?,remarks = ? where client_id = ? and personnel_id = ?',
                [$name, $status, $mail, $password, $management_number, $login_authority, $system_management, $start_day, $finish_day, $remarks, $client_id, $personnel_id]
            );
        }
    }

    /**
     * 削除
     * @param $client 顧客ID
     * @param $personnel_id 人員ID
     *
     */
    public static function delete($client_id, $personnel_id)
    {

        DB::delete('delete from dcji01 where client_id = ? and personnel_id = ?', [$client_id, $personnel_id]);
    }
}
