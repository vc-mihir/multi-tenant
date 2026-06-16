@extends('layouts.admin')

@section('title', 'Edit Company')
@section('page-id', 'central-admin-companies-edit')
@section('page-title', 'Edit Company')
@section('page-subtitle', 'Update details for ' . $company->company_name)

@section('page-actions')
    <a href="{{ route('admin.companies.index') }}"
        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-slate-500 hover:text-teal-600 transition-all uppercase tracking-widest border border-slate-200 rounded-xl hover:border-teal-200 hover:bg-teal-50/50">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to List
    </a>
@endsection


@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 sm:p-8">
            <form action="{{ route('admin.companies.update', $company) }}" method="POST" id="edit-company-form">
                @csrf
                @method('PUT')

                <div class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label for="company_name"
                                class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Company
                                Name</label>
                            <input id="company_name" type="text" name="company_name"
                                value="{{ old('company_name', $company->company_name) }}" readonly
                                class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-2xl text-slate-500 cursor-not-allowed focus:outline-none transition-all duration-300"
                                placeholder="e.g. Acme Corp">
                            @error('company_name')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="subdomain"
                                class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Subdomain</label>
                            <input id="subdomain" type="text" name="subdomain"
                                value="{{ old('subdomain', $company->subdomain) }}" readonly
                                class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-2xl text-slate-500 cursor-not-allowed focus:outline-none transition-all duration-300"
                                placeholder="acme-corp">
                            @error('subdomain')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label for="company_email"
                                class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Company
                                Email</label>
                            <input id="company_email" type="email" name="company_email"
                                value="{{ old('company_email', $company->company_email) }}"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all duration-300"
                                placeholder="admin@co.com">
                            @error('company_email')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="website"
                                class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Company
                                Website</label>
                            <input id="website" type="url" name="website"
                                value="{{ old('website', $company->website) }}"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all duration-300"
                                placeholder="https://acme.com">
                            @error('website')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label for="license_number"
                                class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">License
                                Number</label>
                            <input id="license_number" type="text" name="license_number"
                                value="{{ old('license_number', $company->license_number) }}"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all duration-300"
                                placeholder="REG-123456">
                            @error('license_number')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="space-y-1.5"
                            x-data="{
                                open: false,
                                selected: '{{ old('status', $company->status) }}',
                                options: [
                                    { value: 'active', label: 'Active', dot: 'bg-emerald-500' },
                                    { value: 'inactive', label: 'Inactive', dot: 'bg-slate-400' },
                                    { value: 'suspended', label: 'Suspended', dot: 'bg-rose-500' },
                                    { value: 'pending', label: 'Pending', dot: 'bg-amber-500' },
                                ],
                                get current() { return this.options.find(o => o.value === this.selected) || this.options[0]; },
                            }"
                            x-on:keydown.escape.window="open = false">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Status</label>

                            <input type="hidden" name="status" :value="selected">

                            <div class="relative" x-on:click.outside="open = false">
                                <button type="button" x-on:click="open = !open"
                                    class="flex w-full items-center justify-between px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500"
                                    :class="{ 'ring-4 ring-teal-500/10 border-teal-500': open }">
                                    <span class="flex items-center gap-2.5">
                                        <span class="h-2.5 w-2.5 rounded-full" :class="current.dot"></span>
                                        <span x-text="current.label">{{ ucfirst(old('status', $company->status)) }}</span>
                                    </span>
                                    <svg class="w-5 h-5 text-slate-400 transition-transform duration-200"
                                        :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <ul x-show="open" x-transition.opacity.duration.150ms style="display: none;"
                                    class="absolute z-20 mt-2 w-full overflow-hidden rounded-2xl border border-slate-200 bg-white py-1 shadow-xl shadow-slate-900/5">
                                    <template x-for="option in options" :key="option.value">
                                        <li>
                                            <button type="button" x-on:click="selected = option.value; open = false"
                                                class="flex w-full items-center justify-between px-4 py-2.5 text-left text-sm text-slate-700 transition-colors hover:bg-teal-50 hover:text-teal-700"
                                                :class="{ 'bg-teal-50/60 font-semibold text-teal-700': selected === option.value }">
                                                <span class="flex items-center gap-2.5">
                                                    <span class="h-2.5 w-2.5 rounded-full" :class="option.dot"></span>
                                                    <span x-text="option.label"></span>
                                                </span>
                                                <svg x-show="selected === option.value" class="w-4 h-4 text-teal-600"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                        </li>
                                    </template>
                                </ul>
                            </div>

                            @error('status')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label for="address"
                            class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Address</label>
                        <textarea id="address" name="address" rows="3"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all duration-300 resize-none">{{ old('address', $company->address) }}</textarea>
                        @error('address')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-1.5">
                            <label for="city"
                                class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">City</label>
                            <input id="city" type="text" name="city" value="{{ old('city', $company->city) }}"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all duration-300"
                                placeholder="e.g. SF">
                            @error('city')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="state"
                                class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">State</label>
                            <input id="state" type="text" name="state"
                                value="{{ old('state', $company->state) }}"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all duration-300"
                                placeholder="e.g. CA">
                            @error('state')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="country"
                                class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Country</label>
                            <input id="country" type="text" name="country"
                                value="{{ old('country', $company->country) }}"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all duration-300"
                                placeholder="e.g. USA">
                            @error('country')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 mt-12">
                    <a href="{{ route('admin.companies.index') }}"
                        class="px-6 py-3 text-sm font-bold text-slate-400 hover:text-slate-800 transition-colors uppercase tracking-widest">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-10 py-4 bg-teal-600 text-white font-black rounded-2xl shadow-xl shadow-teal-600/20 hover:bg-teal-700 hover:shadow-teal-600/30 active:scale-[0.98] transition-all duration-300 flex items-center">
                        Update Company
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

