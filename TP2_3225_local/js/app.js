(function ($) {
  getRoutes();
})(jQuery);

function getRoutes() {
  console.log("getRoutes");
  var app = Sammy("#main", function () {
    this.use("template");

    this.get("#/", function (context) {
    //   var str = location.href.toLowerCase();

      context.app.swap("");
      context.render('templates/home.template', {}).appendTo(context.$element());
    });

    this.get("#/help", function (context) {
      context.app.swap("");
      context
        .render("templates/help.template", {})
        .appendTo(context.$element());
    });

    
    this.get("#/concept/:language/:concept", function (context) {
      var language = this.params["language"];
      var concept = this.params["concept"];

      $.ajax({
        url: `http://api.conceptnet.io/c/${language}/${concept}`,
        method: "GET",
        success: function (data) {
          // Render the data in a template
          context.app.swap("");
          context
            .render("templates/concept.template", { data: data })
            .appendTo(context.$element());
        },
        error: function (error) {
          console.log(error);
        },
      });
    });


    this.get(
      "#/relation/:relation/from/:language/:concept",
      function (context) {
        var relation = this.params["relation"];
        var language = this.params["language"];
        var concept = this.params["concept"];

        $.ajax({
          url: `http://api.conceptnet.io/query?node=/c/${language}/${concept}&rel=/r/${relation}`,
          method: "GET",
          success: function (data) {
            // Render the data in a template
            context.app.swap("");
            context
              .render("templates/relation.template", { data: data })
              .appendTo(context.$element());
          },
          error: function (error) {
            console.log(error);
          },
        });
      }
    );




  });
  app.run("#/");
}
