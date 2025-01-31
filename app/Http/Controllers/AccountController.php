<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexTajir()
    {
        $data = Account::where('type', '=', 'tajir')->get();
        return response()->view('cms.Account.indexTajir', ['data' => $data]);
    }


    public function indexMazarie()
    {
        $data = Account::where('type', '=', 'mazarie')->get();
        return response()->view('cms.Account.indexMazarie', ['data' => $data]);
    }

    public function accountsBlocked($id)
    {
        $data = Account::findOrFail($id);
        $data->status = $data->status == 'verefy' ? 'blocked' : 'verefy';
        $data->save();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Account $account)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Account $account)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Account $account)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account)
    {
        //
        $isDelete = $account->delete();
        if ($isDelete) {
            if ($account->image !== Null) {
                Storage::disk('public')->delete($account->image);
            }
        }
        return response()->json([
            'message' => $isDelete ? 'تم حذف المستخدم بنجاح' : 'فشل في عملية الحذف'
        ], $isDelete ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }
}
