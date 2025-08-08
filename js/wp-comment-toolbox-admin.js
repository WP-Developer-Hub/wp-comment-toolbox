(function($){
  $(function(){
      /**
       * jQuery plugin to remove specified URL query parameters
       * when a dismissible admin notice is dismissed.
       *
       * Usage:
       * $('.notice.is-dismissible').adminNoticeDismissCleaner(['block_ip_status', 'blocked_ip']);
       *
       * @param {Array} paramsToRemove List of query param keys to remove from URL
       */
      $.fn.adminNoticeDismissCleaner = function(paramsToRemove) {
          if (!Array.isArray(paramsToRemove) || paramsToRemove.length === 0) {
              console.warn('adminNoticeDismissCleaner requires an array of query parameter keys.');
              return this;
          }

          $(document).on('click', '.notice-dismiss', function() {
              var url = new URL(window.location.href);
              var params = url.searchParams;
              var removed = false;

              paramsToRemove.forEach(function(param) {
                  if (params.has(param)) {
                      params.delete(param);
                      removed = true;
                  }
              });

              if (removed) {
                  url.search = params.toString();
                  var newUrl = url.toString().replace(/\?$/, '');
                  window.history.replaceState({}, document.title, newUrl);
              }
          });

          return this; // for chaining
      };
  });
})(jQuery);
