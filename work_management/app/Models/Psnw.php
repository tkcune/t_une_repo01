<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Psnw extends Model
{
    use HasFactory;

    protected $table = 'dcnw01';

    protected $guarded = array('client_id');

    public $timestamps = true;

    public function get_data($network_datas){
       $network = DB::table($this->table)->insert($network_datas);
        return $network;
    }

    protected $fillable = ['name', 'email', 'password','recieving_server','recieving_server','recieving_port_number'
    ,'sending_server','sending_server_way','sending_port_number','created_at','updated_at'];

    public static $rules = array(
        'name' => 'required',
        'email' => 'email|required',
        'password'=>'required',
        'recieving_server'=>'required',
        'recieving_server_way'=>'required',
        'recieving_port_number' => 'integer|required',
        'sending_server' => 'required',
        'sending_server_way' => 'required',
        'sending_port_number' => 'required|integer',
    );
}
