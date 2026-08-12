@extends('meta-lead-ingester::layout')

@section('content')
<div x-data="{ tab: 'meta' }">
    
    <!-- Header & Tabs -->
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">Accounts Dashboard</h2>
            <p class="mt-1 text-sm text-gray-500">Manage your connected social accounts and webhook keys securely.</p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button @click="tab = 'meta'" :class="{ 'border-brand-500 text-brand-600': tab === 'meta', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'meta' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                    Meta Lead Ads
                </button>
                <button @click="tab = 'google'" :class="{ 'border-brand-500 text-brand-600': tab === 'google', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'google' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                    Google Ads Leads
                </button>
            </nav>
        </div>
    </div>

    <!-- META TAB -->
    <div x-show="tab === 'meta'" x-transition.opacity.duration.300ms x-cloak>
        
        <!-- Add Meta Account Form -->
        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden mb-8 transition-shadow hover:shadow-md">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Add Meta Account</h3>
                <p class="mt-1 text-sm text-gray-500">Connect a new Facebook Page to receive lead events.</p>
            </div>
            <div class="px-6 py-6">
                <form action="{{ route('meta-lead-ingester.meta-accounts.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <label class="block text-sm font-medium text-gray-700">Company / Account Name</label>
                            <div class="mt-1">
                                <input type="text" name="company_name" required class="shadow-sm focus:ring-brand-500 focus:border-brand-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-3 border" placeholder="e.g. Acme Corp">
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <label class="block text-sm font-medium text-gray-700">Page ID</label>
                            <div class="mt-1">
                                <input type="text" name="page_id" required class="shadow-sm focus:ring-brand-500 focus:border-brand-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-3 border" placeholder="10123456789">
                            </div>
                        </div>

                        <div class="sm:col-span-6">
                            <label class="block text-sm font-medium text-gray-700">Page Access Token</label>
                            <div class="mt-1">
                                <input type="text" name="page_access_token" required class="shadow-sm focus:ring-brand-500 focus:border-brand-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-3 border font-mono text-xs" placeholder="EAABwz...">
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-colors">
                            Save Account
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Meta Accounts List -->
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Connected Meta Accounts</h3>
        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Verify Token</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Webhook URL</th>
                                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($metaAccounts as $account)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $account->company_name }}</div>
                                            <div class="text-sm text-gray-500">Page ID: {{ $account->page_id }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 font-mono">
                                                {{ $account->verify_token }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                            {{ url(config('meta-lead-ingester.route_prefix') . '/webhook') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <form action="{{ route('meta-lead-ingester.meta-accounts.destroy', $account->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this account?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-gray-500 text-sm">
                                            No Meta accounts found. Add one above.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- GOOGLE TAB -->
    <div x-show="tab === 'google'" x-transition.opacity.duration.300ms x-cloak>
        
        <!-- Add Google Account Form -->
        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden mb-8 transition-shadow hover:shadow-md">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Add Google Ads Account</h3>
                <p class="mt-1 text-sm text-gray-500">Generate a key to receive Google Lead Form webhooks securely.</p>
            </div>
            <div class="px-6 py-6">
                <form action="{{ route('meta-lead-ingester.google-accounts.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <label class="block text-sm font-medium text-gray-700">Account Name</label>
                            <div class="mt-1">
                                <input type="text" name="account_name" required class="shadow-sm focus:ring-brand-500 focus:border-brand-500 block w-full sm:text-sm border-gray-300 rounded-md py-2 px-3 border" placeholder="e.g. My Google Ads">
                            </div>
                        </div>

                        <div class="sm:col-span-3" x-data="{ randomKey() { return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15); } }">
                            <label class="block text-sm font-medium text-gray-700">Google Key (Webhook Secret)</label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <input type="text" name="google_key" x-ref="gkey" required class="focus:ring-brand-500 focus:border-brand-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300 py-2 px-3 border font-mono" placeholder="Secret string...">
                                <button type="button" @click="$refs.gkey.value = randomKey()" class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm hover:bg-gray-100 transition-colors">
                                    Generate
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">You will paste this key into the Google Ads UI.</p>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition-colors">
                            Save Account
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Google Accounts List -->
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Connected Google Accounts</h3>
        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Google Key (Secret)</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Webhook URL</th>
                                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($googleAccounts as $account)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $account->account_name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 font-mono">
                                                {{ $account->google_key }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                            {{ url(config('meta-lead-ingester.route_prefix') . '/google/webhook') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <form action="{{ route('meta-lead-ingester.google-accounts.destroy', $account->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this account?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-gray-500 text-sm">
                                            No Google accounts found. Add one above.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
