
$(document).ready(function(){
    // Show change About
    $('.info-about p:last-child').click(function(){
        $('.bg-notification').fadeIn('fast',function(){
            $('.change-about').slideDown();
        });
    });


    // define variables
     var countChar = null,
         chars = null,
         leftChar = null;

    // Close Change about
    $('.about-cancel').click(function(){
        $('.about-change textarea').val("");
        countChar = 0;

        $('.change-about').slideUp(function(){
             $('.bg-notification').fadeOut();
        });
    });

    $('.change-about textarea').keyup(function() {
      checkChange();
    });

    function checkChange () {
        chars = $('.change-about textarea').val();
        countChar = chars.length;
        leftChar = 255 - countChar;

        $('.count-chars span:first-child').html(leftChar);

        if (countChar > 10) {
            $('.change-about input').css('pointer-events','all');
        } else {
            $('.change-about input').css('pointer-events','none');
        }
    }

    $('.requests-container table').on('click', 'td span', function() {
      var $this = $(this),
          itemAction = $this.text(),
          userId = $this.parents('tr').attr('id');

      if (itemAction == "Accept") {
          construct(userId, $(this), itemAction);
      } else if (itemAction == "Decline"){
          construct(userId, $(this), itemAction);
      } else {
          construct(userId, $(this), itemAction);
      }

    });

    function construct (user_id, obj, action) {
        $this = obj;
        var text = null;

        var dataString = "user_id=" + user_id + "&handleRequest=true";
        if (action == "Accept") {dataString += "&accept=true";}

        $.ajax({
            type: "POST",
            url: "functions/requests.php",
            data: dataString,
            cache: false,
            success: function(html){
              if (action == "Accept") {
                text = "User Accepted";
              } else if (action == "Decline") {
                text = "User Declined";
              } else {
                text = "Cancelled Request";
              }

              $this.parents('tr').html("<p style='color:#4cd964'>" + text + "</p>");
            }
        });
    }

    function accept (user_id, obj) {
      $this = obj;

      var dataString = "user_id=" + user_id + "&accept=true&handleRequest=true";


    }

    function decline (user_id, obj) {
      $this = obj;

      var dataString = "user_id=" + user_id + "&handleRequest=true";

      $.ajax({
          type: "POST",
          url: "functions/requests.php",
          data: dataString,
          cache: false,
          success: function(html){
              $this.parents('tr').html("<p style='color:#ff3b30>User declined</p>");
          }
      });
    }

    $(".group").on("click", ".leave", function(){
      var confirmLeave = confirm("Do you really want to leave your current group?");

      if (confirmLeave) {
        var dataString = "leave=true";

        $.ajax({
            type: "POST",
            url: "functions/leaveGroup.php",
            data: dataString,
            cache: false,
            success: function(html){
                location.reload();
            }
        });
      }
    });

    $(".view-groups").on("click", ".remove", function(){
      var confirmLeave = confirm("Do you really want to remove your current group?");

      if (confirmLeave) {
        var dataString = "remove=true";

        $.ajax({
            type: "POST",
            url: "functions/removeGroup.php",
            data: dataString,
            cache: false,
            success: function(html){
                location.reload();
            }
        });
      }
    });
});
