function Species(species) {
    var self = this;
    if (species) {
        this.id = species.id;
        this.name = species.name;
        this.url = species.url;
        this.image = species.image;
        this.data = species.data;
    } else {
        this.id = "";
        this.name = "";
        this.url = "";
        this.image = "";
        this.data = [];
    }
    this.filtered = false;

    this.add = function () {
        var species_element = $("#species_template").clone(true);
        species_element.attr("id", "species_" + self.id);
        species_element.find(".name").html(self.name);
        species_element.find("input").click(function() {
            self.filtered = this.checked;
            self.garden.filter_plants();
        });
        species_element.find("button").click(function() {
            self.add_to_form($("[name=edit_species]").show());
        });
        $("#species_list").append(species_element.show());

        self.species_option = $("<div></div>");
        self.species_option.addClass("option");
        self.species_option.data("value", self.id);
        self.species_option.text(self.name);
        self.species_option.click(function() {
            $("[name=add_plant] #slct_species input").val(self.id);
            $("[name=add_plant] .species .name").html(self.name);
            $("[name=add_plant] .species img").attr("src", self.image);
            $("[name=add_plant] .species").show();
        });
        $("#slct_species").append(self.species_option);
        return self;
    }

    this.add_to_form = function(form) {
        form.find("[name=species_id]").val(self.id);
        form.find("[name=name]").val(self.name);
        form.find("[name=url]").val(self.url);

        var data_string = "";
        $.each(self.data, function(name, value) {
            data_string += self.data_row(name, value);
        });
        form.find(".species_data").html(data_string);
        form.find("[name=species_image]").val(self.image);
        if (self.image) {
            form.find(".species_image").attr("src", self.image).show();
        }
        form.find(".btn_add_species_data").off("click");
        form.find(".btn_add_species_data").click(function() {
            var name = form.find(".add_species_data_name").val();
            var value = form.find(".add_species_data_value").val();
            form.find(".species_data").append(self.data_row(name, value));
            self.data['name'] = value;
        });
        return form;
    }

    this.data_row = function(name, value) {
        data_string = '<div class="row"><span class="data_name">' + name + '</span>';
        data_string += '<span class="data_value">' + value + '</span>';
        data_string += '<input type="hidden" name="data[' + name + ']" value="' + value + '"></div>';
        return data_string;
    }

    this.get_option = function() {
        return self.species_option;
    }

    return this;
}
