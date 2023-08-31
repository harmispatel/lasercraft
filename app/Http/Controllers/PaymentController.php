<?php

namespace App\Http\Controllers;

use App\Models\PaymentSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function paymentSettings()
    {
        $data['payment_settings'] = getPaymentSettings();
        return view('client.payment.payment_setting',$data);
    }


    // Function for Update Payment Settings
    public function UpdatePaymentSettings(Request $request)
    {
        $cash = (isset($request->cash)) ? $request->cash : 0;
        $paypal = (isset($request->paypal)) ? $request->paypal : 0;
        $paypal_mode = (isset($request->paypal_mode)) ? $request->paypal_mode : 'sandbox';
        $paypal_public_key = (isset($request->paypal_public_key)) ? $request->paypal_public_key : '';
        $paypal_private_key = (isset($request->paypal_private_key)) ? $request->paypal_private_key : '';
        $upi_payment = (isset($request->upi_payment)) ? $request->upi_payment : 0;
        $upi_id = (isset($request->upi_id)) ? $request->upi_id : '';
        $payee_name = (isset($request->payee_name)) ? $request->payee_name : '';

        $rules = [];
        if($paypal == 1)
        {
            $rules += [
                'paypal_public_key' => 'required',
                'paypal_private_key' => 'required',
            ];
        }

        if($upi_payment == 1)
        {
            $rules += [
                'upi_id' => 'required',
                'payee_name' => 'required'
            ];
        }

        if($request->hasFile('upi_qr'))
        {
            $rules += [
                'upi_qr' => 'mimes:png,jpg,svg,jpeg,PNG,SVG,JPG,JPEG',
            ];
        }

        $this->validate($request,$rules);

        try
        {
            $datas = [
                'cash' => $cash,
                'paypal' => $paypal,
                'paypal_mode' => $paypal_mode,
                'paypal_public_key' => $paypal_public_key,
                'paypal_private_key' => $paypal_private_key,
                'upi_payment' => $upi_payment,
                'upi_id' => $upi_id,
                'payee_name' => $payee_name,
            ];

            if($request->hasFile('upi_qr'))
            {
                $imgname = "upi_".time().".". $request->file('upi_qr')->getClientOriginalExtension();
                $request->file('upi_qr')->move(public_path('admin_uploads/upi_qr/'), $imgname);
                $datas['upi_qr'] = $imgname;
            }

            // Insert or Update Settings
            foreach($datas as $key => $value)
            {
                $query = PaymentSettings::where('key',$key)->first();
                $setting_id = isset($query->id) ? $query->id : '';

                if (!empty($setting_id) || $setting_id != '')  // Update
                {
                    $settings = PaymentSettings::find($setting_id);
                    $settings->value = $value;
                    $settings->update();
                }
                else // Insert
                {
                    $settings = new PaymentSettings();
                    $settings->key = $key;
                    $settings->value = $value;
                    $settings->save();
                }
            }

            return redirect()->route('payment.settings')->with('success','Settings Updated SuccessFully...');

        }
        catch (\Throwable $th)
        {
            return redirect()->route('payment.settings')->with('error','Internal server error!');
        }

    }
}
