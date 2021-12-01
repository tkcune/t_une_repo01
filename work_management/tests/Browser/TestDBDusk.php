<?php

namespace Tests\Browser;

//duskのテストデータ
class TestDBDusk {

    //部署作成のテストデータ
    public static function has_create_bs(){
        return [
            'dcbs01' => [
                [
                    'department_id' => 'bs00000010',
                    'name' => '部署T',
                    'status' => 11
                ]
            ]
        ];
    }
    
    //部署情報更新のテストデータ
    public static function has_update_bs(){
        return [
            'dcbs01' => [
                [
                    'department_id' => 'bs00000001',
                    'name' => '部署Z',
                    'status' => '14'
                ]
            ]
        ];
    }

    //人事情報更新のテストデータ
    public static function has_update_ji(){
        return [
            'dcji01' => [
                [
                    'personnel_id' => 'ji00000002',
                    'name' => '田中五郎',
                    'status' => '13'
                ]
            ]
        ];
    }

    //部署の削除のテストデータ
    public static function has_miss_delete_bs(){
        return [
            'dcbs01' => [
                [
                    'department_id' => 'bs00000004'
                ],
                [
                    'department_id' => 'bs00000008'
                ]
            ],
            'dcji01' => [
                [
                    'personnel_id' => 'ji00000008',
                ],
                [
                    'personnel_id' => 'ji00000009',
                ],
                [
                    'personnel_id' => 'ji00000010',
                ]
            ],
            'dccmta' => [
                [
                    'projection_id' => 'ta00000004'
                ]
            ],
            'dccmks' => [
                [
                    'lower_id' => 'ji00000008',
                    'high_id' => 'bs00000004'
                ],
                [
                    'lower_id' => 'ji00000009',
                    'high_id' => 'bs00000004'
                ],
                [
                    'lower_id' => 'ji00000010',
                    'high_id' => 'bs00000004'
                ],
                [
                    'lower_id' => 'bs00000008',
                    'high_id' => 'bs00000004'
                ],
                [
                    'lower_id' => 'ta00000004',
                    'high_id' => 'bs00000008'
                ]
            ]
        ];
    }

    //人事削除のテストデータ
    public static function has_miss_delete_ji(){
        return [
            'dcji01' => [
                [
                    'personnel_id' => 'ji00000003'
                ]
            ],
            'dccmks' => [
                [
                    'lower_id' => 'ji00000003',
                    'high_id' => 'bs00000002'
                ]
            ]
        ];
    }

    //部署の移動の削除のテストデータ
    public static function has_miss_move_bs(){
        return [
            'dccmks' => [
                [
                    'lower_id' => 'bs00000006',
                    'high_id' => 'bs00000003'
                ]
            ]
        ];
    }

    //部署の移動のテストデータ
    public static function has_move_bs(){
        return [
            'dccmks' => [
                [
                    'lower_id' => 'bs00000006',
                    'high_id' => 'bs00000007'
                ]
            ]
        ];
    }

    public static function has_move_ji(){
        return [
            'dccmks' => [
                [
                    'lower_id' => 'ji00000004',
                    'high_id' => 'bs00000003'
                ]
            ]
        ];
    }

    public static function has_miss_move_ji(){
        return [
            'dccmks' => [
                [
                    'lower_id' => 'ji00000004',
                    'high_id' => 'bs00000002'
                ]
            ]
        ];
    }
}

?>