(function($){
  $(function(){
       var $commentField = $("#comment");
       var $statusBar;

       $commentField.wrap('<div class="wpct_toolbar"></div>');

       // Function to initialize Quicktags toolbar
       $.fn.initializeQuicktags = function() {
         quicktags({
            id: "comment",
            buttons: "em,strong",
         });

         QTags.addButton("eg_underline", "u", "<u>", "</u>", "u", "Underline Text");
       }

       // Function to update the status bar with remaining characters
       function updateStatusBar() {
         var textLength = $commentField.val().length;
         var maxLength = $commentField.attr('maxlength'); // Get the max length of the input

         // If maxlength is not set, default to a large number
         maxLength = maxLength ? maxLength : 65525;

         // Calculate the remaining characters
         var remaining = maxLength - textLength;

         // Display the remaining characters in the status bar
         $statusBar.html(remaining + " characters remaining");
       }

       // Function to initialize the status bar and set up the event listener
       $.fn.initializeStatusBar = function() { // Corrected this line
         $('<div id="wpct_character_count" class="wpct-character-count"></div>').insertAfter($commentField);
         $statusBar = $('#wpct_character_count'); // Target status bar
         updateStatusBar(); // Call the function initially to set the remaining characters
         $commentField.on("input", updateStatusBar); // Update remaining characters when the user types
       }

      function isEmpty(str) {
          return !str.trim().length;
      }

      $.fn.initializeCaptchaValidation = function(options) {
          var captchaField = $('input[name="wpct_math_captcha"]');

          captchaField.on('focus', function(e) {
              var num1 = parseInt($('input[name="wpct_math_num1"]').val());
              var num2 = parseInt($('input[name="wpct_math_num2"]').val());
              var correctAnswer = num1 + num2;

              // Check if CAPTCHA field is empty
              if (isEmpty(captchaField.val())) {
                  captchaField[0].setCustomValidity(wpctCaptchaMessage.wpctCaptchaErrorMessage);
                  e.preventDefault();
              }
              // Check if the answer is incorrect
              else if (parseInt(captchaField.val()) !== correctAnswer) {
                  captchaField[0].setCustomValidity(wpctCaptchaMessage.wpctCaptchaSuccessMessage);
                  e.preventDefault();
              } else {
                  // Reset custom validity if the answer is correct
                  captchaField[0].setCustomValidity('');
              }
          });
      };
  });
})(jQuery);
