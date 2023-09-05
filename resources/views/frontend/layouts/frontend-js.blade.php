<script src="{{ asset('public/frontend/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('public/frontend/js/jquery.min.js') }}"></script>
<script src="{{ asset('public/frontend/js/custom.js') }}"></script>
<script src="{{ asset('public/frontend/js/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('public/admin/assets/vendor/js/toastr.min.js') }}"></script>

<script type="text/javascript">

    // Show & Hide Item Review
    $('.add_review_btn').on('click',function(){
        $('#review-info').toggle(500);
        $('#review-info').toggleClass('review-sec-toggle');
        const clsExists = $('#review-info').hasClass('review-sec-toggle');
        if(clsExists == true)
        {
            $(this).html("Cancel Review.");
        }
        else
        {
            $(this).html("Write a Review.");
        }
    });


    // Function for Submit Review
    function submitItemReview()
    {
        // Clear all Toastr Messages
        toastr.clear();

        var myFormData = new FormData(document.getElementById('itemReviewForm'));

        $.ajax({
            type: "POST",
            url: "{{ route('send.item.review') }}",
            data: myFormData,
            dataType: "JSON",
            contentType: false,
            cache: false,
            processData: false,
            success: function (response)
            {
                if(response.success == 1)
                {
                    $('#itemReviewForm').trigger("reset");
                    toastr.success(response.message);
                    setTimeout(() => {
                        location.reload();
                    }, 1200);
                }
                else
                {
                    toastr.error(response.message);
                }
            },
            error: function(response)
            {
                if(response.responseJSON.errors)
                {
                    $.each(response.responseJSON.errors, function (i, error)
                    {
                        toastr.error(error);
                    });
                }
            }
        });
    }


    // Search Items
    $('#search').on('keyup change search',function(){
        var keywords = $(this).val();

        $.ajax({
            type: "POST",
            url: "{{ route('products.search') }}",
            data: {
                "_token": "{{ csrf_token() }}",
                'keywords':keywords,
            },
            dataType: "JSON",
            success: function (response) {
                if (response.success == 1)
                {
                    $('#item_preview_div').html('');
                    $('#item_preview_div').append(response.data);
                    $('#globalSearchModal #search').blur();
                }
                else
                {
                    toastr.error(response.message);
                }
            }
        });
    });

    // Clear Searchs on Close Model
    $('#globalSearchModal .cls-btn').on('click',function(){
        $('#globalSearchModal #item_preview_div').html('');
        $('#globalSearchModal #search').val('');
    });

</script>
