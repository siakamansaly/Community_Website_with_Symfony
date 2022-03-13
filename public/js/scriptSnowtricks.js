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