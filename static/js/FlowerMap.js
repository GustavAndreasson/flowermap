$(function() {
    if($(".garden").length) {
        garden = new Garden();
    }

    $("form.ajax").submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            processData: false,
            contentType: false,
	    dataType: "json",
            cache: false,
            url: $(this).attr("action"),
            data: formData,
            success: this.success
        });
    });

    $(".pop-up .cancel").click(function() {
        $(this).closest(".pop-up").hide();
    });

    $(".select .option").click(function() {
        $(this).siblings().removeClass("selected");
        $(this).addClass("selected");
        $(this).siblings("input").val($(this).data("value"));
    });
});
