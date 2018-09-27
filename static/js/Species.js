function Species(species, garden) {
    var self = this;
    this.id = species.id;
    this.garden = garden;
    this.name = species.name;
    this.url = species.url;
    this.image = species.image;
    this.data = species.data;
    this.filtered = false;

    function add_species_html() {
        var species_element = $("#species_template").clone(true);
        species_element.attr("id", "species_" + self.id);
        species_element.find(".name").html(self.name);
        species_element.find("input").click(function() {
            self.filtered = this.checked;
            self.garden.filter_plants();
        });
        $("#species_list").append(species_element.show());

        var species_option = $("<div></div>");
        species_option.addClass("option");
        species_option.data("value", self.id);
        species_option.text(self.name)
        species_option.click(function() {
            $("[name=add_plant] .species .name").html(self.name);
            $("[name=add_plant] .species img").attr("src", self.image);
            $("[name=add_plant] .species").show();
        });
        $("#slct_species").append(species_option);
    }

    add_species_html()

    return this;
}
