<footer class="bg-white border-t border-gray-100 px-6 py-3 flex items-center justify-between shrink-0">
    <p class="text-xs text-gray-400">&copy; {{ date('Y') }} {{ Auth::guard('company')->user()->company_name }}</p>
    <p class="text-xs text-gray-400">Powered by <span class="font-bold text-emerald-500">TenantHub</span></p>
</footer>
