/**
 * Admin Layout - Global Search
 */
$(document).ready(function () {
    const searchForm = $("#search-form");
    const searchUrl = searchForm.data("search-url");

    if (!searchUrl) return;

    $("#dashboard-search").on("input", function () {
        let q = $(this).val();
        if (q.length < 2) return $("#search-results").addClass("hidden");

        $.get(searchUrl, { q: q }, function (data) {
            let html = data.length
                ? data
                      .map(
                          (item) => `
                        <a href="${item.url}" class="flex items-center p-3 hover:bg-teal-50 rounded-xl transition-colors group">
                            <div class="w-8 h-8 rounded-lg bg-teal-100 flex items-center justify-center text-teal-600 font-bold mr-3 group-hover:bg-teal-600 group-hover:text-white transition-colors text-xs">
                                ${item.name.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <div class="text-sm font-bold text-slate-800">${item.name}</div>
                                <div class="text-[10px] text-slate-500">${item.email}</div>
                            </div>
                        </a>`
                      )
                      .join("")
                : '<div class="p-4 text-center text-sm text-slate-500">No results found</div>';

            $("#search-results-content").html(html);
            $("#search-results").removeClass("hidden");
        });
    });
});
