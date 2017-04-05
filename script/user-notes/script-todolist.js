$(document).ready(function() {
  $('.create-list-inner span').click(function(){
    // Close add new list window
    $('.create-list').slideUp(function(){
      $('.overlay').fadeOut();
    })
  });

  $('.add').click(function(){
    // Open add new list window
    $('.overlay').fadeIn(400, function() {
      $('.create-list').slideDown();
    })
  });

  // Add new item
  $('#new-item').click(function(){
    $('#new-item').parent().before("<tr class='create-list-item'><td><input type='text' name='item[]' placeholder='New Item' autocomplete='off'></td><td><img src='css/afbeeldingen/delete.png'></td></tr>");
  });

  // Animate delete
  $('.create-list table').on('mouseenter', 'td img', function() {
      $(this).attr('src', 'css/afbeeldingen/delete-hover.png');
  }).on('mouseleave', 'td img', function() {
      $(this).attr('src', 'css/afbeeldingen/delete.png');
  });;

  // Delete current Item
  $('.create-list table').on('click', 'td img', function() {
    $(this).parent().parent().remove();
  });

  // Submit clicked
  $('#submit').click(function(){
    var length = $('.create-list-item').length,
        title = $('form input:first-child').val(),
        prevent = false,
        error = '',
        allData = [];

    // Get data from items
    $(".create-list-item input[type='text']").each(function() {
        $val = $(this).val();

        if ($val != "") {
          allData.push($val);
        }
    });

    // Valide data form
    if (!title) {
      prevent = true;
      error += 'Please fill in a title<br>';
    }

    if (allData.length == 0) {
      prevent = true
      error += 'No items filled in';
    }

    if (prevent) {
      $('.create-list-inner p:last-of-type').html(error);
      return false;
    }
  });
});
