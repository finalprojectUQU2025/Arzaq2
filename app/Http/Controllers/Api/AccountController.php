<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Messages;
use App\Http\Controllers\Controller;
use App\Mail\SendCodeRegister;
use App\Models\Account;
use App\Models\Country;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
{
    //


    public function showCities()
    {
        $data = Country::where('active', '=', true)->get()->map(function ($city) {
            return [
                'id' => $city->id,
                'name' => $city->name,
                'image' => url('storage/',  $city->image),
            ];
        });

        return response()->json([
            'status' => true,
            'message' => Messages::getMessage('SUCCESS_SEND'),
            'data' => $data,
        ]);
    }


    public function userRegister(Request $request)
    {
        $name = $request->input('name');
        if ($this->isArabic($name)) {
            $cardHolderName = $this->transliterateToEnglish($name);
        } else {
            $cardHolderName = $name;
        }
        $validator = Validator($request->all(), [
            'type' => 'required|string|in:mazarie,tajir',
            'image' => 'required|image|mimes:jpg,png,svg',
            'name' => 'required|string|min:3',
            'phone' => 'required|string|numeric|unique:accounts,phone',
            'email' => 'required|string|email|unique:accounts,email',
            'city_id' => 'required|integer|exists:countries,id',
            'password' => 'required|min:6|max:12|',
            'check_conditions' => 'required|string|in:1',
        ]);
        if (!$validator->fails()) {
            $accounts = new Account();
            $accounts->type  = $request->get('type');
            $accounts->name  = $request->get('name');
            $accounts->phone  = $request->get('phone');
            $accounts->email  = $request->get('email');
            $accounts->country_id  = $request->get('city_id');
            $accounts->password  =  Hash::make($request->get('password'));
            $accounts->check_conditions  = $request->get('check_conditions');
            if ($request->hasFile('image')) {
                $imageName = time() . '_' . str_replace(' ', '', $accounts->name) . '.' . $request->file('image')->extension();
                $request->file('image')->storePubliclyAs('Account', $imageName, ['disk' => 'public']);
                $accounts->image = 'Account/' . $imageName;
            }
            $isSaved = $accounts->save();
            if ($isSaved) {
                $code = random_int(10000, 99999);
                $accounts->verification_code = Hash::make($code);
                Mail::to($accounts->email)->send(new SendCodeRegister($code, $accounts));
                $isCodeSaved = $accounts->save();
                if ($isCodeSaved) {
                    $wallet = new Wallet();
                    $wallet->account_id = $accounts->id;
                    // if ($request->get('type') == 'mazarie') {
                    $wallet->card_holder_name = $cardHolderName;
                    $wallet->card_number = $this->generateCardNumber(); // مثال: توليد رقم بطاقة وهمي
                    $wallet->card_expiry = $this->generateExpiryDate(); // مثال: توليد تاريخ انتهاء وهمي
                    $wallet->card_cvv = $this->generateCVV(); // مثال: توليد CVV وهمي
                    // }
                    $wallet->save();
                }
            }
            return response()->json(
                [
                    'statue' => $isSaved ? true : false,
                    'message' => $isSaved ?  Messages::getMessage('SUCCESS_SEND') : Messages::getMessage('REGISTRATION_FAILED'),
                    'code' => $code,
                ],
                $isSaved ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST
            );
        } else {
            return response()->json(
                [
                    'statue' =>  false,
                    'message' => $validator->getMessageBag()->first(),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    function generateCardNumber()
    {
        $prefix = "4";
        $number = $prefix;
        for ($i = 1; $i < 16; $i++) {
            $number .= mt_rand(0, 9);
        }
        return $number;
    }

    function generateExpiryDate()
    {
        $month = str_pad(mt_rand(1, 12), 2, '0', STR_PAD_LEFT);
        $year = date('y') + mt_rand(1, 5); // السنة تكون بصيغة yy
        return "$month/$year";
    }

    function generateCVV()
    {
        return str_pad(mt_rand(0, 999), 3, '0', STR_PAD_LEFT);
    }

    private function isArabic($text)
    {
        return preg_match('/\p{Arabic}/u', $text);
    }

    private function transliterateToEnglish($text)
    {
        $arabicToEnglishMap = [
            'ا' => 'a',
            'ب' => 'b',
            'ت' => 't',
            'ث' => 'th',
            'ج' => 'j',
            'ح' => 'h',
            'خ' => 'kh',
            'د' => 'd',
            'ذ' => 'dh',
            'ر' => 'r',
            'ز' => 'z',
            'س' => 's',
            'ش' => 'sh',
            'ص' => 's',
            'ض' => 'd',
            'ط' => 't',
            'ظ' => 'th',
            'ع' => 'a',
            'غ' => 'gh',
            'ف' => 'f',
            'ق' => 'q',
            'ك' => 'k',
            'ل' => 'l',
            'م' => 'm',
            'ن' => 'n',
            'ه' => 'h',
            'و' => 'w',
            'ي' => 'y',
            'ء' => "'",
            'ئ' => "'",
            'ؤ' => "'",
            'أ' => 'a',
            'إ' => 'e',
            'آ' => 'aa',
            'ى' => 'a',
            'ة' => 'h'
        ];

        // استبدال الحروف العربية بنظيرها الإنجليزي
        $englishText = strtr($text, $arabicToEnglishMap);

        // إضافة مسافة بعد "الـ" إذا كانت موجودة
        $englishText = str_replace('al', 'al ', $englishText);

        return $englishText;
    }
}
