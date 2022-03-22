$(".img-display").on("click", function (event) {
    event.preventDefault();
    var $this = $(this);
    var url = $this.data("url");

    $("#modal-image-vision").attr('src',url);

});

// Activate SimpleLightbox plugin for portfolio items
new SimpleLightbox({
    elements: '#portfolio a.portfolio-box'
});


var removeButton = "<div class='col-sm-2'><button type='button' class='btn btn-danger btn-xs' onclick='removeFile($(this));'><i class='fa fa-times' aria-hidden='true'></i></button></div>";

function removeFile(ob)
{
	ob.parent().parent().remove();
}

// add-collection-widget.js
jQuery(document).ready(function () {
    jQuery('.add-another-collection-widget').click(function (e) {
        var list = jQuery(jQuery(this).attr('data-list-selector'));
        // Try to find the counter of the list or use the length of the list
        var counter = list.data('widget-counter') || list.children().length;

        // grab the prototype template
        var newWidget = list.attr('data-prototype');
        // replace the "__name__" used in the id and name of the prototype
        // with a number that's unique to your emails
        // end name attribute looks like name="contact[emails][2]"
        newWidget = newWidget.replace(/__name__/g, counter);
        newWidget = newWidget.replace('<div class="col-sm-2"></div>',removeButton);

        //jQuery('<button name="delete' + counter + '" class="delete-employee">Delete</button>').insertAfter(list.attr(newWidget));
        
        // Increase the counter
        counter++;
        // And store it, the length cannot be used if deleting widgets is allowed
        list.data('widget-counter', counter);
        
        // create a new list element and add it to the list
        var newElem = jQuery(list.attr('data-widget-tags')).html(newWidget);
        newElem.appendTo(list);
        
    });
});






    $(function () {
        var id = $("#element").data("id");
        $("[id^='trick']").slice(0, id).show().removeClass('d-none');
        $("#loadMore").on('click', function (e) {
            e.preventDefault();
            $("[id^='trick']:hidden").slice(0, id).fadeIn('slow').removeClass('d-none').slideDown();
            if ($("[id^='trick']:hidden").length == 0) {
                $("#loadMore").fadeOut('slow');
                $("#loadMoreEnd").fadeIn('slow');
            }
        });
    });
    
    $(function () {

        $("#seeMedias").on('click', function (e) {
            e.preventDefault();
            $("#medias").toggle("slow").removeClass('d-none');
            $(this).text(function(i, t) {
                return t == 'Hide Medias' ? 'See Medias' : 'Hide Medias';
              }).fadeIn("slow");
        });
    });

    $(".edit").on("click", function (event) {
        event.preventDefault();
        var $this = $(this);
        var id = $this.data("id");
        var action = $this.data("action");
        var form = $this.data("form");
        if (id==-1)
        {
            $("#title_edit_"+ action).html("Add new "+ action);
        }
        else
        {
            $("#title_edit_"+ action).html("Edit "+ action);
        }
        $("#"+form).val(id);

    });

    $(".deleteTrick").on("click", function (event) {
        event.preventDefault();
        var $this = $(this);
        var id = $this.data("id");
        var title = $this.data("title");
        $("#titleDelete").html(title);
        $("#delete_trick_form_delete").val(id);

    });