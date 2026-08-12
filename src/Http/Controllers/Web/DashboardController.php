<?php

namespace Vendor\MetaLeadIngester\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Vendor\MetaLeadIngester\Models\MetaAccount;
use Vendor\MetaLeadIngester\Models\GoogleAccount;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        $metaAccounts = MetaAccount::orderBy('created_at', 'desc')->get();
        $googleAccounts = GoogleAccount::orderBy('created_at', 'desc')->get();

        return view('meta-lead-ingester::dashboard', compact('metaAccounts', 'googleAccounts'));
    }

    public function storeMetaAccount(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'page_id' => 'required|string|max:255|unique:meta_accounts,page_id',
            'page_access_token' => 'required|string',
        ]);

        MetaAccount::create([
            'company_name' => $request->company_name,
            'page_id' => $request->page_id,
            'page_access_token' => $request->page_access_token,
            'verify_token' => Str::random(32),
            'is_active' => true,
        ]);

        return back()->with('success', 'Meta account added successfully.');
    }

    public function destroyMetaAccount($id)
    {
        $account = MetaAccount::findOrFail($id);
        $account->delete();

        return back()->with('success', 'Meta account deleted successfully.');
    }

    public function storeGoogleAccount(Request $request)
    {
        $request->validate([
            'account_name' => 'required|string|max:255',
            'google_key' => 'required|string|max:255|unique:google_accounts,google_key',
        ]);

        GoogleAccount::create([
            'account_name' => $request->account_name,
            'google_key' => $request->google_key,
        ]);

        return back()->with('success', 'Google account added successfully.');
    }

    public function destroyGoogleAccount($id)
    {
        $account = GoogleAccount::findOrFail($id);
        $account->delete();

        return back()->with('success', 'Google account deleted successfully.');
    }
}
