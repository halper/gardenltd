$("#add-personnel").on("click", function (e) {
    e.preventDefault();
    var success = true;
    var tckInput = $('input[name=tck_no]');
    tckInput.parent('div').parent().closest('div.row').removeClass('has-error');
    var nameInput = $('input[name=name]');
    nameInput.parent('div').parent().closest('div.row').removeClass('has-error');
    if(!nameInput.val()){
        success = false;
        nameInput.parent('div').parent().closest('div.row').addClass('has-error');
    }
    var wageInput = $('input[name=wage]');
    wageInput.parent('div').parent().parent().parent().closest('div.row').removeClass('has-error');
    if(wageInput.length > 0 && !wageInput.val()){
        success = false;
        wageInput.parent('div').parent().parent().parent().closest('div.row').addClass('has-error');
    }
    var salaryInput = $('input[name=salary]');
    salaryInput.parent('div').parent().parent().parent().closest('div.row').removeClass('has-error');
    if(salaryInput.length > 0 && !salaryInput.val()){
        success = false;
        salaryInput.parent('div').parent().parent().parent().closest('div.row').addClass('has-error');
    }

    var iddocInput = $('input[name=iddoc]');
    iddocInput.parent('div').parent().closest('div.row').removeClass('has-error');
    if(!iddocInput.val()){
        success = false;
        iddocInput.parent('div').parent().closest('div.row').addClass('has-error');
    }
    $('#select-parent').removeClass('has-error');
    if(!($('select[name=staff_id]').val())){
        success = false;
        $('#select-parent').addClass('has-error');
    }
    var tck = tckInput.val();

    if (tck.length != 11) {
        tckInput.parent('div').parent().closest('div.row').append(
            '<div class="col-sm-4">' +
            '<span class="text-danger">TCK No giriniz!</span>' +
            '</div>'
        );
        tckInput.parent('div').parent().closest('div.row').addClass('has-error');
        return;
    }
    var unique;
    $.ajax({
        type: 'POST',
        url: '/common/check-tck',
        data: {
            "tck_no": tck
        }
    }).success(function (response) {
        unique = (response.indexOf('unique') > -1);
        if (!unique) {
            tckInput.parent('div').parent().closest('div.row').append(
                '<div class="col-sm-4">' +
                '<span class="text-danger">TCK No sistemde kayıtlı!</span>' +
                '</div>'
            );
            tckInput.parent('div').parent().closest('div.row').addClass('has-error');
        }
        else {
            if(success){
                $('#personnelForm').submit();
            }
        }
    });

});