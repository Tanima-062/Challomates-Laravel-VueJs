<?php


namespace App\Traits;


trait HasPermission
{

    protected $permission = array(
        'system_admin' => array(
            'package.view',
            'challo_mates_admin.view',
            'challo_mates_admin.create',
            'challo_mates_admin.edit',
            'company_consultants.view',
            'marketing_fee.view',
            'sweepstakes.view',
            'participation.view',
            'raffles.view',
            'jackpot.view',
            'mobile_app_user.view',

            //sales parnters permissions
            'sales_partner.view',

            //contract permissions
            'contract.view',
        ),

        'challo_mates_admin' => array(
            'package.view',
            'package.create',
            'package.edit',
            'company_consultants.view',
            'company_consultants.create',
            'company_consultants.edit',
            'sweepstakes.view',
            'sweepstakes.create',
            'sweepstakes.edit',
            'participation.view',
            'raffles.view',
            'raffles.edit',
            'jackpot.view',
            'mobile_app_user.view',
            'mobile_app_user.edit',
            'mobile_app_user.delete',
            //'participation.create',
            //'participation.edit',
            'marketing_fee.view',
            'marketing_fee.create',
            'marketing_fee.edit',
            'marketing_fee.enable/disable',

            //sales parnters permissions
            'sales_partner.view',
            'sales_partner.create',
            'sales_partner.edit',

            //contract permissions
            'contract.view',
            'contract.create',
            'contract.edit',

            //booster
            'booster.create',
            'booster.edit',
            'booster.view',
            'booster.enable/disable'
        ),

        'company_consultant' => array(
            'package.view',
            'package.create',
            'package.edit',
        ),

        'sales_partners' => array(
            'challo_mates_admin.view',
            'challo_mates_admin.create',
        ),

        'mobile_app_user' => array(
            'challo_mates_admin.view',
            'challo_mates_admin.create',
        )
    );

    public function getAllPermissions()
    {
        return $this->permission[auth()->user()->type];
    }

    public function can($permission, $arguments = [])
    {
        return in_array($permission, $this->permission[auth()->user()->type]);
        //return false;
    }
}
