jQuery(document).ready(function(){
  if(typeof(wpcf_repeater) == "undefined" || wpcf_repeater.length == 0){
    return;
  }
  function create_repeater_panel(repeater){
    if(repeater.count >= repeater.max && repeater.max >= 0){
      return;
    }
    var main = jQuery(".wpcf7-repeater-"+repeater.id);
    var html = main.find(".wpcf7-repeater-content").html();
    html = html.replace(/{{repeater}}/g, repeater.index).replace(/{{index}}/g, repeater.item);
    repeater.count = repeater.count + 1;
    repeater.item = repeater.item + 1;
    var list = main.find(".wpcf7-repeater-list");
    list.append('<div class="wpcf7-repeater-item">' + html + '</div>');
    if(repeater.count >= repeater.max - 1 && repeater.max >= 0){
      jQuery(".wpcf7-repeater-" + repeater.id).find("a.wpcf7-repeater-add").hide();
    }
  }
  function remove_repeater_panel(repeater){
    repeater.count = repeater.count - 1;
    if(repeater.count < repeater.max - 1 && repeater.max >= 0){
      jQuery(".wpcf7-repeater-" + repeater.id).find("a.wpcf7-repeater-add").show();
    }
  }
  for(i = 0; i< wpcf_repeater.length; i ++){
    if(wpcf_repeater[i].min > 0){
      for(var j = 0; j < wpcf_repeater[i].min; j++){
        create_repeater_panel(wpcf_repeater[i]);
      }
      jQuery(".wpcf7-repeater-" + wpcf_repeater[i].id).find("a.wpcf7-repeater-remove").remove();
      wpcf_repeater[i].count = wpcf_repeater[i].min - 1;
    }
    if(wpcf_repeater[i].show > wpcf_repeater[i].count){
      var to = wpcf_repeater[i].show - wpcf_repeater[i].count;
      for(var j = 0; j < to; j++){
        create_repeater_panel(wpcf_repeater[i]);
      }
      wpcf_repeater[i].count = wpcf_repeater[i].show;
    }
    jQuery(".wpcf7-repeater-" + wpcf_repeater[i].id).on("click", "a.wpcf7-repeater-add", wpcf_repeater[i], function(event){
      var data = event.data;
      create_repeater_panel(data);
      return false;
    });
    jQuery(".wpcf7-repeater-" + wpcf_repeater[i].id).on("click", "a.wpcf7-repeater-remove", wpcf_repeater[i], function(event){
      jQuery(this).closest(".wpcf7-repeater-item").remove();
      remove_repeater_panel(event.data);
      return false;
    });
  }
});