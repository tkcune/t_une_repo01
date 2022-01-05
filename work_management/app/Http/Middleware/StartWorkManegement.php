<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use App\Facades\OutputLog;

class StartWorkManegement
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        //webサーバー起動時の処理開始のログメッセージ
        OutputLog::message_log(__FUNCTION__, 'mhcmsi0001');
        return $next($request);
    }
}
