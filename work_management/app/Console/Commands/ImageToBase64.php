<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;

//コマンドラインアプリケーション
//画像をbase64に変換する
class ImageToBase64 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:transbase64';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'translate image to base64';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    //storage/imageの画像をbase64に変換して、config/base64に書き込む
    public function handle()
    {
        try {
            //@var file ファイルポインタ。base64を保存するconfigファイル
            $fp = fopen(__DIR__.'\..\..\..\config\base64.php', 'w');
            //@var array 変換する画像ファイルの配列
            $png_file = glob(__DIR__.'\..\..\..\storage\image\*');
            //phpコードの先頭行を書き込む
            fwrite($fp, '<?php'."\n");
            fwrite($fp, "\n");
            fwrite($fp, 'return ['."\n");
            
            foreach($png_file as $file){
                //@var array ファイルをディレクトリセパレートで分割した配列
                $sepalete_file = explode(DIRECTORY_SEPARATOR, $file);
                //@var string $sepalete_fileの最後のファイル名をピリオドで分割する
                $png_file_name = explode('.', $sepalete_file[count($sepalete_file) - 1])[0];
                //@var string 画像をbase64化
                $png_base64 = base64_encode(file_get_contents($file));
                //configにbase64を書き込む
                fwrite($fp, '\''. $png_file_name. '\' => \''. $png_base64. '\',' . "\n");
            }
            //phpコードの最終行を書き込む
            fwrite($fp, "\n");
            fwrite($fp, '];');
        } catch (Exception $e) {
            echo '異常が出ました';
        }
        echo '正常に終わりました';
        return 0;
    }
}
