function Species(species, garden) {
    var self = this;
    if (species) {
        this.id = species.id;
        this.name = species.name;
        this.url = species.url;
        this.image = species.image;
        this.data = species.data;
        this.garden = garden;
    } else {
        this.id = "";
        this.name = "";
        this.url = "";
        this.image = "";
        this.data = [];
        this.garden = garden;
    }
    this.filtered = false;
    this.plants = [];

    this.update = function(species) {
        self.name = species.name;
        self.url = species.url;
        self.image = species.image;
        self.data = species.data;
        if (self.option) {
            self.option.text(self.name);
        }
        if (self.element) {
            self.element.find(".name").html(self.name)
        }
        $.each(this.plants, function(plant_id, plant) {
            plant.set_name(self.name);
        });
    }

    this.add = function () {
        self.element = $("#species_template").clone(true);
        self.element.attr("id", "species_" + self.id);
        self.element.find(".name").html(self.name);
        self.element.find("input").click(function() {
            self.filtered = this.checked;
            self.garden.filter_plants();
        });
        self.element.find("button").click(function() {
            self.add_to_form($("[name=edit_species]").show());
        });
        $("#species_list").append(self.element.show());

        var select = new Select("#slct_species");
        self.option = select.add_option(self.name, self.id, function() {
            $("[name=add_plant] .species .name").html(self.name);
            $("[name=add_plant] .species img").attr("src", self.image);
            $("[name=add_plant] .species").show();
        });
        return self;
    }

    this.add_to_form = function(form) {
        form.find("[name=species_id]").val(self.id);
        form.find("[name=name]").val(self.name);
        form.find("[name=url]").val(self.url);

        var data_string = "";
        $.each(self.data, function(name, value) {
            data_string += data_row(name, value);
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
            form.find(".species_data").append(data_row(name, value));
            self.data['name'] = value;
        });
        return form;
    }

    function data_row(name, value) {
        data_string = '<div class="row"><span class="data_name">' + name + '</span>';
        data_string += '<span class="data_value">' + value + '</span>';
        data_string += '<input type="hidden" name="data[' + name + ']" value="' + value + '"></div>';
        return data_string;
    }

    this.get_option = function() {
        return self.option;
    }

    return this;
}
