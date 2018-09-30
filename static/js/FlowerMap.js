$(function() {
    if($(".garden").length) {
        garden = new Garden();
    }

    $("form.ajax").submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        if (this.error) {
            error = this.error;
        } else {
            error = function() {
                location.reload();
            };
        }
        $.ajax({
            type: 'POST',
            processData: false,
            contentType: false,
            dataType: "json",
            cache: false,
            url: $(this).attr("action"),
            data: formData,
            success: this.success,
            error: error
        });
    });

    $(".pop-up .cancel").click(function() {
        $(this).closest(".pop-up").hide();
    });
});

function Select(element) {
    var self = this;
    this.element = $(element);
    this.add_option = function(name, value, onclick) {
        var option = $("<div></div>");
        option.addClass("option");
        option.text(name);
        option.click(function() {
            self.element.find("input").val(value);
            self.element.find(".option").removeClass("selected");
            option.addClass("selected");
            if (onclick) {
                onclick();
            }
        });
        self.element.append(option);
        return option;
    }

    return this;
}
