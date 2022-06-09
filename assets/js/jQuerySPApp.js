(function ($) {
  $.jQuerySPApp = function (options) {
    // set config and routes
    let config; const
      routes = {};

    config = $.extend({
      defaultView: $('main#jqueryspapp > section:last-child').attr('id'),
      templateDir: './views/',
      pageNotFound: false,
    }, options);

    $('main#jqueryspapp > section').each(function (k, e) {
      const elm = $(this);
      routes[elm.attr('id')] = {
        view: elm.attr('id'),
        load: elm.data('load'),
        onCreate() { },
        onReady() { },
      };
    });
    // update rotues programatically
    this.route = function (options) { $.extend(routes[options.view], options); };

    // manage hash change
    const routeChange = function () {
      const id = location.hash.slice(1);
      const route = routes[id];
      const elm = $(`#${id}`);

      if (!elm || !route) {
        if (config.pageNotFound) {
          window.location.hash = config.pageNotFound;
          return;
        }
        console.log(`${id} not defined`);
        return;
      }

      if (elm.hasClass('spapp-created')) {
        route.onReady();
      } else {
        elm.addClass('spapp-created');
        if (!route.load) {
          route.onCreate();
          route.onReady();
        } else {
          elm.load(config.templateDir + route.load, () => {
            route.onCreate();
            route.onReady();
          });
        }
      }
    };

    // and run
    this.run = function () {
      window.addEventListener('hashchange', () => { routeChange(); });
      if (!window.location.hash) {
        window.location.hash = config.defaultView;
      } else {
        routeChange();
      }
    };

    return this;
  };
}(jQuery));
