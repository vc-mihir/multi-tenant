@extends('layouts.admin')

@section('title', 'Companies')
@section('page-id', 'central-admin-companies-index')
@section('page-title', 'Companies')
@section('page-subtitle', 'Manage all registered tenant companies')


@section('content')
    <div class="rounded-3xl border border-teal-100 bg-white p-4 shadow-sm ring-1 ring-slate-900/5 sm:p-6">
        <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <h2 class="text-lg font-semibold text-slate-800">Company Records</h2>
                <div id="bulk-actions" class="hidden">
                    <button id="bulk-delete-btn"
                        class="flex items-center gap-2 px-4 py-2 bg-rose-50 text-rose-600 border border-rose-100 rounded-xl hover:bg-rose-100 transition-all text-xs font-bold uppercase tracking-wider shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                        </svg>
                        Delete Selected (<span id="selected-count">0</span>)
                    </button>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Status:</span>
                    <div class="relative"
                        x-data="{
                            open: false,
                            selected: '',
                            options: [
                                { value: '', label: 'All', dot: '' },
                                { value: 'active', label: 'Active', dot: 'bg-emerald-500' },
                                { value: 'inactive', label: 'Inactive', dot: 'bg-slate-400' },
                                { value: 'suspended', label: 'Suspended', dot: 'bg-rose-500' },
                                { value: 'pending', label: 'Pending', dot: 'bg-amber-500' },
                            ],
                            get current() { return this.options.find(o => o.value === this.selected) || this.options[0]; },
                            choose(value) {
                                this.selected = value;
                                this.open = false;
                                this.$nextTick(() => this.$refs.filterInput.dispatchEvent(new Event('change', { bubbles: true })));
                            },
                        }"
                        x-on:keydown.escape.window="open = false"
                        x-on:click.outside="open = false">
                        <input type="hidden" id="status-filter" x-ref="filterInput" :value="selected">

                        <button type="button" x-on:click="open = !open"
                            class="flex w-full min-w-[140px] items-center justify-between gap-3 px-4 py-2 text-sm bg-slate-50/50 border border-slate-200 rounded-xl outline-none transition-all cursor-pointer focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                            :class="{ 'ring-2 ring-teal-500 border-teal-500': open }">
                            <span class="flex items-center gap-2">
                                <span x-show="current.dot" class="h-2 w-2 rounded-full" :class="current.dot"></span>
                                <span x-text="current.label" class="text-slate-700">All</span>
                            </span>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200"
                                :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <ul x-show="open" x-transition.opacity.duration.150ms style="display: none;"
                            class="absolute right-0 z-20 mt-2 w-44 overflow-hidden rounded-xl border border-slate-200 bg-white py-1 shadow-xl shadow-slate-900/5">
                            <template x-for="option in options" :key="option.value">
                                <li>
                                    <button type="button" x-on:click="choose(option.value)"
                                        class="flex w-full items-center justify-between px-4 py-2.5 text-left text-sm text-slate-700 transition-colors hover:bg-teal-50 hover:text-teal-700"
                                        :class="{ 'bg-teal-50/60 font-semibold text-teal-700': selected === option.value }">
                                        <span class="flex items-center gap-2.5">
                                            <span x-show="option.dot" class="h-2 w-2 rounded-full" :class="option.dot"></span>
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
                </div>
            </div>
        </div>

        <div class="relative">
            <table id="companies-table" class="w-full text-left border-collapse"
                data-url="{{ route('admin.companies.data') }}"
                data-bulk-delete-url="{{ route('admin.companies.bulk-delete') }}">
                <thead>
                    <tr>
                        <th class="w-10">
                            <input type="checkbox" id="select-all"
                                class="w-4 h-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500 cursor-pointer">
                        </th>
                        <th>ID</th>
                        <th>Company Name</th>
                        <th>Subdomain</th>
                        <th>Email</th>
                        <th>Website</th>
                        <th>License Number</th>
                        <th>Address</th>
                        <th>Country</th>
                        <th>State</th>
                        <th>City</th>
                        <th>Status</th>
                        <th>Email Verified At</th>
                        <th>Database Name</th>
                        <th class="whitespace-nowrap">Created At</th>
                        <th class="whitespace-nowrap">Updated At</th>
                        <th class="text-right px-6">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

