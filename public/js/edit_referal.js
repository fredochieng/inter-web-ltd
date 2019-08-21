$(function () {
    $("#phone_no_id").select2({

        ajax: {
            url: "/phones/get_numbers",
            type: 'GET',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                console.log(params);
                return {
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                var retVal = [];
                $.each(data, function (index, element) {
                    var lineObj = {
                        id: element.id,
                        text: element.text,
                        referer_name: element.referer_name
                    }
                    retVal.push(lineObj);
                });
                return {
                    results: retVal,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        },
        placeholder: 'Search referer by phone number',
        escapeMarkup: function (markup) {
            return markup;
        }, // let our custom formatter work
        minimumInputLength: 4,
        templateResult: formatRepo,
        templateSelection: formatRepoSelection
    }).on('select2:select', function (e) {
        var data = e.params.data;
        console.log(data);
        $("#referer_name1").val(data.referer_name);
        $("#referer_id1").val(data.id);
        $("#referer_phone1").val(data.text);
        console.log();
    });

    function formatRepo(repo) {
        if (repo.loading) {
            return repo.text;
        }
        var markup = repo.text;
        return markup;
    }

    function formatRepoSelection(repo) {
        return repo.text;
    }
})
