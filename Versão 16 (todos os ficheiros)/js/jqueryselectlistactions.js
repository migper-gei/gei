/**
 *  jQuery.SelectListActions
 *  https://github.com/esausilva/jquery.selectlistactions.js
 *
 *  (c) http://esausilva.com
 */

(function ($) {

	//alert("a");

    //Moves selected item(s) from sourceList to destinationList
    $.fn.moveToList = function (sourceList, destinationList) {
        var opts = $(sourceList + ' option:selected');
        if (opts.length == 0) {
            //alert("Deve escolher um equipamento.");

            event.preventDefault(); // prevent form submit

            swal({
            
            title: " Deve escolher um equipamento!",
            //text: "Sala: "+s1+" (Escola: "+ne1+")",
            type: "warning",
            //showCancelButton: true,
            //confirmButtonColor: "#DD6B55",
            
            
            confirmButtonText: "OK",
            //cancelButtonText: "Não",
            closeOnConfirm: false,
            closeOnCancel: false
            
            } );

        }

        $(destinationList).append($(opts).clone());
    };

    //Moves all items from sourceList to destinationList
    $.fn.moveAllToList = function (sourceList, destinationList) {
        var opts = $(sourceList + ' option');
        if (opts.length == 0) {
           // alert("Deve escolher um equipamento....");
           event.preventDefault(); // prevent form submit

           swal({
           
           title: " Deve escolher um equipamento!",
           //text: "Sala: "+s1+" (Escola: "+ne1+")",
           type: "warning",
           //showCancelButton: true,
           //confirmButtonColor: "#DD6B55",
           
           
           confirmButtonText: "OK",
           //cancelButtonText: "Não",
           closeOnConfirm: false,
           closeOnCancel: false
           
           } );
        }

        $(destinationList).append($(opts).clone());
    };

    //Moves selected item(s) from sourceList to destinationList and deleting the
    // selected item(s) from the source list
    $.fn.moveToListAndDelete = function (sourceList, destinationList) {
        var opts = $(sourceList + ' option:selected');
        if (opts.length == 0) {
            //alert("Deve escolher um equipamento.--");

            event.preventDefault(); // prevent form submit

            swal({
            
            title: " Deve escolher um equipamento!",
            //text: "Sala: "+s1+" (Escola: "+ne1+")",
            type: "warning",
            //showCancelButton: true,
            //confirmButtonColor: "#DD6B55",
            
            
            confirmButtonText: "OK",
            //cancelButtonText: "Não",
            closeOnConfirm: false,
            closeOnCancel: false
            
            } );
        }

        $(opts).remove();
        $(destinationList).append($(opts).clone());
    };

    //Moves all items from sourceList to destinationList and deleting
    // all items from the source list
    $.fn.moveAllToListAndDelete = function (sourceList, destinationList) {
        var opts = $(sourceList + ' option');
        if (opts.length == 0) {
            //alert("Deve escolher um equipamento.m");
            event.preventDefault(); // prevent form submit

            swal({
            
            title: " Deve escolher um equipamento!",
            //text: "Sala: "+s1+" (Escola: "+ne1+")",
            type: "warning",
            //showCancelButton: true,
            //confirmButtonColor: "#DD6B55",
            
            
            confirmButtonText: "OK",
            //cancelButtonText: "Não",
            closeOnConfirm: false,
            closeOnCancel: false
            
            } );
        }

        $(opts).remove();
        $(destinationList).append($(opts).clone());
    };

    //Removes selected item(s) from list
    $.fn.removeSelected = function (list) {
        var opts = $(list + ' option:selected');
        if (opts.length == 0) {
            //alert("Deve escolher um equipamento.n");
            event.preventDefault(); // prevent form submit

            swal({
            
            title: " Deve escolher um equipamento!",
            //text: "Sala: "+s1+" (Escola: "+ne1+")",
            type: "warning",
            //showCancelButton: true,
            //confirmButtonColor: "#DD6B55",
            
            
            confirmButtonText: "OK",
            //cancelButtonText: "Não",
            closeOnConfirm: false,
            closeOnCancel: false
            
            } );
        }

        $(opts).remove();
    };

    //Moves selected item(s) up or down in a list
    $.fn.moveUpDown = function (list, btnUp, btnDown) {
        var opts = $(list + ' option:selected');
        if (opts.length == 0) {
            //alert("Deve escolher um equipamento.o");
            event.preventDefault(); // prevent form submit

            swal({
            
            title: " Deve escolher um equipamento!",
            //text: "Sala: "+s1+" (Escola: "+ne1+")",
            type: "warning",
            //showCancelButton: true,
            //confirmButtonColor: "#DD6B55",
            
            
            confirmButtonText: "OK",
            //cancelButtonText: "Não",
            closeOnConfirm: false,
            closeOnCancel: false
            
            } );
        }

        if (btnUp) {
            opts.first().prev().before(opts);
        } else if (btnDown) {
            opts.last().next().after(opts);
        }
    };
})(jQuery);

