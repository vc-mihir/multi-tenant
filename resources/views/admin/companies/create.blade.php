@extends('layouts.admin')

@section('title', 'Create New Company')
@section('page-title', 'Create Company')
@section('page-subtitle', 'Manually register a new tenant and provision their database')

@section('page-actions')
    <a href="{{ route('admin.dashboard') }}"
        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-slate-500 hover:text-teal-600 transition-all uppercase tracking-widest border border-slate-200 rounded-xl hover:border-teal-200 hover:bg-teal-50/50">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to Dashboard
    </a>
@endsection

@push('styles')
    <style>
        .error {
            color: #ef4444;
            font-size: 10px;
            font-weight: 700;
            margin-left: 0.25rem;
            margin-top: 0.25rem;
            display: block;
        }

        input.error,
        select.error,
        textarea.error {
            border-color: #ef4444 !important;
            background-color: #fef2f2 !important;
        }
    </style>
@endpush

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="rounded-3xl border border-slate-100 bg-white p-8 shadow-sm ring-1 ring-slate-900/5">
            <form action="{{ route('admin.companies.store') }}" method="POST" id="create-company-form">
                @csrf

                <div class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div class="space-y-1.5">
                            <label for="company_name"
                                class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Company
                                Name</label>
                            <input id="company_name" type="text" name="company_name" value="{{ old('company_name') }}"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all duration-300"
                                placeholder="e.g. Acme Corp">
                            @error('company_name')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="company_email"
                                class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Company
                                Email</label>
                            <input id="company_email" type="email" name="company_email" value="{{ old('company_email') }}"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all duration-300"
                                placeholder="admin@co.com">
                            @error('company_email')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label for="password"
                                class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Password</label>
                            <input id="password" type="password" name="password"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all duration-300"
                                placeholder="••••••••">
                            @error('password')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="password_confirmation"
                                class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Confirm
                                Password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all duration-300"
                                placeholder="••••••••">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label for="website"
                                class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Company
                                Website</label>
                            <input id="website" type="url" name="website" value="{{ old('website') }}"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all duration-300"
                                placeholder="https://acme.com">
                            @error('website')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="license_number"
                                class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">License
                                Number</label>
                            <input id="license_number" type="text" name="license_number"
                                value="{{ old('license_number') }}"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all duration-300"
                                placeholder="REG-123456">
                            @error('license_number')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label for="address"
                            class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Address</label>
                        <textarea id="address" name="address" rows="3"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all duration-300 resize-none">{{ old('address') }}</textarea>
                        @error('address')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-1.5">
                            <label for="city"
                                class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">City</label>
                            <input id="city" type="text" name="city" value="{{ old('city') }}"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all duration-300"
                                placeholder="e.g. SF">
                            @error('city')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="state"
                                class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">State</label>
                            <input id="state" type="text" name="state" value="{{ old('state') }}"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all duration-300"
                                placeholder="e.g. CA">
                            @error('state')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="country"
                                class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1">Country</label>
                            <input id="country" type="text" name="country" value="{{ old('country') }}"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all duration-300"
                                placeholder="e.g. USA">
                            @error('country')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 mt-12">
                    <a href="{{ route('admin.dashboard') }}"
                        class="px-6 py-3 text-sm font-bold text-slate-400 hover:text-slate-800 transition-colors uppercase tracking-widest">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-10 py-4 bg-teal-600 text-white font-black rounded-2xl shadow-xl shadow-teal-600/20 hover:bg-teal-700 hover:shadow-teal-600/30 active:scale-[0.98] transition-all duration-300 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Create Company
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const form = $('#create-company-form');

            $.validator.addMethod("strongPassword", function(value, element) {
                return this.optional(element) ||
                    (value.length >= 8 && value.length <= 16 &&
                        /[A-Z]/.test(value) &&
                        /[a-z]/.test(value) &&
                        /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(value));
            }, "Use 8-16 chars with Upper, Lower & Symbol.");

            form.validate({
                onfocusout: function(element) {
                    $(element).valid();
                },
                errorElement: "span",
                rules: {
                    ...window.CommonValidationRules,
                    password: {
                        required: true,
                        strongPassword: true
                    },
                    password_confirmation: {
                        required: true,
                        equalTo: "#password"
                    }
                },
                messages: {
                    company_email: {
                        email: "Please enter a valid business email."
                    }
                }
            });
        });
    </script>
@endpush
