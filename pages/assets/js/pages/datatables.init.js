$(document).ready(function () {
  // Basic table
  if ($("#datatable").length && $.fn.DataTable) {
    $("#datatable").DataTable();
  }

  // Buttons table (only if Buttons extension is loaded)
  if ($("#datatable-buttons").length && $.fn.DataTable) {
    var hasButtons =
      $.fn.dataTable &&
      ($.fn.dataTable.Buttons || ($.fn.DataTable && $.fn.DataTable.Buttons));

    if (hasButtons) {
      var dt = $("#datatable-buttons").DataTable({
        lengthChange: !1,
        buttons: ["copy", "excel", "pdf", "colvis"],
      });

      if (dt.buttons && typeof dt.buttons === "function") {
        dt.buttons()
          .container()
          .appendTo("#datatable-buttons_wrapper .col-md-6:eq(0)");
      }
    } else {
      // Fallback: initialize without buttons instead of throwing an error
      $("#datatable-buttons").DataTable({ lengthChange: !1 });
    }
  }

  $(".dataTables_length select").addClass("form-select form-select-sm");
});