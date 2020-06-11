cj(function($) {
  CRM.alert(ts('dsa.js is loaded!'));

  /*
  function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++) {
      var sParameterName = sURLVariables[i].split('=');
      if (sParameterName[0] == sParam) {
        return sParameterName[1];
      }
    }
  }
  */

  /*
  //cj("[data-crm-custom='DSA_Main_Activity:DSA_Country']")
  cj(document).ready(function() {
    cj("select")
      .change(function() {
        alert('changed');
      })
      .change();
  });
  */

  /*
  $( "select" )
    .change(function () {
      var str = "";
      $( "select option:selected" ).each(function() {
        str += $( this ).text() + " ";
      });
      $( "div" ).text( str );
    })
  .change();
  */

});
