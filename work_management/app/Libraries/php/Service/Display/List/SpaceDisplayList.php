<?php

namespace App\Libraries\php\Service\Display\List;

use App\Libraries\php\Domain\WorkSpaceDataBase;
use App\Libraries\php\Domain\ProjectionDataBase;

/**
 * 作業場所の一覧表示クラス
 */

class SpaceDisplayList extends AbstractDisplayList
{

    public static function get($client_id, $select_id)
    {

        //一覧に記載する作業場所データの取得
        $space_db = new WorkSpaceDataBase();

        //一覧に記載する掲示板データの取得
        $space_data = $space_db->getSelectList($client_id, $select_id);

        //一覧の投影部署データの取得
        $projection_db = new ProjectionDataBase();
        $projection_space = $projection_db->getSpaceList($client_id, $select_id);

        //投影データを一覧に追加
        $space_data = array_merge($space_data, $projection_space);

        return $space_data;
    }

    public static function search($client_id, $select_id, $search)
    {
        $space_db = new WorkSpaceDataBase();

        //一覧に記載する作業場所データの取得
        if ($select_id == 'sb00000000') {
            $space_data = $space_db->getSearchTop($client_id, $search);
        } else {
            $space_data = $space_db->getSearchList($client_id, $select_id, $search);
        }

        //一覧の投影部署データの取得
        $projection_db = new ProjectionDataBase();
        $projection_space = $projection_db->getSpaceSearch($client_id, $select_id, $search);

        //投影データを一覧に追加
        $space_data = array_merge($space_data, $projection_space);

        return $space_data;
    }
}
