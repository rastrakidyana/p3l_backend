<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;
use App\Histori_Bahan_Keluar;
use App\Bahan;
use App\Menu;

class cronEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'waste:auto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function handle()
    {        
        $dt = Carbon::today()->toDateString();

        $bahans = Bahan::where('status_hapus', '=', 0)->get();
        
        $i = 0;
        foreach ($bahans as $bahan) {            
            if ($bahan->stok_bahan > 0) {
                $store_data = [];
                $store_data['id_bahan'] = $bahan->id;
                $store_data['jml_keluar'] = $bahan->stok_bahan;
                $store_data['tgl_keluar'] = $dt;
                $store_data['status_keluar'] = 1;   

                $bahan->stok_bahan = $bahan->stok_bahan - $store_data['jml_keluar'];

                if ($bahan->id_menu != null) {
                    $menu = Menu::find($bahan->id_menu);
                    $menu->stok_menu = $bahan->stok_bahan / $menu->serving_size;
                    $menu->save();
                }

                $waste[$i] = Histori_Bahan_Keluar::create($store_data);
                $bahan->save();
                $i = $i + 1;
            }            
            
        }                                    
    }
}
