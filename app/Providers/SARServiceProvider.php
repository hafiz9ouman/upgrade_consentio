<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SARServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*', function ($view)
        {
            if (Auth::check())
            {
               /* $client_forms = DB::table('client_forms')
                                ->join('forms', 'client_forms.form_id', '=', 'forms.id')
                                ->where(['forms.code'                    =>  'f10',
                                         'client_forms.client_id'        =>   Auth::user()->client_id])->first();
                                         */
$client_forms =          DB::table('forms')
                      ->join('client_forms',      'forms.id',     '=', 'client_forms.form_id')
                      ->where('client_forms.client_id', '=', Auth::user()->client_id)->first();
                                                               

                if (!empty($client_forms))
                {
                   $form_id = $client_forms->form_id;
                    $SAR_company_subform = DB::table('sub_forms')
                    ->where(['parent_form_id' => $form_id,
                             'client_id'      => Auth::user()->client_id])->first();

                    /*
                    echo "<pre>";
                    print_r($SAR_company_subform);
                    echo "</pre>";
                    exit;
                    */

                    if (!empty($SAR_company_subform)) 
                    {
                        $view->with('SAR_company_subform', $SAR_company_subform);
                    }
                }
            }
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
