$(document).ready(function(){
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

    var $join = $('.group-join'),
        $create = $('.group-create');

    // Check if option is clicked
    $('.join').click(function(){openWindow($join);});
    $('.create').click(function(){openWindow($create);});

    // Check if cross is clicked
    $('.create-inner span').click(function(){closeWindow($create);});
    $('.join-inner span').click(function(){closeWindow($join)});

    // When clicked send data to createGroup to make a group
    $(".create-inner input[name='create']").click(function(){
        // Get value user input
        var groupName = $(".create-inner input[name='group_name']").val(),
            groupDescription =  $(".create-inner textarea").val(),
            groupCategory = $(".create-inner input[name='category']:checked").val();

        // Give group category readable value for php
        if (groupCategory == null) {
            groupCategory = '';
        }

        // Setup data to be send
        var dataString = 'groupName=' + groupName + '&groupDescription=' + groupDescription + '&groupCategory=' + groupCategory + '&run=true';

        // ajax call
        $.ajax({
            type: "POST",
            url: "functions/createGroup.php",
            data:dataString,
            cache: false,
            success: function(html){
                $('.group-create label').fadeIn();
                $('.group-create label').html(html);
            }
        });

        return false;
    });

    // Clicked search for groups
    $(".join-inner input[name='submit']").click(function(){
        // Get value user input
        var inputSearch = $(".join-inner input[type='text']").val();

        // Setup datastring
        var dataString = "search=" + inputSearch;

        // If inputSearch is not empty
        if (inputSearch) {
            // Ajax call
            $.ajax({
                type: "POST",
                url: "functions/searchGroup.php",
                data: dataString,
                cache: false,
                beforeSend: function(html) {
                    $('.results').html("");
                },
                success: function(html){
                    $('.results').html(html);
                }
            });
        } else {
            $('.results').html("<li style='color:red'>Type something to search</li>")
        }

        return false;
    });

    // Animate search list
    $('.results').on('click','.list-text',function(){
        $(this).parent().animate({
            marginLeft: '-100%'
        });
    });

    $('.results').on('click','.list-join div:last-child',function(){
        $(this).closest('li').animate({
            marginLeft: '0'
        });
    });

    // Check if send request is clicked
    $('.results').on('click','#send_request', function(){
        // Get Group id value
        var group_id = $(this).parent().parent().find('#group_id').text();

        // Setup data to be send
        var dataString = 'groupId=' + group_id + '&request=true';

        $.ajax({
                type: "POST",
                url: "functions/sendRequest.php",
                data: dataString,
                cache: false,
                beforeSend: function(html) {
                    $('.results').html("");
                },
                success: function(html){
                    $('.results').html(html);
                }
            });
    });

    if ($('form').hasClass('disabled')) {
      $('form :input').attr('readonly', 'true');
    }

});
