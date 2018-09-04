$(function() {
    garden = new Garden();
    garden.load_species();
    garden.load_plants();

    $("[name=add_plant]").submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            processData: false,
            contentType: false,
            cache: false,
            url: $(this).attr("action"),
            data: formData,
            success: function (data) {
                var plant = JSON.parse(data);
                garden.plants[plant.id] = new Plant(plant, garden);
                $("[name=add_plant]").hide();
            }
        });
    });

    $("[name=add_species]").submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            processData: false,
            contentType: false,
            cache: false,
            url: $(this).attr("action"),
            data: formData,
            success: function (data) {
                var species = JSON.parse(data);
                garden.species[species.id] = species;
                $("[name=add_species]").hide();
                $("[name=add_plant] #slct_species .option").removeClass("selected");
                var species_html = "<div class=\"option selected\" data-value=\"" + species.id + "\">" + species.name + "</div>";
                $("[name=add_plant] #slct_species").append(species_html);
                $("[name=add_plant] .species .name").html(garden.species[species.id].name);
                $("[name=add_plant] .species img").attr("src", garden.species[species.id].image);
                $("[name=add_plant] .species").show();
            }
        });
    });

    $("#btn_add_species").click(function() {
        $("[name=add_species]").show();
    });

    $("#btn_load_species").click(function() {
        var url = $("#add_species_url").val();
        var name = $("#add_species_name").val();

        $.getJSON(
            "controllers/species.php",
            {action: "load_species_url", url: url, name: name},
            function (species) {
                var species_string = '<input type="hidden" name="loaded_species_name" value="' + species['name'] + '">';
                species_string += '<input type="hidden" name="loaded_species_url" value="' + species['url'] + '">';
                $.each(species['data'], function(name, value) {
                    species_string += '<div class="row"><span class="data_name">' + name + '</span>';
                    species_string += '<span class="data_value">' + value + '</span>';
                    species_string += '<input type="hidden" name="data[' + name + ']" value="' + value + '"></div>';
                });
                species_string += '<div class="row">';
                species_string += '<input type="hidden" name="species_image" id="add_plant_image" value="' + species['image'] + '">';
                species_string += '<img src="' + species['image'] + '"></div>';
                $("[name=add_species] .species_data").html(species_string)
                $("#add_species_name").val(species['name']);
                $("#add_species_url").val(species['url']);
            }
        );
    });

    $("#btn_add_species_data").click(function() {
        var name = $("#add_species_data_name").val();
        var value = $("#add_species_data_value").val();
        var html = "<div class='row'><span class='data_name'>" + name;
        html += "</span><span class='data_value'>" + value + "</span>";
        html += "<input type='hidden' name='data[" + name + "]' value='" + value;
        html += "'></div>";
        $("[name=add_plant] .species_data").append(html);
    });

    $("#btn_edit_garden").click(function() {
        $("[name=edit_garden]").show();
    });

    $("#slct_species .option").click(function() {
        var species_id = $(this).data("value");
        if (species_id) {
            $("[name=add_plant] .species .name").html(garden.species[species_id].name);
            $("[name=add_plant] .species img").attr("src", garden.species[species_id].image);
            $("[name=add_plant] .species").show();
        } else {
            $("[name=add_plant] .species").hide();
        }
        /*$("[name=add_plant] .species_data").html("");
        if ($(this).data("value")) {
        $.getJSON(
        "controllers/species.php",
        {action: "load_species_id", id: $(this).data("value")},
        function (species) {
        var species_string = '<div class="row"><span class="name">' + $T->__("Name") + '</span>';
        species_string += '<span class="value">' + species['name'] + '</span></div>';
        $.each(species['data'], function(name, value) {
        species_string += '<div class="row"><span class="data_name">' + name + '</span>';
        species_string += '<span class="data_value">' + value + '</span>';
    });
    if (species['image']) {
    species_string += '<div class="row">';
    species_string += '<img src="' + species['image'] + '"></div>';
}
$("[name=add_plant] .species_data").html(species_string);
}
);
$("[name=add_plant] .new_species").hide();
$("#add_plant_name").attr("name", "name_disabled");
} else {
$("[name=add_plant] .new_species").show();
$("#add_plant_name").attr("name", "name");
}*/
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
