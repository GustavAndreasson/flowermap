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
        $.each(this.plants, function(plantId, plant) {
            plant.setName(self.name);
        });
    }

    this.add = function () {
        self.element = $("#species_template").clone(true);
        self.element.attr("id", "species_" + self.id);
        self.element.find(".name").html(self.name);
        self.element.find("input").click(function() {
            self.filtered = this.checked;
            self.garden.filterPlants();
        });
        self.element.find("button").click(function() {
            self.addToForm($("[name=edit_species]").show());
        });
        $("#species_list").append(self.element.show());

        var select = new Select("#slct_species");
        self.option = select.addOption(self.name, self.id, function() {
            $("[name=add_plant] .species .name").html(self.name);
            $("[name=add_plant] .species img").attr("src", self.image);
            $("[name=add_plant] .species").show();
        });
        return self;
    }

    this.addToForm = function(form) {
	if (self.id) {
	    form.find("[name=species_id]").val(self.id);
	}
        form.find("[name=name]").val(self.name);
        form.find("[name=url]").val(self.url);

        var dataString = "";
        $.each(self.data, function(name, value) {
            dataString += dataRow(name, value);
        });
        form.find(".species_data").html(dataString);
        form.find("[name=species_image]").val(self.image);
        if (self.image) {
            form.find(".species_image").attr("src", self.image).show();
        }
        form.find(".btn_add_species_data").off("click");
        form.find(".btn_add_species_data").click(function() {
            var name = form.find(".add_species_data_name").val();
            var value = form.find(".add_species_data_value").val();
            form.find(".species_data").append(dataRow(name, value));
            self.data['name'] = value;
        });
        return form;
    }

    function dataRow(name, value) {
        dataString = '<div class="row"><span class="data_name">' + name + '</span>';
        dataString += '<span class="data_value">' + value + '</span>';
        dataString += '<input type="hidden" name="data[' + name + ']" value="' + value + '"></div>';
        return dataString;
    }

    this.getOption = function() {
        return self.option;
    }

    return this;
}
