$(document).ready(function() {
  // close the current popup
  function closeWindow ($current) {
      $current.slideUp(function(){
          $('.overlay').fadeOut();
      });

      $('body,html').css('overflow-y','auto');
  }

  // open selected popup
  function openWindow ($current) {
      $('.overlay').fadeIn(function(){
          $current.slideDown();
      });

      $('body,html').css('overflow-y','hidden');
  }

  var $add = $('.add-event');

  // Check if option is clicked
  $('#add').click(function(){openWindow($add);});

  // Check if cross is clicked
  $('.add-inner span').click(function(){closeWindow($add);});

  $("input[name='event']").click(function(){
    var title = $("input[name='event-title']").val(),
        description = $("textarea[name='event_description']").val(),
        date = $("input[name='date']").val();

    var dataString = 'addEvent=true&title=' + title + "&description=" + description + "&date=" + date;

    $.ajax({
        type: "POST",
        url: "functions/addEvent.php",
        data:dataString,
        cache: false,
        success: function(html){
            $("label[for='event-title']").html(html);
            $("input[name='event-title']").val(''),
            $("textarea[name='event_description']").val(''),
            $("input[name='date']").val('');
        }
    });

    return false;
  });

  $('.container').on('click', 'td', function() {
    var event_day = $(this).data('day');

    $('.box-title span').html('- ' + event_day);

    var dataString = 'loadItems=true' + "&day=" + event_day;

    $.ajax({
        type: "POST",
        url: "functions/loadItems.php",
        data:dataString,
        cache: false,
        success: function(html){
            $('.events').html(html);
        }
    });
  });

  // $('.event-items').click(function() {
  //   var date = $('.')
  // });
});
